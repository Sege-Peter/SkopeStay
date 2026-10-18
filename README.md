# SkopeStay

A modern hotel and resort management system with seamless online booking, room reservation, event hall management, restaurant ordering, and staff administration.

## Features

- **Guest Facing Experience**:
  - Live room availability and luxury suite showcase
  - Online booking with server-side pricing and double-booking protection (bookings arrive as *Pending* for staff to confirm)
  - Event hall reservations and conference booking
  - Pool pass ticketing and visitor tracking
  - Restaurant dining menu and walk-in ordering
  - Contact form, newsletter sign-up and WhatsApp concierge button

- **Management & Administration**:
  - Role-Based Access Control (RBAC) enforced on every admin page and API
  - Room status tracking (Available, Occupied, Cleaning, Maintenance)
  - Booking lifecycle management (Confirm, Check-in, Check-out, Folio/Billing)
  - Guest Inquiries inbox with reply by email/WhatsApp, staff notes and CSV export
  - Live header notifications (pending bookings, arrivals, low stock, urgent work orders) and global search (Ctrl K)
  - Inventory, procurement, HRMS, maintenance and multi-ledger accounting
  - My Profile page with password change

- **System Settings** (`modules/settings.php`):
  - **Branding:** property name, two-part wordmark, captions, logo / favicon / sign-in background upload, icon-badge mode
  - **Theme colours:** 8 presets or custom primary, accent, success and sidebar colours with live preview and contrast warning
  - Property contacts, website & social links, SEO description, billing (currency, VAT, receipt footer, booking prefix)
  - Maintenance mode with staff bypass link, alert rules, security (idle timeout, public sign-up, demo login), staff directory
  - One-click full database backup (.sql download)

All branding is read through `includes/branding.php` (`brand()`, `brand_logo_url()`, `brand_wordmark()`, `brand_palette()`), so the website, sign-in page, admin panel, maintenance screen and receipts all follow the settings.

## Requirements

- PHP 8.0+ (PDO MySQL and fileinfo extensions enabled)
- MySQL / MariaDB 10.4+
- Apache with `.htaccess` support (XAMPP / Laragon), or equivalent rules on Nginx

## Setup & Installation

1. Clone the repository into your web root (e.g., `C:/xampp/htdocs/SkopeStay`):
   ```bash
   git clone https://github.com/Sege-Peter/SkopeStay.git
   ```
2. Create the database `skopestay` in MySQL:
   ```sql
   CREATE DATABASE skopestay;
   ```
3. Import the schemas and run the setup scripts **from the command line** (the `database/` folder is blocked from the web):
   ```bash
   mysql -u root skopestay < database/schema.sql
   php database/setup_erp.php
   php database/setup_rbac.php
   ```
   Later schema changes are applied automatically by `includes/migrations.php` on the first page load.
4. Configure database connection credentials in `includes/config.php` if different from defaults.
5. Open `http://localhost/SkopeStay`, sign in, and set your name, logo and colours under **System Settings**.

## Going live checklist

- Settings → Security: turn off **Show demo credentials** and change the default admin password (My Profile).
- Settings → Maintenance: generate a new bypass key.
- Make sure `assets/uploads/` is writable by the web server.

## License
MIT License
