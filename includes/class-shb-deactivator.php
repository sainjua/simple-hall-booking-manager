<?php
/**
 * Plugin deactivation handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deactivator class
 */
class SHB_Deactivator {

	/**
	 * Deactivate the plugin
	 *
	 * Note: We don't delete data here. Data deletion is handled in uninstall.php
	 */
	public static function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();

		// Clear any scheduled cron jobs if we add them later
		wp_clear_scheduled_hook( 'shb_daily_cleanup' );
	}
}

