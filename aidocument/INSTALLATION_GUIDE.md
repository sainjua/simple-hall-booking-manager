# Installation & Setup Guide

## Quick Start (5 Minutes)

### Step 1: Activate the Plugin

1. The plugin is already in your WordPress plugins directory
2. Go to **WordPress Admin → Plugins**
3. Find "Simple Hall Booking Manager"
4. Click **Activate**

✅ The plugin will automatically create the necessary database tables.

### Step 2: Create Your First Hall

1. Go to **Hall Booking → Halls** in your WordPress admin
2. Click **Add New**
3. Fill in the details:
   ```
   Hall Name: Main Conference Hall
   Description: Our largest conference room
   Capacity: 100
   Cleaning Buffer: 30 (minutes)
   Status: Active
   ```
4. Click **Save Hall**

### Step 3: Add Time Slots

After saving the hall, you'll see the **Time Slots** section on the same page.

#### Option A: Create a Full Day Slot
```
Slot Type: Full Day
Label: Full Day Booking
Start Time: 09:00
End Time: 18:00
Enabled Days: ✓ All days
Status: ✓ Active
```

#### Option B: Create Partial Slots
Create three slots:

**Morning Slot:**
```
Slot Type: Partial
Label: Morning Session
Start Time: 09:00
End Time: 12:00
Enabled Days: ✓ All days
Status: ✓ Active
```

**Day Slot:**
```
Slot Type: Partial
Label: Day Session
Start Time: 12:00
End Time: 16:00
Enabled Days: ✓ All days
Status: ✓ Active
```

**Evening Slot:**
```
Slot Type: Partial
Label: Evening Session
Start Time: 16:00
End Time: 20:00
Enabled Days: ✓ All days
Status: ✓ Active
```

### Step 4: Create Booking Pages

#### Page 1: Booking Form

1. Go to **Pages → Add New**
2. Title: "Book a Hall"
3. Add this shortcode to the content:
   ```
   [shb_booking_form]
   ```
4. Click **Publish**

#### Page 2: Manage Booking

1. Go to **Pages → Add New**
2. Title: "Manage Your Booking"
3. Add this shortcode to the content:
   ```
   [shb_user_bookings]
   ```
4. Click **Publish**

#### Page 3: Hall List (Optional)

1. Go to **Pages → Add New**
2. Title: "Our Halls"
3. Add this shortcode to the content:
   ```
   [shb_hall_list columns="3"]
   ```
4. Click **Publish**

### Step 5: Configure Email Settings

1. Go to **Hall Booking → Settings**
2. Configure email settings:
   ```
   From Name: Your Venue Name
   From Email: bookings@yourdomain.com
   Admin Notification Email: admin@yourdomain.com
   ```
3. Click **Save Settings**

### Step 6: Test the System

1. Visit your "Book a Hall" page
2. Select a hall and date
3. Choose a time slot
4. Fill in your details
5. Submit the booking
6. Check your email for confirmation

## Understanding the Workflow

### Guest Booking Flow

```
Guest visits booking page
    ↓
Selects hall and date
    ↓
System shows available slots (via AJAX)
    ↓
Guest fills in details and submits
    ↓
Booking created with unique token
    ↓
Emails sent to admin and guest
    ↓
Admin reviews in WordPress admin
    ↓
Admin confirms or cancels
    ↓
Guest receives status update email
```

### Admin Management Flow

```
Hall Booking → Bookings
    ↓
Filter by status/hall/date
    ↓
Click "Edit" on a booking
    ↓
Review booking details
    ↓
Change status (Pending → Confirmed)
    ↓
Add admin notes (optional)
    ↓
Click "Update Booking"
    ↓
Email automatically sent to guest
```

## Important Concepts

### Full Day vs Partial Slots

**Full Day Slot:**
- When booked, blocks the entire day
- No partial slots available if full day is booked
- Perfect for all-day events

**Partial Slots:**
- Multiple bookings possible per day
- If ANY partial slot is booked, full day becomes unavailable
- Perfect for shorter events (meetings, workshops)

### Cleaning Buffer Time

- Set per hall (in minutes)
- Automatically added between bookings
- Example: 30-minute buffer means 30 minutes between end of one booking and start of next
- Prevents back-to-back bookings

### Access Tokens

- Unique 64-character token generated for each booking
- Sent to guest via email
- Allows guests to view/manage booking without login
- Secure and non-guessable

## Customization Tips

### Change Email Templates

Emails are sent from `includes/class-shb-emails.php`. You can customize them by:

1. Creating a child theme or custom plugin
2. Using WordPress filters (for developers)
3. Editing the template methods (not recommended - will be overwritten on update)

### Modify Form Fields

The booking form is in `public/partials/booking-form.php`. You can:

1. Add custom fields
2. Make fields required/optional
3. Add validation rules

### Styling

**Admin Styles:** `admin/css/shb-admin.css`
**Frontend Styles:** `public/css/shb-frontend.css`

You can override these styles in your theme's CSS file.

## Troubleshooting

### Emails Not Sending

1. Check **Settings → Email Settings**
2. Verify your WordPress mail configuration
3. Install a plugin like "WP Mail SMTP" for better email delivery
4. Check spam folder

### Slots Not Showing

1. Verify the hall is set to "Active"
2. Check that slots are marked as "Active"
3. Ensure the selected date is enabled in slot's "Enabled Days"
4. Check browser console for JavaScript errors

### Bookings Not Saving

1. Check PHP error logs
2. Verify database tables were created (look for `wp_shb_*` tables)
3. Try deactivating and reactivating the plugin
4. Check file permissions

### AJAX Not Working

1. Check that JavaScript is enabled in browser
2. Look for JavaScript errors in browser console
3. Verify WordPress AJAX URL is correct
4. Check for plugin conflicts

## Database Tables

The plugin creates three tables:

```sql
{prefix}_shb_halls       -- Stores hall information
{prefix}_shb_slots       -- Stores time slot configurations  
{prefix}_shb_bookings    -- Stores booking records
```

To view data directly:
1. Use phpMyAdmin or similar tool
2. Look for tables starting with your WordPress prefix + `shb_`

## Uninstalling

### Keep Data (Default)

Simply deactivate and delete the plugin. Data remains in database.

### Delete All Data

1. Go to **Hall Booking → Settings**
2. Check "Delete all plugin data when uninstalling"
3. Click **Save Settings**
4. Deactivate and delete the plugin
5. All tables and data will be removed

## Support & Documentation

- **Technical Architecture:** See `ARCHITECTURE.md`
- **Main README:** See `README.md`
- **WordPress.org README:** See `readme.txt`

## Next Steps

1. ✅ Create more halls if needed
2. ✅ Customize email templates
3. ✅ Add booking pages to your navigation menu
4. ✅ Test the complete booking flow
5. ✅ Configure your theme's styling to match
6. ✅ Set up email notifications properly
7. ✅ Train your staff on the admin panel

## Pro Tips

1. **Use descriptive slot labels** - Instead of "Slot 1", use "Morning Session (9 AM - 12 PM)"
2. **Set realistic cleaning buffers** - 30-60 minutes is typical for most venues
3. **Test email delivery** - Send a test booking to yourself first
4. **Create a FAQ page** - Answer common questions about booking policies
5. **Add booking link to header** - Make it easy for users to find
6. **Monitor pending bookings** - Check daily and respond promptly
7. **Use admin notes** - Keep track of special requests or issues

---

**Need Help?** Check the documentation files or contact support.

**Ready to Go?** Start accepting bookings! 🎉

