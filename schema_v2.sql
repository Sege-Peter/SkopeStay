-- Add expenses table
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `category` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `notes` text,
  `status` enum('Paid','Pending') DEFAULT 'Paid',
  `expense_date` date NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- Add guests table
CREATE TABLE IF NOT EXISTS `guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- Add guest_id FK to bookings if not there
ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `guest_phone` varchar(50) DEFAULT NULL;

-- Seed expenses
INSERT IGNORE INTO `expenses` (title, category, amount, payment_method, status, expense_date) VALUES
('Monthly Electricity Bill', 'Utilities (Water/Electricity)', 28500, 'Bank Transfer', 'Paid', CURDATE() - INTERVAL 2 DAY),
('Kitchen Groceries Restock', 'Kitchen & Restaurant Supplies', 12800, 'Cash', 'Paid', CURDATE() - INTERVAL 1 DAY),
('Pipe Repair - Lobby', 'Facility Maintenance', 4500, 'Cash', 'Pending', CURDATE()),
('Internet Subscription', 'Utilities (Water/Electricity)', 8000, 'Card / M-Pesa', 'Paid', CURDATE() - INTERVAL 2 DAY),
('Housekeeping Supplies', 'Housekeeping Inventory', 5200, 'Cash', 'Paid', CURDATE() - INTERVAL 3 DAY);
