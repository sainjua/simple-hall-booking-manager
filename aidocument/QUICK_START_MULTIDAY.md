# Multi-Day Booking - Quick Start Guide

## ✅ Implementation Complete!

The multi-day booking feature is now fully implemented and ready to use.

---

## 🚀 Quick Test

### Test the Frontend Booking Form

1. **Navigate to your booking page** (where you have `[shb_booking_form]` shortcode)

2. **You should see:**
   - A new checkbox: "Book for multiple days"
   - When checked, an interactive calendar appears
   - Click dates to select them (they turn blue)
   - Selected dates list shows below the calendar

3. **Make a test booking:**
   - Check "Book for multiple days"
   - Click 2-3 dates on the calendar
   - Select a time slot
   - Fill in your details
   - Submit

4. **Expected result:**
   - ✅ "Booking submitted successfully!"
   - Email sent with all selected dates listed

---

## 🔍 Verify in Admin

1. **Go to:** WordPress Admin → Hall Booking → Bookings

2. **You should see:**
   - Multi-day bookings have a 📅 icon
   - Date range displayed (e.g., "Jan 15 - Jan 17")
   - Day count shown (e.g., "3 days")

3. **Click Edit on a multi-day booking:**
   - Shows "Multi-Day Booking" badge
   - Lists all dates in a table
   - Shows day of week for each date

---

## 📧 Check Emails

**Admin Email:**
- Subject: "[New Booking] Booking Request #123"
- Shows "Multi-Day Booking (3 days)"
- Lists all dates

**Guest Email:**
- Subject: "Booking Request Received - #123"
- Shows "3 days booking"
- Lists all selected dates

---

## 🎨 Styling

All CSS is already added! The calendar should look professional with:
- Blue selected dates
- Hover effects
- Disabled past dates (grayed out)
- Month navigation buttons
- Responsive design

---

## 🔧 Troubleshooting

### Calendar not showing?

**Check:**
1. JavaScript console for errors (F12)
2. Make sure jQuery is loaded
3. Clear browser cache

**Fix:**
```bash
# Clear WordPress cache
wp cache flush

# Or in admin: Clear any caching plugin
```

---

### Dates not saving?

**Check:**
1. Browser console (F12) for AJAX errors
2. WordPress debug.log for PHP errors
3. Database table exists: `wp_shb_booking_dates`

**Enable debug mode:**
```php
// Add to wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

---

### Styling looks broken?

**Check:**
1. CSS file loaded: `public/css/shb-frontend.css`
2. No CSS conflicts from theme
3. Clear browser cache

**Verify CSS loaded:**
```
View Page Source → Search for "shb-frontend.css"
```

---

## 📊 Database Check

### Verify tables exist:

```sql
-- Check if booking_type column exists
DESCRIBE wp_shb_bookings;
-- Should show: booking_type enum('single','multiday')

-- Check if booking_dates table exists
SHOW TABLES LIKE 'wp_shb_booking_dates';
-- Should return: wp_shb_booking_dates
```

### View multiday bookings:

```sql
SELECT 
    b.id,
    b.booking_type,
    b.customer_name,
    COUNT(d.id) as total_dates
FROM wp_shb_bookings b
LEFT JOIN wp_shb_booking_dates d ON b.id = d.booking_id
WHERE b.booking_type = 'multiday'
GROUP BY b.id;
```

---

## 🎯 Features at a Glance

### Frontend
- ✅ Interactive calendar
- ✅ Multiple date selection
- ✅ Visual feedback (blue for selected)
- ✅ Past dates disabled
- ✅ Month navigation
- ✅ Selected dates list
- ✅ Date count display
- ✅ Mobile responsive

### Backend
- ✅ Multiday badge in list
- ✅ Date range display
- ✅ All dates shown in edit view
- ✅ Day of week for each date
- ✅ Cascade delete (all dates removed with booking)

### Emails
- ✅ All dates listed
- ✅ Day count shown
- ✅ Professional formatting

### Database
- ✅ New `booking_type` column
- ✅ New `booking_dates` table
- ✅ Automatic migration
- ✅ Backward compatible

---

## 📱 Test Scenarios

### Scenario 1: Weekend Workshop
- Select Friday, Saturday, Sunday
- Book Morning slot
- Verify all 3 days saved

### Scenario 2: Weekly Class
- Select every Monday for a month
- Book Evening slot
- Verify all Mondays saved

### Scenario 3: Non-consecutive Dates
- Select Jan 15, Jan 20, Jan 25
- Book Day slot
- Verify all 3 dates saved (not a range)

---

## 🔐 Security

All implemented with WordPress best practices:
- ✅ Nonce verification
- ✅ Data sanitization
- ✅ SQL prepared statements
- ✅ Capability checks
- ✅ CSRF protection

---

## 🚦 Status Indicators

### In Booking List:
- 📅 = Multi-day booking
- Single date = Regular booking

### In Edit View:
- Blue badge = Multi-Day Booking
- Table = All dates listed

---

## 💡 Tips

1. **Maximum dates:** No hard limit, but recommend 30 days max for UX
2. **Date selection:** Click to select, click again to deselect
3. **Calendar navigation:** Use ◄ ► buttons to change months
4. **Availability:** Each date checked independently
5. **Cancellation:** Deleting booking removes ALL dates

---

## 📞 Support

If you encounter issues:

1. **Check browser console** (F12 → Console tab)
2. **Check WordPress debug log** (`wp-content/debug.log`)
3. **Verify database tables** (see SQL above)
4. **Clear all caches** (browser, WordPress, CDN)
5. **Test with default theme** (to rule out theme conflicts)

---

## 🎉 Success!

Your plugin now supports multi-day bookings! Users can:
- Book multiple dates in one transaction
- See all their dates in confirmation email
- Manage bookings via access token link

Admins can:
- See which bookings are multi-day at a glance
- View all dates for each booking
- Approve/reject entire multi-day booking

---

## 📈 Next Steps (Optional)

Future enhancements you could add:
- Date range picker ("From - To" selector)
- Bulk discounts for multi-day bookings
- Different slots per date
- Edit individual dates after creation
- Calendar view in admin
- Export bookings to iCal/Google Calendar

---

**Version:** 1.1.0  
**Status:** ✅ Production Ready  
**Last Updated:** 2025-01-01

**Enjoy your new multi-day booking feature!** 🎊

