INSERT IGNORE INTO rooms (room_number, type, price, floor, status) VALUES
('101','Deluxe King',14500,1,'Available'),
('102','Executive Suite',24900,1,'Occupied'),
('103','Single Standard',8200,1,'Cleaning'),
('104','Family Suite',22000,1,'Available'),
('201','Deluxe King',16000,2,'Available'),
('202','Honeymoon Suite',32000,2,'Occupied'),
('203','Executive Suite',25500,2,'Available'),
('204','Single Standard',9000,2,'Occupied'),
('301','Presidential Suite',58000,3,'Available'),
('302','Deluxe King',17000,3,'Cleaning');

INSERT IGNORE INTO inventory_items (name, sku, category, current_stock, min_level, status, supplier) VALUES
('Luxury Towel Set XL','SS-HK-001','Housekeeping',24,50,'Low Stock','TextilePro Global'),
('Artisanal Roast Coffee','SS-FB-412','F&B',120,30,'In Stock','Bean & Brew Co.'),
('SMART LED Bulb Warm','SS-MN-992','Maintenance',0,15,'Out of Stock','Lumina Supplies'),
('Premium Bed Sheets','SS-HK-020','Housekeeping',80,40,'In Stock','TextilePro Global'),
('Mineral Water 500ml','SS-FB-100','F&B',500,100,'In Stock','AquaFresh Ltd'),
('Toilet Tissue Roll','SS-HK-050','Housekeeping',200,80,'In Stock','CleanPro Supplies'),
('Air Freshener Spray','SS-HK-071','Housekeeping',8,20,'Low Stock','ScentCo'),
('Printer Ink Cartridge','SS-ST-005','Stationery',2,10,'Low Stock','OfficeSupply KE');
