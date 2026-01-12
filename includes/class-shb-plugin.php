<?php
/**
 * Main plugin class
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class - Singleton
 */
class SHB_Plugin {

	/**
	 * Single instance of the class
	 *
	 * @var SHB_Plugin
	 */
	private static $instance = null;

	/**
	 * Database handler instance
	 *
	 * @var SHB_DB
	 */
	public $db;

	/**
	 * Admin handler instance
	 *
	 * @var SHB_Admin
	 */
	public $admin;

	/**
	 * Frontend handler instance
	 *
	 * @var SHB_Frontend
	 */
	public $frontend;

	/**
	 * Shortcodes handler instance
	 *
	 * @var SHB_Shortcodes
	 */
	public $shortcodes;

	/**
	 * AJAX handler instance
	 *
	 * @var SHB_AJAX
	 */
	public $ajax;

	/**
	 * Emails handler instance
	 *
	 * @var SHB_Emails
	 */
	public $emails;

	/**
	 * Get instance
	 *
	 * @return SHB_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->init_hooks();
		$this->init_components();
	}

	/**
	 * Load required files
	 */
	private function load_dependencies() {
		// Core classes
		require_once SHB_PLUGIN_DIR . 'includes/class-shb-db.php';
		require_once SHB_PLUGIN_DIR . 'includes/class-shb-admin.php';
		require_once SHB_PLUGIN_DIR . 'includes/class-shb-frontend.php';
		require_once SHB_PLUGIN_DIR . 'includes/class-shb-shortcodes.php';
		require_once SHB_PLUGIN_DIR . 'includes/class-shb-ajax.php';
		require_once SHB_PLUGIN_DIR . 'includes/class-shb-emails.php';
		require_once SHB_PLUGIN_DIR . 'includes/functions-helpers.php';
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initialize components
	 */
	private function init_components() {
		// Database layer
		$this->db = new SHB_DB();

		// Admin area
		if ( is_admin() ) {
			$this->admin = new SHB_Admin();
		}

		// Frontend
		if ( ! is_admin() ) {
			$this->frontend = new SHB_Frontend();
		}

		// Shortcodes (both admin and frontend)
		$this->shortcodes = new SHB_Shortcodes();

		// AJAX handlers
		$this->ajax = new SHB_AJAX();

		// Email notifications
		$this->emails = new SHB_Emails();
	}

	/**
	 * Load plugin textdomain
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'simple-hall-booking-manager',
			false,
			dirname( SHB_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Init hook callback
	 */
	public function init() {
		// Register any custom post types, taxonomies, etc. if needed in the future
		do_action( 'shb_init' );
	}
}

