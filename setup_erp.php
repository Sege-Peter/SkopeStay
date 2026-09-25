<?php
require_once 'includes/config.php';

echo "Running SkopeStay ERP Database Migration & Master Seeding...\n";

// 1. Execute schema_erp.sql
$sqlContent = file_get_contents(__DIR__ . '/schema_erp.sql');
try {
    $pdo->exec($sqlContent);
    echo "✅ ERP Tables created successfully.\n";
} catch (Exception $e) {
    echo "❌ Error creating tables: " . $e->getMessage() . "\n";
}

// 2. Seed Default Branch
try {
    $pdo->exec("INSERT IGNORE INTO branches (id, name, location, contact, is_main) VALUES 
        (1, 'SkopeStay Resort & Suites (Flagship)', 'Mombasa Coastal Highway, Beachfront', '+254 742 380 183', 1)
    ");
    echo "✅ Master Branch seeded.\n";
} catch (Exception $e) {
    echo "ℹ️ Branch note: " . $e->getMessage() . "\n";
}

// 3. Seed Departments
$depts = [
    [1, 'Front Office & Concierge'],
    [2, 'Housekeeping & Laundry'],
    [3, 'Food & Beverage Service'],
    [4, 'Culinary & Executive Kitchen'],
    [5, 'Engineering & Maintenance'],
    [6, 'Finance & Accounting'],
    [7, 'Human Capital & HR'],
    [8, 'Banquets, Conferences & MICE'],
    [9, 'Estate Security & Safety']
];

$stmtDept = $pdo->prepare("INSERT IGNORE INTO departments (id, branch_id, name) VALUES (?, 1, ?)");
foreach ($depts as $d) {
    $stmtDept->execute([$d[0], $d[1]]);
}
echo "✅ Departments seeded.\n";

// 4. Seed Enterprise Roles into `roles`
$roles = [
    ['Super Admin', 'Full unrestricted enterprise control across all properties and ledgers', 'Staff', 1],
    ['Hotel Manager', 'Operational oversight across front desk, F&B, housekeeping, and finance', 'Staff', 1],
    ['Financial Controller', 'General ledger, accounts receivable/payable, payroll signoff, audit', 'Staff', 0],
    ['HR Director', 'Human capital management, employee profiles, rosters, leave approvals', 'Staff', 0],
    ['Procurement Manager', 'Vendor relations, purchase order approvals, inventory restock', 'Staff', 0],
    ['Executive Chef', 'Kitchen management, recipe costing, F&B inventory requisitions', 'Staff', 0],
    ['Housekeeping Supervisor', 'Room turnover management, inspector signoffs, amenity restock', 'Staff', 0],
    ['Chief Engineer', 'Facility maintenance, equipment work orders, asset lifecycle', 'Staff', 0],
    ['Receptionist', 'Front desk reservations, guest check-in/out, folio billing', 'Staff', 0]
];

$stmtRole = $pdo->prepare("INSERT IGNORE INTO roles (name, description, type, is_system) VALUES (?, ?, ?, ?)");
foreach ($roles as $r) {
    $stmtRole->execute([$r[0], $r[1], $r[2], $r[3]]);
}
echo "✅ Master ERP Roles seeded.\n";

// 5. Seed Staff Members (Employees)
$employees = [
    ['EMP-001', 'David', 'Kimani', 'david.k@skopestay.com', '+254 712 111 222', 1, 'Front Office Manager', 95000.00, '2023-01-15', '29871654', 'KCB Bank', '1102938475'],
    ['EMP-002', 'Grace', 'Achieng', 'grace.a@skopestay.com', '+254 722 333 444', 2, 'Housekeeping Executive', 78000.00, '2023-03-01', '30982716', 'Equity Bank', '0281928374'],
    ['EMP-003', 'Chef Marcus', 'Odhiambo', 'marcus.o@skopestay.com', '+254 733 555 666', 4, 'Executive Head Chef', 140000.00, '2022-11-10', '28716253', 'Co-op Bank', '0112938471'],
    ['EMP-004', 'Lillian', 'Mwangi', 'lillian.m@skopestay.com', '+254 744 777 888', 6, 'Financial Controller', 135000.00, '2023-02-20', '31827364', 'Standard Chartered', '9928172635'],
    ['EMP-005', 'Samson', 'Kariuki', 'samson.k@skopestay.com', '+254 755 999 000', 5, 'Chief Facilities Engineer', 90000.00, '2023-04-12', '27615243', 'KCB Bank', '1122334455'],
    ['EMP-006', 'Brenda', 'Mutua', 'brenda.m@skopestay.com', '+254 766 123 456', 7, 'HR & Payroll Specialist', 85000.00, '2023-05-18', '32718293', 'Equity Bank', '0192837465'],
    ['EMP-007', 'Kevin', 'Otieno', 'kevin.o@skopestay.com', '+254 777 234 567', 1, 'Senior Receptionist', 52000.00, '2024-01-10', '34827162', 'KCB Bank', '1234567890'],
    ['EMP-008', 'Faith', 'Wambui', 'faith.w@skopestay.com', '+254 788 345 678', 2, 'Senior Floor Housekeeper', 45000.00, '2024-02-01', '35918273', 'Co-op Bank', '0283746152']
];

$stmtEmp = $pdo->prepare("INSERT IGNORE INTO employees (employee_code, first_name, last_name, email, phone, department_id, designation, salary, hire_date, national_id, bank_name, bank_account, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?, 'Active')");
foreach ($employees as $e) {
    $stmtEmp->execute([$e[0], $e[1], $e[2], $e[3], $e[4], $e[5], $e[6], $e[7], $e[8], $e[9], $e[10], $e[11]]);
}
echo "✅ Employees master directory seeded.\n";

// 6. Seed Employee Shifts for Today and Tomorrow
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$shifts = [
    [1, 'Morning Shift', $today, '07:00:00', '15:30:00', 'Front desk check-in supervision'],
    [2, 'Morning Shift', $today, '08:00:00', '16:30:00', 'Suites morning turnover inspection'],
    [3, 'Split Shift', $today, '10:00:00', '21:30:00', 'Lunch & Dinner VIP tasting service'],
    [4, 'Day Shift', $today, '08:30:00', '17:00:00', 'Month-end audit reconciliation'],
    [5, 'Morning Shift', $today, '07:30:00', '16:00:00', 'HVAC chiller routine maintenance'],
    [7, 'Evening Shift', $today, '15:00:00', '23:30:00', 'Night reception & guest arrivals'],
    [1, 'Morning Shift', $tomorrow, '07:00:00', '15:30:00', 'VIP delegations check-in'],
    [2, 'Morning Shift', $tomorrow, '08:00:00', '16:30:00', 'Pre-arrival inspections']
];

$stmtShift = $pdo->prepare("INSERT IGNORE INTO employee_shifts (employee_id, shift_name, shift_date, start_time, end_time, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'Scheduled')");
foreach ($shifts as $s) {
    $stmtShift->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $s[5]]);
}
echo "✅ Employee shift rosters seeded.\n";

// 7. Seed Sample Leave Request
$pdo->exec("INSERT IGNORE INTO leave_requests (id, employee_id, leave_type, start_date, end_date, days_count, reason, status) VALUES 
    (1, 8, 'Annual', DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 5, 'Scheduled family annual leave', 'Approved'),
    (2, 7, 'Sick', DATE_SUB(CURDATE(), INTERVAL 2 DAY), CURDATE(), 2, 'Dental medical appointment', 'Approved')
");

// 8. Seed Suppliers (SCM)
$suppliers = [
    ['SUP-001', 'Highland Fresh Farms', 'F&B Provisions', 'James Kariuki', 'orders@highlandfarms.ke', '+254 711 000 111', 'Limuru Farm Road, Kiambu', 'P051283928X', 'Net 14 Days', 4.9],
    ['SUP-002', 'Coastal Wine & Spirits Dist.', 'Beverages & Spirits', 'Fatima Omar', 'supply@coastalwines.ke', '+254 722 000 222', 'Port Reitz Logistics, Mombasa', 'P059283716Y', 'Net 30 Days', 4.8],
    ['SUP-003', 'LinenPro Luxury Hospitality', 'Housekeeping Supplies', 'Edward Mutiso', 'sales@linenpro.ke', '+254 733 000 333', 'Industrial Area, Nairobi', 'P058192837Z', 'Net 30 Days', 4.9],
    ['SUP-004', 'Apex Chiller & Electro-Mech', 'Engineering & Spares', 'Eng. John Koech', 'support@apexchillers.ke', '+254 744 000 444', 'Commercial Street, Mombasa', 'P057182930A', 'Immediate / Cash', 4.7],
    ['SUP-005', 'AquaChem Pool Systems', 'Pool Chemicals', 'Sarah Baraza', 'service@aquachem.ke', '+254 755 000 555', 'Nyali Commercial Hub', 'P056192837B', 'Net 14 Days', 4.8]
];

$stmtSup = $pdo->prepare("INSERT IGNORE INTO suppliers (supplier_code, name, category, contact_person, email, phone, address, tax_pin, payment_terms, rating, status) VALUES (?,?,?,?,?,?,?,?,?,?, 'Active')");
foreach ($suppliers as $sup) {
    $stmtSup->execute([$sup[0], $sup[1], $sup[2], $sup[3], $sup[4], $sup[5], $sup[6], $sup[7], $sup[8], $sup[9]]);
}
echo "✅ SCM Suppliers master database seeded.\n";

// 9. Seed Sample Purchase Orders
$pdo->exec("INSERT IGNORE INTO purchase_orders (id, po_number, supplier_id, order_date, expected_date, subtotal, tax, total_amount, status, notes, created_by, approved_by) VALUES
    (1, 'PO-2026-001', 1, CURDATE() - INTERVAL 5 DAY, CURDATE() - INTERVAL 1 DAY, 85000.00, 13600.00, 98600.00, 'Received', 'Weekly organic farm delivery: vegetables, dairy, prime beef', 'Chef Marcus', 'Hotel Manager'),
    (2, 'PO-2026-002', 2, CURDATE() - INTERVAL 2 DAY, CURDATE() + INTERVAL 2 DAY, 145000.00, 23200.00, 168200.00, 'Approved', 'Restock of vintage champagne, scotch whiskey, and dry wines', 'Lillian Mwangi', 'Hotel Manager'),
    (3, 'PO-2026-003', 3, CURDATE(), CURDATE() + INTERVAL 7 DAY, 62000.00, 9920.00, 71920.00, 'Pending Approval', 'Egyptian cotton duvet sets & bath sheet replenishments', 'Grace Achieng', NULL)
");

$pdo->exec("INSERT IGNORE INTO purchase_order_items (id, po_id, item_name, quantity, unit_price, total_price) VALUES
    (1, 1, 'Aged Angus Ribeye Loins (40kg)', 40, 1500.00, 60000.00),
    (2, 1, 'Organic Hydroponic Herbs & Greens', 50, 500.00, 25000.00),
    (3, 2, 'Veuve Clicquot Brut Champagne Case', 2, 45000.00, 90000.00),
    (4, 2, 'Glenfiddich 18yr Single Malt Scotch', 5, 11000.00, 55000.00),
    (5, 3, 'Luxury King Satin Bed Linens', 20, 3100.00, 62000.00)
");
echo "✅ Purchase Orders and PO Line Items seeded.\n";

// 10. Seed Housekeeping Tasks
$pdo->exec("INSERT IGNORE INTO housekeeping_tasks (id, task_number, room_id, clean_type, priority, status, inspected_by, notes) VALUES
    (1, 'HK-101', 1, 'Departure Deep Clean', 'High', 'Cleaned', 'Grace Achieng', 'Guest departed at 11:00 AM. Room sanitized and restocked.'),
    (2, 'HK-102', 2, 'Stayover Service', 'Normal', 'In Progress', NULL, 'Guest requested afternoon refresh.'),
    (3, 'HK-103', 3, 'Pre-Arrival Inspection', 'VIP Priority', 'Inspected & Ready', 'Grace Achieng', 'VIP arriving at 16:00. Fruit basket placed.'),
    (4, 'HK-104', 4, 'Departure Deep Clean', 'Normal', 'Dirty', NULL, 'Checkout scheduled for 12:00 PM.')
");
echo "✅ Housekeeping inspection queue seeded.\n";

// 11. Seed Facilities & Maintenance Work Orders
$pdo->exec("INSERT IGNORE INTO maintenance_orders (id, ticket_number, title, category, priority, room_id, description, assigned_to, status, reported_by) VALUES
    (1, 'WO-801', 'Balcony Sliding Door Track Alignment', 'Carpentry & Furniture', 'Medium', 2, 'Sliding glass door friction when opening. Needs realignment and lubrication.', 'Samson Kariuki', 'In Progress', 'Housekeeping Staff'),
    (2, 'WO-802', 'Chiller Pump 2 Pressure Calibration', 'HVAC & AC', 'High', NULL, 'Chilled water temperature variance detected on 2nd floor riser.', 'Apex Engineers', 'Open', 'Chief Engineer'),
    (3, 'WO-803', 'Ballroom Crystal Chandelier LED Driver Servicing', 'Electrical', 'Low', NULL, 'Two LED module banks in East Ballroom flickering intermittently.', 'In-House Electrician', 'Resolved', 'Banquet Manager')
");
echo "✅ Maintenance Work Orders seeded.\n";

// 12. Seed Double-Entry Chart of Accounts (COA)
$accounts = [
    ['1010', 'Cash on Hand & Petty Cash', 'Asset', 125000.00],
    ['1020', 'Operating Bank Account - KCB', 'Asset', 4850000.00],
    ['1030', 'Guest Accounts Receivable (Folios)', 'Asset', 340000.00],
    ['1050', 'F&B & Operating Inventory Stock', 'Asset', 890000.00],
    ['1500', 'Hotel Property, Plant & Equipment (PPE)', 'Asset', 85000000.00],
    ['2010', 'Trade Accounts Payable (Suppliers)', 'Liability', 420000.00],
    ['2020', 'Accrued Payroll & Statutory Liabilities', 'Liability', 680000.00],
    ['3010', 'Owner Capital / Retained Earnings', 'Equity', 82000000.00],
    ['4010', 'Rooms & Suites Division Revenue', 'Revenue', 3450000.00],
    ['4020', 'Food & Beverage Restaurant Revenue', 'Revenue', 1820000.00],
    ['4030', 'Banquets & Event Venues Revenue', 'Revenue', 1250000.00],
    ['4040', 'Infinity Pool & Leisure Club Revenue', 'Revenue', 380000.00],
    ['5010', 'Cost of Goods Sold (F&B Provisions)', 'Cost of Sales', 680000.00],
    ['6010', 'Staff Salaries, Wages & Benefits', 'Operating Expense', 720000.00],
    ['6020', 'Utilities: Electricity, Gas & Water', 'Operating Expense', 185000.00],
    ['6030', 'Facility Maintenance & Repairs', 'Operating Expense', 65000.00],
    ['6040', 'Marketing, OTA Commissions & Software', 'Operating Expense', 95000.00]
];

$stmtCOA = $pdo->prepare("INSERT IGNORE INTO chart_of_accounts (account_code, account_name, account_type, balance, status) VALUES (?, ?, ?, ?, 'Active')");
foreach ($accounts as $a) {
    $stmtCOA->execute([$a[0], $a[1], $a[2], $a[3]]);
}
echo "✅ Chart of Accounts (COA) seeded.\n";

echo "\n🎉 SkopeStay ERP Master Architecture successfully initialized!\n";
