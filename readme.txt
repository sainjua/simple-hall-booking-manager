=== Simple Hall Booking Manager ===
Contributors: sainjua
Tags: booking, hall booking, event booking, calendar, reservation
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight WordPress plugin to manage hall bookings with full-day vs partial slot logic, guest bookings (no login required), and a clean admin UI.

== Description ==

Simple Hall Booking Manager is a powerful yet easy-to-use plugin that allows you to manage hall bookings on your WordPress site. Perfect for community centers, event venues, meeting rooms, and any facility that needs to manage space rentals.

### Key Features

* **Guest Bookings** - No user account required. Guests can book using just their email address.
* **Smart Slot Management** - Support for both full-day and partial (Morning/Day/Evening) time slots.
* **Conflict Prevention** - Automatically handles conflicts between full-day and partial bookings.
* **Cleaning Buffer Time** - Set buffer time between bookings for hall preparation and cleaning.
* **Email Notifications** - Automatic emails to admins and guests for booking confirmations and status changes.
* **Access Token System** - Guests receive a unique link to view and manage their bookings.
* **Clean Admin UI** - Intuitive interface for managing halls, time slots, and bookings.
* **Shortcodes** - Easy integration with your existing pages using simple shortcodes.
* **Responsive Design** - Works beautifully on desktop, tablet, and mobile devices.

### Perfect For

* Community centers
* Event venues
* Meeting rooms
* Conference halls
* Wedding venues
* Training facilities
* Co-working spaces
* Any space rental business

### How It Works

1. **Create Halls** - Add your halls with details like capacity and cleaning buffer time.
2. **Define Time Slots** - Set up full-day or partial time slots for each hall.
3. **Add Shortcodes** - Place booking forms on your website pages.
4. **Receive Bookings** - Guests submit booking requests without needing to create accounts.
5. **Manage Bookings** - Review, approve, or decline booking requests from the admin panel.
6. **Automated Emails** - Both you and your guests receive email notifications automatically.

### Shortcodes

* `[shb_hall_list columns="3"]` - Display a grid of available halls
* `[shb_booking_form hall_id="123"]` - Display the booking form
* `[shb_user_bookings]` - Display booking details for guests (using token from URL)

### Developer Friendly

* Clean, well-documented code following WordPress Coding Standards
* Action and filter hooks for customization
* Extensible architecture
* Translatable - ready for internationalization

== Installation ==

### Automatic Installation

1. Log in to your WordPress admin panel
2. Go to Plugins → Add New
3. Search for "Simple Hall Booking Manager"
4. Click "Install Now" and then "Activate"

### Manual Installation

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Go to Plugins → Add New → Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Activate the plugin

### Setup

1. Go to "Hall Booking" in your WordPress admin menu
2. Add your first hall with details (capacity, cleaning buffer, etc.)
3. Create time slots for your hall (full-day or partial)
4. Create a new page and add the `[shb_booking_form]` shortcode
5. Create another page for guest booking management and add `[shb_user_bookings]`
6. Configure email settings under Hall Booking → Settings
7. Start receiving bookings!

== Frequently Asked Questions ==

= Do guests need to create an account to make a booking? =

No! Guests can book without creating a WordPress user account. They only need to provide their name, email, and event details.

= How do guests manage their bookings? =

After booking, guests receive an email with a unique access link. They can use this link to view their booking details and cancel if needed.

= What's the difference between full-day and partial slots? =

* Full-day slots block the entire day for that hall when booked
* Partial slots (Morning/Day/Evening) allow multiple bookings in the same day
* If a full-day slot is booked, no partial slots are available for that day
* If any partial slot is booked, the full-day slot becomes unavailable

= What is cleaning buffer time? =

Cleaning buffer is extra time (in minutes) automatically added between bookings to allow for hall cleaning and preparation. For example, with a 30-minute buffer, if one booking ends at 2:00 PM, the next available time slot starts at 2:30 PM.

= Can I customize the email templates? =

Currently, email templates can be customized by developers using WordPress filters. We plan to add a visual email template editor in a future version.

= Can I accept payments for bookings? =

Payment gateway integration (Stripe/PayPal) is planned for version 2.0. Currently, the plugin focuses on booking management.

= Is the plugin translatable? =

Yes! The plugin is fully translatable and ready for internationalization. All strings use proper WordPress text domain.

= Can I export booking data? =

You can view all bookings in the admin panel. Export functionality is planned for a future update. For now, you can access booking data directly from the WordPress database.

== Screenshots ==

1. Admin - Halls Management List
2. Admin - Hall Edit Page with Slots
3. Admin - Bookings List with Filters
4. Admin - Booking Details Page
5. Admin - Settings Page
6. Frontend - Hall List Display
7. Frontend - Booking Form
8. Frontend - Guest Booking Management

== Changelog ==

= 1.0.0 - 2025-01-01 =
* Initial release
* Hall management with capacity and cleaning buffer
* Time slot creation (full-day and partial)
* Guest booking without login requirement
* Booking conflict prevention
* Email notifications for admins and guests
* Access token system for guest booking management
* Three shortcodes for easy integration
* Responsive admin and frontend design
* WordPress Coding Standards compliant

== Upgrade Notice ==

= 1.0.0 =
Initial release of Simple Hall Booking Manager.

== Additional Info ==

### Support

For support, feature requests, or bug reports, please visit our [support forum](https://wordpress.org/support/plugin/simple-hall-booking-manager/).

### Contributing

We welcome contributions! Please see our [GitHub repository](https://github.com/sainjua/simple-hall-booking-manager) for development guidelines.

### Roadmap

**Phase 2 Features (Planned):**
* Payment gateway integration (Stripe/PayPal)
* Google Calendar synchronization
* Advanced calendar view in admin
* Booking reports and analytics
* Custom email template editor
* Booking export functionality
* Multi-currency support
* Recurring bookings

### Privacy Policy

This plugin stores booking information including customer names, email addresses, and phone numbers in your WordPress database. This data is used solely for booking management and communication purposes.

**External Services:**
If enabled in the settings, this plugin uses **Google reCAPTCHA v3** to prevent spam submissions. Use of reCAPTCHA is subject to the [Google Privacy Policy](https://policies.google.com/privacy) and [Terms of Use](https://policies.google.com/terms). Data shared with Google includes your users' IP address and behavioral data for risk analysis.

The plugin does not share data with other external services except for sending emails via your WordPress mail configuration.

### Credits

Developed with ❤️ for the WordPress community.

