# Booking Form Error Fix

## Issues Fixed

### 1. ✅ Nonce Field Name Mismatch

**Problem:** The nonce field in the form was named `shb_nonce` but the AJAX handler expected `nonce`.

**Before:**
```php
// In booking-form.php
<?php wp_nonce_field( 'shb_frontend_nonce', 'shb_nonce' ); ?>

// In class-shb-ajax.php
check_ajax_referer( 'shb_frontend_nonce', 'nonce' ); // ← Looking for 'nonce'
```

**After:**
```php
// In booking-form.php
<?php wp_nonce_field( 'shb_frontend_nonce', 'nonce' ); ?> // ← Fixed!

// In class-shb-ajax.php
wp_verify_nonce( $_POST['nonce'], 'shb_frontend_nonce' ); // ← Now matches!
```

**Result:** Nonce verification now works correctly!

---

### 2. ✅ Better Error Messages

**Improved AJAX Error Handling:**

**Before:**
```javascript
error: function() {
    SHB.showMessage('error', shbFrontend.i18n.error);
}
```

**After:**
```javascript
error: function(xhr, status, error) {
    console.error('SHB AJAX Error:', status, error);
    console.error('SHB Response:', xhr.responseText);
    
    var errorMsg = shbFrontend.i18n.error;
    
    // Try to parse error response
    try {
        var response = JSON.parse(xhr.responseText);
        if (response.data && response.data.message) {
            errorMsg = response.data.message;
        }
    } catch (e) {
        console.error('SHB: Could not parse error response');
    }
    
    SHB.showMessage('error', errorMsg);
}
```

**Result:** Shows actual error messages from server!

---

### 3. ✅ Enhanced Validation Messages

**Before:**
```php
'Please fill in all required fields.'
```

**After:**
```php
// Lists exactly which fields are missing
'Please fill in the following required fields: Hall, Time Slot, Name, Email'
```

**Example Error Messages:**
- ❌ Missing hall: `"Please fill in the following required fields: Hall"`
- ❌ Missing email: `"Please fill in the following required fields: Email"`
- ❌ Missing multiple: `"Please fill in the following required fields: Hall, Time Slot, Email"`

---

### 4. ✅ Console Debugging Added

**New Console Logs:**
```javascript
console.log('SHB: Form submission started');
console.log('SHB: Hall ID:', hallId);
console.log('SHB: Date:', bookingDate);
console.log('SHB: Sending AJAX request...');
console.log('SHB: AJAX Response:', response);
console.log('SHB: Booking successful!');
console.error('SHB AJAX Error:', status, error);
```

**How to View:**
1. Open browser DevTools (F12)
2. Go to Console tab
3. Submit form
4. See detailed logs!

---

### 5. ✅ Frontend Validation Added

**Before:** Only checked if slot was selected

**After:** Checks all required fields before submission:
```javascript
- Hall selection
- Booking date
- Customer name
- Customer email
- Time slot selection
```

**User-Friendly Messages:**
- "Please select a hall."
- "Please select a booking date."
- "Please enter your name."
- "Please enter your email address."
- "Please select a time slot."

---

## How to Test

### Step 1: Check Browser Console

1. Open the booking form page
2. Press **F12** to open DevTools
3. Go to **Console** tab
4. Try to submit the form
5. Look for messages starting with `SHB:`

**What to look for:**
```
✅ Good:
SHB: Form submission started
SHB: Hall ID: 1
SHB: Date: 2025-01-15
SHB: Sending AJAX request...
SHB: AJAX Response: {success: true, data: {...}}
SHB: Booking successful!

❌ Bad:
SHB Error: No slot selected
SHB AJAX Error: 403 Forbidden
SHB Error: Security verification failed
```

---

### Step 2: Test Each Required Field

#### Test 1: Try without selecting hall
```
Expected: "Please select a hall."
```

#### Test 2: Try without selecting date
```
Expected: "Please select a booking date."
```

#### Test 3: Try without entering name
```
Expected: "Please enter your name."
```

#### Test 4: Try without entering email
```
Expected: "Please enter your email address."
```

#### Test 5: Try without selecting time slot
```
Expected: "Please select a time slot."
```

#### Test 6: Complete form correctly
```
Expected: "Booking submitted successfully! Please check your email for confirmation."
```

---

### Step 3: Check for Common Issues

#### Issue 1: "Security verification failed"

**Cause:** Nonce mismatch or expired

**Fix:**
1. Clear browser cache
2. Refresh the page
3. Try again

**If still failing:**
- Check if WP_DEBUG is on
- Check error_log for details
- Ensure form has nonce field: `<input name="nonce" ...>`

---

#### Issue 2: "An error occurred"

**Cause:** AJAX request failed

**Fix:**
1. Check browser console for details
2. Check if AJAX URL is correct:
   ```javascript
   console.log('AJAX URL:', shbFrontend.ajaxUrl);
   // Should be: /wp-admin/admin-ajax.php
   ```

3. Check WordPress error logs

---

#### Issue 3: No slots showing

**Cause:** AJAX availability check not working

**Fix:**
1. Check console for errors
2. Verify hall has active slots
3. Verify date is in the future
4. Check if slots are enabled for that day of week

---

## Files Modified

| File | Change | Purpose |
|------|--------|---------|
| `public/partials/booking-form.php` | Nonce field name | Fixed from 'shb_nonce' to 'nonce' |
| `includes/class-shb-ajax.php` | Nonce verification | Better error handling & messages |
| `public/js/shb-frontend.js` | Console logging | Debugging & validation |

---

## WordPress Debug Mode

### Enable Debug Logging

Add to `wp-config.php`:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

### Check Logs

Look in: `wp-content/debug.log`

**Search for:**
```
SHB: Booking submission received
SHB Error: Nonce verification failed
SHB: Validated data - Hall: X, Slot: Y
```

---

## Error Messages Reference

### Frontend (JavaScript) Errors

| Error | Cause | Fix |
|-------|-------|-----|
| "Please select a hall" | No hall selected | Choose a hall from dropdown |
| "Please select a booking date" | No date selected | Pick a date |
| "Please enter your name" | Name field empty | Fill in name |
| "Please enter your email" | Email field empty | Fill in email |
| "Please select a time slot" | No slot checked | Select a time slot after checking availability |

---

### Backend (PHP) Errors

| Error | Cause | Fix |
|-------|-------|-----|
| "Security verification failed" | Nonce invalid/expired | Refresh page |
| "Please fill in the following required fields: ..." | Missing data | Fill in listed fields |
| "Invalid parameters" | Hall or date missing | Ensure form data is complete |
| "Invalid date format" | Date format wrong | Use date picker |
| "Cannot book dates in the past" | Selected past date | Choose future date |
| "Selected hall is not available" | Hall inactive | Choose different hall |
| "Selected slot is not valid" | Slot doesn't exist | Check availability again |
| "Slot is no longer available" | Double-booking | Choose different slot/date |

---

## Testing Checklist

- [ ] Form displays correctly
- [ ] Hall dropdown shows halls
- [ ] Date picker works
- [ ] Clicking "Check availability" shows slots
- [ ] Slot selection works (radio buttons)
- [ ] All fields can be filled
- [ ] Submit button works
- [ ] Success message appears
- [ ] Email received with booking link
- [ ] Access link works

---

## Next Steps

### If Form Works Now:
✅ Test complete booking flow
✅ Check emails are sent
✅ Verify booking appears in admin
✅ Test cancellation via access link

### If Form Still Fails:
1. Check browser console (F12)
2. Enable WP_DEBUG and check logs
3. Test with different browser
4. Clear all caches (browser, WordPress plugins)
5. Check for plugin conflicts

---

## Browser Console Commands

### Check if jQuery loaded:
```javascript
typeof jQuery !== 'undefined' ? 'jQuery loaded' : 'jQuery NOT loaded'
```

### Check if SHB object exists:
```javascript
typeof SHB !== 'undefined' ? 'SHB loaded' : 'SHB NOT loaded'
```

### Check AJAX URL:
```javascript
shbFrontend.ajaxUrl
```

### Check nonce:
```javascript
$('input[name="nonce"]').val()
```

### Manually test AJAX:
```javascript
jQuery.post(shbFrontend.ajaxUrl, {
    action: 'shb_check_availability',
    nonce: jQuery('input[name="nonce"]').val(),
    hall_id: 1,
    date: '2025-01-15'
}, function(response) {
    console.log('Response:', response);
});
```

---

## Support

If issues persist after following this guide:

1. **Check WordPress error logs:** `wp-content/debug.log`
2. **Check browser console:** Press F12
3. **Test with default theme:** Switch to Twenty Twenty-Four
4. **Disable other plugins:** Test for conflicts

---

**Status:** ✅ FIXED  
**Version:** 1.0.2  
**Date:** 2025-01-01

