# Simple Hall Booking Manager - Complete System Documentation

**Version:** 1.4.0  
**Last Updated:** January 20, 2026  
**Document Type:** Master Technical Documentation

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture & Components](#architecture--components)
3. [Database Schema & Data Flow](#database-schema--data-flow)
4. [Client-Side Workflows](#client-side-workflows)
5. [Admin-Side Workflows](#admin-side-workflows)
6. [Booking Lifecycle](#booking-lifecycle)
7. [Email Notification System](#email-notification-system)
8. [Security & Validation](#security--validation)
9. [Multi-Day Booking System](#multi-day-booking-system)
10. [PIN Access System](#pin-access-system)

---

## System Overview

### Purpose
A WordPress plugin that enables hall/venue booking management with guest access (no login required), smart conflict prevention, and comprehensive admin controls.

### Key Capabilities
- **Guest Booking**: Users can book without WordPress accounts
- **Smart Scheduling**: Prevents conflicts between full-day and partial slots
- **Multi-Day Bookings**: Support for consecutive day bookings
- **PIN Access**: Secure 6-character PIN for booking management
- **Email Notifications**: Automated emails for all status changes
- **Organization Tracking**: Optional field to track company/organization bookings

### User Roles
1. **Guest Users** (Frontend)
   - Browse available halls
   - Check availability
   - Submit booking requests
   - Manage their bookings via PIN/token

2. **Administrators** (Backend)
   - Manage halls and time slots
   - Review and approve bookings
   - Configure system settings
   - Send notifications

---

## Architecture & Components

### Plugin Structure

```
┌─────────────────────────────────────────────────────────┐
│                    WordPress Core                        │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│              Simple Hall Booking Manager                 │
│                                                          │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐       │
│  │  Frontend  │  │   Admin    │  │   AJAX     │       │
│  │  (Public)  │  │  (Backend) │  │  Handler   │       │
│  └────────────┘  └────────────┘  └────────────┘       │
│         │               │                │              │
│         └───────────────┴────────────────┘              │
│                         │                                │
│                         ▼                                │
│         ┌───────────────────────────────┐               │
│         │      Database Layer           │               │
│         │  - Halls                      │               │
│         │  - Slots                      │               │
│         │  - Bookings                   │               │
│         │  - Booking Dates              │               │
│         └───────────────────────────────┘               │
│                         │                                │
│                         ▼                                │
│         ┌───────────────────────────────┐               │
│         │      Email System             │               │
│         │  - Admin Notifications        │               │
│         │  - Guest Confirmations        │               │
│         └───────────────────────────────┘               │
└─────────────────────────────────────────────────────────┘
```

### Core Components

#### 1. **Frontend System** (`public/`)
- **Booking Form**: Interactive form with real-time availability
- **Hall List**: Display of available venues
- **User Booking Management**: PIN-based access to bookings
- **Calendar Interface**: Visual date selection

#### 2. **Admin System** (`admin/`)
- **Halls Management**: CRUD operations for venues
- **Slots Management**: Time slot configuration
- **Bookings Dashboard**: Review and manage requests
- **Settings Panel**: Email and system configuration
- **Calendar View**: Visual booking overview

#### 3. **AJAX Handler** (`includes/class-shb-ajax.php`)
- Real-time availability checking
- Booking submission processing
- Slot overlap validation
- Multi-day availability verification

#### 4. **Database Layer** (`includes/class-shb-db.php`)
- Data persistence
- Query optimization
- Migration management
- Conflict detection

#### 5. **Email System** (`includes/class-shb-emails.php`)
- Template rendering
- Status-based notifications
- Admin alerts
- Guest communications

---

## Database Schema & Data Flow

### Tables Structure

#### 1. **wp_shb_halls**
Stores venue information.

**Fields:**
- `id` - Primary key
- `title` - Hall name
- `description` - Detailed description
- `capacity` - Maximum attendees
- `status` - active/inactive
- `cleaning_buffer` - Minutes between bookings
- `created_at` - Timestamp
- `updated_at` - Timestamp

**Purpose:** Central registry of all bookable venues.

---

#### 2. **wp_shb_slots**
Defines available time slots for each hall.

**Fields:**
- `id` - Primary key
- `hall_id` - Foreign key to halls
- `slot_type` - full_day/partial
- `label` - Display name (e.g., "Morning Session")
- `start_time` - Time (HH:MM:SS)
- `end_time` - Time (HH:MM:SS)
- `days_enabled` - JSON array [0-6] for days of week
- `is_active` - Boolean
- `sort_order` - Display order
- `created_at` - Timestamp

**Purpose:** Flexible scheduling configuration per hall.

**Slot Types:**
- **Full Day**: Blocks entire day (e.g., 9:00 AM - 6:00 PM)
- **Partial**: Specific time ranges (e.g., Morning: 9:00 AM - 12:00 PM)

**Conflict Rules:**
- One full-day booking = No partial slots available
- Any partial booking = Full-day slot unavailable

---

#### 3. **wp_shb_bookings**
Main booking records.

**Fields:**
- `id` - Primary key
- `hall_id` - Foreign key to halls
- `booking_type` - single/multiday
- `customer_name` - Required
- `customer_email` - Required
- `customer_phone` - Optional
- `customer_organization` - Optional (v1.4.0)
- `event_purpose` - Optional
- `attendees_count` - Optional
- `status` - pending/confirmed/cancelled
- `access_token` - 64-char unique token
- `pin` - 6-char unique PIN (AA1111 format)
- `admin_notes` - Internal notes
- `created_at` - Timestamp
- `updated_at` - Timestamp

**Purpose:** Core booking information and customer details.

**Status Flow:**
```
pending → confirmed → [completed]
   ↓
cancelled
```

---

#### 4. **wp_shb_booking_dates**
Stores individual dates for bookings (supports multi-day).

**Fields:**
- `id` - Primary key
- `booking_id` - Foreign key to bookings
- `booking_date` - Date (YYYY-MM-DD)
- `slot_id` - Foreign key to slots
- `created_at` - Timestamp

**Purpose:** Enables single and multi-day bookings with different slots per day.

**Relationship:**
- Single-day booking: 1 booking → 1 booking_date record
- Multi-day booking: 1 booking → N booking_date records

---

### Data Flow Diagrams

#### Booking Creation Flow

```
┌─────────────┐
│   Guest     │
│  Selects    │
│  Hall       │
└──────┬──────┘
       │
       ▼
┌─────────────────────┐
│  AJAX Request       │
│  Check Availability │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  Database Query                 │
│  - Get hall slots               │
│  - Check existing bookings      │
│  - Apply cleaning buffer        │
│  - Filter by date/day           │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────┐
│  Return Available   │
│  Slots to Frontend  │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Guest Selects      │
│  Slot & Fills Form  │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  AJAX Submit Booking            │
│  - Validate inputs              │
│  - Re-check availability        │
│  - Generate token & PIN         │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  Database Transaction           │
│  1. Insert into bookings        │
│  2. Insert into booking_dates   │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  Send Emails                    │
│  - Admin notification           │
│  - Guest confirmation           │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────┐
│  Return Success     │
│  + Access Token     │
│  + PIN              │
└─────────────────────┘
```

---

## Client-Side Workflows

### 1. Hall Browsing

**Entry Point:** `[shb_hall_list]` shortcode

**Process:**
1. Page loads with shortcode
2. Plugin queries active halls from database
3. Renders grid layout with hall cards
4. Each card shows:
   - Hall name
   - Capacity
   - Description
   - "Book Now" button

**Data Flow:**
```
WordPress Page
    ↓
Shortcode Handler (class-shb-shortcodes.php)
    ↓
Database Query (get_halls with status='active')
    ↓
Template Rendering (public/partials/hall-list.php)
    ↓
HTML Output to User
```

---

### 2. Booking Form Interaction

**Entry Point:** `[shb_booking_form]` shortcode

**Step-by-Step Process:**

#### Step 1: Form Load
1. User lands on booking page
2. Form renders with:
   - Hall selection dropdown (or pre-selected)
   - Date picker (disabled initially)
   - Customer information fields
   - Submit button (disabled)

#### Step 2: Hall Selection
1. User selects a hall
2. JavaScript enables date picker
3. Calendar highlights:
   - Available dates (clickable)
   - Past dates (disabled)
   - Dates with no slots (disabled)

#### Step 3: Date Selection
1. User clicks on a date
2. **AJAX Request** to `shb_check_availability`
   - Sends: hall_id, date
   - Receives: Available slots array

3. **Frontend Processing:**
   - Displays available slots
   - Shows slot details (time, label)
   - Enables slot selection

#### Step 4: Multi-Day Selection (Optional)
1. User selects multiple dates
2. For each date:
   - AJAX checks availability
   - User selects slot per date
3. System validates:
   - All dates have slots selected
   - No conflicts exist

#### Step 5: Customer Information
User fills in:
- **Name** (required)
- **Email** (required)
- **Phone** (optional)
- **Organization** (optional) - v1.4.0
- **Event Purpose** (optional)
- **Attendees Count** (optional)

#### Step 6: Form Submission
1. JavaScript validates all fields
2. **AJAX Request** to `shb_submit_booking`
   - Sends: All form data + selected dates/slots
   - Includes: reCAPTCHA token (if enabled)

3. **Server-Side Processing:**
   - Validates all inputs
   - Re-checks availability (prevent race conditions)
   - Generates unique access token (64 chars)
   - Generates unique PIN (AA1111 format)
   - Creates booking record
   - Creates booking_dates records
   - Sends emails

4. **Response:**
   - Success: Shows confirmation with PIN
   - Error: Shows specific error message

**Data Flow:**
```
User Input
    ↓
JavaScript Validation
    ↓
AJAX Request (shb_submit_booking)
    ↓
Nonce Verification
    ↓
reCAPTCHA Verification (if enabled)
    ↓
Input Sanitization
    ↓
Availability Re-check
    ↓
Database Transaction
    │
    ├─→ Insert Booking
    │
    └─→ Insert Booking Dates
    ↓
Email Queue
    │
    ├─→ Admin Notification
    │
    └─→ Guest Confirmation
    ↓
JSON Response
    ↓
Frontend Display (Success/Error)
```

---

### 3. Booking Management (Guest)

**Entry Point:** `[shb_user_bookings]` shortcode

**Access Methods:**

#### Method 1: Email Link
1. Guest receives confirmation email
2. Email contains link with token: `?token=abc123...`
3. Clicking link loads booking details

#### Method 2: PIN Entry
1. Guest visits booking management page
2. Sees PIN entry form
3. Enters 6-character PIN (e.g., AA1234)
4. System looks up booking by PIN
5. Displays booking details

**Booking Details Display:**
- Booking ID
- Status badge (Pending/Confirmed/Cancelled)
- Access PIN (displayed prominently)
- Customer information
- Event details
- Booking schedule:
  - Single-day: Large date display with time
  - Multi-day: Table of dates with slots
- Booking timestamp
- Cancellation button (if pending/confirmed)

**Cancellation Process:**
1. User clicks "Cancel Booking"
2. JavaScript confirmation dialog
3. Form submission with nonce
4. Server updates status to 'cancelled'
5. Email sent to guest (cancellation confirmation)
6. Email sent to admin (cancellation notice)
7. Page refreshes showing cancelled status

**Data Flow:**
```
PIN/Token Input
    ↓
Database Lookup
    ↓
Booking Found?
    │
    ├─→ Yes: Load booking details
    │         ↓
    │    Get booking_dates records
    │         ↓
    │    Get hall details
    │         ↓
    │    Get slot details
    │         ↓
    │    Render booking view
    │
    └─→ No: Show error message
```

---

## Admin-Side Workflows

### 1. Hall Management

**Location:** Hall Booking → Halls

#### Creating a Hall

**Process:**
1. Admin clicks "Add New"
2. Form displays with fields:
   - Title (required)
   - Description (optional, rich text)
   - Capacity (required, number)
   - Cleaning Buffer (minutes, default: 0)
   - Status (active/inactive)

3. Admin fills form and clicks "Save Hall"

4. **Server Processing:**
   - Validates inputs
   - Sanitizes data
   - Inserts into wp_shb_halls
   - Redirects to hall edit page

5. **Time Slots Section** (on edit page):
   - Shows existing slots table
   - "Add Slot" button opens modal

**Data Flow:**
```
Admin Input
    ↓
Form Submission (POST)
    ↓
Nonce Verification
    ↓
Capability Check (manage_options)
    ↓
Input Validation & Sanitization
    ↓
Database Insert (wp_shb_halls)
    ↓
Redirect to Edit Page
```

---

#### Managing Time Slots

**Slot Creation:**
1. Admin clicks "Add Slot" on hall edit page
2. Modal opens with form:
   - **Slot Type**: Full Day / Partial
   - **Label**: Display name
   - **Start Time**: Time picker
   - **End Time**: Time picker
   - **Days Enabled**: Checkboxes (Mon-Sun)
   - **Active Status**: Yes/No

3. **Validation:**
   - End time must be after start time
   - For partial slots: Check overlap with existing slots
   - Full-day slots: Only one allowed per hall

4. **Server Processing:**
   - Validates times
   - Checks for overlaps
   - Inserts into wp_shb_slots
   - Returns success/error via AJAX

**Overlap Detection Logic:**
```
For Partial Slots:
- Query existing partial slots for same hall
- Check if new slot times overlap:
  - New start < Existing end AND
  - New end > Existing start
- If overlap found: Reject
- If no overlap: Allow

For Full-Day Slots:
- Check if full-day slot already exists
- If exists: Reject
- If not: Allow
```

**Slot Editing:**
1. Admin clicks "Edit" on slot
2. Modal pre-fills with existing data
3. Admin modifies and saves
4. **Overlap check excludes current slot** from validation
5. Updates database record

**Slot Deletion:**
1. Admin clicks "Delete"
2. JavaScript confirmation
3. Checks if slot has future bookings
4. If bookings exist: Show warning
5. If confirmed: Soft delete (set is_active=0)

---

### 2. Booking Management

**Location:** Hall Booking → Bookings

#### Bookings List View

**Display:**
- Filterable table showing:
  - Booking ID
  - Customer name & email
  - Hall name
  - Date(s)
  - Status
  - Created date
  - Actions (View/Edit)

**Filters:**
- Status: All / Pending / Confirmed / Cancelled
- Hall: All / Specific hall
- Date Range: From - To
- Search: Customer name/email

**Data Query:**
```
SELECT 
    b.*, 
    h.title as hall_name,
    GROUP_CONCAT(bd.booking_date) as dates
FROM wp_shb_bookings b
LEFT JOIN wp_shb_halls h ON b.hall_id = h.id
LEFT JOIN wp_shb_booking_dates bd ON b.id = bd.booking_id
WHERE [filters]
GROUP BY b.id
ORDER BY b.created_at DESC
```

---

#### Booking Edit/Review

**Process:**
1. Admin clicks "Edit" on a booking
2. Loads detailed view with:

**Information Sections:**

**A. Customer Information**
- Name
- Email
- Phone
- Organization (if provided)

**B. Event Details**
- Purpose
- Attendees count
- Hall name
- Booking type (single/multi-day)

**C. Schedule**
- Single-day: Date + Slot + Time
- Multi-day: Table of dates with slots

**D. Status Management**
- Current status badge
- Status dropdown (Pending/Confirmed/Cancelled)
- Admin notes textarea

**E. Conflict Detection**
- Shows if editing would create conflicts
- Lists conflicting bookings
- Prevents saving if conflicts exist

**Status Change Process:**
1. Admin selects new status
2. Optionally adds admin notes
3. Clicks "Update Booking"

4. **Server Processing:**
   - Validates status transition
   - Updates booking record
   - Triggers email notification
   - Logs status change

5. **Email Sent Based on Status:**
   - **Confirmed**: Guest receives confirmation email
   - **Cancelled**: Guest receives cancellation email
   - **Any change**: Admin receives update notification

**Data Flow:**
```
Admin Action (Status Change)
    ↓
Form Submission
    ↓
Nonce & Capability Check
    ↓
Validate Status Transition
    ↓
Update Database
    ↓
Trigger Email Hook
    ↓
Email System Processes
    │
    ├─→ Render Email Template
    │
    ├─→ Replace Placeholders
    │
    └─→ Send via wp_mail()
    ↓
Redirect with Success Message
```

---

### 3. Calendar View

**Location:** Hall Booking → Calendar

**Purpose:** Visual overview of all bookings

**Display:**
- Full calendar interface
- Color-coded by status:
  - Pending: Yellow
  - Confirmed: Green
  - Cancelled: Red
- Click on booking: Shows quick details popup
- Filter by hall

**Data Loading:**
```
AJAX Request (on month change)
    ↓
Get all bookings for date range
    ↓
Format for calendar library
    ↓
Return JSON:
    {
        id: booking_id,
        title: "Customer Name - Hall",
        start: date,
        end: date,
        color: status_color,
        extendedProps: { booking_details }
    }
    ↓
Calendar renders events
```

---

### 4. Settings Management

**Location:** Hall Booking → Settings

**Sections:**

#### A. Email Settings
- **From Name**: Sender name for emails
- **From Email**: Sender email address
- **Admin Email**: Where to send admin notifications
- **Email Templates**: Customizable for each status

**Template Placeholders:**
- `{customer_name}` - Booking customer name
- `{hall_name}` - Hall name
- `{booking_date}` - Date(s)
- `{booking_time}` - Time slot
- `{booking_id}` - Booking ID
- `{access_token}` - Access token
- `{pin}` - Booking PIN
- `{admin_notes}` - Admin notes

#### B. General Settings
- **Confirmation Page**: Redirect after booking
- **Date Format**: Display format
- **Time Format**: 12/24 hour

#### C. reCAPTCHA Settings (v3)
- **Enable/Disable**: Toggle
- **Site Key**: Public key
- **Secret Key**: Private key
- **Threshold**: Score threshold (0.0-1.0)

**Save Process:**
```
Admin Input
    ↓
Form Submission
    ↓
Nonce Verification
    ↓
Sanitize Each Setting
    ↓
Update WordPress Options
    ↓
Show Success Message
```

---

## Booking Lifecycle

### Complete Lifecycle Flow

```
┌─────────────────────────────────────────────────────────┐
│                    BOOKING CREATED                       │
│  Status: PENDING                                         │
│  - Guest submits booking form                           │
│  - System generates token & PIN                         │
│  - Emails sent to guest & admin                         │
└────────────────┬────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────┐
│              ADMIN REVIEWS BOOKING                       │
│  Admin Options:                                          │
│  1. Confirm → Status: CONFIRMED                         │
│  2. Cancel → Status: CANCELLED                          │
│  3. Leave Pending                                        │
└────────────────┬────────────────────────────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
        ▼                 ▼
┌──────────────┐  ┌──────────────┐
│  CONFIRMED   │  │  CANCELLED   │
│              │  │              │
│ - Email sent │  │ - Email sent │
│ - Booking    │  │ - Slot freed │
│   locked     │  │              │
└──────┬───────┘  └──────────────┘
       │
       ▼
┌──────────────┐
│ EVENT OCCURS │
│ (Booking     │
│  Date)       │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  COMPLETED   │
│ (Historical) │
└──────────────┘
```

### Status Transitions

**Valid Transitions:**
- `pending` → `confirmed`
- `pending` → `cancelled`
- `confirmed` → `cancelled`

**Invalid Transitions:**
- `cancelled` → `confirmed` (Cannot reactivate)
- `cancelled` → `pending` (Cannot revert)

---

## Email Notification System

### Email Types

#### 1. **Admin New Booking Notification**

**Trigger:** New booking created

**Recipients:** Admin email (from settings)

**Content:**
- Subject: "New Booking Request #[ID]"
- Booking details
- Customer information
- Link to admin edit page

**Template Variables:**
- All booking fields
- Direct admin link

---

#### 2. **Guest Pending Confirmation**

**Trigger:** Booking created

**Recipients:** Customer email

**Content:**
- Subject: "Booking Request Received #[ID]"
- Booking details
- Access PIN prominently displayed
- Link to manage booking
- "Pending approval" notice

---

#### 3. **Guest Confirmed Notification**

**Trigger:** Admin confirms booking

**Recipients:** Customer email

**Content:**
- Subject: "Booking Confirmed #[ID]"
- Confirmation message
- Booking details
- Access PIN
- Link to manage booking

---

#### 4. **Guest Cancelled Notification**

**Trigger:** Admin or guest cancels

**Recipients:** Customer email

**Content:**
- Subject: "Booking Cancelled #[ID]"
- Cancellation notice
- Booking details (for reference)
- Contact information

---

### Email Processing Flow

```
Status Change Event
    ↓
Email Hook Triggered
    ↓
Get Booking Details
    ↓
Load Email Template (from settings)
    ↓
Replace Placeholders
    │
    ├─→ {customer_name} → John Doe
    ├─→ {hall_name} → Main Hall
    ├─→ {booking_date} → Jan 20, 2026
    ├─→ {pin} → AB1234
    └─→ ... (all placeholders)
    ↓
Build Email Headers
    │
    ├─→ From: [From Name] <[From Email]>
    ├─→ Reply-To: [Admin Email]
    └─→ Content-Type: text/html
    ↓
wp_mail() Function
    ↓
Email Sent
```

---

## Security & Validation

### Input Validation

#### Frontend (JavaScript)
- Required field checks
- Email format validation
- Phone number format
- Date format validation
- Slot selection validation

#### Backend (PHP)
- **Sanitization:**
  - `sanitize_text_field()` - Text inputs
  - `sanitize_email()` - Email addresses
  - `absint()` - Integer values
  - `wp_kses_post()` - Rich text

- **Validation:**
  - Email format: `is_email()`
  - Date format: Regex pattern
  - Required fields: Empty checks
  - Capability checks: `current_user_can()`

### Security Measures

#### 1. **Nonce Verification**
Every form and AJAX request includes nonce:
```
Frontend: wp_nonce_field('action_name', 'nonce_field')
Backend: wp_verify_nonce($_POST['nonce'], 'action_name')
```

#### 2. **Capability Checks**
Admin operations require:
```
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}
```

#### 3. **SQL Injection Prevention**
All queries use prepared statements:
```
$wpdb->prepare("SELECT * FROM table WHERE id = %d", $id)
```

#### 4. **XSS Prevention**
All output escaped:
```
echo esc_html($data);        // Plain text
echo esc_attr($data);        // Attributes
echo esc_url($data);         // URLs
echo wp_kses_post($data);    // Rich content
```

#### 5. **Access Token Security**
- 64 characters, cryptographically secure
- Generated using `wp_generate_password(64, true, true)`
- Unique constraint in database

#### 6. **PIN Security**
- Format: 2 uppercase letters + 4 digits (e.g., AB1234)
- Unique constraint in database
- Easy to communicate verbally/written

#### 7. **reCAPTCHA Integration**
- Google reCAPTCHA v3
- Score-based validation
- Configurable threshold
- Prevents spam submissions

---

## Multi-Day Booking System

### Architecture

**Design:** Separate table for booking dates allows flexibility

**Benefits:**
- Single-day and multi-day bookings use same structure
- Different slots per day supported
- Easy to query by date
- Efficient conflict detection

### Workflow

#### 1. **Date Selection**
1. User selects multiple dates on calendar
2. Each date highlighted
3. Counter shows total days selected

#### 2. **Slot Selection Per Date**
1. For each selected date:
   - System checks available slots
   - User selects slot for that date
   - Slot can differ per day

2. **Visual Display:**
   ```
   Date 1: Jan 20, 2026 → Morning Session (9:00 AM - 12:00 PM)
   Date 2: Jan 21, 2026 → Full Day (9:00 AM - 6:00 PM)
   Date 3: Jan 22, 2026 → Evening Session (4:00 PM - 8:00 PM)
   ```

#### 3. **Availability Validation**
1. **AJAX Request** to `shb_check_multiday_availability`
2. **Server checks each date:**
   - Is slot available?
   - Any conflicts?
   - Cleaning buffer respected?

3. **Response:**
   - All available: Proceed
   - Some unavailable: Show which dates/slots

#### 4. **Booking Creation**
```
Transaction Start
    ↓
Insert into wp_shb_bookings
    - booking_type = 'multiday'
    - customer details
    - status = 'pending'
    ↓
For each selected date:
    Insert into wp_shb_booking_dates
        - booking_id (from above)
        - booking_date
        - slot_id
    ↓
Transaction Commit
```

### Conflict Detection for Multi-Day

**Process:**
```
For each date in booking:
    1. Get all existing bookings for that date
    2. Check if selected slot is available:
        - If full-day: No other bookings allowed
        - If partial: Check time overlap
    3. Apply cleaning buffer
    4. If conflict found: Return error with details
    5. If all clear: Proceed
```

---

## PIN Access System

### PIN Generation

**Format:** `[A-Z]{2}[0-9]{4}`
- Examples: AB1234, XY9876, QW5432

**Generation Process:**
```
1. Generate 2 random uppercase letters
2. Generate 4 random digits
3. Combine: letters + digits
4. Check uniqueness in database
5. If duplicate: Regenerate
6. If unique: Assign to booking
```

**Code Logic:**
```
do {
    $letters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2);
    $numbers = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $pin = $letters . $numbers;
    
    $exists = check_pin_exists($pin);
} while ($exists);

return $pin;
```

### PIN Lookup

**Process:**
1. User enters PIN on booking management page
2. System queries: `SELECT * FROM bookings WHERE pin = ?`
3. If found: Display booking
4. If not found: Show error

**Security:**
- PIN is case-insensitive (converted to uppercase)
- No rate limiting needed (6-char alphanumeric = 45,697,600 combinations)
- Unique constraint prevents duplicates

### PIN vs Token

**PIN:**
- Short, memorable
- Easy to share verbally
- Used for quick lookup
- Displayed in emails

**Token:**
- Long, secure (64 chars)
- Used in URLs
- Harder to guess
- Not meant to be typed

**Both:**
- Provide access to same booking
- No authentication required
- Unique per booking

---

## Advanced Features

### Cleaning Buffer Time

**Purpose:** Prevent back-to-back bookings, allow cleanup time

**Configuration:** Per-hall setting (in minutes)

**Application:**
```
Booking 1: 9:00 AM - 12:00 PM
Cleaning Buffer: 30 minutes
Next Available: 12:30 PM onwards

If someone tries to book 12:00 PM - 2:00 PM:
    → Rejected (conflicts with buffer)

If someone tries to book 12:30 PM - 2:00 PM:
    → Allowed
```

**Implementation:**
```
When checking availability:
1. Get all bookings for date
2. For each booking:
    - Add cleaning_buffer to end_time
    - Check if new booking starts after buffered end
3. If overlap with buffer: Slot unavailable
```

---

### Slot Overlap Prevention

**Validation Points:**
1. **Slot Creation:** Check against existing slots
2. **Slot Editing:** Check against other slots (excluding self)
3. **Booking Creation:** Check against existing bookings

**Overlap Logic:**
```
Slot A: start_a to end_a
Slot B: start_b to end_b

Overlap exists if:
    (start_a < end_b) AND (end_a > start_b)

Visual:
    A: |-------|
    B:     |-------|
       ↑ Overlap region
```

---

### Responsive Calendar

**Desktop:**
- Full month view
- Click to select dates
- Hover shows availability

**Mobile:**
- Swipe between months
- Tap to select
- Optimized touch targets

**Accessibility:**
- Keyboard navigation
- Screen reader support
- ARIA labels

---

## Data Retention & Cleanup

### Automatic Cleanup (Future Feature)

**Planned:**
- Archive completed bookings after X days
- Delete cancelled bookings after X days
- Export historical data

**Current:**
- All data retained indefinitely
- Manual deletion via admin

---

## Performance Optimization

### Database Indexes

**Indexed Fields:**
- `wp_shb_bookings.access_token` (unique)
- `wp_shb_bookings.pin` (unique)
- `wp_shb_bookings.status`
- `wp_shb_bookings.hall_id`
- `wp_shb_booking_dates.booking_id`
- `wp_shb_booking_dates.booking_date`
- `wp_shb_booking_dates.slot_id`

**Query Optimization:**
- Prepared statements
- Selective field retrieval
- JOIN optimization
- Date range queries use indexes

### Caching Strategy

**Current:**
- No persistent caching (WordPress transients not used)
- Database queries run on each request

**Future Enhancement:**
- Cache hall/slot data (rarely changes)
- Cache availability for popular dates
- Invalidate on booking creation

---

## Error Handling

### User-Facing Errors

**Categories:**
1. **Validation Errors**
   - Missing required fields
   - Invalid email format
   - Invalid date selection

2. **Availability Errors**
   - Slot no longer available
   - Date in the past
   - Conflict detected

3. **System Errors**
   - Database error
   - Email sending failed
   - Unknown error

**Display:**
- Clear, actionable messages
- Specific field highlighting
- Suggestions for resolution

### Admin Errors

**Logging:**
- PHP errors logged to debug.log
- Database errors captured
- Email failures logged

**Display:**
- Admin notices in WordPress admin
- Detailed error messages
- Stack traces (if WP_DEBUG enabled)

---

## Internationalization (i18n)

### Text Domain
`simple-hall-booking-manager`

### Translation Ready
- All strings wrapped in translation functions
- `.pot` file generated
- Supports `.po` and `.mo` files

### Translation Functions Used
- `__()` - Returns translated string
- `_e()` - Echoes translated string
- `esc_html__()` - Returns escaped translated string
- `esc_html_e()` - Echoes escaped translated string
- `_n()` - Plural forms

---

## Future Enhancements

### Planned Features

1. **Payment Integration**
   - Stripe/PayPal support
   - Deposit/full payment options
   - Refund handling

2. **Advanced Calendar**
   - Drag-and-drop booking creation
   - Visual conflict indicators
   - Resource allocation view

3. **Reporting**
   - Booking statistics
   - Revenue reports
   - Occupancy rates
   - Export to CSV/PDF

4. **Customer Portal**
   - User accounts (optional)
   - Booking history
   - Favorite halls
   - Repeat bookings

5. **Automated Reminders**
   - Email reminders before event
   - Follow-up surveys
   - Review requests

6. **Integration APIs**
   - Google Calendar sync
   - Outlook Calendar sync
   - Zapier integration
   - REST API for external systems

---

## Conclusion

This document provides a comprehensive overview of the Simple Hall Booking Manager plugin's architecture, workflows, and data flows. It serves as the master reference for understanding how the system operates from both client and admin perspectives.

**Key Takeaways:**
- Guest-friendly booking without login requirements
- Robust conflict prevention and validation
- Flexible multi-day booking support
- Secure PIN-based access system
- Comprehensive admin controls
- Email-driven communication
- WordPress standards compliant

For technical implementation details, refer to the individual documentation files in the `aidocument/` folder.

---

**Document Version:** 1.0  
**Plugin Version:** 1.4.0  
**Last Updated:** January 20, 2026
