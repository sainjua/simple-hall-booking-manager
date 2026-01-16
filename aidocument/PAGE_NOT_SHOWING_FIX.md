# Page Not Showing - Troubleshooting Guide

## Quick Diagnostic Steps

### Step 1: Which Page Is Not Showing?

**Please identify which page:**

1. ☐ Booking form page (with `[shb_booking_form]` shortcode)
2. ☐ User booking page (with `[shb_user_bookings]` shortcode)
3. ☐ Hall list page (with `[shb_hall_list]` shortcode)
4. ☐ Admin page in WordPress dashboard
5. ☐ Blank/white screen
6. ☐ Page shows but form/content missing

---

## Common Issues & Solutions

### Issue 1: Blank/White Screen

**Symptoms:**
- Completely white/blank page
- No HTML at all

**Causes:**
- PHP fatal error
- Syntax error

**Solutions:**

**A. Enable Error Display**

Add to `wp-config.php`:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG_LOG', true );
```

**B. Check Error Log**

Look in: `wp-content/debug.log`

**C. Check PHP Error Log**

Location varies by server:
- Local by Flywheel: `/path/to/site/logs/php_error.log`
- XAMPP: `xampp/php/logs/php_error_log.txt`
- MAMP: `/Applications/MAMP/logs/php_error.log`

---

### Issue 2: Shortcode Shows as Text

**Symptoms:**
```
[shb_booking_form]  ← Shows literally on page
```

**Causes:**
- Plugin not activated
- Shortcode registered incorrectly

**Solutions:**

**A. Check Plugin is Active**
```
WordPress Admin → Plugins → Installed Plugins
Find: Simple Hall Booking Manager
Status should be: Active
```

**B. Re-activate Plugin**
```
1. Deactivate
2. Activate
3. Refresh page
```

**C. Check Shortcode Registration**

Run this test in browser console:
```javascript
// Check if WordPress AJAX URL is available
console.log('AJAX URL:', typeof ajaxurl !== 'undefined' ? ajaxurl : 'Not found');
```

---

### Issue 3: Page Shows But Form Missing

**Symptoms:**
- Page header/footer show
- Where shortcode should be = blank

**Causes:**
- Error in template file
- Missing data (no halls)

**Solutions:**

**A. Check Browser Console (F12)**
```
1. Press F12
2. Go to Console tab
3. Look for JavaScript errors
4. Look for red error messages
```

**B. Check if Halls Exist**
```
WordPress Admin → Hall Booking → Halls
Should have at least 1 active hall
```

**C. View Page Source**
```
1. Right-click page → View Page Source
2. Search for: "shb-booking-form"
3. If not found: shortcode not rendering
4. If found but empty: check for errors in HTML comments
```

---

### Issue 4: Assets Not Loading

**Symptoms:**
- Form shows but looks broken/unstyled
- No interactivity

**Causes:**
- CSS/JS files not loading
- Path issues

**Solutions:**

**A. Check Network Tab**
```
1. Press F12
2. Go to Network tab
3. Reload page
4. Look for 404 errors (red)
5. Check if these files load:
   - shb-frontend.css
   - shb-frontend.js
```

**B. Verify File Permissions**
```bash
# Check if files exist and are readable
ls -la /path/to/plugins/simple-hall-booking-manager/public/css/
ls -la /path/to/plugins/simple-hall-booking-manager/public/js/
```

**C. Clear All Caches**
```
1. Browser cache (Ctrl+Shift+Del)
2. WordPress cache plugin
3. Server cache
4. CDN cache
```

---

## Specific Page Solutions

### Booking Form Page (`[shb_booking_form]`)

**Checklist:**

1. ☐ Plugin activated
2. ☐ At least 1 active hall created
3. ☐ Hall has at least 1 active slot
4. ☐ Shortcode added to page correctly: `[shb_booking_form]`
5. ☐ Page published (not draft)

**Test Code:**

Add this to page temporarily to test:
```
Before shortcode
[shb_booking_form]
After shortcode
```

**Expected:**
```
Before shortcode
[Booking form appears here]
After shortcode
```

**If shows:**
```
Before shortcode
After shortcode
```
= Form not rendering (check error logs)

---

### User Bookings Page (`[shb_user_bookings]`)

**Checklist:**

1. ☐ Shortcode on page: `[shb_user_bookings]`
2. ☐ URL has token parameter: `?token=abc123...`
3. ☐ Token is valid (from email)

**Test:**

1. Create a test booking
2. Copy token from database:
   ```sql
   SELECT access_token FROM wp_shb_bookings LIMIT 1;
   ```
3. Visit: `yoursite.com/booking-page/?token=[paste-token-here]`

**Expected:** Shows booking details

**If blank:** Check error logs

---

### Hall List Page (`[shb_hall_list]`)

**Checklist:**

1. ☐ At least 1 hall with status = 'active'
2. ☐ Shortcode: `[shb_hall_list]` or `[shb_hall_list columns="3"]`

**If No Halls Show:**

Check database:
```sql
SELECT * FROM wp_shb_halls WHERE status = 'active';
```

If empty → Create halls first!

---

## Emergency Diagnostic Commands

### Run These in Terminal

**1. Check Plugin Files Exist:**
```bash
ls -la /Users/yujeshkc/Local\ Sites/hallbooking/app/public/wp-content/plugins/simple-hall-booking-manager/
```

**2. Check for PHP Errors:**
```bash
php -l /path/to/plugins/simple-hall-booking-manager/includes/class-shb-plugin.php
php -l /path/to/plugins/simple-hall-booking-manager/includes/class-shb-shortcodes.php
php -l /path/to/plugins/simple-hall-booking-manager/public/partials/booking-form.php
```

**3. Check WordPress Constants:**

Add this to a test page:
```php
<?php
echo 'ABSPATH: ' . ABSPATH . '<br>';
echo 'WP_CONTENT_DIR: ' . WP_CONTENT_DIR . '<br>';
echo 'WP_PLUGIN_DIR: ' . WP_PLUGIN_DIR . '<br>';
echo 'SHB_PLUGIN_DIR: ' . (defined('SHB_PLUGIN_DIR') ? SHB_PLUGIN_DIR : 'NOT DEFINED!') . '<br>';
?>
```

---

## Browser Console Tests

### Test 1: Check jQuery

Open Console (F12) and run:
```javascript
typeof jQuery !== 'undefined' ? 'jQuery loaded: ' + jQuery.fn.jquery : 'jQuery NOT loaded'
```

**Expected:** `"jQuery loaded: 3.x.x"`

---

### Test 2: Check Plugin JavaScript

```javascript
typeof shbFrontend !== 'undefined' ? 'Plugin JS loaded' : 'Plugin JS NOT loaded'
```

**Expected:** `"Plugin JS loaded"`

---

### Test 3: Check AJAX URL

```javascript
console.log('AJAX URL:', shbFrontend.ajaxUrl);
```

**Expected:** `"/wp-admin/admin-ajax.php"`

---

### Test 4: Manual AJAX Test

```javascript
jQuery.post(
    shbFrontend.ajaxUrl,
    {
        action: 'shb_check_availability',
        nonce: jQuery('input[name="nonce"]').val(),
        hall_id: 1,
        date: '2025-01-15'
    },
    function(response) {
        console.log('Response:', response);
    }
).fail(function(xhr) {
    console.error('Error:', xhr.responseText);
});
```

---

## Theme Compatibility Issues

### Issue: Theme Breaking Plugin

**Symptoms:**
- Works with default theme
- Breaks with your theme

**Solutions:**

**A. Test with Default Theme**
```
1. Go to: Appearance → Themes
2. Activate: Twenty Twenty-Four
3. Test your page
4. If works: Theme conflict
```

**B. Check Theme's template**

Some themes override single page templates.

**C. Add Theme Support**

Add to theme's `functions.php`:
```php
add_action('wp_enqueue_scripts', function() {
    // Ensure jQuery is loaded
    wp_enqueue_script('jquery');
});
```

---

## Plugin Conflict Check

### Disable Other Plugins

```
1. Go to: Plugins → Installed Plugins
2. Deactivate ALL except Simple Hall Booking Manager
3. Test page
4. If works: Plugin conflict
5. Reactivate plugins one by one to find culprit
```

**Common Conflicts:**
- Cache plugins
- Security plugins (blocking AJAX)
- Page builders
- Other booking plugins

---

## Database Issues

### Check Tables Exist

Run in database:
```sql
SHOW TABLES LIKE 'wp_shb_%';
```

**Expected:**
```
wp_shb_halls
wp_shb_slots
wp_shb_bookings
```

**If missing:**
```
1. Deactivate plugin
2. Reactivate plugin
3. Tables should be created
```

---

## Server Environment Issues

### Check PHP Version

```bash
php -v
```

**Required:** PHP 7.4+

**If older:** Update PHP

---

### Check PHP Extensions

```bash
php -m | grep -E 'mysqli|pdo'
```

**Should show:**
```
mysqli
pdo_mysql
```

---

### Check WordPress Version

```
Dashboard → Updates
```

**Required:** WordPress 6.0+

---

## Quick Fix Checklist

Try these in order:

1. ☐ Clear all caches (browser, WordPress, server)
2. ☐ Deactivate and reactivate plugin
3. ☐ Enable WP_DEBUG and check logs
4. ☐ Test with default theme
5. ☐ Disable other plugins
6. ☐ Check browser console (F12)
7. ☐ Verify halls exist and are active
8. ☐ Verify slots exist and are active
9. ☐ Check file permissions
10. ☐ Verify shortcode syntax is correct

---

## Get Detailed Error Information

### Enable Maximum Debug Mode

Add to `wp-config.php`:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'SCRIPT_DEBUG', true );
define( 'SAVEQUERIES', true );
@ini_set( 'display_errors', 1 );
```

**Then:**
1. Refresh problem page
2. Check `wp-content/debug.log`
3. Look for errors related to 'SHB' or 'simple-hall-booking'

---

## Still Not Working?

### Provide These Details:

1. **Which page:** (booking form / user bookings / hall list / admin)
2. **What you see:** (blank / shortcode text / error message / partial content)
3. **Browser console errors:** (Press F12, copy errors)
4. **PHP error log:** (Last 20 lines of wp-content/debug.log)
5. **Steps to reproduce:** (What you clicked/did)

### Run Diagnostic and Send Output:

```bash
cd /Users/yujeshkc/Local\ Sites/hallbooking/app/public/wp-content/plugins/simple-hall-booking-manager

echo "=== Plugin Files ==="
ls -la

echo "\n=== PHP Syntax Check ==="
find . -name "*.php" -exec php -l {} \; 2>&1 | grep -i error

echo "\n=== WordPress Debug Log (last 20 lines) ==="
tail -20 ../../debug.log
```

---

**Updated:** 2025-01-01  
**Version:** Troubleshooting Guide v1.0

