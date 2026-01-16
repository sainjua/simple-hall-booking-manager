# Settings Save Fix

**Date:** 2026-01-16
**Status:** Applied

## Issue
Users reported that reCAPTCHA keys and other settings (like Confirmation Page) were not saving in the admin settings page (`http://hallbooking.local/wp-admin/admin.php?page=shb-settings`).

## Root Cause
The `sanitize_general_settings` method in `includes/class-shb-admin.php` only included sanitization logic for `delete_data_on_uninstall`, `date_format`, and `time_format`. 

When `update_option('shb_general_settings', ...)` is called, WordPress applies the registered sanitization callback for that option. Since the callback returns a new array containing *only* the fields it explicitly handles, all other fields (recaptcha keys, enabled status, confirmation page) were being effectively stripped out before saving to the database.

## Fix
Updated `SHB_Admin::sanitize_general_settings` in `includes/class-shb-admin.php` to explicitly handle and sanitize all fields present in the General Settings form:

- `confirmation_page`: Sanitized with `absint`.
- `recaptcha_enabled`: Checked for 'yes' value.
- `recaptcha_site_key`: Sanitized with `sanitize_text_field`.
- `recaptcha_secret_key`: Sanitized with `sanitize_text_field`.
- `recaptcha_threshold`: Sanitized with `floatval`.

## Code Change
```php
public function sanitize_general_settings($input) {
    $sanitized = array();
    
    // ... existing sanitization ...

    if (isset($input['confirmation_page'])) {
        $sanitized['confirmation_page'] = absint($input['confirmation_page']);
    }

    $sanitized['recaptcha_enabled'] = isset($input['recaptcha_enabled']) && 'yes' === $input['recaptcha_enabled'] ? 'yes' : 'no';

    if (isset($input['recaptcha_site_key'])) {
        $sanitized['recaptcha_site_key'] = sanitize_text_field($input['recaptcha_site_key']);
    }

    if (isset($input['recaptcha_secret_key'])) {
        $sanitized['recaptcha_secret_key'] = sanitize_text_field($input['recaptcha_secret_key']);
    }

    if (isset($input['recaptcha_threshold'])) {
        $sanitized['recaptcha_threshold'] = floatval($input['recaptcha_threshold']);
    }

    return $sanitized;
}
```
