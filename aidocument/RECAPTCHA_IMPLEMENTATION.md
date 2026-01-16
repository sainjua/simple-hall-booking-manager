# reCAPTCHA Implementation Update

**Date:** 2026-01-16
**Status:** Applied

## 1. Settings Save Fix (Recap)
The settings page was previously stripping reCAPTCHA keys upon save. This has been fixed in `includes/class-shb-admin.php` by properly sanitizing and including these fields.

## 2. Frontend Implementation
**Requirement:**
The user requested that if reCAPTCHA is enabled, it should be implemented on the frontend form.

**Implementation Details:**
1.  **Script Loading:** `includes/class-shb-frontend.php` already correctly checks for `recaptcha_enabled` = 'yes' and enqueues the Google reCAPTCHA v3 script with the site key.
2.  **Token Generation:** `public/js/shb-frontend.js` executes the reCAPTCHA action `'booking_submit'` before form submission and appends the token to the AJAX request.
3.  **Visual Notice:** Added a visual "Protected by reCAPTCHA" notice to `public/partials/booking-form.php`. This appears below the submit button ONLY if reCAPTCHA is enabled and a site key is present. This provides confirmation to the site owner and transparency to users (required by Google's T&C for invisible reCAPTCHA).

## Code Change (Visual Notice)
Added to `public/partials/booking-form.php`:
```php
<?php
$general_settings = get_option( 'shb_general_settings', array() );
$recaptcha_enabled = isset( $general_settings['recaptcha_enabled'] ) ? $general_settings['recaptcha_enabled'] : 'no';
$recaptcha_site_key = isset( $general_settings['recaptcha_site_key'] ) ? $general_settings['recaptcha_site_key'] : '';

if ( 'yes' === $recaptcha_enabled && ! empty( $recaptcha_site_key ) ) :
    ?>
    <div class="shb-recaptcha-notice" style="font-size: 12px; color: #666; margin-top: 10px; text-align: center;">
        <?php esc_html_e( 'This site is protected by reCAPTCHA and the Google', 'simple-hall-booking-manager' ); ?>
        <a href="https://policies.google.com/privacy" target="_blank"><?php esc_html_e( 'Privacy Policy', 'simple-hall-booking-manager' ); ?></a>
        <?php esc_html_e( 'and', 'simple-hall-booking-manager' ); ?>
        <a href="https://policies.google.com/terms" target="_blank"><?php esc_html_e( 'Terms of Service', 'simple-hall-booking-manager' ); ?></a>
        <?php esc_html_e( 'apply.', 'simple-hall-booking-manager' ); ?>
    </div>
<?php endif; ?>
```
