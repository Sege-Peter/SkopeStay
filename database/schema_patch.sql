-- Restaurant tables (run if not already present)
CREATE TABLE IF NOT EXISTS `restaurant_orders` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `table_number` VARCHAR(20)  NOT NULL DEFAULT 'Table 1',
  `guest_name`   VARCHAR(100) NOT NULL DEFAULT 'Walk-in Guest',
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status`       ENUM('Pending','Preparing','Served','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `restaurant_order_items` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `order_id`      INT NOT NULL,
  `item_id`       INT NOT NULL,
  `price_at_time` DECIMAL(10,2) NOT NULL,
  `quantity`      INT NOT NULL DEFAULT 1,
  FOREIGN KEY (`order_id`) REFERENCES `restaurant_orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure expenses table exists
CREATE TABLE IF NOT EXISTS `expenses` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `title`          VARCHAR(200) NOT NULL,
  `category`       VARCHAR(100) NOT NULL,
  `amount`         DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50)  NOT NULL DEFAULT 'Cash',
  `notes`          TEXT,
  `status`         ENUM('Paid','Pending') NOT NULL DEFAULT 'Paid',
  `expense_date`   DATE NOT NULL,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure users table exists
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `username`   VARCHAR(80)  NOT NULL UNIQUE,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       VARCHAR(50)  NOT NULL DEFAULT 'Receptionist',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin user (password: admin123)
INSERT IGNORE INTO `users` (`username`,`email`,`password`,`role`)
VALUES ('admin','admin@skopestay.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Manager');

-- Sample expenses data
INSERT IGNORE INTO `expenses` (`id`,`title`,`category`,`amount`,`payment_method`,`status`,`expense_date`) VALUES
(1,'Monthly Electricity','Utilities (Water/Electricity)',18500,'Bank Transfer','Paid',DATE_FORMAT(NOW(),'%Y-%m-01')),
(2,'Housekeeping Supplies','Housekeeping Inventory',7200,'Cash','Paid',DATE_FORMAT(NOW(),'%Y-%m-05')),
(3,'Kitchen Gas Cylinders','Kitchen & Restaurant Supplies',4800,'Cash','Paid',DATE_FORMAT(NOW(),'%Y-%m-10')),
(4,'Water Bill','Utilities (Water/Electricity)',3600,'Bank Transfer','Paid',DATE_FORMAT(NOW(),'%Y-%m-12')),
(5,'Staff Salaries','Staff Payroll',120000,'Bank Transfer','Paid',DATE_FORMAT(NOW(),'%Y-%m-01'));

-- Sample restaurant orders
INSERT IGNORE INTO `restaurant_orders` (`id`,`table_number`,`guest_name`,`total_amount`,`status`) VALUES
(1,'Table 3','James Otieno',3480.00,'Served'),
(2,'Table 7','Walk-in Guest',1566.00,'Pending'),
(3,'Table 1','Mary Wanjiku',2900.00,'Preparing');
