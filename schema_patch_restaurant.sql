-- Add restaurant tables
CREATE TABLE IF NOT EXISTS `restaurant_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_number` varchar(20) NOT NULL UNIQUE,
  `capacity` int(11) DEFAULT 4,
  `status` enum('Available','Reserved','Occupied','Awaiting Bill','Cleaning') DEFAULT 'Available',
  PRIMARY KEY (`id`)
);

-- Seed some tables
INSERT IGNORE INTO `restaurant_tables` (`table_number`, `capacity`) VALUES
('Table 1', 4), ('Table 2', 4), ('Table 3', 4), ('Table 4', 6), ('Table 5', 6),
('Table 6', 2), ('Table 7', 2), ('Table 8', 8), ('Table 9', 4), ('Table 10', 4);

-- Alter restaurant_orders
ALTER TABLE `restaurant_orders` 
MODIFY COLUMN `status` enum('Pending','Accepted','Preparing','Ready','Served','Awaiting Payment','Paid','Completed','Cancelled') DEFAULT 'Pending';

ALTER TABLE `restaurant_orders`
ADD COLUMN `order_type` enum('Dine-In','Room Service','Takeaway','Delivery') DEFAULT 'Dine-In' AFTER `guest_name`,
ADD COLUMN `room_id` int(11) DEFAULT NULL AFTER `order_type`,
ADD COLUMN `kitchen_status` enum('New Order','Accepted','Preparing','Ready') DEFAULT 'New Order' AFTER `status`;

-- Add table reservations
CREATE TABLE IF NOT EXISTS `restaurant_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guest_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100),
  `date` date NOT NULL,
  `time` time NOT NULL,
  `number_of_guests` int(11) NOT NULL DEFAULT 2,
  `table_id` int(11) DEFAULT NULL,
  `special_requests` text,
  `status` enum('Pending','Confirmed','Seated','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reservation_datetime` (`date`,`time`),
  KEY `idx_reservation_status` (`status`),
  CONSTRAINT `fk_reservations_table` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables`(`id`) ON DELETE SET NULL
);

ALTER TABLE `restaurant_orders`
ADD CONSTRAINT `fk_restaurant_orders_room` FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE SET NULL;
