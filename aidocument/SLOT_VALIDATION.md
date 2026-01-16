# Slot Validation Features

## Overview

The plugin now includes comprehensive validation for time slots to prevent overlapping times and enforce the Full Day slot restriction. Validation is implemented at both **frontend (JavaScript)** and **backend (PHP)** levels for maximum data integrity.

## Features Implemented

### 1. **Prevent Overlapping Time Slots**

**Frontend Validation:**
- Real-time validation when adding/editing slots
- Checks if new slot times overlap with existing slots
- Shows clear error message indicating which slot is overlapping
- Prevents form submission until conflict is resolved

**Backend Validation:**
- Server-side validation in `SHB_DB::check_slot_time_overlap()`
- Double-checks overlap even if frontend validation is bypassed
- Uses precise time comparison algorithm
- Returns detailed error message with conflicting slot information

**Example Scenario:**
```
Existing Slot: Morning Session (9:00 AM - 12:00 PM)
Attempting to Add: Workshop (11:00 AM - 2:00 PM)
Result: ❌ Error - "Time slot overlaps with existing slot: Morning Session (9:00 AM - 12:00 PM)"
```

### 2. **Full Day Slot Restriction**

**Frontend Validation:**
- Automatically hides "Full Day" option in dropdown when hall already has a Full Day slot
- Shows informational message explaining why option is hidden
- Prevents accidental attempts to create duplicate Full Day slots

**Backend Validation:**
- Server-side check in `SHB_DB::hall_has_full_day_slot()`
- Validates that only one Full Day slot exists per hall
- Returns clear error message if restriction violated

**Example Scenario:**
```
Hall: Main Conference Hall
Existing Slot: Full Day Booking (9:00 AM - 6:00 PM) [Type: Full Day]
Attempting to Add: Another Full Day slot
Result: ❌ Error - "This hall already has a Full Day slot. Only one Full Day slot is allowed per hall."
```

### 3. **Time Validation**

**Validations:**
- End time must be after start time
- Time format validation (HH:MM)
- Prevents invalid time ranges

**Example Scenario:**
```
Start Time: 2:00 PM
End Time: 1:00 PM
Result: ❌ Error - "End time must be after start time."
```

## Technical Implementation

### Database Layer (`class-shb-db.php`)

#### New Methods Added:

1. **`hall_has_full_day_slot( $hall_id, $exclude_slot_id )`**
   - Checks if a hall has a Full Day slot
   - Excludes specific slot ID (useful when editing)
   - Returns: boolean

2. **`check_slot_time_overlap( $hall_id, $start_time, $end_time, $exclude_slot_id )`**
   - Checks for time overlaps with existing slots
   - Uses overlap formula: (StartA < EndB) AND (EndA > StartB)
   - Returns: false or overlapping slot object

3. **`validate_slot_data( $data, $slot_id )`**
   - Comprehensive validation method
   - Checks: hall exists, time format, time range, full day restriction, overlaps
   - Returns: array with 'valid' (bool) and 'message' (string)

### Admin Handler (`class-shb-admin.php`)

#### Updated Method:

**`handle_save_slot()`**
- Calls `validate_slot_data()` before saving
- If validation fails, redirects with error message
- Error message passed via URL parameter
- Preserves form data context (returns to hall edit page)

### Admin View (`view-hall-edit.php`)

#### UI Enhancements:

1. **Error Display Area**
   - Red error box at top of slot form
   - Auto-populated from URL parameter (server-side errors)
   - JavaScript-populated for client-side validation
   - Animated slide-down effect

2. **Conditional Full Day Option**
   ```php
   <?php if ( ! $has_full_day_slot ) : ?>
       <option value="full_day">Full Day</option>
   <?php endif; ?>
   ```

3. **Informational Message**
   - Yellow info box explaining why Full Day option is hidden
   - Only shown when Full Day slot exists

#### JavaScript Validation:

**Features:**
- Form submission interception
- Real-time validation before server submission
- Overlap checking against existing slots array
- Time comparison using JavaScript Date objects
- Formatted error messages
- Scroll to error on validation failure
- Auto-dismiss success messages

**Validation Flow:**
```javascript
1. User submits form
2. Validate required fields (label, times)
3. Validate time range (end > start)
4. Check for overlaps with existing slots
5. Check Full Day restriction
6. If errors: Show error box, prevent submission
7. If valid: Allow form submission to server
```

### Admin List View (`view-halls-list.php`)

#### Error Handling:
- Displays slot validation errors
- Error vs success message styling
- URL-encoded error message support

## User Experience Flow

### Adding a New Slot (Success)

1. Admin clicks "Add Slot" button
2. Modal opens with form
3. Admin fills in:
   - Slot Type: Partial
   - Label: "Afternoon Session"
   - Start Time: 1:00 PM
   - End Time: 4:00 PM
4. Admin clicks "Save Slot"
5. ✅ JavaScript validation passes
6. ✅ Server validation passes
7. ✅ Slot saved successfully
8. Page reloads with success message

### Adding an Overlapping Slot (Error - Frontend)

1. Admin clicks "Add Slot" button
2. Admin fills in:
   - Slot Type: Partial
   - Label: "Workshop"
   - Start Time: 3:00 PM (overlaps with existing 1:00 PM - 4:00 PM slot)
   - End Time: 6:00 PM
3. Admin clicks "Save Slot"
4. ❌ JavaScript catches overlap BEFORE server submission
5. Red error box appears: "Time slot overlaps with existing slot: Afternoon Session (1:00 PM - 4:00 PM)"
6. Form stays open, data preserved
7. Admin can correct times immediately

### Adding Second Full Day Slot (Error - Both Levels)

1. Admin clicks "Add Slot" button
2. Admin notices:
   - ⚠️ "Full Day" option is NOT in dropdown
   - ⚠️ Yellow info message: "Full Day option is hidden because this hall already has a Full Day slot..."
3. Admin cannot select Full Day (prevented at UI level)

**If someone bypasses frontend:**
4. Malicious attempt to POST Full Day type
5. ❌ Server validation catches it
6. Error message: "This hall already has a Full Day slot. Only one Full Day slot is allowed per hall."
7. Redirects back to hall edit page with error

### Editing an Existing Slot

1. Admin clicks "Edit" on a slot
2. Modal opens (TODO: implement edit functionality)
3. Validation excludes the current slot from overlap checks
4. Admin can change times without false positives

## Error Messages

### Client-Side (JavaScript)

```javascript
"Label is required."
"Start time and end time are required."
"End time must be after start time."
"Time slot overlaps with existing slot: [Slot Name] ([Start] - [End])"
"This hall already has a Full Day slot. Only one Full Day slot is allowed per hall."
```

### Server-Side (PHP)

```php
"Invalid hall selected."
"Invalid time format."
"End time must be after start time."
"This hall already has a Full Day slot. Only one Full Day slot is allowed per hall."
"Time slot overlaps with existing slot: [Slot Name] ([Start] - [End])"
```

## Testing Checklist

### Overlap Validation

- [ ] Add slot that overlaps at start (new start < existing end)
- [ ] Add slot that overlaps at end (new end > existing start)
- [ ] Add slot completely inside existing slot
- [ ] Add slot completely outside existing slot (should pass)
- [ ] Add slot with same start/end as existing (should fail)
- [ ] Edit slot to not overlap itself

### Full Day Validation

- [ ] Add Full Day slot to hall with no Full Day slot (should pass)
- [ ] Attempt to add second Full Day slot (should fail)
- [ ] Verify Full Day option hidden in UI when one exists
- [ ] Verify info message shown when Full Day option hidden
- [ ] Edit existing Full Day slot (should allow)

### Time Validation

- [ ] End time before start time (should fail)
- [ ] End time equal to start time (should fail)
- [ ] End time after start time (should pass)
- [ ] Invalid time format (should fail)

### Edge Cases

- [ ] Adding slot to hall with 0 existing slots
- [ ] Adding slot when browser JavaScript disabled (backend catches)
- [ ] Rapid form submissions
- [ ] Network error during submission
- [ ] Form data with special characters in labels

## Developer Notes

### Overlap Algorithm

Uses the standard interval overlap formula:
```
Two time intervals [A_start, A_end] and [B_start, B_end] overlap if:
(A_start < B_end) AND (A_end > B_start)
```

This catches all overlap scenarios:
- Partial overlap at start
- Partial overlap at end
- Complete containment (one inside another)
- Exact same times

### Future Enhancements

1. **Visual Timeline**
   - Show existing slots on a timeline
   - Drag-and-drop to adjust times
   - Visual indication of available time ranges

2. **Bulk Operations**
   - Import multiple slots from CSV
   - Copy slots from one hall to another
   - Batch time adjustment

3. **Advanced Warnings**
   - Show warning if slot is very close (< 30 min) to another
   - Suggest optimal slot times based on existing slots
   - Show hall occupancy percentage

4. **Edit Functionality**
   - Pre-populate form with slot data when editing
   - Allow inline editing in slots table
   - Drag-and-drop reordering

## Security Considerations

- ✅ All inputs sanitized (sanitize_text_field, absint)
- ✅ Nonce verification on form submission
- ✅ Capability checks (manage_options)
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS prevention (esc_html, esc_attr)
- ✅ Double validation (client + server)

## Performance

- Validation queries are optimized with indexes
- Overlap checking is O(n) where n = number of slots per hall
- Typical hall has 3-5 slots, so performance impact is negligible
- Could optimize with time-based indexing if halls have 20+ slots

## Localization

All error messages are translatable:
- Text domain: `simple-hall-booking-manager`
- Messages include context for translators
- Some messages use sprintf for variable insertion

---

**Last Updated:** 2025-01-01  
**Version:** 1.0.0

