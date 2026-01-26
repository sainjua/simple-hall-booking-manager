<?php
/**
 * Plugin Name: Simple Hall Booking Manager
 * Plugin URI: https://wordpress.org/plugins/simple-hall-booking-manager/
 * Description: A powerful, lightweight WordPress plugin to manage hall bookings with full-day vs partial slot logic, guest bookings (no login required), and a clean admin UI.
 * Version: 1.1.4
 * Author: Yujesh K C
 * Author URI:       https://profiles.wordpress.org/sainjua/
 * Text Domain: simple-hall-booking-manager
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

// Define plugin constants
define('SHB_VERSION', '1.1.4');
define('SHB_PLUGIN_FILE', __FILE__);
define('SHB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SHB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SHB_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Require the main plugin class
require_once SHB_PLUGIN_DIR . 'includes/class-shb-plugin.php';

/**
 * Activation hook
 */
function shb_activate_plugin()
{
	require_once SHB_PLUGIN_DIR . 'includes/class-shb-activator.php';
	SHB_Activator::activate();
}
register_activation_hook(__FILE__, 'shb_activate_plugin');

/**
 * Deactivation hook
 */
function shb_deactivate_plugin()
{
	require_once SHB_PLUGIN_DIR . 'includes/class-shb-deactivator.php';
	SHB_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'shb_deactivate_plugin');

/**
 * Initialize the plugin
 */
function shb_init_plugin()
{
	return SHB_Plugin::get_instance();
}

// Start the plugin
shb_init_plugin();

