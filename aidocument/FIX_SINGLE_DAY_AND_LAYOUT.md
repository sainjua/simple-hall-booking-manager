# Single Day Booking and Layout Fixes

**Date:** 2026-01-16
**Status:** Applied

## 1. Single Day Booking Issue
**Problem:**
When selecting a single day for booking, the request was failing server-side validation. The error "booking date missing" was not directly visible, but the logic relied on `booking_date` being present for single-day bookings. The frontend JavaScript was not sending the `booking_date` parameter in the form submission when only one day was selected; it was only sending `slot_id`.

**Fix:**
Modified `public/js/shb-frontend.js` in the `updateSelectedDatesDisplay` function.
Added a hidden input field for `booking_date` to the generated HTML for single-date selection.

```javascript
var html = '<div class="shb-single-date-selection">';
// Added this line:
html += '<input type="hidden" name="booking_date" value="' + dateStr + '">'; 
html += '<div class="shb-date-info">';
```

## 2. Layout Overflow Issue
**Problem:**
The input fields and buttons in the booking form were overflowing their container cards. This is a common CSS issue when `width: 100%` is combined with padding and borders without `box-sizing: border-box`.

**Fix:**
Modified `public/css/shb-frontend.css`.
1.  Added `box-sizing: border-box;` to the `.shb-form-group` inputs, selects, and textareas.
2.  Added `box-sizing: border-box;` to the `.shb-btn` class.
3.  Removed `display: inline-block;` from `.shb-slot-status` as it was also using `float: right`, causing a CSS linting warning (inline-block is ignored when floated).

## Verification
- **Single Day Booking:** Submitting a booking for a single day now includes `booking_date` in the POST request, passing validation.
- **Layout:** Input fields and buttons now respect the container width and do not overflow.
