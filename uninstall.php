<?php
/**
 * Uninstall handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if uninstall not called from WordPress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check if user wants to delete data
$general_settings = get_option( 'shb_general_settings', array() );
$delete_data      = isset( $general_settings['delete_data_on_uninstall'] ) ? $general_settings['delete_data_on_uninstall'] : 0;

if ( ! $delete_data ) {
	// User wants to keep data, exit
	return;
}

global $wpdb;

// Drop custom tables
$table_halls    = $wpdb->prefix . 'shb_halls';
$table_slots    = $wpdb->prefix . 'shb_slots';
$table_bookings = $wpdb->prefix . 'shb_bookings';

$wpdb->query( "DROP TABLE IF EXISTS {$table_bookings}" );
$wpdb->query( "DROP TABLE IF EXISTS {$table_slots}" );
$wpdb->query( "DROP TABLE IF EXISTS {$table_halls}" );

// Delete plugin options
delete_option( 'shb_email_settings' );
delete_option( 'shb_general_settings' );
delete_option( 'shb_activation_time' );
delete_option( 'shb_version' );

// Clear any cached data
wp_cache_flush();

