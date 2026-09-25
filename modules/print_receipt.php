<?php
require_once '../includes/config.php';

$type = $_GET['type'] ?? 'restaurant';
$id   = (int)($_GET['id'] ?? 0);

$items      = [];
$subtotal   = 0;
$tax        = 0;
$total      = 0;
$date       = date('d/m/Y');
$time       = date('H:i');
$cashier    = isset($_SESSION['username']) ? $_SESSION['username'] : 'Staff';
$ref        = 'TXN-' . str_pad($id, 4, '0', STR_PAD_LEFT);
$receipt_no = 'INV-' . rand(10000, 99999) . '-SK';
$room_table = 'Walk-in';
$pay_method = 'System Payment';
$guest_name = 'Guest';
$module_label = strtoupper($type) . ' RECEIPT';

if ($type === 'restaurant' && $id > 0) {
    $order = $pdo->prepare("SELECT * FROM restaurant_orders WHERE id = ?");
    $order->execute([$id]);
    $order_data = $order->fetch();

    if ($order_data) {
        $room_table = 'Table ' . ($order_data['table_number'] ?? '—');
        $total    = (float)$order_data['total_amount'];
        $subtotal = $total / 1.16;
        $tax      = $total - $subtotal;
        $date     = date('d/m/Y', strtotime($order_data['created_at']));
        $time     = date('H:i',   strtotime($order_data['created_at']));

        $items_stmt = $pdo->prepare("SELECT roi.*, ri.name as item_name FROM restaurant_order_items roi LEFT JOIN restaurant_items ri ON roi.item_id = ri.id WHERE roi.order_id = ?");
        $items_stmt->execute([$id]);
        foreach ($items_stmt->fetchAll() as $it) {
            $items[] = [
                'name'  => $it['item_name'] ?? 'Unknown Item',
                'qty'   => (int)$it['quantity'],
                'price' => (float)$it['price_at_time'] * (int)$it['quantity'],
            ];
        }
    }

} elseif ($type === 'booking' && $id > 0) {
    $bk = $pdo->prepare("SELECT b.*, r.room_number, r.type as room_type FROM bookings b LEFT JOIN rooms r ON b.room_id=r.id WHERE b.id=?");
    $bk->execute([$id]);
    $bk_data = $bk->fetch();

    if ($bk_data) {
        $nights = 1;
        if (!empty($bk_data['check_out']) && !empty($bk_data['check_in'])) {
            $diff = (strtotime($bk_data['check_out']) - strtotime($bk_data['check_in'])) / 86400;
            if ($diff > 0) $nights = (int)$diff;
        }
        $total      = (float)$bk_data['total_amount'];
        $subtotal   = $total / 1.16;
        $tax        = $total - $subtotal;
        $date       = date('d/m/Y', strtotime($bk_data['created_at']));
        $time       = date('H:i',   strtotime($bk_data['created_at']));
        $room_table = 'Room ' . ($bk_data['room_number'] ?? 'N/A');
        $guest_name = $bk_data['guest_name'] ?? 'Guest';
        $ci = !empty($bk_data['check_in'])  ? date('d/m/Y', strtotime($bk_data['check_in']))  : '—';
        $co = !empty($bk_data['check_out']) ? date('d/m/Y', strtotime($bk_data['check_out'])) : '—';

        $items[] = [
            'name'  => ($bk_data['room_type'] ?? 'Room') . ' / ' . $room_table,
            'qty'   => $nights . ' night' . ($nights > 1 ? 's' : ''),
            'price' => $total,
        ];
        $items[] = [
            'name'  => 'Check-in:  ' . $ci,
            'qty'   => '',
            'price' => null,
        ];
        $items[] = [
            'name'  => 'Check-out: ' . $co,
            'qty'   => '',
            'price' => null,
        ];
    }

} elseif ($type === 'pool' && $id > 0) {
    $pass = $pdo->prepare("SELECT p.*, c.full_name as customer_name FROM pool_passes p LEFT JOIN customers c ON p.customer_id = c.id WHERE p.id = ?");
    $pass->execute([$id]);
    $pass_data = $pass->fetch();

    if ($pass_data) {
        $room_table = 'Pool Access';
        $total      = (float)$pass_data['amount'];
        $subtotal   = $total / 1.16;
        $tax        = $total - $subtotal;
        $date       = date('d/m/Y', strtotime($pass_data['created_at']));
        $time       = date('H:i',   strtotime($pass_data['created_at']));
        $guest_name = $pass_data['customer_name'] ?? $pass_data['guest_name'] ?? 'Guest';
        $items[] = [
            'name'  => $pass_data['pass_type'] . ' Pass',
            'qty'   => $pass_data['number_of_guests'] . ' pax',
            'price' => $total,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Receipt <?= $receipt_no ?></title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body {
        background: #e5e7eb;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 40px 16px;
        font-family: 'Courier New', Courier, monospace; /* EPOS Monospace */
        color: #000;
        font-size: 13px;
        line-height: 1.4;
    }
    
    .action-bar {
        position: fixed; top: 0; left: 0; right: 0; height: 50px;
        background: #111; color: #fff;
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 24px; z-index: 100; font-family: sans-serif;
    }
    
    .action-bar button {
        background: #333; color: #fff; border: none; border-radius: 4px;
        padding: 6px 12px; cursor: pointer; font-size: 13px;
        transition: background 0.2s;
    }
    .action-bar button:hover { background: #444; }

    .epos-receipt {
        background: #fff;
        width: 320px; /* Standard 80mm equivalent approx */
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: left;
    }
    
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    
    .divider {
        border-top: 1px dashed #000;
        margin: 12px 0;
    }
    
    .header h1 { font-size: 24px; margin-bottom: 5px; font-weight: bold; letter-spacing: 1px; }
    .header p { margin: 2px 0; }
    
    .info-grid {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 2px 10px;
        margin-bottom: 10px;
    }

    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 4px 0; vertical-align: top; }
    th { border-bottom: 1px dashed #000; text-align: left; padding-bottom: 6px; }
    .td-qty { width: 15%; }
    .td-item { width: 55%; padding-right: 5px; }
    .td-price { width: 30%; text-align: right; }
    
    .totals-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 4px;
        margin: 12px 0;
    }
    .totals-grid .label { text-align: right; padding-right: 15px; }
    
    .total-row {
        font-size: 16px;
        font-weight: bold;
        margin-top: 4px;
        padding-top: 4px;
        border-top: 1px dashed #000;
    }
    
    .qr-container {
        display: flex; justify-content: center; margin: 20px 0;
    }
    
    .footer { text-align: center; margin-top: 10px; font-size: 11px; }

    @media print {
        @page { margin: 0; }
        body { background: #fff; padding: 0; margin: 0; align-items: flex-start; justify-content: flex-start; }
        .action-bar { display: none !important; }
        .epos-receipt { width: 100%; max-width: 80mm; box-shadow: none; margin: 0; padding: 5mm; }
    }
</style>
</head>
<body>

<div class="action-bar no-print">
    <span class="logo font-bold">SKOPESTAY EPOS</span>
    <div class="btns">
        <button onclick="window.close()">Close</button>
        <button onclick="window.print()">Print</button>
        <button onclick="downloadPDF()">Download PDF</button>
    </div>
</div>

<div class="epos-receipt" id="receipt-canvas">
    <div class="header text-center">
        <img src="../assets/images/SkopeStay logo.png" alt="SkopeStay Logo" style="max-width: 140px; margin-bottom: 8px; filter: grayscale(100%) contrast(120%);">
        <p>Kisumu, Kenya</p>
        <p>Tel: +254 742 380 183</p>
        <p>VAT No: P000000000X</p>
        <p style="margin-top:5px; font-weight:bold;"><?= $module_label ?></p>
    </div>
    
    <div class="divider"></div>
    
    <div class="info-grid">
        <span>Receipt:</span> <span class="text-right"><?= $receipt_no ?></span>
        <span>Date:</span> <span class="text-right"><?= $date ?> <?= $time ?></span>
        <span>Cashier:</span> <span class="text-right"><?= htmlspecialchars($cashier) ?></span>
        <span>Ref:</span> <span class="text-right"><?= htmlspecialchars($room_table) ?></span>
        <span>Guest:</span> <span class="text-right"><?= htmlspecialchars($guest_name) ?></span>
    </div>

    <div class="divider"></div>
    
    <table>
        <thead>
            <tr>
                <th class="td-qty">Qty</th>
                <th class="td-item">Item</th>
                <th class="td-price text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td class="td-qty"><?= htmlspecialchars((string)$item['qty']) ?></td>
                <td class="td-item"><?= htmlspecialchars($item['name']) ?></td>
                <td class="td-price text-right"><?= is_numeric($item['price']) ? number_format((float)$item['price'], 2) : '' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="divider"></div>
    
    <div class="totals-grid">
        <div class="label">Subtotal:</div> <div><?= number_format($subtotal, 2) ?></div>
        <div class="label">Tax (16%):</div> <div><?= number_format($tax, 2) ?></div>
    </div>
    
    <div class="totals-grid total-row">
        <div class="label">TOTAL KES:</div> <div><?= number_format($total, 2) ?></div>
    </div>
    
    <div class="totals-grid">
        <div class="label">Payment:</div> <div><?= htmlspecialchars($pay_method) ?></div>
    </div>
    
    <div class="divider"></div>
    
    <div class="qr-container" id="qrcode"></div>
    
    <div class="footer">
        <p>Thank you for your visit!</p>
        <p>Please keep this receipt for your records.</p>
        <p style="margin-top:8px">Powered by SkopeStay</p>
    </div>
</div>

<script>
setTimeout(function() {
    new QRCode(document.getElementById("qrcode"), {
        text: "RECEIPT: <?= $receipt_no ?> | REF: <?= $ref ?> | TOTAL: <?= number_format($total, 2) ?>",
        width: 120,
        height: 120,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.L
    });
}, 100);

function downloadPDF() {
    const el = document.getElementById('receipt-canvas');
    const opt = {
        margin:      0,
        filename:    '<?= $receipt_no ?>.pdf',
        image:       { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, backgroundColor: '#ffffff', useCORS: true },
        jsPDF:       { unit: 'mm', format: [80, 250], orientation: 'portrait' }
    };
    html2pdf().set(opt).from(el).save();
}
</script>

</body>
</html>
