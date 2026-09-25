<?php
$page_title = "SkopeStay ERP - Supply Chain & Procurement";
require_once __DIR__ . '/../includes/config.php';

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Register Supplier
    if ($action === 'add_supplier') {
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'F&B Provisions');
        $contact = trim($_POST['contact_person'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $tax_pin = trim($_POST['tax_pin'] ?? '');
        $terms = trim($_POST['payment_terms'] ?? 'Net 30 Days');

        $count = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn() + 1;
        $code = 'SUP-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        try {
            $stmt = $pdo->prepare("INSERT INTO suppliers (supplier_code, name, category, contact_person, email, phone, address, tax_pin, payment_terms, status) VALUES (?,?,?,?,?,?,?,?,?, 'Active')");
            $stmt->execute([$code, $name, $category, $contact, $email, $phone, $address, $tax_pin, $terms]);
            header("Location: procurement.php?tab=suppliers&msg=supplier_added");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error registering supplier: " . $e->getMessage();
        }
    }

    // 2. Create Purchase Order
    if ($action === 'create_po') {
        $supplier_id = (int)$_POST['supplier_id'];
        $expected_date = $_POST['expected_date'] ?? date('Y-m-d', strtotime('+7 days'));
        $notes = trim($_POST['notes'] ?? '');
        
        $item_names = $_POST['item_name'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unit_prices = $_POST['unit_price'] ?? [];

        $subtotal = 0;
        for($i=0; $i<count($item_names); $i++) {
            $qty = (int)($quantities[$i] ?? 1);
            $price = (float)($unit_prices[$i] ?? 0);
            $subtotal += ($qty * $price);
        }
        $tax = round($subtotal * 0.16); // 16% VAT
        $total = $subtotal + $tax;

        $poCount = $pdo->query("SELECT COUNT(*) FROM purchase_orders")->fetchColumn() + 1;
        $poNumber = 'PO-' . date('Y') . '-' . str_pad($poCount, 3, '0', STR_PAD_LEFT);

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO purchase_orders (po_number, supplier_id, order_date, expected_date, subtotal, tax, total_amount, status, notes, created_by) VALUES (?, ?, CURDATE(), ?, ?, ?, ?, 'Pending Approval', ?, 'Procurement Officer')");
            $stmt->execute([$poNumber, $supplier_id, $expected_date, $subtotal, $tax, $total, $notes]);
            $po_id = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO purchase_order_items (po_id, item_name, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
            for($i=0; $i<count($item_names); $i++) {
                if(!empty($item_names[$i])) {
                    $qty = (int)$quantities[$i];
                    $price = (float)$unit_prices[$i];
                    $lineTotal = $qty * $price;
                    $stmtItem->execute([$po_id, $item_names[$i], $qty, $price, $lineTotal]);
                }
            }
            $pdo->commit();
            header("Location: procurement.php?tab=orders&msg=po_created");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Error creating purchase order: " . $e->getMessage();
        }
    }

    // 3. Update PO Status
    if ($action === 'update_po_status') {
        $po_id = (int)$_POST['po_id'];
        $new_status = $_POST['status'];

        try {
            if ($new_status === 'Received') {
                // Auto-Restock inventory if matches name
                $items = $pdo->prepare("SELECT * FROM purchase_order_items WHERE po_id = ?");
                $items->execute([$po_id]);
                $poItems = $items->fetchAll(PDO::FETCH_ASSOC);

                foreach($poItems as $pi) {
                    $stmtInv = $pdo->prepare("UPDATE inventory_items SET current_stock = current_stock + ?, status = 'In Stock' WHERE name LIKE ?");
                    $stmtInv->execute([$pi['quantity'], '%' . $pi['item_name'] . '%']);
                }
            }

            $stmt = $pdo->prepare("UPDATE purchase_orders SET status = ?, approved_by = IF(?='Approved', 'General Manager', approved_by) WHERE id = ?");
            $stmt->execute([$new_status, $new_status, $po_id]);
            header("Location: procurement.php?tab=orders&msg=status_updated");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error updating PO status: " . $e->getMessage();
        }
    }
}

$active_tab = $_GET['tab'] ?? 'orders';

// Metrics
$total_suppliers = $pdo->query("SELECT COUNT(*) FROM suppliers WHERE status='Active'")->fetchColumn() ?: 0;
$pending_pos = $pdo->query("SELECT COUNT(*) FROM purchase_orders WHERE status IN ('Pending Approval','Draft')")->fetchColumn() ?: 0;
$approved_pos_total = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM purchase_orders WHERE status IN ('Approved','Received') AND MONTH(order_date)=MONTH(CURDATE())")->fetchColumn() ?: 0;
$received_pos_count = $pdo->query("SELECT COUNT(*) FROM purchase_orders WHERE status='Received'")->fetchColumn() ?: 0;

// Fetch Suppliers
$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch POs
$purchase_orders = $pdo->query("
    SELECT po.*, s.name as supplier_name, s.category as supplier_category, s.phone as supplier_phone
    FROM purchase_orders po
    JOIN suppliers s ON po.supplier_id = s.id
    ORDER BY po.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="md:ml-64 min-h-screen flex flex-col">
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="flex-1 p-4 md:p-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-primary bg-primary/10 px-2.5 py-1 rounded-full">Hospitality ERP</span>
                <span class="text-xs font-semibold text-gray-500">Supply Chain Management (SCM)</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-on-surface mt-1">Procurement & Purchase Orders</h2>
            <p class="text-sm text-gray-500">Oversee suppliers, approve purchase requisitions, and automate inventory stock inflows.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('po-modal')" class="bg-primary hover:bg-primary/90 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                <span>Create Purchase Order</span>
            </button>
            <button onclick="openModal('supplier-modal')" class="bg-navy hover:bg-navy/90 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">domain_add</span>
                <span>Register Vendor</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if(!empty($error_msg)): ?>
    <div class="p-4 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm flex items-center gap-2">
        <span class="material-symbols-outlined">error</span>
        <span><?= htmlspecialchars($error_msg) ?></span>
    </div>
    <?php endif; ?>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">local_shipping</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Approved Vendors</span>
                <span class="text-2xl font-bold text-navy"><?= $total_suppliers ?></span>
                <span class="text-[11px] text-gray-500 block">Active Supply Contracts</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">pending_actions</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Pending POs</span>
                <span class="text-2xl font-bold text-amber-600"><?= $pending_pos ?></span>
                <span class="text-[11px] text-gray-500 block">Awaiting Management</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Monthly SCM Spend</span>
                <span class="text-xl sm:text-2xl font-bold text-navy">KSh <?= number_format($approved_pos_total, 0) ?></span>
                <span class="text-[11px] text-purple-600 block">Goods & Services</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">inventory</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Fulfilled Orders</span>
                <span class="text-2xl font-bold text-emerald-600"><?= $received_pos_count ?></span>
                <span class="text-[11px] text-emerald-600 block">Stock Auto-Replenished</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 overflow-x-auto gap-2">
        <a href="?tab=orders" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'orders' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Purchase Orders (<?= count($purchase_orders) ?>)
        </a>
        <a href="?tab=suppliers" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'suppliers' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Vendor Directory (<?= count($suppliers) ?>)
        </a>
    </div>

    <!-- TAB 1: PURCHASE ORDERS -->
    <?php if($active_tab === 'orders'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-navy">Purchase Order Register</h3>
            <button onclick="openModal('po-modal')" class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow">
                + New Requisition
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 font-bold uppercase text-[11px] tracking-wider">
                        <th class="p-4">PO Number</th>
                        <th class="p-4">Vendor</th>
                        <th class="p-4">Order Date</th>
                        <th class="p-4">Delivery Due</th>
                        <th class="p-4">Total (Inc. VAT)</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Workflow Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($purchase_orders as $po): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 font-mono font-bold text-navy">
                            <?= htmlspecialchars($po['po_number']) ?>
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-navy block"><?= htmlspecialchars($po['supplier_name']) ?></span>
                            <span class="text-xs text-gray-400"><?= htmlspecialchars($po['supplier_category']) ?></span>
                        </td>
                        <td class="p-4 text-gray-600">
                            <?= date('M d, Y', strtotime($po['order_date'])) ?>
                        </td>
                        <td class="p-4 text-gray-600">
                            <?= $po['expected_date'] ? date('M d, Y', strtotime($po['expected_date'])) : 'N/A' ?>
                        </td>
                        <td class="p-4 font-bold text-navy">
                            KSh <?= number_format($po['total_amount'], 2) ?>
                        </td>
                        <td class="p-4">
                            <?php 
                                $statusColors = [
                                    'Draft' => 'bg-gray-100 text-gray-700',
                                    'Pending Approval' => 'bg-amber-100 text-amber-700',
                                    'Approved' => 'bg-blue-100 text-blue-700',
                                    'Received' => 'bg-emerald-100 text-emerald-700',
                                    'Cancelled' => 'bg-red-100 text-red-700'
                                ];
                                $stColor = $statusColors[$po['status']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $stColor ?>">
                                <?= htmlspecialchars($po['status']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-1">
                            <?php if($po['status'] === 'Pending Approval'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_po_status">
                                <input type="hidden" name="po_id" value="<?= $po['id'] ?>">
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700">Approve</button>
                            </form>
                            <?php elseif($po['status'] === 'Approved'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_po_status">
                                <input type="hidden" name="po_id" value="<?= $po['id'] ?>">
                                <input type="hidden" name="status" value="Received">
                                <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700" title="Receive shipment & credit inventory stock">
                                    Receive & Restock
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="text-xs text-gray-400 font-semibold">Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 2: VENDORS -->
    <?php if($active_tab === 'suppliers'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden p-6 space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg text-navy">Approved Suppliers & Trade Accounts</h3>
                <p class="text-xs text-gray-500">Vetted hospitality vendors for farm produce, cellar wines, luxury linens, and facility engineering.</p>
            </div>
            <button onclick="openModal('supplier-modal')" class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow">
                + Register Supplier
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($suppliers as $sup): ?>
            <div class="p-5 rounded-2xl border border-gray-100 bg-gray-50/40 hover:border-primary/40 transition-all flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-gray-200 text-gray-700"><?= htmlspecialchars($sup['supplier_code']) ?></span>
                        <div class="flex items-center text-gold text-xs font-bold">
                            <span class="material-symbols-outlined text-sm">star</span>
                            <span><?= htmlspecialchars($sup['rating'] ?? '4.8') ?></span>
                        </div>
                    </div>
                    <h4 class="font-bold text-navy text-base"><?= htmlspecialchars($sup['name']) ?></h4>
                    <span class="text-xs text-primary font-semibold block mb-3"><?= htmlspecialchars($sup['category']) ?></span>
                    
                    <div class="space-y-1 text-xs text-gray-600">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px] text-gray-400">person</span>
                            <span><?= htmlspecialchars($sup['contact_person'] ?? 'Account Rep') ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px] text-gray-400">call</span>
                            <span><?= htmlspecialchars($sup['phone']) ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[15px] text-gray-400">mail</span>
                            <span><?= htmlspecialchars($sup['email']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="text-gray-400">Terms: <strong class="text-gray-700"><?= htmlspecialchars($sup['payment_terms']) ?></strong></span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Active</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
</main>

<!-- MODAL 1: CREATE PO -->
<div id="po-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-lg text-navy">Raise Purchase Order (PO)</h3>
            <button onclick="closeModal('po-modal')" class="text-gray-400 hover:text-navy"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" class="space-y-4 text-xs sm:text-sm">
            <input type="hidden" name="action" value="create_po">
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Select Supplier *</label>
                    <select name="supplier_id" required class="w-full border rounded-xl p-2.5">
                        <?php foreach($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['category']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Expected Delivery Date</label>
                    <input type="date" name="expected_date" value="<?= date('Y-m-d', strtotime('+5 days')) ?>" class="w-full border rounded-xl p-2.5">
                </div>
            </div>

            <!-- Dynamic Line Items -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">Order Line Items</label>
                <div class="space-y-2" id="po-items-container">
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <input type="text" name="item_name[]" placeholder="Item description" required class="col-span-6 border rounded-xl p-2 text-xs">
                        <input type="number" name="quantity[]" placeholder="Qty" value="10" required class="col-span-3 border rounded-xl p-2 text-xs">
                        <input type="number" step="0.01" name="unit_price[]" placeholder="Unit Price" value="500" required class="col-span-3 border rounded-xl p-2 text-xs">
                    </div>
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <input type="text" name="item_name[]" placeholder="Item description" class="col-span-6 border rounded-xl p-2 text-xs">
                        <input type="number" name="quantity[]" placeholder="Qty" value="5" class="col-span-3 border rounded-xl p-2 text-xs">
                        <input type="number" step="0.01" name="unit_price[]" placeholder="Unit Price" value="1200" class="col-span-3 border rounded-xl p-2 text-xs">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Requisition Notes</label>
                <textarea name="notes" rows="2" placeholder="Delivery notes or department specification..." class="w-full border rounded-xl p-2.5"></textarea>
            </div>

            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow hover:bg-primary/90 mt-2">
                Submit Requisition for Approval
            </button>
        </form>
    </div>
</div>

<!-- MODAL 2: REGISTER SUPPLIER -->
<div id="supplier-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-lg text-navy">Register New Trade Vendor</h3>
            <button onclick="closeModal('supplier-modal')" class="text-gray-400 hover:text-navy"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" class="space-y-3 text-xs sm:text-sm">
            <input type="hidden" name="action" value="add_supplier">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Company / Vendor Name *</label>
                <input type="text" name="name" required placeholder="e.g. Apex Hospitality Supplies" class="w-full border rounded-xl p-2.5">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full border rounded-xl p-2.5">
                        <option value="F&B Provisions">F&B Provisions</option>
                        <option value="Beverages & Spirits">Beverages & Spirits</option>
                        <option value="Housekeeping Supplies">Housekeeping & Linen</option>
                        <option value="Engineering & Spares">Engineering & Spares</option>
                        <option value="Pool Chemicals">Pool Chemicals</option>
                        <option value="Stationery & IT">Stationery & IT</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="Sales Manager" class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required placeholder="orders@vendor.ke" class="w-full border rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Phone Number *</label>
                    <input type="tel" name="phone" required placeholder="+254 7..." class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tax PIN</label>
                    <input type="text" name="tax_pin" placeholder="P05..." class="w-full border rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Payment Terms</label>
                    <select name="payment_terms" class="w-full border rounded-xl p-2.5">
                        <option value="Immediate / Cash">Immediate / Cash</option>
                        <option value="Net 14 Days">Net 14 Days</option>
                        <option value="Net 30 Days" selected>Net 30 Days</option>
                        <option value="Net 60 Days">Net 60 Days</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Warehouse / Physical Address</label>
                <input type="text" name="address" placeholder="Physical premises..." class="w-full border rounded-xl p-2.5">
            </div>
            <button type="submit" class="w-full bg-navy text-white font-bold py-3 rounded-xl shadow hover:bg-navy/90 mt-2">
                Commit Vendor to ERP
            </button>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }
</script>

</body>
</html>
