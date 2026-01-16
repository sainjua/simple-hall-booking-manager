# Multi-Day Booking Feature - Implementation Guide

## Overview

This document describes the complete implementation of multi-day booking functionality for the Simple Hall Booking Manager plugin.

**Version:** 1.1.0  
**Status:** Architecture Defined - Ready for Implementation  
**Date:** 2025-01-01

---

## Feature Summary

### What It Does

- Allows users to book the same hall and slot for multiple dates in one booking
- Supports both consecutive dates (Jan 15-17) and non-consecutive dates (Jan 15, 20, 25)
- Each date is validated independently for availability
- All dates linked to single booking for easy management

### Why This Feature

**User Benefits:**
- Book weekly classes or events in one transaction
- Book multiple workshop days together
- Easier management of recurring events

**Admin Benefits:**
- See full booking context (all dates together)
- Single confirmation/cancellation for entire event
- Better reporting and analytics

---

## Database Changes

### 1. Modify Existing Table: `wp_shb_bookings`

**Add New Column:**

```sql
ALTER TABLE `wp_shb_bookings` 
ADD COLUMN `booking_type` ENUM('single','multiday') NOT NULL DEFAULT 'single' 
AFTER `slot_id`;
```

**Column Details:**
- **Name:** `booking_type`
- **Type:** ENUM('single', 'multiday')
- **Default:** 'single'
- **Null:** NOT NULL
- **Position:** After `slot_id` column

**Purpose:**
- Identifies if booking is for single day or multiple days
- Determines which table to query for dates

**Backward Compatibility:**
- Existing bookings automatically get `booking_type = 'single'`
- `booking_date` column remains for single-day bookings
- No breaking changes to existing functionality

---

### 2. Create New Table: `wp_shb_booking_dates`

**Table Structure:**

```sql
CREATE TABLE `wp_shb_booking_dates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `slot_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `booking_date` (`booking_date`),
  KEY `slot_id` (`slot_id`),
  CONSTRAINT `fk_booking_dates_booking` 
    FOREIGN KEY (`booking_id`) 
    REFERENCES `wp_shb_bookings` (`id`) 
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Column Details:**

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key, auto-increment |
| `booking_id` | BIGINT | Foreign key to `shb_bookings.id` |
| `booking_date` | DATE | Individual booking date |
| `slot_id` | BIGINT | Slot ID for this date (can vary per date) |
| `created_at` | DATETIME | When this date was added |

**Indexes:**
- Primary key on `id`
- Index on `booking_id` (for fast lookup of all dates)
- Index on `booking_date` (for availability checking)
- Index on `slot_id` (for slot queries)

**Foreign Key:**
- `booking_id` references `shb_bookings(id)`
- **ON DELETE CASCADE:** If booking deleted, all dates deleted automatically
- **ON UPDATE CASCADE:** If booking ID changes, all dates updated

---

## Complete Database Schema (Updated)

### wp_shb_bookings (Modified)

```sql
CREATE TABLE `wp_shb_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hall_id` bigint(20) UNSIGNED NOT NULL,
  `slot_id` bigint(20) UNSIGNED NOT NULL,
  `booking_type` enum('single','multiday') NOT NULL DEFAULT 'single',
  `booking_date` date DEFAULT NULL COMMENT 'Used for single bookings only',
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `event_purpose` varchar(255) DEFAULT NULL,
  `attendees_count` int(11) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `access_token` varchar(64) NOT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_token` (`access_token`),
  KEY `hall_id` (`hall_id`),
  KEY `slot_id` (`slot_id`),
  KEY `booking_date` (`booking_date`),
  KEY `status` (`status`),
  KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### wp_shb_booking_dates (New)

See "Create New Table" section above.

---

## Data Relationships

### Entity Relationship Diagram

```
┌─────────────────────┐
│   shb_bookings      │
├─────────────────────┤
│ id (PK)             │
│ hall_id (FK)        │
│ slot_id (FK)        │
│ booking_type        │◄───────┐
│ booking_date        │        │ 1:Many
│ customer_name       │        │
│ customer_email      │        │
│ status              │        │
│ access_token        │        │
└─────────────────────┘        │
                               │
                       ┌───────┴──────────────┐
                       │  shb_booking_dates   │
                       ├──────────────────────┤
                       │ id (PK)              │
                       │ booking_id (FK)      │
                       │ booking_date         │
                       │ slot_id (FK)         │
                       │ created_at           │
                       └──────────────────────┘
```

### Relationship Rules

1. **One-to-Many:** One booking → Many booking dates
2. **Conditional:** Only used when `booking_type = 'multiday'`
3. **Cascade Delete:** Delete booking → Delete all dates
4. **All-or-Nothing:** Cannot have booking without dates (for multiday)

---

## Code Changes Required

### 1. Update Activator (Database Setup)

**File:** `includes/class-shb-activator.php`

**Changes:**
- Add `booking_type` column to `shb_bookings` table creation
- Create new `shb_booking_dates` table
- Add migration check for existing installations

**New Method:**
```php
private static function upgrade_database_to_v110() {
    // Add booking_type column if not exists
    // Create booking_dates table if not exists
}
```

---

### 2. Update SHB_DB Class

**File:** `includes/class-shb-db.php`

**New Methods:**

```php
// Table name getter
public function get_table_booking_dates() {
    return $this->wpdb->prefix . 'shb_booking_dates';
}

// Create multiday booking with dates
public function create_multiday_booking( $booking_data, $dates_array ) {
    // 1. Validate all dates are available
    // 2. Insert into shb_bookings with booking_type='multiday'
    // 3. Insert each date into shb_booking_dates
    // 4. Return booking ID or false
}

// Get all dates for a booking
public function get_booking_dates( $booking_id ) {
    // Query shb_booking_dates WHERE booking_id
    // Return array of date objects
}

// Insert single booking date
public function insert_booking_date( $data ) {
    // Insert into shb_booking_dates
    // Return insert ID
}

// Delete all dates for a booking
public function delete_booking_dates( $booking_id ) {
    // DELETE FROM shb_booking_dates WHERE booking_id
}

// Check if multiple dates are available
public function check_multiday_availability( $hall_id, $slot_id, $dates_array ) {
    // Loop through each date
    // Check is_slot_available() for each
    // Return array of unavailable dates or true if all available
}

// Update existing get_available_slots to check booking_dates table
public function get_available_slots( $hall_id, $date ) {
    // Modified to also check shb_booking_dates
    // Account for multiday bookings
}

// Override get_bookings to include date info
public function get_bookings( $args = array() ) {
    // If booking_type = 'multiday', join with booking_dates
    // Add date_from and date_to for multiday
}
```

---

### 3. Update AJAX Handler

**File:** `includes/class-shb-ajax.php`

**Modified Method:**

```php
public function submit_booking() {
    // Check if booking_dates[] array exists in POST
    // If exists: Call create_multiday_booking()
    // If not: Use existing single-day logic
    
    // Validation:
    // - All dates must be in future
    // - All dates must be available
    // - Maximum 30 days per booking (configurable)
}
```

**New AJAX Action:**

```php
public function check_multiday_availability() {
    // action: shb_check_multiday_availability
    // Accepts: hall_id, slot_id, dates[]
    // Returns: { available: true/false, unavailable_dates: [] }
}
```

---

### 4. Update Frontend Form

**File:** `public/partials/booking-form.php`

**Changes:**
- Add checkbox: "Book for multiple days?"
- When checked, show date picker that allows multiple date selection
- Hidden field: `booking_dates[]` (array of dates)
- Update validation to handle array of dates

**HTML Addition:**
```html
<div class="shb-booking-type">
    <label>
        <input type="checkbox" id="shb_multiday_toggle" name="multiday_toggle">
        Book for multiple days?
    </label>
</div>

<div id="shb-multiday-dates" style="display:none;">
    <label>Select Dates:</label>
    <!-- Multi-select date picker -->
    <div id="shb-multiday-calendar"></div>
    <input type="hidden" name="booking_dates[]" class="shb-selected-dates">
</div>

<div id="shb-single-date">
    <!-- Existing single date picker -->
    <input type="date" name="booking_date" id="shb_booking_date">
</div>
```

---

### 5. Update Frontend JavaScript

**File:** `public/js/shb-frontend.js`

**New Functionality:**

```javascript
// Toggle between single/multiday
jQuery('#shb_multiday_toggle').on('change', function() {
    if (this.checked) {
        jQuery('#shb-single-date').hide();
        jQuery('#shb-multiday-dates').show();
    } else {
        jQuery('#shb-single-date').show();
        jQuery('#shb-multiday-dates').hide();
    }
});

// Multiday date selection
SHB.initMultidayCalendar = function() {
    // Initialize date picker with multi-select
    // Can use: jQuery UI Datepicker, Flatpickr, or custom
    // Store selected dates in hidden field as JSON array
};

// Check multiday availability before submission
SHB.checkMultidayAvailability = function(hallId, slotId, dates) {
    return jQuery.ajax({
        url: shbFrontend.ajaxUrl,
        method: 'POST',
        data: {
            action: 'shb_check_multiday_availability',
            nonce: shbFrontend.nonce,
            hall_id: hallId,
            slot_id: slotId,
            dates: dates
        }
    });
};
```

---

### 6. Update Admin Views

**File:** `admin/views/view-bookings-list.php`

**Changes:**
- Display "Multi-Day" badge for multiday bookings
- Show date range instead of single date
- Add column: "Dates" showing count (e.g., "3 days")

**File:** `admin/views/view-booking-edit.php`

**Changes:**
- Show all dates in list format
- Display dates as read-only (no edit for now)
- Add section: "Booking Dates" with table

**Example Display:**
```
Booking Type: Multi-Day
Dates:
- January 15, 2025 - Morning Slot
- January 16, 2025 - Morning Slot
- January 17, 2025 - Morning Slot
Total: 3 days
```

---

### 7. Update Email Templates

**File:** `includes/class-shb-emails.php`

**Changes:**
- Detect booking type
- For multiday: List all dates in email
- Update subject line: "Your booking for [dates]"

**Template Variables:**
```php
{dates_list}  // All dates formatted
{date_count}  // Number of days
{date_range}  // "Jan 15 - Jan 17, 2025"
```

---

## Migration Strategy

### For Existing Installations

**Step 1: Detect Version**
```php
$db_version = get_option( 'shb_db_version', '1.0.0' );
if ( version_compare( $db_version, '1.1.0', '<' ) ) {
    // Run migration
}
```

**Step 2: Add Column**
```php
$wpdb->query( "
    ALTER TABLE {$wpdb->prefix}shb_bookings 
    ADD COLUMN booking_type ENUM('single','multiday') NOT NULL DEFAULT 'single' 
    AFTER slot_id
" );
```

**Step 3: Create New Table**
```php
// Use dbDelta to create wp_shb_booking_dates
```

**Step 4: Update Version**
```php
update_option( 'shb_db_version', '1.1.0' );
```

**Step 5: Verify**
- Check existing bookings still work
- All existing bookings have `booking_type = 'single'`
- No data loss

---

## Testing Checklist

### Database Tests

- [ ] New column `booking_type` added to `shb_bookings`
- [ ] Default value is 'single' for new rows
- [ ] New table `shb_booking_dates` created
- [ ] Foreign key constraint works (delete cascade)
- [ ] Indexes created correctly

### Functionality Tests

**Single-Day Booking (Backward Compatibility):**
- [ ] Can create single-day booking as before
- [ ] Uses `booking_date` field
- [ ] No records in `booking_dates` table
- [ ] Email sends correctly
- [ ] Admin sees booking correctly

**Multi-Day Booking:**
- [ ] Can select multiple dates in frontend
- [ ] Form validates all dates
- [ ] AJAX checks availability for all dates
- [ ] Creates booking with `booking_type='multiday'`
- [ ] Creates records in `booking_dates` table
- [ ] Email lists all dates
- [ ] Admin sees all dates

**Availability:**
- [ ] Multiday booking blocks all selected dates
- [ ] Cannot book already-booked date
- [ ] Partial dates don't block full-day
- [ ] Full-day blocks all partials
- [ ] Cleaning buffer applies between different bookings

**Cancellation:**
- [ ] Cancel multiday booking removes all dates
- [ ] Cascade delete works
- [ ] Dates become available again
- [ ] Email sent with all dates

**Admin:**
- [ ] List shows multiday bookings clearly
- [ ] Edit page shows all dates
- [ ] Can confirm/cancel multiday booking
- [ ] Search works for multiday bookings
- [ ] Reports include multiday bookings

**Edge Cases:**
- [ ] 1 date available, 1 not: Booking fails
- [ ] Dates not in order: Works correctly
- [ ] Non-consecutive dates: Works correctly
- [ ] Maximum dates limit enforced
- [ ] Dates in past: Validation error

---

## Configuration Options

### Add to Settings

**File:** `admin/views/view-settings.php`

**New Settings:**

```php
// Maximum days per multiday booking
shb_max_multiday_dates (default: 30)

// Enable/disable multiday booking
shb_enable_multiday (default: true)

// Minimum days for multiday (prevent single day via multiday)
shb_min_multiday_dates (default: 2)
```

---

## UI/UX Enhancements

### Frontend Improvements

1. **Visual Calendar:**
   - Use interactive calendar for date selection
   - Highlight unavailable dates in red
   - Show selected dates in green
   - Display price calculation for total days

2. **Date Summary:**
   ```
   Selected Dates:
   ✓ January 15, 2025
   ✓ January 16, 2025
   ✓ January 17, 2025
   Total: 3 days
   ```

3. **Availability Feedback:**
   - Real-time check as dates selected
   - Show "✓ Available" or "✗ Not Available" per date
   - Disable submit until all dates available

### Admin Improvements

1. **Calendar View:**
   - Show multiday bookings as spanning bars
   - Different color for single vs multiday
   - Hover to see details

2. **Quick Actions:**
   - "Duplicate to other dates" button
   - "Extend booking" option
   - "Split multiday booking" (future)

---

## API for Developers

### Actions (Hooks)

```php
// Before multiday booking created
do_action( 'shb_before_create_multiday_booking', $booking_data, $dates_array );

// After multiday booking created
do_action( 'shb_after_create_multiday_booking', $booking_id, $booking_data, $dates_array );

// Before booking dates inserted
do_action( 'shb_before_insert_booking_dates', $booking_id, $dates_array );

// After booking dates inserted
do_action( 'shb_after_insert_booking_dates', $booking_id, $date_ids );
```

### Filters

```php
// Modify max multiday dates
apply_filters( 'shb_max_multiday_dates', 30 );

// Modify availability check for multiday
apply_filters( 'shb_multiday_availability_check', $is_available, $hall_id, $slot_id, $dates_array );

// Modify booking dates before save
apply_filters( 'shb_before_save_booking_dates', $dates_array, $booking_id );
```

---

## Security Considerations

### Validation

1. **Date Validation:**
   - All dates must be valid format (Y-m-d)
   - All dates must be in future
   - No duplicate dates in array
   - Maximum dates limit enforced

2. **Availability:**
   - Re-check availability on submission
   - Race condition handling (lock table if needed)
   - Prevent booking unavailable dates

3. **Permissions:**
   - Nonce validation for AJAX
   - Sanitize all inputs
   - Escape all outputs
   - Prepared statements for queries

---

## Performance Optimization

### Database Optimization

1. **Indexes:**
   - Index on `booking_id` for fast joins
   - Index on `booking_date` for availability checks
   - Composite index on `(booking_id, booking_date)` for queries

2. **Queries:**
   - Use single query to get all dates (not loop)
   - Cache availability results
   - Use transactions for multiday inserts

3. **Cleanup:**
   - Automatic cleanup of orphaned date records
   - Archive old bookings
   - Optimize tables periodically

### Frontend Optimization

1. **AJAX:**
   - Debounce availability checks
   - Batch date checks in single request
   - Cache slot information

2. **Calendar:**
   - Lazy load unavailable dates
   - Virtual scrolling for date picker
   - Lightweight date library

---

## Documentation Updates Required

### Files to Update:

1. ✅ **ARCHITECTURE.md** - Already updated
   - Database schema section
   - Multi-day booking logic section
   - SHB_DB methods

2. **README.md**
   - Add multiday booking to features
   - Update version number
   - Add usage examples

3. **readme.txt** (WordPress.org)
   - Changelog for v1.1.0
   - Feature list update
   - Screenshots of multiday booking

4. **User Guide** (New)
   - How to make multiday booking
   - Admin management guide
   - Troubleshooting

---

## Timeline Estimate

### Phase 1: Database & Backend (2-3 hours)
- [ ] Update activator with new table
- [ ] Add migration logic
- [ ] Update SHB_DB class with new methods
- [ ] Test database operations

### Phase 2: Availability Logic (2 hours)
- [ ] Update availability checking
- [ ] Add multiday validation
- [ ] Test edge cases

### Phase 3: AJAX & API (1-2 hours)
- [ ] Update submit_booking handler
- [ ] Add check_multiday_availability action
- [ ] Test AJAX calls

### Phase 4: Frontend Form (3-4 hours)
- [ ] Add multiday toggle
- [ ] Implement date picker
- [ ] Update JavaScript
- [ ] Style new elements

### Phase 5: Admin Views (2-3 hours)
- [ ] Update bookings list
- [ ] Update booking edit page
- [ ] Add date display
- [ ] Test admin workflow

### Phase 6: Emails & Notifications (1 hour)
- [ ] Update email templates
- [ ] Add date list to emails
- [ ] Test email sending

### Phase 7: Testing & QA (2-3 hours)
- [ ] Full test suite
- [ ] Edge case testing
- [ ] User acceptance testing
- [ ] Bug fixes

### Phase 8: Documentation (1-2 hours)
- [ ] Update all docs
- [ ] Create user guide
- [ ] Write changelog

**Total Estimated Time:** 14-20 hours

---

## Rollout Plan

### Version 1.1.0 Release

**Pre-Release:**
1. Backup instructions for users
2. Beta testing with select users
3. Compatibility testing

**Release:**
1. Push to repository
2. Announce on WordPress.org
3. Email existing users
4. Update documentation site

**Post-Release:**
1. Monitor for issues
2. Quick patch if critical bugs
3. Gather feedback
4. Plan v1.2.0 improvements

---

## Future Enhancements (v1.2.0+)

### Advanced Features

1. **Partial Date Cancellation:**
   - Cancel individual dates within multiday booking
   - Partial refunds
   - Rebook cancelled dates

2. **Different Slots Per Date:**
   - Morning on Day 1, Evening on Day 2
   - Mixed Full Day / Partial
   - Custom per-date configuration

3. **Recurring Bookings:**
   - Weekly recurrence
   - Monthly recurrence
   - Custom patterns

4. **Pricing Integration:**
   - Bulk discount for multiday
   - Different prices per date
   - Dynamic pricing

5. **Calendar Sync:**
   - Export to Google Calendar
   - iCal feed
   - Outlook integration

---

## Support & Troubleshooting

### Common Issues

**Q: Existing bookings disappeared after update?**  
A: Run migration manually or restore from backup and contact support.

**Q: Cannot create multiday booking?**  
A: Check all dates are available individually first.

**Q: Foreign key error?**  
A: Check InnoDB engine enabled on database.

**Q: Dates not showing in admin?**  
A: Clear cache and check database for booking_dates records.

---

## Conclusion

This implementation provides a solid foundation for multi-day booking functionality while maintaining backward compatibility and following WordPress best practices.

**Key Benefits:**
- ✅ Flexible date selection
- ✅ Robust validation
- ✅ Backward compatible
- ✅ Scalable architecture
- ✅ Easy to extend

**Next Steps:**
1. Review this document with team
2. Create implementation tasks
3. Set up development environment
4. Begin Phase 1 implementation

---

**Document Version:** 1.0  
**Last Updated:** 2025-01-01  
**Author:** Development Team  
**Status:** Ready for Implementation

