-- =========================================================================
-- SkopeStay Hospitality ERP Core Database Migration
-- =========================================================================

-- 1. Ensure Branches Exist
CREATE TABLE IF NOT EXISTS `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Ensure Departments Exist
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Human Capital (HRMS) - Employees Master Table
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(50) NOT NULL UNIQUE,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL UNIQUE,
  `phone` varchar(50) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT 1,
  `designation` varchar(100) NOT NULL,
  `salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `hire_date` date NOT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT 'KCB Bank',
  `bank_account` varchar(50) DEFAULT NULL,
  `status` enum('Active','On Leave','Probation','Terminated') DEFAULT 'Active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_emp_dept` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Employee Shifts & Rostering
CREATE TABLE IF NOT EXISTS `employee_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `shift_name` varchar(50) NOT NULL DEFAULT 'Morning Shift',
  `shift_date` date NOT NULL,
  `start_time` time NOT NULL DEFAULT '07:00:00',
  `end_time` time NOT NULL DEFAULT '15:30:00',
  `notes` varchar(255) DEFAULT NULL,
  `status` enum('Scheduled','Completed','Absent','Swapped') DEFAULT 'Scheduled',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Staff Leave Management
CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type` enum('Annual','Sick','Maternity','Paternity','Compassionate','Unpaid') NOT NULL DEFAULT 'Annual',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_count` int(11) NOT NULL DEFAULT 1,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Monthly Payroll Records & Payslips
CREATE TABLE IF NOT EXISTS `payroll_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL,
  `allowances` decimal(12,2) DEFAULT 0.00,
  `deductions` decimal(12,2) DEFAULT 0.00,
  `nssf` decimal(10,2) DEFAULT 1080.00,
  `nhif` decimal(10,2) DEFAULT 1700.00,
  `paye` decimal(12,2) DEFAULT 0.00,
  `net_salary` decimal(12,2) NOT NULL,
  `payment_status` enum('Draft','Pending','Paid') DEFAULT 'Paid',
  `payment_method` varchar(50) DEFAULT 'Bank Transfer',
  `paid_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_emp_month_year` (`employee_id`, `month`, `year`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Suppliers & Vendors Master (SCM)
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(50) NOT NULL UNIQUE,
  `name` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'F&B Provisions',
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_pin` varchar(50) DEFAULT NULL,
  `payment_terms` varchar(50) DEFAULT 'Net 30 Days',
  `rating` decimal(2,1) DEFAULT 4.8,
  `status` enum('Active','Inactive','Blacklisted') DEFAULT 'Active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Purchase Orders (Procurement)
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) NOT NULL UNIQUE,
  `supplier_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `expected_date` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Draft','Pending Approval','Approved','Received','Cancelled') DEFAULT 'Draft',
  `notes` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT 'Procurement Officer',
  `approved_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Purchase Order Items
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` int(11) NOT NULL,
  `inventory_item_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`po_id`) REFERENCES `purchase_orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Housekeeping Inspection & Turnover Workflow
CREATE TABLE IF NOT EXISTS `housekeeping_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_number` varchar(50) NOT NULL UNIQUE,
  `room_id` int(11) NOT NULL,
  `housekeeper_id` int(11) DEFAULT NULL,
  `clean_type` enum('Departure Deep Clean','Stayover Service','Turndown','Pre-Arrival Inspection') DEFAULT 'Departure Deep Clean',
  `priority` enum('Normal','High','VIP Priority') DEFAULT 'Normal',
  `status` enum('Dirty','In Progress','Cleaned','Inspected & Ready','Do Not Disturb') DEFAULT 'Dirty',
  `inspected_by` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Facilities & Asset Maintenance Work Orders
CREATE TABLE IF NOT EXISTS `maintenance_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(50) NOT NULL UNIQUE,
  `title` varchar(200) NOT NULL,
  `category` enum('HVAC & AC','Plumbing','Electrical','Carpentry & Furniture','Kitchen Appliance','Pool & Spa Equipment','IT & Audio-Visual') DEFAULT 'HVAC & AC',
  `priority` enum('Low','Medium','High','Critical Emergency') DEFAULT 'Medium',
  `room_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `assigned_to` varchar(100) DEFAULT 'Chief Engineer',
  `status` enum('Open','In Progress','Pending Parts','Resolved','Closed') DEFAULT 'Open',
  `reported_by` varchar(100) DEFAULT 'Staff Member',
  `resolution_notes` text DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Chart of Accounts (ERP Multi-Ledger Accounting)
CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` varchar(20) NOT NULL UNIQUE,
  `account_name` varchar(150) NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Cost of Sales','Operating Expense') NOT NULL,
  `currency` varchar(10) DEFAULT 'KES',
  `balance` decimal(14,2) DEFAULT 0.00,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. General Ledger Journal Entries
CREATE TABLE IF NOT EXISTS `general_ledger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_date` date NOT NULL,
  `account_id` int(11) NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `debit` decimal(12,2) DEFAULT 0.00,
  `credit` decimal(12,2) DEFAULT 0.00,
  `created_by` varchar(100) DEFAULT 'System ERP',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
