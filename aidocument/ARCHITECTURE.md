# Architecture Documentation

## Admin Views
- **Halls:** List and edit halls.
- **Bookings:** List and manage bookings.
- **Calendar:** Visual calendar view of bookings using FullCalendar.
- **Settings:** Plugin settings.

## Core Components
- **SHB_Admin:** Handles admin menu, asset enqueuing, and main CRUD actions (Save Hall, Delete Slot, etc.).
- **SHB_AJAX:** (Enhanced) Handles all AJAX requests, including:
    - `shb_get_slot`: Fetches slot search/data and returns a pre-rendered HTML form partial.
    - `shb_check_slot_overlap`: Real-time validation for time conflicts during slot creation/editing.
- **SHB_DB:** Wraps all database interactions. Includes complex logic for:
    - Conflict detection (Partial vs Full Day slots).
    - Cleaning buffer enforcement.
    - Slot availability checking.

## Slot Management System
The slot management has been refactored to use an AJAX-driven modal system:
1. **Partial Template:** The slot form is centralized in `admin/views/partials/_slot-form.php`.
2. **AJAX Loading:** Forms are dynamically loaded via `window.shb_get_slot()` to ensure the UI state (Add vs Edit) is handled server-side.
3. **Real-time Validation:** Users can check for time overlaps using the "Check Overlap" feature before committing changes.
4. **Notification System:** The admin UI uses standard WordPress notices (`.notice-success`, `.notice-error`) with auto-dismissal for clear feedback on slot operations.
