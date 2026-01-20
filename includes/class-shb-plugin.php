<?php
/**
 * Main plugin class
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Main plugin class - Singleton
 */
class SHB_Plugin
{

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
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct()
	{
		$this->load_dependencies();
		$this->init_hooks();
		$this->init_components();
	}

	/**
	 * Load required files
	 */
	private function load_dependencies()
	{
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
	private function init_hooks()
	{
		add_action('init', array($this, 'init'));

		// Log plugin SQL queries
		add_filter('query', array($this, 'log_plugin_queries'));
	}

	/**
	 * Log SQL queries related to the plugin
	 * 
	 * @param string $query The SQL query.
	 * @return string The SQL query (unmodified).
	 */
	public function log_plugin_queries($query)
	{
		if (false !== strpos($query, 'shb_')) {
			$log_file = SHB_PLUGIN_DIR . 'shb_sql_log.txt';
			$entry = "[" . date('Y-m-d H:i:s') . "] " . $query . "\n";
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			file_put_contents($log_file, $entry, FILE_APPEND);
		}
		return $query;
	}

	/**
	 * Initialize components
	 */
	private function init_components()
	{
		// Database layer
		$this->db = new SHB_DB();

		// Admin area
		if (is_admin()) {
			$this->admin = new SHB_Admin();
		}

		// Frontend
		if (!is_admin()) {
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
	 *
	 * Note: Since WordPress 4.6, translations are automatically loaded for plugins
	 * hosted on WordPress.org. This method is kept for documentation purposes only.
	 * WordPress will automatically load translations from the /languages directory
	 * when needed.
	 */

	/**
	 * Init hook callback
	 */
	public function init()
	{
		// Register any custom post types, taxonomies, etc. if needed in the future
		do_action('shb_init');
	}
}

