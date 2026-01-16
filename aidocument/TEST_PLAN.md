# Simple Hall Booking Manager - Manual Testing Plan

## Test Environment
- **WordPress Admin URL**: `/wp-admin`
- **Username**: `admin`
- **Password**: `admin@1`

> **Note for Automation Testing (Antigravity):**
> Please perform automation testing based on this plan. You may skip some test numbers if they are redundant or if high-level flows cover multiple points. Priority should be given to critical booking logic and security checks. Also Skip Installation & Deactivation if the user doesn't mention.

This document outlines the systematic testing steps required to verify the functionality of the plugin before submission to the WordPress.org directory.

## 1. Installation & Deactivation
- [ ] **Fresh Install**: Zip the plugin, upload via Plugins > Add New.
- [ ] **Activation**: Activate the plugin. Check if custom database tables are created (`shb_halls`, `shb_slots`, `shb_bookings`, `shb_booking_dates`).
- [ ] **Deactivation**: Deactivate the plugin. Tables should remain.
- [ ] **Uninstall**: Delete the plugin. If "Delete data on uninstall" is checked in Settings, verify tables are dropped.

## 2. Admin: Hall Management
- [ ] **Create Hall**: Add a hall with title, capacity, and cleaning buffer.
- [ ] **Validation**: Try to save without a title (should fail).
- [ ] **Edit Hall**: Update capacity or buffer and save.
- [ ] **Delete Hall**: Delete a hall. Verify all associated slots are also deleted from the DB.

## 3. Admin: Slot Management (AJAX Modal)
- [ ] **Add Partial Slot**: Add a 9:00 AM - 12:00 PM slot.
- [ ] **Add Full Day Slot**: Add a "Full Day" slot (00:00 to 23:59).
- [ ] **Overlap Check (Internal)**: Create a second partial slot 10:00 AM - 1:00 PM. Click "Check Overlap". It should flag the conflict.
- [ ] **Edit Slot**: Change an existing slot's time. Verify "Check Overlap" doesn't flag the slot against itself.
- [ ] **Full Day Restriction**: Try to add a second Full Day slot to the same hall. It should be prevented server-side.

## 4. Frontend: Booking Process
- [ ] **Single Day Booking**: 
    - [ ] Select a hall and a date.
    - [ ] Select a partial slot.
    - [ ] Fill customer info and submit.
    - [ ] Verify success message and redirection to confirmation page.
- [ ] **Multi-Day Booking**:
    - [ ] Select a hall.
    - [ ] Click multiple dates on the calendar.
    - [ ] Select different slots for different dates.
    - [ ] Submit and verify.
- [ ] **Conflict Logic (Full Day vs Partial)**:
    - [ ] Book a Full Day slot for Jan 15.
    - [ ] Try to book a Partial slot for Jan 15 (should be unavailable).
    - [ ] Book a Partial slot for Jan 16.
    - [ ] Try to book a Full Day slot for Jan 16 (should be unavailable).

## 5. Guest Management (The PIN System)
- [ ] **Confirmation Email**: Check if the guest receives an email with the correct PIN and access link.
- [ ] **Access Link**: Click the link in the email. It should bypass the PIN form and show booking details.
- [ ] **Manual PIN Lookup**: Visit the shortcode page without a token. Enter the PIN manually.
- [ ] **Cancellation**: Cancel the booking as a guest. Verify status changes to "Cancelled" and email is sent.

## 6. Security & Standards
- [ ] **Nonce Security**: Try to submit the booking form with a modified/missing nonce. It should fail.
- [ ] **Capability Check**: Verify a "Subscriber" user cannot access the Hall Booking admin menus.
- [ ] **Data Sanitization**: Submit "<b>Name</b>" as customer name. Verify it's sanitized in the database/display.

## 7. Performance & UI
- [ ] **Responsive Test**: Test the booking form on a mobile view.
- [ ] **AJAX Spinners**: Verify spinners appear during overlap checks and booking submissions.
- [ ] **Auto-Dismiss Notices**: Verify admin success messages disappear after 5 seconds.
