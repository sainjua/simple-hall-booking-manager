# Slot Overlap Logic - Clarified

## Two Separate Concepts

### 1. Full Day Slots
- **Purpose**: Represents the entire day availability
- **Restriction**: Maximum **ONE** Full Day slot per hall
- **Overlap Check**: ❌ **NO time overlap checking**
- **Reason**: Full Day is a booking-time concept, not a slot-time concept

### 2. Partial Slots
- **Purpose**: Represents specific time periods (Morning, Afternoon, Evening)
- **Restriction**: No limit on number of partial slots
- **Overlap Check**: ✅ **YES - only checks against OTHER partial slots**
- **Reason**: Partial slots must not overlap with each other

## Visual Example

```
Hall: Conference Room A

✅ VALID Configuration:
┌─────────────────────────────────┐
│ Full Day (9 AM - 6 PM)          │ ← Full Day slot
└─────────────────────────────────┘

┌──────────┬──────────┬──────────┐
│ Morning  │ Afternoon│ Evening  │ ← Partial slots
│ 9-12     │ 12-4     │ 4-9      │
└──────────┴──────────┴──────────┘

Both can exist together! Conflict happens at BOOKING time:
- If someone books Full Day → All partial slots blocked
- If someone books any Partial → Full Day blocked
```

## Validation Rules

### Full Day Slots

| Check | Rule | Error Message |
|-------|------|---------------|
| ✅ Duplicate Full Day | Only 1 per hall | "This hall already has a Full Day slot" |
| ❌ Time Overlap | Not checked | N/A |
| ✅ Time Range | End > Start | "End time must be after start time" |

**Implementation:**
- Frontend: Hides "Full Day" option when one exists
- Backend: Checks `hall_has_full_day_slot()`

### Partial Slots

| Check | Rule | Error Message |
|-------|------|---------------|
| ❌ Duplicate Full Day | Not applicable | N/A |
| ✅ Time Overlap | Only with other partials | "Time slot overlaps with existing partial slot: [Name]" |
| ✅ Time Range | End > Start | "End time must be after start time" |

**Implementation:**
- Frontend: Checks overlap with `slot_type === 'partial'` slots only
- Backend: `check_slot_time_overlap()` filters by `slot_type => 'partial'`

## Code Logic

### Backend (PHP)

```php
// In check_slot_time_overlap()
public function check_slot_time_overlap( $hall_id, $start_time, $end_time, $slot_type = 'partial', $exclude_slot_id = 0 ) {
    // Full Day slots don't need time overlap checking
    if ( 'full_day' === $slot_type ) {
        return false; // ← No overlap check for Full Day
    }

    // Get only PARTIAL slots for overlap checking
    $slots = $this->get_slots_by_hall( $hall_id, array( 'slot_type' => 'partial' ) );
    
    // Check overlaps...
}
```

### Frontend (JavaScript)

```javascript
// Check for overlaps ONLY for partial slots
if (slotType === 'partial') {
    for (var i = 0; i < existingSlots.length; i++) {
        var existingSlot = existingSlots[i];
        
        // Only check overlap with other PARTIAL slots
        if (existingSlot.slot_type !== 'partial') {
            continue; // ← Skip Full Day slots
        }

        // Check overlap...
    }
}
```

## Examples

### ✅ Valid Scenarios

#### 1. Full Day + Multiple Partials
```
✓ Full Day: 9 AM - 6 PM
✓ Partial Morning: 9 AM - 12 PM
✓ Partial Afternoon: 12 PM - 4 PM
✓ Partial Evening: 4 PM - 9 PM

Result: All slots can coexist
Why: Conflict resolved at BOOKING time, not SLOT time
```

#### 2. No Overlapping Partials
```
✓ Partial Morning: 9 AM - 12 PM
✓ Partial Afternoon: 12 PM - 4 PM
✓ Partial Evening: 4 PM - 9 PM

Result: All slots valid
Why: No time overlap between partial slots
```

#### 3. Gap Between Partials
```
✓ Partial Morning: 9 AM - 11 AM
✓ Partial Afternoon: 2 PM - 5 PM

Result: Valid even with gap
Why: Gaps are allowed
```

### ❌ Invalid Scenarios

#### 1. Two Full Day Slots
```
✗ Full Day #1: 9 AM - 6 PM
✗ Full Day #2: 8 AM - 8 PM

Result: ERROR
Message: "This hall already has a Full Day slot"
```

#### 2. Overlapping Partial Slots
```
✓ Partial Morning: 9 AM - 12 PM
✗ Partial Workshop: 11 AM - 2 PM (overlaps Morning)

Result: ERROR
Message: "Time slot overlaps with existing partial slot: Morning (9:00 AM - 12:00 PM)"
```

#### 3. Partial Completely Inside Another
```
✓ Partial Day Session: 9 AM - 5 PM
✗ Partial Lunch Break: 12 PM - 1 PM (inside Day Session)

Result: ERROR
Message: "Time slot overlaps with existing partial slot: Day Session (9:00 AM - 5:00 PM)"
```

## Booking Time Conflict Resolution

At booking time, the availability logic works as follows:

```php
// From get_available_slots() in class-shb-db.php

$has_full_day = false;
foreach ( $bookings as $booking ) {
    $slot = $this->get_slot( $booking->slot_id );
    if ( $slot && 'full_day' === $slot->slot_type ) {
        $has_full_day = true;
    }
}

// If there's a full_day booking, no partial slots are available
if ( $has_full_day && 'partial' === $slot->slot_type ) {
    continue; // Skip this partial slot
}

// If there are partial bookings and this is full_day, it's not available
if ( ! empty( $booked_slot_ids ) && 'full_day' === $slot->slot_type ) {
    continue; // Skip full day slot
}
```

## Why This Design?

### Flexibility for Administrators
- Can set up both Full Day and Partial slots upfront
- Guests see what's available based on what's already booked
- No need to delete/recreate slots based on booking type

### Clear User Experience
- Full Day option appears when no partial slots are booked
- Partial slots appear when Full Day is not booked
- Real-time availability reflects actual conflicts

### Database Efficiency
- All possible slot configurations stored once
- No dynamic slot creation/deletion
- Availability calculated on-the-fly from bookings

## Migration Note

If you had the old validation logic that checked Full Day vs Partial overlaps:
1. ✅ Existing data is not affected
2. ✅ Old slots remain valid
3. ✅ New logic is more permissive (allows more configurations)
4. ✅ No breaking changes

## Summary

| Aspect | Full Day | Partial |
|--------|----------|---------|
| **Max per hall** | 1 | Unlimited |
| **Checks overlap with Full Day?** | No | No |
| **Checks overlap with Partial?** | No | Yes |
| **When conflict matters** | At booking time | At booking time |
| **UI behavior** | Option hidden when exists | Always visible |

**Remember**: Slot creation is permissive. Conflict checking happens when guests make bookings!

---

**Updated:** 2025-01-01  
**Version:** 1.0.1

