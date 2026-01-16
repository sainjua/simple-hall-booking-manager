# Booking Confirmation Page with PIN Access

## Overview

The Booking Confirmation Page feature allows customers to view their booking details using a secure PIN (access token). This provides a self-service portal where guests can check their booking status and manage their reservations.

## Features

### ✅ What's Included

1. **Admin Settings Integration**
   - Select a dedicated page for booking confirmations
   - Built-in page selector in Settings → General Settings
   - Automatic validation and preview link

2. **Secure PIN Access System**
   - Each booking gets a unique 32-character access token
   - Token displayed prominently in all emails
   - Direct link provided for easy access

3. **Email Integration**
   - **Pending Bookings**: Blue-themed PIN box with instructions
   - **Confirmed Bookings**: Green-themed PIN box celebrating approval
   - "View Booking Details" button for one-click access

4. **Multi-Day Booking Support**
   - Shows all dates in a clean table format
   - Displays individual slot for each day
   - Mobile-responsive design

5. **Self-Service Actions**
   - View all booking details
   - Cancel booking (for pending/confirmed status)
   - See real-time status updates

---

## Setup Guide

### Step 1: Create Confirmation Page

1. Go to **Pages → Add New** in WordPress
2. Create a new page (e.g., "My Booking")
3. Add the shortcode: `[shb_user_bookings]`
4. Publish the page

### Step 2: Configure Settings

1. Go to **Hall Booking → Settings**
2. Scroll to **General Settings**
3. Find **"Booking Confirmation Page"**
4. Select your newly created page from the dropdown
5. Click **"Save Settings"**

### Step 3: Test It Out

1. Make a test booking from the frontend
2. Check the confirmation email
3. Click the "View Booking Details" button
4. Or manually visit the page and enter the PIN from the email

---

## How It Works

### For Customers

#### 1. **Receive Booking Email**
After booking, customers receive an email with:
- Large, easy-to-read ACCESS PIN
- One-click "View Booking Details" button
- Instructions for future access

**Example Email (Pending):**
```
┌─────────────────────────────────────────┐
│ Your Booking Access PIN:                │
│                                          │
│  a3k9mF2pL7xQ8nR5tY1w4vC6zB0hD9jE3gS   │
│                                          │
│ Save this PIN to access your booking    │
│ details anytime.                         │
└─────────────────────────────────────────┘

[View Booking Details]  ← Click here
```

#### 2. **Access Booking Page**
Two ways to access:
- **Option A**: Click the button in the email (auto-fills PIN)
- **Option B**: Visit page manually and enter PIN

#### 3. **View & Manage**
On the confirmation page, they can:
- See complete booking details
- Check current status (Pending/Confirmed/Cancelled)
- Cancel if needed (with confirmation)

### For Admins

#### **Automatic PIN Generation**
- System automatically creates unique 32-character tokens
- Tokens are cryptographically secure
- Never reused across bookings

#### **Email Templates Updated**
All guest emails now include:
1. Prominent PIN display box
2. Styled "View Booking Details" button
3. Clear instructions for access

---

## Shortcode Reference

### `[shb_user_bookings]`

Displays booking details when accessed with a valid token.

**Usage:**
```
[shb_user_bookings]
```

**URL Parameters:**
- `?token=YOUR_ACCESS_TOKEN` - The booking access PIN

**Example URL:**
```
https://yoursite.com/my-booking/?token=a3k9mF2pL7xQ8nR5tY1w4vC6zB0hD9jE3gS
```

**What It Shows:**
- Booking ID
- Hall name
- Dates (single or multi-day)
- Time slots
- Customer information
- Event details
- Current status
- Action buttons (Cancel if applicable)

---

## Email Examples

### Pending Status Email

**Subject:** Booking Request Received - #123

**Body:**
```
Dear John Smith,

Thank you for your booking request. We have received your
request and will review it shortly.

┌─────────────────────────────────────────┐
│ Your Booking Access PIN:                │
│                                          │
│  a3k9mF2pL7xQ8nR5tY1w4vC6zB0hD9jE3gS   │
│                                          │
│ Save this PIN to access your booking    │
│ details anytime.                         │
└─────────────────────────────────────────┘

[View Booking Details]

You can use the link above or visit the booking page
and enter your PIN to view details or cancel your
booking if needed.
```

### Confirmed Status Email

**Subject:** Booking Confirmed - #123

**Body:**
```
Dear John Smith,

Great news! Your booking has been confirmed.

┌─────────────────────────────────────────┐
│ Your Booking Access PIN:                │
│                                          │
│  a3k9mF2pL7xQ8nR5tY1w4vC6zB0hD9jE3gS   │
│                                          │
│ Save this PIN to access your booking    │
│ details anytime.                         │
└─────────────────────────────────────────┘

[View Booking Details]
```

---

## Confirmation Page Display

### Single-Day Booking

```
╔═══════════════════════════════════════════╗
║    Booking Details                        ║
╠═══════════════════════════════════════════╣
║                                           ║
║  Status: [ CONFIRMED ]                    ║
║                                           ║
║  Booking ID:      #123                    ║
║  Hall:            Main Conference Room    ║
║  Date:            January 15, 2025        ║
║  Time Slot:       Morning (9 AM - 12 PM)  ║
║  Customer Name:   John Smith              ║
║  Email:           john@example.com        ║
║                                           ║
║  ┌─────────────────────┐                 ║
║  │  Cancel Booking     │                 ║
║  └─────────────────────┘                 ║
║                                           ║
╚═══════════════════════════════════════════╝
```

### Multi-Day Booking

```
╔═══════════════════════════════════════════╗
║    Booking Details                        ║
╠═══════════════════════════════════════════╣
║                                           ║
║  Status: [ CONFIRMED ]                    ║
║                                           ║
║  Booking ID:      #124                    ║
║  Hall:            Main Conference Room    ║
║  Booking Type:    📅 Multi-Day Booking    ║
║                                           ║
║  Booking Dates:   3 days                  ║
║                                           ║
║  ┌───────────────┬────────────────────┐  ║
║  │ Date          │ Time Slot          │  ║
║  ├───────────────┼────────────────────┤  ║
║  │ Jan 15, 2025  │ Morning (9-12 PM)  │  ║
║  │ Jan 16, 2025  │ Afternoon (1-5 PM) │  ║
║  │ Jan 17, 2025  │ Full Day           │  ║
║  └───────────────┴────────────────────┘  ║
║                                           ║
╚═══════════════════════════════════════════╝
```

---

## Security Features

### Token Generation
- Uses `shb_generate_token(32)` function
- Cryptographically secure random string
- 32 characters (alphanumeric)
- Virtually impossible to guess

### Access Control
- Token required for all access
- Invalid tokens show error message
- No personal info exposed without valid token
- Cancelled bookings still accessible (read-only)

### Database Storage
- Tokens stored in `wp_shb_bookings.access_token`
- Indexed for fast lookup
- Never exposed in URLs except when needed

---

## Troubleshooting

### Issue: Page Not Working

**Problem:** Clicking link shows 404 or blank page

**Solution:**
1. Go to **Settings → Permalinks**
2. Click "Save Changes" (flush rewrite rules)
3. Clear any caching plugins
4. Test again

### Issue: Invalid Token Error

**Problem:** "Invalid access token" message appears

**Solutions:**
- Verify the entire token was copied correctly
- Check for extra spaces before/after token
- Ensure the booking hasn't been deleted
- Try clicking the email link instead of manual entry

### Issue: Emails Not Showing PIN

**Problem:** Emails don't display the PIN box

**Solutions:**
- Check if emails are HTML-enabled
- Test with different email clients
- Verify email templates weren't customized elsewhere
- Check spam folder (some clients strip styling)

---

## Developer Notes

### Function Reference

#### `shb_get_booking_access_url( $token, $page_id )`
Generates the full URL to access a booking.

**Parameters:**
- `$token` (string) - The booking access token
- `$page_id` (int|null) - Page ID containing shortcode (optional)

**Returns:** (string) Full URL with token parameter

**Example:**
```php
$url = shb_get_booking_access_url( $booking->access_token );
// Returns: https://site.com/my-booking/?token=abc123...
```

### Database Schema

```sql
wp_shb_bookings.access_token VARCHAR(255)
- Stores the unique access token
- Indexed for fast lookups
- Generated on booking creation
```

### Template File

**Location:** `public/partials/user-booking.php`

**Template Tags Available:**
- `$booking` - Main booking object
- `$hall` - Hall object
- `$slot` - Slot object
- `$booking_dates` - Array (for multi-day bookings)

---

## Best Practices

### For Site Owners

1. **Create a Dedicated Page**
   - Use a clear name like "My Booking" or "View Booking"
   - Don't add other content to this page
   - Keep the URL simple and memorable

2. **Customize Email Instructions**
   - Add site contact info for questions
   - Include office hours if applicable
   - Mention cancellation policy

3. **Monitor Access**
   - Guests should receive emails immediately
   - Test the full flow regularly
   - Ensure confirmation page is mobile-friendly

### For Customers

1. **Save Your PIN**
   - Keep the email in a safe folder
   - Or copy PIN to notes app
   - Don't share PIN with others

2. **Bookmark the Page**
   - Add confirmation page to browser bookmarks
   - You'll need to enter PIN each visit (security)

3. **Check Status Regularly**
   - Pending bookings may be approved anytime
   - You'll get email updates
   - But can also check manually anytime

---

## Changelog

### Version 1.2.0
- ✅ Added booking confirmation page setting
- ✅ Updated email templates with prominent PIN display
- ✅ Added multi-day booking support to confirmation page
- ✅ Improved mobile responsiveness
- ✅ Enhanced security with better token generation

---

## Support

For questions or issues:
1. Check this documentation first
2. Test with a fresh booking
3. Clear caches (browser + plugins)
4. Review WordPress error logs

---

**Related Documentation:**
- [Multi-Day Booking Feature](MULTIDAY_BOOKING_FEATURE.md)
- [Conflict Management System](CONFLICT_MANAGEMENT.md)
- [Email Configuration](ARCHITECTURE.md#email-system)

