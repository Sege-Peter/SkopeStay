<?php
$page_title = "Inventory Management | SkopeStay";
require_once '../includes/config.php';

$items = $pdo->query("SELECT * FROM inventory_items ORDER BY name ASC")->fetchAll();
$total    = count($items);
$low      = count(array_filter($items, fn($i) => $i['status'] === 'Low Stock'));
$out      = count(array_filter($items, fn($i) => $i['status'] === 'Out of Stock'));
$in_stock = count(array_filter($items, fn($i) => $i['status'] === 'In Stock'));

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">

<?php include '../includes/sidebar.php'; ?>

<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>

<main class="p-4 md:p-8 flex-grow space-y-6">

    <!-- Page Title -->
    <div class="flex justify-between items-end flex-wrap gap-3">
        <div>
            <nav class="flex items-center gap-2 text-xs font-semibold text-on-surface-variant mb-2">
                <span>Management</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold" id="breadcrumb-label">Inventory</span>
            </nav>
            <h1 class="font-bold text-2xl md:text-3xl text-on-surface" id="page-heading">Stock Repository</h1>
            <p class="text-on-surface-variant text-sm mt-1">Track and manage assets across all SkopeStay departments.</p>
        </div>
        <div class="flex gap-2 items-center">
            <!-- Tab Switcher -->
            <div class="flex bg-surface-container-high rounded-xl p-1 gap-1">
                <button id="tab-inventory" onclick="switchTab('inventory')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all bg-white text-primary shadow">
                    <span class="material-symbols-outlined text-[14px] align-middle mr-1">inventory_2</span>Stock
                </button>
                <button id="tab-menu" onclick="switchTab('menu')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all text-on-surface-variant hover:bg-white/50">
                    <span class="material-symbols-outlined text-[14px] align-middle mr-1">restaurant_menu</span>Menu
                </button>
            </div>
            <button id="add-btn-inventory" onclick="openModal('item-modal')" class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span> Add Item
            </button>
            <button id="add-btn-menu" onclick="openMenuModal()" class="hidden flex items-center gap-2 bg-amber-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-amber-600 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span> Add Dish
            </button>
        </div>
    </div>

    <!-- KPI Strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-4 flex items-center gap-4 group hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary transition-all">
                <span class="material-symbols-outlined text-primary group-hover:text-white transition-all">inventory</span>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase">Total Items</p>
                <p class="text-3xl font-extrabold text-on-surface"><?= $total ?></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-4 flex items-center gap-4 group hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-500 transition-all">
                <span class="material-symbols-outlined text-green-600 group-hover:text-white transition-all">check_circle</span>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase">In Stock</p>
                <p class="text-3xl font-extrabold text-green-600"><?= $in_stock ?></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-secondary-container shadow-sm p-4 flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute top-0 right-0 w-1.5 h-full bg-secondary"></div>
            <div class="w-12 h-12 bg-secondary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-on-secondary-container">warning</span>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase">Low Stock</p>
                <p class="text-3xl font-extrabold text-secondary"><?= $low ?></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-error/30 shadow-sm p-4 flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute top-0 right-0 w-1.5 h-full bg-error"></div>
            <div class="w-12 h-12 bg-error-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-error">error</span>
            </div>
            <div>
                <p class="text-xs text-on-surface-variant font-semibold uppercase">Out of Stock</p>
                <p class="text-3xl font-extrabold text-error"><?= $out ?></p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-4 flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[240px]">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
            <input id="inv-search" oninput="filterTable()" type="text" placeholder="Search by name, SKU or supplier..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-outline-variant text-sm bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
        </div>
        <select id="cat-filter" onchange="filterTable()" class="px-4 py-2.5 rounded-xl border border-outline-variant text-sm bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all min-w-[160px]">
            <option value="">All Categories</option>
            <option>Housekeeping</option>
            <option>F&B</option>
            <option>Maintenance</option>
            <option>Stationery</option>
        </select>
        <select id="status-filter" onchange="filterTable()" class="px-4 py-2.5 rounded-xl border border-outline-variant text-sm bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all min-w-[140px]">
            <option value="">All Statuses</option>
            <option>In Stock</option>
            <option>Low Stock</option>
            <option>Out of Stock</option>
        </select>
        <button onclick="exportCSV()" class="flex items-center gap-2 px-4 py-2.5 bg-surface-container rounded-xl border border-outline-variant text-sm font-semibold text-on-surface hover:bg-surface-container-high transition-colors ml-auto">
            <span class="material-symbols-outlined text-[18px]">download</span> Export CSV
        </button>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="inv-table">
                <thead>
                    <tr class="bg-surface-container border-b border-outline-variant/30">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-on-surface">Item</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-on-surface hidden md:table-cell">Category</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-on-surface">Stock</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-on-surface hidden lg:table-cell">Min Level</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-on-surface">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-on-surface hidden lg:table-cell">Supplier</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-on-surface text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20" id="inv-tbody">
                    <?php if (empty($items)): ?>
                    <tr><td colspan="7" class="text-center py-16 text-on-surface-variant opacity-40">
                        <span class="material-symbols-outlined text-5xl block mb-2">inventory_2</span>
                        No inventory items yet. Add your first item!
                    </td></tr>
                    <?php else: foreach ($items as $item):
                        $statusMap = [
                            'In Stock'    => 'bg-green-100 text-green-700 border-green-200',
                            'Low Stock'   => 'bg-secondary-container text-on-secondary-container border-secondary-container',
                            'Out of Stock'=> 'bg-error-container text-error border-error/20',
                        ];
                        $sBadge = $statusMap[$item['status']] ?? $statusMap['In Stock'];
                        $stockColor = $item['status'] === 'Out of Stock' ? 'text-error' : ($item['status'] === 'Low Stock' ? 'text-secondary' : 'text-green-600');
                    ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors inv-row"
                        data-name="<?= strtolower(htmlspecialchars($item['name'])) ?>"
                        data-sku="<?= strtolower(htmlspecialchars($item['sku'])) ?>"
                        data-supplier="<?= strtolower(htmlspecialchars($item['supplier'] ?? '')) ?>"
                        data-category="<?= htmlspecialchars($item['category']) ?>"
                        data-status="<?= htmlspecialchars($item['status']) ?>">
                        <td class="px-5 py-4">
                            <div>
                                <p class="font-semibold text-sm text-on-surface"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-[11px] text-on-surface-variant uppercase tracking-wider mt-0.5">SKU: <?= htmlspecialchars($item['sku']) ?></p>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-on-surface-variant hidden md:table-cell"><?= htmlspecialchars($item['category']) ?></td>
                        <td class="px-5 py-4">
                            <div>
                                <span class="font-extrabold text-base <?= $stockColor ?>"><?= $item['current_stock'] ?></span>
                                <span class="text-[11px] text-on-surface-variant ml-1">units</span>
                                <?php if ($item['status'] !== 'Out of Stock'): ?>
                                <div class="w-full bg-surface-container rounded-full h-1.5 mt-1.5">
                                    <?php $pct = $item['min_level'] > 0 ? min(100, round(($item['current_stock'] / ($item['min_level'] * 3)) * 100)) : 100; ?>
                                    <div class="h-full rounded-full <?= $item['status'] === 'Low Stock' ? 'bg-secondary' : 'bg-green-500' ?>" style="width:<?= $pct ?>%"></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-on-surface-variant hidden lg:table-cell"><?= $item['min_level'] ?> units</td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-full border text-[11px] font-bold uppercase tracking-wide <?= $sBadge ?>"><?= $item['status'] ?></span>
                        </td>
                        <td class="px-5 py-4 text-sm text-on-surface-variant hidden lg:table-cell"><?= htmlspecialchars($item['supplier'] ?? '—') ?></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <?php if ($item['status'] !== 'In Stock'): ?>
                                <button onclick="reorderItem(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>')" class="px-3 py-1.5 text-[11px] font-bold bg-secondary-container text-on-secondary-container rounded-lg hover:opacity-90 transition-opacity active:scale-95">
                                    Order More
                                </button>
                                <?php endif; ?>
                                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($item)) ?>)" class="p-1.5 text-on-surface-variant hover:text-primary transition-colors" title="Edit">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <button onclick="deleteItem(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>')" class="p-1.5 text-on-surface-variant hover:text-error transition-colors" title="Delete">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- RESTAURANT MENU SECTION (hidden by default)                      -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div id="section-menu" class="hidden space-y-5">
        <!-- Search -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-4 flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-[220px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input id="menu-search" oninput="filterMenuCards()" type="text" placeholder="Search dishes..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-outline-variant text-sm bg-surface-container-low focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 outline-none transition-all"/>
            </div>
            <select id="menu-cat-filter" onchange="filterMenuCards()" class="px-4 py-2.5 rounded-xl border border-outline-variant text-sm bg-surface-container-low outline-none min-w-[160px]">
                <option value="">All Categories</option>
                <option>Breakfast</option><option>Lunch</option><option>Dinner</option>
                <option>Drinks</option><option>Desserts</option><option>Snacks</option>
            </select>
            <select id="menu-status-filter" onchange="filterMenuCards()" class="px-4 py-2.5 rounded-xl border border-outline-variant text-sm bg-surface-container-low outline-none min-w-[140px]">
                <option value="">All Statuses</option>
                <option>Available</option>
                <option>Unavailable</option>
            </select>
        </div>

        <!-- Cards Grid -->
        <div id="menu-grid-admin" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <div class="col-span-full flex items-center justify-center h-48 text-on-surface-variant opacity-40">
                <span class="material-symbols-outlined text-5xl animate-spin">sync</span>
            </div>
        </div>
    </div>

</main>

<?php include '../includes/footer.php'; ?>
</div>

<!-- ===================== ADD/EDIT ITEM MODAL ===================== -->
<div id="item-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('item-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                </div>
                <div>
                    <h3 id="modal-title" class="font-bold text-on-surface text-base">Add New Item</h3>
                    <p class="text-xs text-on-surface-variant">Fill in the stock item details</p>
                </div>
            </div>
            <button onclick="closeModal('item-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form id="item-form" class="p-6 space-y-4">
            <input type="hidden" name="id" id="item-id"/>
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Item Name *</label>
                    <input type="text" name="name" id="item-name" required placeholder="e.g. Premium Bed Sheets" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">SKU Code *</label>
                    <input type="text" name="sku" id="item-sku" required placeholder="e.g. SS-HK-099" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Category *</label>
                    <select name="category" id="item-category" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all">
                        <option value="">-- Select --</option>
                        <option>Housekeeping</option>
                        <option>F&B</option>
                        <option>Maintenance</option>
                        <option>Stationery</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Current Stock *</label>
                    <input type="number" name="current_stock" id="item-stock" required min="0" placeholder="e.g. 100" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Minimum Level *</label>
                    <input type="number" name="min_level" id="item-min" required min="0" placeholder="e.g. 30" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Supplier</label>
                    <input type="text" name="supplier" id="item-supplier" placeholder="e.g. CleanPro Supplies" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('item-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" id="item-submit-btn" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span id="item-btn-label">Save Item</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- RESTAURANT MENU ITEM MODAL                                   -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div id="menu-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('menu-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-auto animate-modal flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600">restaurant_menu</span>
                </div>
                <div>
                    <h3 id="menu-modal-title" class="font-bold text-on-surface text-base">Add New Dish</h3>
                    <p class="text-xs text-on-surface-variant">Fill in the menu item details below</p>
                </div>
            </div>
            <button onclick="closeModal('menu-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 p-6">
            <form id="menu-item-form" enctype="multipart/form-data">
                <input type="hidden" id="menu-item-id" name="id">
                <input type="hidden" id="menu-item-old-image" name="image_url">

                <!-- Image Upload Section -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-2">Dish Photo</label>
                    <div class="flex gap-3 items-start">
                        <!-- Preview -->
                        <div id="img-preview-wrap" class="w-28 h-28 rounded-2xl overflow-hidden bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center flex-shrink-0 relative">
                            <img id="img-preview" src="" alt="" class="w-full h-full object-cover hidden">
                            <span id="img-preview-icon" class="text-4xl">🍽️</span>
                        </div>
                        <div class="flex-1 space-y-2">
                            <!-- Upload from file -->
                            <label class="cursor-pointer flex items-center gap-2 px-4 py-2.5 bg-surface-container-high rounded-xl border border-outline-variant text-sm font-semibold text-on-surface hover:bg-surface-container transition-colors w-full justify-center">
                                <span class="material-symbols-outlined text-[18px]">upload_file</span> Upload Image
                                <input type="file" id="menu-image-file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                            </label>
                            <!-- Camera capture -->
                            <button type="button" onclick="startCamera()" class="flex items-center gap-2 px-4 py-2.5 bg-blue-50 rounded-xl border border-blue-200 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition-colors w-full justify-center">
                                <span class="material-symbols-outlined text-[18px]">photo_camera</span> Use Camera
                            </button>
                            <p class="text-[10px] text-gray-400 text-center">JPG, PNG, WEBP — max 5MB</p>
                        </div>
                    </div>
                    <!-- Camera UI -->
                    <div id="camera-container" class="hidden mt-3 rounded-2xl overflow-hidden border border-blue-200 bg-black relative">
                        <video id="camera-video" class="w-full max-h-56 object-cover" autoplay playsinline></video>
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-3">
                            <button type="button" onclick="capturePhoto()" class="bg-white text-gray-900 px-5 py-2 rounded-full font-bold text-sm flex items-center gap-2 shadow-lg hover:bg-amber-400 hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[18px]">camera</span> Capture
                            </button>
                            <button type="button" onclick="stopCamera()" class="bg-red-500 text-white px-4 py-2 rounded-full font-bold text-sm shadow-lg">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                        <canvas id="camera-canvas" class="hidden"></canvas>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Dish Name *</label>
                        <input type="text" name="name" id="menu-name" required placeholder="e.g. Chicken Biryani" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 outline-none text-sm bg-surface-container-low transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Category *</label>
                        <select name="category" id="menu-category" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 outline-none text-sm bg-surface-container-low transition-all">
                            <option value="">-- Select --</option>
                            <option>Breakfast</option><option>Lunch</option><option>Dinner</option>
                            <option>Drinks</option><option>Desserts</option><option>Snacks</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Price (KSh) *</label>
                        <input type="number" name="price" id="menu-price" required min="1" placeholder="e.g. 850" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 outline-none text-sm bg-surface-container-low transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Emoji Icon</label>
                        <input type="text" name="emoji" id="menu-emoji" placeholder="🍽️" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-amber-400 outline-none text-sm bg-surface-container-low transition-all" oninput="document.getElementById('img-preview-icon').textContent = this.value || '🍽️'">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Badge Label</label>
                        <input type="text" name="badge" id="menu-badge" placeholder="e.g. POPULAR, NEW" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-amber-400 outline-none text-sm bg-surface-container-low transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Availability</label>
                        <select name="status" id="menu-status" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-amber-400 outline-none text-sm bg-surface-container-low transition-all">
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Description</label>
                        <textarea name="description" id="menu-description" rows="2" placeholder="Short description of the dish..." class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 outline-none text-sm bg-surface-container-low transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('menu-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                    <button type="submit" id="menu-submit-btn" class="flex-1 py-3 rounded-xl bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span id="menu-btn-label">Save Dish</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes modal-in {
    from { opacity:0; transform: scale(0.95) translateY(20px); }
    to   { opacity:1; transform: scale(1) translateY(0); }
}
.animate-modal { animation: modal-in 0.25s cubic-bezier(0.2,0.8,0.2,1) forwards; }
.menu-admin-card { transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.35s ease; }
.menu-admin-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px -8px rgba(0,0,0,0.15); }
</style>

<script>
function openModal(id)  { const el=document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
function closeModal(id) { const el=document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); stopCamera(); }

// ══════════════════════════════════════════════════════════════
// TAB SWITCHER
// ══════════════════════════════════════════════════════════════
function switchTab(tab) {
    const isMenu = tab === 'menu';
    // Sections
    document.getElementById('section-menu').classList.toggle('hidden', !isMenu);
    // KPI strip + filter bar + table (everything except section-menu and headings)
    document.querySelectorAll('main > .grid, main > .bg-white.rounded-2xl.border, main > .bg-white.rounded-2xl.overflow-hidden').forEach(el => {
        el.classList.toggle('hidden', isMenu);
    });
    // Tab buttons
    document.getElementById('tab-inventory').className = `px-4 py-2 rounded-lg text-xs font-bold transition-all ${!isMenu ? 'bg-white text-primary shadow' : 'text-on-surface-variant hover:bg-white/50'}`;
    document.getElementById('tab-menu').className      = `px-4 py-2 rounded-lg text-xs font-bold transition-all ${isMenu  ? 'bg-white text-amber-600 shadow' : 'text-on-surface-variant hover:bg-white/50'}`;
    // Add buttons
    document.getElementById('add-btn-inventory').classList.toggle('hidden', isMenu);
    document.getElementById('add-btn-menu').classList.toggle('hidden', !isMenu);
    // Breadcrumb + heading
    document.getElementById('breadcrumb-label').textContent = isMenu ? 'Restaurant Menu' : 'Inventory';
    document.getElementById('page-heading').textContent = isMenu ? 'Menu Management' : 'Stock Repository';
    if (isMenu) loadMenuItems();
}

function showSuccess(msg) { Swal.fire({icon:'success',title:'Success!',text:msg,timer:3000,timerProgressBar:true,showConfirmButton:false,toast:true,position:'top-end'}); }
function showError(msg) { Swal.fire({icon:'error',title:'Error',text:msg,confirmButtonColor:'rgb(37,99,235)'}); }

// ---- Client-side filter ----
function filterTable() {
    const q    = document.getElementById('inv-search').value.toLowerCase();
    const cat  = document.getElementById('cat-filter').value;
    const stat = document.getElementById('status-filter').value;
    document.querySelectorAll('.inv-row').forEach(row => {
        const matchQ  = !q   || row.dataset.name.includes(q) || row.dataset.sku.includes(q) || row.dataset.supplier.includes(q);
        const matchC  = !cat || row.dataset.category === cat;
        const matchS  = !stat|| row.dataset.status === stat;
        row.style.display = (matchQ && matchC && matchS) ? '' : 'none';
    });
}

// ---- Open modal for editing ----
let editMode = false;
function openEditModal(item) {
    editMode = true;
    document.getElementById('modal-title').textContent = 'Edit Item';
    document.getElementById('item-btn-label').textContent = 'Update Item';
    document.getElementById('item-id').value       = item.id;
    document.getElementById('item-name').value     = item.name;
    document.getElementById('item-sku').value      = item.sku;
    document.getElementById('item-sku').disabled   = true; // SKU shouldn't change
    document.getElementById('item-category').value = item.category;
    document.getElementById('item-stock').value    = item.current_stock;
    document.getElementById('item-min').value      = item.min_level;
    document.getElementById('item-supplier').value = item.supplier || '';
    openModal('item-modal');
}

// Reset when opening fresh Add modal
document.querySelector('button[onclick="openModal(\'item-modal\')"]')?.addEventListener('click', () => {
    editMode = false;
    document.getElementById('modal-title').textContent = 'Add New Item';
    document.getElementById('item-btn-label').textContent = 'Save Item';
    document.getElementById('item-id').value = '';
    document.getElementById('item-form').reset();
    document.getElementById('item-sku').disabled = false;
});

// ---- Form Submit (Add or Edit) ----
document.getElementById('item-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('item-submit-btn');
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Saving...';
    btn.disabled = true;

    const itemId = this.id.value;
    const data = {
        id:            itemId || undefined,
        name:          this.name.value,
        sku:           this.sku.value,
        category:      this.category.value,
        current_stock: parseInt(this.current_stock.value),
        min_level:     parseInt(this.min_level.value),
        supplier:      this.supplier.value,
    };

    try {
        const res = await fetch('../api/inventory.php', {
            method: editMode ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) {
            closeModal('item-modal');
            this.reset();
            document.getElementById('item-sku').disabled = false;
            showSuccess(json.message);
            setTimeout(() => location.reload(), 1800);
        } else {
            showError(json.message);
        }
    } catch(err) {
        showError('Network error. Please try again.');
    } finally {
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">save</span> <span id="item-btn-label">Save Item</span>';
        btn.disabled = false;
    }
});

// ---- Delete item ----
function deleteItem(id, name) {
    Swal.fire({
        title: `Delete "${name}"?`, text: 'This stock record will be permanently removed.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: 'rgb(239,68,68)', cancelButtonColor: 'rgb(100,116,139)',
        confirmButtonText: 'Delete'
    }).then(async r => {
        if (!r.isConfirmed) return;
        try {
            const res = await fetch(`../api/inventory.php?id=${id}`, { method: 'DELETE' });
            const json = await res.json();
            if (json.success) { showSuccess(json.message); setTimeout(() => location.reload(), 1800); }
            else showError(json.message);
        } catch(e) { showError('Network error.'); }
    });
}

// ---- Reorder item ----
function reorderItem(id, name) {
    Swal.fire({
        title: `Reorder "${name}"`,
        html: `<p class="text-sm text-gray-600 mb-4">Enter the quantity to restock:</p>
               <input type="number" id="reorder-qty" value="50" min="1" class="swal2-input" style="width:80%">`,
        icon: 'info', showCancelButton: true,
        confirmButtonColor: 'rgb(37,99,235)', confirmButtonText: 'Place Order',
        preConfirm: () => {
            const qty = parseInt(document.getElementById('reorder-qty').value);
            if (!qty || qty < 1) { Swal.showValidationMessage('Enter a valid quantity'); }
            return qty;
        }
    }).then(async r => {
        if (!r.isConfirmed) return;
        // Simulate update — in production this would call a purchase order API
        showSuccess(`Order placed for ${r.value} units of "${name}"! Supplier notified.`);
    });
}

// ---- Export CSV ----
function exportCSV() {
    const rows = [['Item Name','SKU','Category','Current Stock','Min Level','Status','Supplier']];
    document.querySelectorAll('.inv-row').forEach(row => {
        const cells = row.querySelectorAll('td');
        const name  = row.dataset.name;
        const sku   = row.dataset.sku;
        const cat   = row.dataset.category;
        const status= row.dataset.status;
        const stock = cells[2]?.querySelector('.font-extrabold')?.textContent?.trim() || '';
        const min   = cells[3]?.textContent?.trim() || '';
        const sup   = row.dataset.supplier;
        rows.push([name, sku, cat, stock, min, status, sup]);
    });
    const csv = rows.map(r => r.join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `skopestay_inventory_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    showSuccess('CSV exported successfully!');
}

// ══════════════════════════════════════════════════════════════
// RESTAURANT MENU MANAGEMENT
// ══════════════════════════════════════════════════════════════
let allMenuItems = [];
let menuEditMode = false;
let cameraStream = null;

async function loadMenuItems() {
    try {
        const res = await fetch('../api/menu_items.php');
        const json = await res.json();
        if (json.success) {
            allMenuItems = json.data;
            renderMenuCards(allMenuItems);
        }
    } catch(e) {
        document.getElementById('menu-grid-admin').innerHTML = '<div class="col-span-full text-center py-16 text-red-500">Failed to load menu items.</div>';
    }
}

function renderMenuCards(items) {
    const grid = document.getElementById('menu-grid-admin');
    if (!items.length) {
        grid.innerHTML = `<div class="col-span-full flex flex-col items-center justify-center h-48 text-on-surface-variant opacity-40">
            <span class="material-symbols-outlined text-5xl mb-2">restaurant_menu</span>
            <p class="text-sm font-semibold">No dishes yet. Add your first menu item!</p></div>`;
        return;
    }
    grid.innerHTML = items.map(item => {
        const imgHtml = item.image_url
            ? `<img src="${item.image_url}" alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
               <div class="w-full h-full hidden items-center justify-center absolute inset-0" style="background:linear-gradient(135deg,#f0f4ff,#e8f0fe)"><span class="text-4xl">${item.emoji || '🍽️'}</span></div>`
            : `<div class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg,#f0f4ff,#e8f0fe)"><span class="text-5xl">${item.emoji || '🍽️'}</span></div>`;

        const statusBadge = item.status === 'Unavailable'
            ? '<span class="absolute top-2 left-2 bg-red-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Unavailable</span>'
            : '';
        const labelBadge = item.badge
            ? `<span class="absolute top-2 right-2 bg-gray-900 text-white text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">${item.badge}</span>`
            : '';

        return `
        <div class="menu-admin-card bg-white rounded-[1.25rem] border border-gray-100 shadow-md overflow-hidden group ${item.status === 'Unavailable' ? 'opacity-70' : ''}">
            <div class="h-36 relative overflow-hidden bg-gray-100">
                ${imgHtml}
                ${statusBadge}
                ${labelBadge}
            </div>
            <div class="p-3">
                <h4 class="font-bold text-sm text-gray-900 truncate mb-0.5">${item.name}</h4>
                <p class="text-[10px] text-gray-400 mb-2 truncate">${item.category}</p>
                <div class="flex justify-between items-center">
                    <span class="font-extrabold text-amber-500 text-sm">KSh ${Number(item.price).toLocaleString()}</span>
                    <div class="flex gap-1">
                        <button onclick="openMenuModal(${JSON.stringify(item).replace(/"/g, '&quot;')})" 
                            class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors">
                            <span class="material-symbols-outlined text-[14px]">edit</span>
                        </button>
                        <button onclick="deleteMenuItem(${item.id}, '${item.name.replace(/'/g, "\\'")}')"
                            class="w-7 h-7 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors">
                            <span class="material-symbols-outlined text-[14px]">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

function filterMenuCards() {
    const q      = document.getElementById('menu-search').value.toLowerCase();
    const cat    = document.getElementById('menu-cat-filter').value;
    const status = document.getElementById('menu-status-filter').value;

    const filtered = allMenuItems.filter(item =>
        (!q      || item.name.toLowerCase().includes(q) || (item.description || '').toLowerCase().includes(q)) &&
        (!cat    || item.category === cat) &&
        (!status || item.status === status)
    );
    renderMenuCards(filtered);
}

function openMenuModal(item = null) {
    menuEditMode = !!item;
    document.getElementById('menu-modal-title').textContent = item ? 'Edit Dish' : 'Add New Dish';
    document.getElementById('menu-btn-label').textContent   = item ? 'Update Dish' : 'Save Dish';
    document.getElementById('menu-item-form').reset();

    // Reset image preview
    document.getElementById('img-preview').classList.add('hidden');
    document.getElementById('img-preview').src = '';
    document.getElementById('img-preview-icon').style.display = '';

    if (item) {
        document.getElementById('menu-item-id').value       = item.id;
        document.getElementById('menu-item-old-image').value= item.image_url || '';
        document.getElementById('menu-name').value          = item.name;
        document.getElementById('menu-category').value      = item.category;
        document.getElementById('menu-price').value         = item.price;
        document.getElementById('menu-emoji').value         = item.emoji || '';
        document.getElementById('menu-badge').value         = item.badge || '';
        document.getElementById('menu-status').value        = item.status;
        document.getElementById('menu-description').value   = item.description || '';
        document.getElementById('img-preview-icon').textContent = item.emoji || '🍽️';

        if (item.image_url) {
            const img = document.getElementById('img-preview');
            img.src = item.image_url;
            img.classList.remove('hidden');
            document.getElementById('img-preview-icon').style.display = 'none';
        }
    } else {
        document.getElementById('menu-item-id').value = '';
        document.getElementById('menu-item-old-image').value = '';
        document.getElementById('img-preview-icon').textContent = '🍽️';
    }

    openModal('menu-modal');
}

// Form submit — uses FormData (multipart for file upload)
document.getElementById('menu-item-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('menu-submit-btn');
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Saving...';
    btn.disabled = true;

    const fd = new FormData(this);

    try {
        let res, json;

        if (menuEditMode) {
            // PUT via POST + _method override — we use a trick:
            // Send form data as JSON for PUT (without file re-upload)
            const body = {
                id:          fd.get('id'),
                name:        fd.get('name'),
                category:    fd.get('category'),
                price:       fd.get('price'),
                description: fd.get('description'),
                emoji:       fd.get('emoji'),
                badge:       fd.get('badge'),
                status:      fd.get('status'),
                image_url:   fd.get('image_url'),
            };

            // If a new file was selected, upload via POST first to get the URL
            const fileInput = document.getElementById('menu-image-file');
            if (fileInput.files.length > 0) {
                const uploadFd = new FormData();
                uploadFd.append('image', fileInput.files[0]);
                uploadFd.append('name', '_temp_upload');
                uploadFd.append('category', body.category);
                uploadFd.append('price', body.price);
                // We just want the image URL, so we post a partial record and delete it
                // Simpler: send the whole update as POST with _method=PUT
                fd.append('_method', 'PUT');
                res  = await fetch('../api/menu_items.php?method=PUT&id=' + body.id, { method: 'POST', body: fd });
            } else {
                res  = await fetch('../api/menu_items.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(body)
                });
            }
        } else {
            res = await fetch('../api/menu_items.php', { method: 'POST', body: fd });
        }

        json = await res.json();
        if (json.success) {
            closeModal('menu-modal');
            showSuccess(json.message);
            loadMenuItems();
        } else {
            showError(json.message || 'Failed to save dish.');
        }
    } catch(err) {
        showError('Network error. Please try again.');
    } finally {
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">save</span> <span id="menu-btn-label">' + (menuEditMode ? 'Update Dish' : 'Save Dish') + '</span>';
        btn.disabled = false;
    }
});

async function deleteMenuItem(id, name) {
    const r = await Swal.fire({
        title: `Delete "${name}"?`, text: 'This dish will be permanently removed from the menu.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: 'rgb(239,68,68)', cancelButtonColor: 'rgb(100,116,139)',
        confirmButtonText: 'Delete'
    });
    if (!r.isConfirmed) return;
    try {
        const res  = await fetch(`../api/menu_items.php?id=${id}`, { method: 'DELETE' });
        const json = await res.json();
        if (json.success) { showSuccess(json.message); loadMenuItems(); }
        else showError(json.message);
    } catch(e) { showError('Network error.'); }
}

// ── Image Preview ──────────────────────────────────────────────
function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img  = document.getElementById('img-preview');
        const icon = document.getElementById('img-preview-icon');
        img.src = e.target.result;
        img.classList.remove('hidden');
        icon.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}

// ── Camera Capture ─────────────────────────────────────────────
async function startCamera() {
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
        const video  = document.getElementById('camera-video');
        video.srcObject = cameraStream;
        document.getElementById('camera-container').classList.remove('hidden');
    } catch(e) {
        showError('Camera access denied. Please allow camera permissions.');
    }
}

function capturePhoto() {
    const video  = document.getElementById('camera-video');
    const canvas = document.getElementById('camera-canvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    canvas.toBlob(blob => {
        // Convert blob to File and attach to the file input
        const file = new File([blob], 'camera_capture.jpg', { type: 'image/jpeg' });
        const dt   = new DataTransfer();
        dt.items.add(file);
        const fileInput = document.getElementById('menu-image-file');
        fileInput.files = dt.files;

        // Show preview
        const img  = document.getElementById('img-preview');
        const icon = document.getElementById('img-preview-icon');
        img.src = canvas.toDataURL('image/jpeg');
        img.classList.remove('hidden');
        icon.style.display = 'none';

        stopCamera();
    }, 'image/jpeg', 0.92);
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
    const container = document.getElementById('camera-container');
    if (container) container.classList.add('hidden');
}

</script>
</body>
</html>
