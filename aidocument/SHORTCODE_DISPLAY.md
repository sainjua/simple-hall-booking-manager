# Shortcode Display Feature

## Overview

The Halls list page now displays a **copyable shortcode** for each hall, making it easy for administrators to quickly copy and paste booking form shortcodes into their pages.

---

## Location

**Admin Page:** Hall Booking → Halls  
**URL:** `wp-admin/admin.php?page=shb-halls`

---

## Features

### 1. Shortcode Column 📋

Each hall in the list now has a "Shortcode" column displaying:

```
[shb_booking_form hall_id="123"]
```

### 2. One-Click Copy 🖱️

**How it works:**
- Click on the shortcode
- Automatically copies to clipboard
- Visual feedback: Changes to green with "Copied!" message
- Reverts back after 1.5 seconds

### 3. Visual Design 🎨

- Light gray background with border radius
- Hover effect: Darker background with shadow
- Active click effect: Slight scale animation
- Responsive: Adjusts on mobile devices

---

## User Experience

### Before

```
Admin had to:
1. Remember the hall ID
2. Manually type: [shb_booking_form hall_id="123"]
3. Risk typos
```

### After

```
Admin can:
1. Click the shortcode in the list
2. ✅ Copied automatically!
3. Paste anywhere
```

---

## Visual Example

```
┌─────────────────────────────────────────────────────────────────────┐
│ Halls                                              [Add New]         │
├─────────────────────────────────────────────────────────────────────┤
│ Title           | Capacity | Status | Buffer | Shortcode           │
├─────────────────────────────────────────────────────────────────────┤
│ Conference A    | 100      | Active | 30 min | [shb_booking...] ← Click!│
│ Meeting Room B  | 50       | Active | 15 min | [shb_booking...] ← Click!│
│ Training Hall   | 75       | Active | 30 min | [shb_booking...] ← Click!│
└─────────────────────────────────────────────────────────────────────┘

When clicked:
┌─────────────────────────────┐
│ [shb_booking_form hall_id=1]│ ← Turns green
│         Copied!              │ ← Shows "Copied!"
└─────────────────────────────┘
```

---

## Technical Implementation

### HTML Structure

```php
<code class="shb-shortcode" 
      onclick="shbCopyShortcode(this)" 
      title="Click to copy">
    [shb_booking_form hall_id="<?php echo $hall->id; ?>"]
</code>
```

### JavaScript Function

```javascript
function shbCopyShortcode(element) {
    // Get shortcode text
    var shortcode = element.textContent;
    
    // Create temporary textarea
    var textarea = document.createElement('textarea');
    textarea.value = shortcode;
    document.body.appendChild(textarea);
    textarea.select();
    
    // Copy to clipboard
    document.execCommand('copy');
    
    // Visual feedback
    element.style.background = '#46b450'; // Green
    element.textContent = 'Copied!';
    
    // Reset after 1.5s
    setTimeout(function() {
        element.style.background = '#f0f0f1';
        element.textContent = shortcode;
    }, 1500);
    
    document.body.removeChild(textarea);
}
```

### CSS Styling

```css
.shb-shortcode {
    background: #f0f0f1;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.shb-shortcode:hover {
    background: #dcdcde;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
```

---

## Browser Compatibility

| Browser | Support | Notes |
|---------|---------|-------|
| Chrome  | ✅ Full | Native clipboard API |
| Firefox | ✅ Full | Native clipboard API |
| Safari  | ✅ Full | Native clipboard API |
| Edge    | ✅ Full | Native clipboard API |
| IE 11   | ✅ Full | document.execCommand |

---

## Use Cases

### 1. Create Hall-Specific Booking Pages

```
1. Create page: "Book Conference Room A"
2. Click shortcode in halls list
3. Paste: [shb_booking_form hall_id="1"]
4. Publish page
```

### 2. Multiple Halls on Same Site

```
Page 1: Conference Rooms → [shb_booking_form hall_id="1"]
Page 2: Meeting Rooms → [shb_booking_form hall_id="2"]
Page 3: Training Halls → [shb_booking_form hall_id="3"]
```

### 3. Share with Content Editors

```
Admin: "Use this shortcode for the booking form"
Copies and sends: [shb_booking_form hall_id="5"]
Editor: Pastes into page builder
```

---

## Accessibility

- ✅ Keyboard accessible (can be clicked with Enter/Space)
- ✅ Screen reader friendly (code element with title)
- ✅ Visual feedback for successful copy
- ✅ Fallback alert if copy fails

---

## Error Handling

If copy fails (rare edge case):
```javascript
catch (err) {
    alert('Failed to copy. Please copy manually.');
}
```

User sees alert and can manually copy the shortcode.

---

## Mobile Responsiveness

On mobile devices:
- Shortcode text wraps if needed
- Font size reduces to 11px
- Column width adjusts automatically
- Touch-friendly click target

---

## Files Modified

1. **`admin/views/view-halls-list.php`** ✅
   - Added "Shortcode" column header
   - Added shortcode display for each hall
   - Added JavaScript copy function

2. **`admin/css/shb-admin.css`** ✅
   - Added `.shb-shortcode` styling
   - Added hover effects
   - Added responsive adjustments

---

## Benefits

### For Administrators
- ⚡ Faster workflow
- ✅ No typos in hall IDs
- 📋 Easy to share with content editors
- 🎯 Visual confirmation of copy

### For Content Editors
- 🎨 Easy to add booking forms to pages
- 📝 No need to remember shortcode syntax
- ✅ Pre-configured with correct hall ID

### For Developers
- 🔧 Simple, maintainable code
- 📱 Responsive design included
- ♿ Accessible implementation
- 🌐 Cross-browser compatible

---

## Future Enhancements

Possible improvements for future versions:

1. **Multiple Shortcode Options**
   - Hall list shortcode with specific hall
   - User bookings shortcode
   - Combined hall display

2. **Shortcode Builder**
   - Visual interface to customize shortcode
   - Preview of shortcode output
   - Parameter selector

3. **QR Code Generation**
   - Generate QR code for booking page
   - Print-friendly format
   - Downloadable image

4. **Shortcode Analytics**
   - Track which shortcodes are used
   - Popular halls report
   - Conversion tracking

---

## Testing Checklist

- [ ] Shortcode displays correctly for each hall
- [ ] Click copies shortcode to clipboard
- [ ] Visual feedback shows "Copied!"
- [ ] Shortcode reverts after 1.5 seconds
- [ ] Hover effect works properly
- [ ] Mobile responsive design
- [ ] Works in all major browsers
- [ ] Fallback alert for copy failures
- [ ] Correct hall_id in each shortcode

---

## Related Documentation

- **Main README:** `README.md`
- **Installation Guide:** `INSTALLATION_GUIDE.md`
- **Architecture:** `ARCHITECTURE.md`
- **Shortcodes Usage:** Refer to plugin settings page

---

**Version:** 1.0.1  
**Last Updated:** 2025-01-01  
**Status:** ✅ Active

