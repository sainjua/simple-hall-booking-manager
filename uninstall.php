<?php
/**
 * Uninstall handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if uninstall not called from WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

// Check if user wants to delete data
$general_settings = get_option('shb_general_settings', array());
$delete_data = isset($general_settings['delete_data_on_uninstall']) ? $general_settings['delete_data_on_uninstall'] : 0;

if (!$delete_data) {
	// User wants to keep data, exit
	return;
}

global $wpdb;

// Drop custom tables
$table_halls = $wpdb->prefix . 'shb_halls';
$table_slots = $wpdb->prefix . 'shb_slots';
$table_bookings = $wpdb->prefix . 'shb_bookings';
$table_booking_dates = $wpdb->prefix . 'shb_booking_dates';

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table names must be interpolated, uninstall operation
$wpdb->query("DROP TABLE IF EXISTS {$table_booking_dates}");
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table names must be interpolated, uninstall operation
$wpdb->query("DROP TABLE IF EXISTS {$table_bookings}");
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table names must be interpolated, uninstall operation
$wpdb->query("DROP TABLE IF EXISTS {$table_slots}");
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table names must be interpolated, uninstall operation
$wpdb->query("DROP TABLE IF EXISTS {$table_halls}");

// Delete plugin options
delete_option('shb_email_settings');
delete_option('shb_general_settings');
delete_option('shb_activation_time');
delete_option('shb_version');

// Clear any cached data
wp_cache_flush();

