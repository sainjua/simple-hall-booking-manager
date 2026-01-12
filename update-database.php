<?php
/**
 * Temporary script to update database for multi-day bookings
 * Run this once, then delete this file
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('activate_plugins')) {
    die('You must be an administrator to run this script.');
}

echo "Starting database update...\n\n";

// Load the database class
require_once('includes/class-shb-db.php');

$db = new SHB_DB();

// Run create_tables which includes migration
$db->create_tables();

echo "✅ Database tables created/updated successfully!\n\n";

// Verify the changes
global $wpdb;

echo "Verifying changes...\n\n";

// Check if booking_type column exists
$table_bookings = $wpdb->prefix . 'shb_bookings';
$columns = $wpdb->get_results("SHOW COLUMNS FROM {$table_bookings}");

$has_booking_type = false;
foreach ($columns as $column) {
    if ($column->Field === 'booking_type') {
        $has_booking_type = true;
        echo "✅ Column 'booking_type' exists in shb_bookings table\n";
        break;
    }
}

if (!$has_booking_type) {
    echo "❌ Column 'booking_type' NOT found - migration may have failed\n";
}

// Check if booking_dates table exists
$table_booking_dates = $wpdb->prefix . 'shb_booking_dates';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_booking_dates}'");

if ($table_exists) {
    echo "✅ Table 'shb_booking_dates' exists\n";
} else {
    echo "❌ Table 'shb_booking_dates' NOT found - creation may have failed\n";
}

echo "\n";
echo "Database update complete!\n";
echo "You can now delete this file: update-database.php\n";
