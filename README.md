# Simple Hall Booking Manager

A lightweight WordPress plugin to manage hall bookings with **full-day vs partial slot** logic, guest bookings (no login), and a clean admin UI.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/wordpress-6.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/php-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)

## Features

- ✅ **Guest booking** (no WP user account required)
- ✅ **Customer organization field** (optional) for tracking company/organization bookings
- ✅ **Smart conflict handling** between Full Day and Partial (Morning/Day/Evening) slots
- ✅ **Per-hall cleaning buffer time** (in minutes) between bookings
- ✅ **Admin management** for halls, slots, bookings, and email settings
- ✅ **Shortcodes** for hall list, booking form, and guest self-management via token
- ✅ **Email notifications** for booking status changes
- ✅ **Responsive design** for both admin and frontend
- ✅ Built following **WordPress Coding Standards** and Plugin Handbook guidelines

## Requirements

- WordPress 6.0+
- PHP 7.4+
- MySQL 5.6+ / MariaDB 10.1+

## Installation

### From GitHub

1. Download or clone this repository
2. Upload the `simple-hall-booking-manager` folder to `/wp-content/plugins/`
3. Activate the plugin from "Plugins → Installed Plugins"
4. Configure halls, slots, and settings under the "Hall Booking" menu in the admin

### From WordPress.org (Coming Soon)

1. Go to Plugins → Add New
2. Search for "Simple Hall Booking Manager"
3. Click Install and then Activate

## Quick Start

### 1. Create Your First Hall

1. Navigate to **Hall Booking → Halls**
2. Click **Add New**
3. Fill in hall details:
   - Hall Name (e.g., "Main Conference Hall")
   - Capacity (e.g., 100)
   - Cleaning Buffer Time (e.g., 30 minutes)
4. Click **Save Hall**

### 2. Add Time Slots

1. On the hall edit page, scroll to **Time Slots** section
2. Click **Add Slot**
3. Configure your slot:
   - **Full Day** (9:00 AM - 6:00 PM) or
   - **Partial** slots (Morning: 9:00 AM - 12:00 PM, Day: 12:00 PM - 4:00 PM, Evening: 4:00 PM - 8:00 PM)
4. Select which days the slot is available
5. Click **Save Slot**

### 3. Add Booking Form to Your Site

Create a new page and add this shortcode:

```
[shb_booking_form]
```

Or specify a specific hall:

```
[shb_booking_form hall_id="1"]
```

### 4. Create Guest Booking Management Page

Create another page and add:

```
[shb_user_bookings]
```

This page allows guests to view and manage their bookings using the access token from their email.

### 5. Configure Email Settings

1. Go to **Hall Booking → Settings**
2. Set your email preferences:
   - From Name
   - From Email
   - Admin Notification Email

## Shortcodes

### Display Hall List

```
[shb_hall_list columns="3"]
```

Displays a grid of active halls with basic info and "Book Now" buttons.

**Attributes:**
- `columns` (optional): Number of columns (1-4, default: 3)

### Booking Form

```
[shb_booking_form hall_id="123"]
```

Shows the booking form. If `hall_id` is omitted, users can select a hall from a dropdown.

**Attributes:**
- `hall_id` (optional): Pre-select a specific hall

### User Booking Management

```
[shb_user_bookings]
```

Shows booking details using an access token from the query string (`?token=xxx`).

## How It Works

### Booking Flow

1. **Guest visits your booking page** and selects a hall and date
2. **System checks availability** via AJAX, showing only available time slots
3. **Guest fills in their details** (name, email, phone, organization - optional) and submits the booking
4. **Booking is created** with a unique access token
5. **Emails are sent** to both admin and guest
6. **Admin reviews** the booking in the WordPress admin panel
7. **Admin confirms or cancels** the booking
8. **Guest receives email** with updated status

### Conflict Prevention

The plugin intelligently handles booking conflicts:

- **Full-day booking** blocks all partial slots for that date
- **Any partial booking** makes the full-day slot unavailable
- **Cleaning buffer time** is automatically applied between consecutive bookings
- **Real-time availability checking** prevents double bookings

## Admin Panel

### Dashboard Sections

- **Halls** - Manage your halls and their time slots
- **Bookings** - View and manage all booking requests
- **Settings** - Configure email and general settings

### Managing Bookings

1. Go to **Hall Booking → Bookings**
2. Use filters to find specific bookings (by status, hall, date)
3. Click **Edit** to view booking details
4. Update the status (Pending → Confirmed or Cancelled)
5. Add admin notes if needed
6. Click **Update Booking** to save and send email notification

## Database Schema

### Tables Created

The plugin creates four custom database tables:

- `{prefix}_shb_halls` - Stores hall information
- `{prefix}_shb_slots` - Stores time slot configurations
- `{prefix}_shb_bookings` - Stores booking records (includes customer_organization field)
- `{prefix}_shb_booking_dates` - Stores booking dates for multi-day bookings

All tables use proper indexes for optimal performance.

### Recent Updates

**v1.4.0** - Added `customer_organization` field to bookings table for tracking company/organization bookings (optional field).

## Development

### File Structure

```
simple-hall-booking-manager/
├── simple-hall-booking-manager.php  # Main plugin file
├── readme.txt                        # WordPress.org readme
├── uninstall.php                     # Uninstall handler
│
├── includes/                         # Core plugin classes
│   ├── class-shb-plugin.php
│   ├── class-shb-activator.php
│   ├── class-shb-deactivator.php
│   ├── class-shb-db.php
│   ├── class-shb-admin.php
│   ├── class-shb-frontend.php
│   ├── class-shb-shortcodes.php
│   ├── class-shb-ajax.php
│   ├── class-shb-emails.php
│   └── functions-helpers.php
│
├── admin/                            # Admin area files
│   ├── css/
│   │   └── shb-admin.css
│   ├── js/
│   │   └── shb-admin.js
│   └── views/
│       ├── view-halls-list.php
│       ├── view-hall-edit.php
│       ├── view-bookings-list.php
│       ├── view-booking-edit.php
│       └── view-settings.php
│
├── public/                           # Frontend files
│   ├── css/
│   │   └── shb-frontend.css
│   ├── js/
│   │   └── shb-frontend.js
│   └── partials/
│       ├── booking-form.php
│       ├── hall-list.php
│       └── user-booking.php
│
└── languages/                        # Translation files
    └── simple-hall-booking-manager.pot
```

### Naming Conventions

- **Prefix:** `shb_` for functions, `SHB_` for classes
- **Text Domain:** `simple-hall-booking-manager`
- **Script Handles:** `shb-admin-js`, `shb-admin-css`, `shb-frontend-js`, `shb-frontend-css`

### Hooks & Filters

#### Actions

- `shb_init` - Fires after plugin initialization
- `shb_booking_created` - Fires when a new booking is created
- `shb_booking_status_changed` - Fires when booking status changes

#### Filters

- `shb_email_headers` - Modify email headers
- `shb_email_subject_{type}` - Modify email subjects
- `shb_email_body_{type}` - Modify email bodies
- `shb_booking_form_fields` - Add/modify booking form fields

## Security

- All inputs are sanitized and validated
- All outputs are properly escaped
- Nonces are used on all forms and AJAX requests
- Capability checks on all admin operations
- Access tokens use cryptographically secure random generation
- SQL queries use prepared statements

## Translation

The plugin is fully translatable. All strings use the `simple-hall-booking-manager` text domain.

To translate:

1. Use a tool like Poedit
2. Generate translations from the `.pot` file in `/languages/`
3. Save `.po` and `.mo` files to the `/languages/` directory

## Support

- **Documentation:** See `ARCHITECTURE.md` for technical details
- **Issues:** Report bugs on GitHub Issues
- **Questions:** Use WordPress.org support forums

## Roadmap

### Phase 2 (Planned)

- Payment gateway integration (Stripe/PayPal)
- Google Calendar synchronization
- Advanced admin calendar UI
- Booking reports and analytics
- Custom email template editor
- Multi-currency support
- Recurring bookings
- Booking export (CSV/PDF)

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Follow WordPress Coding Standards
4. Submit a pull request

## License

This plugin is licensed under the GPL v2 or later.

```
Simple Hall Booking Manager
Copyright (C) 2025

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## Credits

Developed with ❤️ for the WordPress community.

---

**Version:** 1.0.0  
**Author:** Your Name  
**Website:** https://yourwebsite.com

