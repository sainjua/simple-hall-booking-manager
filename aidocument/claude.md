# Simple Hall Booking Manager

A lightweight WordPress plugin to manage hall bookings with **full-day vs partial slot** logic, guest bookings (no login), and a clean admin UI.

- **Slug:** `simple-hall-booking-manager`
- **Prefix / Namespace:** `shb_` / `SHB_`
- **Text Domain:** `simple-hall-booking-manager`

## Features

- Guest booking (no WP user account required).
- Smart conflict handling between **Full Day** and **Partial** (Morning / Day / Evening) slots.
- Per-hall cleaning buffer time (in minutes) between bookings.
- Admin management for halls, slots, bookings, and basic email settings.
- Shortcodes for hall list, booking form, and guest self-management via token.
- Built following WordPress Coding Standards and Plugin Handbook guidelines.[web:13][web:17]

## Shortcodes

- `[shb_hall_list columns="3"]`  
  Displays a grid of active halls with basic info and a “Book Now” button.

- `[shb_booking_form hall_id="123"]`  
  Shows the booking form. If `hall_id` is omitted, users select a hall first.

- `[shb_user_bookings]`  
  Shows a single booking using an access token from the query string.

## Requirements

- WordPress 6.0+  
- PHP 7.4+  
- MySQL 5.6+ / MariaDB 10.1+

## Installation

1. Upload the `simple-hall-booking-manager` folder to `/wp-content/plugins/`.
2. Activate the plugin from “Plugins → Installed Plugins”.
3. Configure halls, slots, and settings under the “Hall Booking” menu in the admin.
4. Add `[shb_booking_form]` to a page to start receiving bookings.

## Documentation

- Technical architecture: `docs/ARCHITECTURE.md`
- Development tasks & AI prompts: `docs/DEVELOPMENT_TASKS.md`
- WordPress.org readme: `readme.txt` (for directory parsing).[web:1][web:49]

## Roadmap

Phase 1 (current):

- Core booking system.
- Full-day vs partial slot logic.
- Guest token management.
- Email notifications.

Phase 2 (planned):

- Payment gateways (Stripe/PayPal).
- Google Calendar synchronization.
- Advanced admin calendar UI.

Contributions and feedback are welcome.
