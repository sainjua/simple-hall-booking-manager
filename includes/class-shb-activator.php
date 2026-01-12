<?php
/**
 * Plugin activation handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activator class
 */
class SHB_Activator {

	/**
	 * Activate the plugin
	 */
	public static function activate() {
		// Load the database class
		require_once SHB_PLUGIN_DIR . 'includes/class-shb-db.php';

		// Create database tables
		$db = new SHB_DB();
		$db->create_tables();

		// Set default options
		self::set_default_options();

		// Flush rewrite rules
		flush_rewrite_rules();

		// Store activation time
		add_option( 'shb_activation_time', time() );
		add_option( 'shb_version', SHB_VERSION );
	}

	/**
	 * Set default plugin options
	 */
	private static function set_default_options() {
		// Email settings
		$email_defaults = array(
			'from_name'  => get_bloginfo( 'name' ),
			'from_email' => get_bloginfo( 'admin_email' ),
			'admin_email' => get_bloginfo( 'admin_email' ),
		);
		add_option( 'shb_email_settings', $email_defaults );

		// General settings
		$general_defaults = array(
			'delete_data_on_uninstall' => false,
			'date_format' => 'Y-m-d',
			'time_format' => 'H:i',
		);
		add_option( 'shb_general_settings', $general_defaults );
	}
}

