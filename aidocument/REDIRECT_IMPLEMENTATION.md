# Confirmation Page Redirect Implementation

**Date:** 2026-01-16
**Status:** Applied

## 1. Requirement
If a "Confirmation Page" is selected in the backend settings, the user should be redirected to that page upon successful booking instead of seeing the default inline success message.

## 2. Implementation

### Backend (`includes/class-shb-ajax.php`)
Modified `SHB_AJAX::submit_booking` to:
1.  Check if a `confirmation_page` setting exists and is non-zero.
2.  Retrieve the permalink for that page ID.
3.  Append the booking `token` as a query parameter (`?token=...`).
4.  Include this URL as `redirect_url` in the JSON success response.

### Frontend (`public/js/shb-frontend.js`)
Modified the AJAX success handler to:
1.  Check if `response.data.redirect_url` is present.
2.  If present, immediately set `window.location.href` to that URL.
3.  If NOT present, proceed with the standard inline success message and "View Your Booking" button display.

## Code Changes

**Backend:**
```php
$general_settings = get_option('shb_general_settings', array());
$confirmation_page_id = isset($general_settings['confirmation_page']) ? absint($general_settings['confirmation_page']) : 0;
$redirect_url = '';

if ($confirmation_page_id) {
    $redirect_url = get_permalink($confirmation_page_id);
    if ($redirect_url) {
        $redirect_url = add_query_arg('token', $booking->access_token, $redirect_url);
    }
}

wp_send_json_success(array(
    // ...
    'redirect_url' => $redirect_url,
));
```

**Frontend:**
```javascript
if (response.success) {
    if (response.data.redirect_url) {
        window.location.href = response.data.redirect_url;
        return;
    }
    // ... standard success handling ...
}
```
