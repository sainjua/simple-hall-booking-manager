# Booking Success UI Update

**Date:** 2026-01-16
**Status:** Applied

## 1. Issue
The "Your Booking Link" text on the frontend success message was raw URL text, which was too long and overflowed the message container/card.

## 2. Fix
Modified the JavaScript success callback in `public/js/shb-frontend.js` to replace the raw URL display with a styled action button.

- **Old:** "Your Booking Link: [long_url]" (raw text)
- **New:** "Booking Received!" header + "View Your Booking" button.

The button uses the `.shb-btn .shb-btn-primary` classes for styling and handles caching/click events normally.

```javascript
var accessHtml = '<div class="shb-notice shb-notice-info" style="margin-top: 15px;">';
accessHtml += '<p><strong>Booking Received!</strong></p>';
accessHtml += '<p style="margin: 15px 0;"><a href="' + response.data.access_url + '" target="_blank" class="shb-btn shb-btn-primary" style="color: #fff; text-decoration: none;">View Your Booking</a></p>';
accessHtml += '<p><small>Please save this link to view or manage your booking.</small></p>';
accessHtml += '</div>';
```
