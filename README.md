# SkopeStay

A modern hotel and resort management system with seamless online booking, room reservation, event hall management, restaurant ordering, and staff administration.

## Features

- **Guest Facing Experience**:
  - Live room availability and luxury suite showcase
  - Event hall reservations and conference booking
  - Pool pass ticketing and visitor tracking
  - Restaurant dining menu and walk-in ordering
  - Contact and customer inquiry handling

- **Management & Administration**:
  - Role-Based Access Control (RBAC): Super Admin, Hotel Manager, Receptionist, Guest
  - Room status tracking (Available, Occupied, Cleaning, Maintenance)
  - Booking lifecycle management (Check-in, Check-out, Folio/Billing)
  - Inventory and restaurant order dispatch
  - Expense tracking and financial summaries

## Requirements

- PHP 8.0+ (PDO MySQL extension enabled)
- MySQL / MariaDB 10.4+
- Web server (Apache via XAMPP / Laragon / Nginx)

## Setup & Installation

1. Clone the repository into your web root (e.g., `C:/xampp/htdocs/SkopeStay`):
   ```bash
   git clone https://github.com/Sege-Peter/SkopeStay.git
   ```
2. Create the database `skopestay` in MySQL:
   ```sql
   CREATE DATABASE skopestay;
   ```
3. Import the schemas and run initial setup scripts:
   - Import `schema.sql`, `schema_v2.sql`, `schema_patch.sql`, `schema_patch_rooms.sql`
   - Run `setup_extended.php` and `setup_public.php` to populate halls, pool passes, and inquiry tables.
4. Configure database connection credentials in `includes/config.php` if different from defaults.
5. Launch your local server and open `http://localhost/SkopeStay` in your browser.

## License
MIT License
