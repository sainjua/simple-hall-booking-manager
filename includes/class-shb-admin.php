<?php
/**
 * Admin area handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Admin class
 */
class SHB_Admin
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		add_action('admin_menu', array($this, 'add_admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
		add_action('admin_init', array($this, 'register_settings'));
		add_action('admin_init', array($this, 'handle_admin_actions'));
	}

	/**
	 * Add admin menu pages
	 */
	public function add_admin_menu()
	{
		// Main menu
		add_menu_page(
			__('Hall Booking', 'simple-hall-booking-manager'),
			__('Hall Booking', 'simple-hall-booking-manager'),
			'manage_options',
			'shb-halls',
			array($this, 'render_halls_page'),
			'dashicons-calendar-alt',
			30
		);

		// Halls submenu (default)
		add_submenu_page(
			'shb-halls',
			__('Halls', 'simple-hall-booking-manager'),
			__('Halls', 'simple-hall-booking-manager'),
			'manage_options',
			'shb-halls',
			array($this, 'render_halls_page')
		);

		// Bookings submenu
		add_submenu_page(
			'shb-halls',
			__('Bookings', 'simple-hall-booking-manager'),
			__('Bookings', 'simple-hall-booking-manager'),
			'manage_options',
			'shb-bookings',
			array($this, 'render_bookings_page')
		);

		// Calendar submenu
		add_submenu_page(
			'shb-halls',
			__('Calendar', 'simple-hall-booking-manager'),
			__('Calendar', 'simple-hall-booking-manager'),
			'manage_options',
			'shb-calendar',
			array($this, 'render_calendar_page')
		);

		// Settings submenu
		add_submenu_page(
			'shb-halls',
			__('Settings', 'simple-hall-booking-manager'),
			__('Settings', 'simple-hall-booking-manager'),
			'manage_options',
			'shb-settings',
			array($this, 'render_settings_page')
		);
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets($hook)
	{
		// Only load on our plugin pages
		if (false === strpos($hook, 'shb-')) {
			return;
		}

		// CSS
		wp_enqueue_style(
			'shb-admin-css',
			SHB_PLUGIN_URL . 'admin/css/shb-admin.css',
			array(),
			SHB_VERSION
		);

		// JS
		wp_enqueue_script(
			'shb-admin-js',
			SHB_PLUGIN_URL . 'admin/js/shb-admin.js',
			array('jquery'),
			SHB_VERSION,
			true
		);

		// Localize script
		wp_localize_script(
			'shb-admin-js',
			'shbAdmin',
			array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('shb_admin_nonce'),
				'i18n' => array(
					'confirmDelete' => __('Are you sure you want to delete this item?', 'simple-hall-booking-manager'),
					'confirmReset' => __('Are you sure you want to reset this template to default?', 'simple-hall-booking-manager'),
					'error' => __('An error occurred. Please try again.', 'simple-hall-booking-manager'),
				),
			)
		);

		// Enqueue FullCalendar for calendar page only
		if ('toplevel_page_shb-calendar' === $hook) {
			// FullCalendar CSS (bundled locally)
			wp_enqueue_style(
				'fullcalendar',
				SHB_PLUGIN_URL . 'admin/vendor/fullcalendar/main.min.css',
				array(),
				'5.11.3'
			);

			// FullCalendar JS (bundled locally)
			wp_enqueue_script(
				'fullcalendar',
				SHB_PLUGIN_URL . 'admin/vendor/fullcalendar/main.min.js',
				array(),
				'5.11.3',
				true
			);
		}
	}

	/**
	 * Register plugin settings
	 */
	public function register_settings()
	{
		// Email settings
		register_setting(
			'shb_email_settings',
			'shb_email_settings',
			array($this, 'sanitize_email_settings')
		);

		// General settings
		register_setting(
			'shb_general_settings',
			'shb_general_settings',
			array($this, 'sanitize_general_settings')
		);
	}

	/**
	 * Sanitize email settings
	 *
	 * @param array $input Settings input.
	 * @return array
	 */
	public function sanitize_email_settings($input)
	{
		$sanitized = array();

		if (isset($input['from_name'])) {
			$sanitized['from_name'] = sanitize_text_field(wp_unslash($input['from_name']));
		}

		if (isset($input['from_email'])) {
			$sanitized['from_email'] = sanitize_email(wp_unslash($input['from_email']));
		}

		if (isset($input['admin_email'])) {
			$sanitized['admin_email'] = sanitize_email(wp_unslash($input['admin_email']));
		}

		// Admin Notification
		if (isset($input['admin_notification_subject'])) {
			$sanitized['admin_notification_subject'] = sanitize_text_field($input['admin_notification_subject']);
		}
		if (isset($input['admin_notification_body'])) {
			$sanitized['admin_notification_body'] = wp_kses_post($input['admin_notification_body']);
		}

		// Guest Pending
		if (isset($input['guest_pending_subject'])) {
			$sanitized['guest_pending_subject'] = sanitize_text_field($input['guest_pending_subject']);
		}
		if (isset($input['guest_pending_body'])) {
			$sanitized['guest_pending_body'] = wp_kses_post($input['guest_pending_body']);
		}

		// Guest Confirmed
		if (isset($input['guest_confirmed_subject'])) {
			$sanitized['guest_confirmed_subject'] = sanitize_text_field($input['guest_confirmed_subject']);
		}
		if (isset($input['guest_confirmed_body'])) {
			$sanitized['guest_confirmed_body'] = wp_kses_post($input['guest_confirmed_body']);
		}

		// Guest Cancelled
		if (isset($input['guest_cancelled_subject'])) {
			$sanitized['guest_cancelled_subject'] = sanitize_text_field($input['guest_cancelled_subject']);
		}
		if (isset($input['guest_cancelled_body'])) {
			$sanitized['guest_cancelled_body'] = wp_kses_post($input['guest_cancelled_body']);
		}

		return $sanitized;
	}

	/**
	 * Sanitize general settings
	 *
	 * @param array $input Settings input.
	 * @return array
	 */
	public function sanitize_general_settings($input)
	{
		$sanitized = array();

		$sanitized['delete_data_on_uninstall'] = isset($input['delete_data_on_uninstall']) ? 1 : 0;

		if (isset($input['date_format'])) {
			$sanitized['date_format'] = sanitize_text_field($input['date_format']);
		}

		if (isset($input['time_format'])) {
			$sanitized['time_format'] = sanitize_text_field($input['time_format']);
		}

		if (isset($input['confirmation_page'])) {
			$sanitized['confirmation_page'] = absint($input['confirmation_page']);
		}

		$sanitized['recaptcha_enabled'] = isset($input['recaptcha_enabled']) && 'yes' === $input['recaptcha_enabled'] ? 'yes' : 'no';

		if (isset($input['recaptcha_site_key'])) {
			$sanitized['recaptcha_site_key'] = sanitize_text_field($input['recaptcha_site_key']);
		}

		if (isset($input['recaptcha_secret_key'])) {
			$sanitized['recaptcha_secret_key'] = sanitize_text_field($input['recaptcha_secret_key']);
		}

		if (isset($input['recaptcha_threshold'])) {
			$sanitized['recaptcha_threshold'] = floatval($input['recaptcha_threshold']);
		}

		return $sanitized;
	}

	/**
	 * Handle admin actions (save, delete, etc.)
	 */
	public function handle_admin_actions()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checking page parameter for routing
		if (!isset($_GET['page']) || false === strpos(sanitize_text_field(wp_unslash($_GET['page'])), 'shb-')) {
			return;
		}

		// Handle hall actions
		if (isset($_POST['shb_save_hall'])) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_save_hall method
			$this->handle_save_hall();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified in handle_delete_hall method
		if (isset($_GET['action']) && 'delete_hall' === sanitize_text_field(wp_unslash($_GET['action'])) && isset($_GET['id'])) {
			$this->handle_delete_hall();
		}

		// Handle slot actions
		if (isset($_POST['shb_save_slot'])) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_save_slot method
			$this->handle_save_slot();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified in handle_delete_slot method
		if (isset($_GET['action']) && 'delete_slot' === sanitize_text_field(wp_unslash($_GET['action'])) && isset($_GET['id'])) {
			$this->handle_delete_slot();
		}

		// Handle booking actions
		if (isset($_POST['shb_save_booking'])) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_save_booking method
			$this->handle_save_booking();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified in handle_delete_booking method
		if (isset($_GET['action']) && 'delete_booking' === sanitize_text_field(wp_unslash($_GET['action'])) && isset($_GET['id'])) {
			$this->handle_delete_booking();
		}
	}

	/**
	 * Handle save hall
	 */
	private function handle_save_hall()
	{
		check_admin_referer('shb_save_hall');

		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have permission to perform this action.', 'simple-hall-booking-manager'));
		}

		$hall_id = isset($_POST['hall_id']) ? absint($_POST['hall_id']) : 0;
		$data = array(
			'title' => isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '',
			'description' => isset($_POST['description']) ? wp_kses_post(wp_unslash($_POST['description'])) : '',
			'capacity' => isset($_POST['capacity']) ? absint($_POST['capacity']) : 0,
			'status' => isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : 'active',
			'cleaning_buffer' => isset($_POST['cleaning_buffer']) ? absint($_POST['cleaning_buffer']) : 0,
		);

		$db = shb()->db;

		if ($hall_id > 0) {
			// Update existing hall
			$result = $db->update_hall($hall_id, $data);
			$message = $result ? 'updated' : 'error';
		} else {
			// Create new hall
			$hall_id = $db->insert_hall($data);
			$message = $hall_id ? 'created' : 'error';
		}

		wp_safe_redirect(add_query_arg(array('message' => $message), admin_url('admin.php?page=shb-halls')));
		exit;
	}

	/**
	 * Handle delete hall
	 */
	private function handle_delete_hall()
	{
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Validated via check_admin_referer
		if (!isset($_GET['id'])) {
			wp_die(esc_html__('Invalid hall ID.', 'simple-hall-booking-manager'));
		}

		$hall_id = absint($_GET['id']);
		check_admin_referer('shb_delete_hall_' . $hall_id);

		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have permission to perform this action.', 'simple-hall-booking-manager'));
		}

		$db = shb()->db;
		$result = $db->delete_hall($hall_id);
		$message = $result ? 'deleted' : 'error';

		wp_safe_redirect(add_query_arg(array('message' => $message), admin_url('admin.php?page=shb-halls')));
		exit;
	}

	/**
	 * Handle save slot
	 */
	private function handle_save_slot()
	{
		check_admin_referer('shb_save_slot');

		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have permission to perform this action.', 'simple-hall-booking-manager'));
		}

		$slot_id = isset($_POST['slot_id']) ? absint($_POST['slot_id']) : 0;
		$hall_id = isset($_POST['hall_id']) ? absint($_POST['hall_id']) : 0;

		$days_enabled = isset($_POST['days_enabled']) && is_array($_POST['days_enabled'])
			? array_map('absint', $_POST['days_enabled'])
			: array(0, 1, 2, 3, 4, 5, 6);

		// Add seconds to time if not present
		$start_time = isset($_POST['start_time']) ? sanitize_text_field(wp_unslash($_POST['start_time'])) : '00:00:00';
		$end_time = isset($_POST['end_time']) ? sanitize_text_field(wp_unslash($_POST['end_time'])) : '23:59:59';

		// Ensure time format includes seconds
		if (strlen($start_time) === 5) {
			$start_time .= ':00';
		}
		if (strlen($end_time) === 5) {
			$end_time .= ':00';
		}

		$data = array(
			'hall_id' => $hall_id,
			'slot_type' => isset($_POST['slot_type']) ? sanitize_text_field(wp_unslash($_POST['slot_type'])) : 'partial',
			'label' => isset($_POST['label']) ? sanitize_text_field(wp_unslash($_POST['label'])) : '',
			'start_time' => $start_time,
			'end_time' => $end_time,
			'days_enabled' => $days_enabled,
			'is_active' => isset($_POST['is_active']) ? 1 : 0,
			'sort_order' => isset($_POST['sort_order']) ? absint($_POST['sort_order']) : 0,
		);

		$db = shb()->db;

		// Validate slot data
		$validation = $db->validate_slot_data($data, $slot_id);

		if (!$validation['valid']) {
			// Validation failed
			$message = 'slot_error';
			$error_message = $validation['message'];

			wp_safe_redirect(
				add_query_arg(
					array(
						'message' => $message,
						'error_message' => urlencode($error_message),
						'action' => 'edit',
						'id' => $hall_id,
					),
					admin_url('admin.php?page=shb-halls')
				)
			);
			exit;
		}

		// Validation passed, save slot
		if ($slot_id > 0) {
			// Update
			$result = $db->update_slot($slot_id, $data);

			if ($result) {
				$message = 'slot_updated';
			} else {
				// Failed
				if ($db->check_slot_overlap($hall_id, $start_time, $end_time, $slot_id)) {
					$message = 'slot_error';
					$error_message = __('Time slot overlaps with an existing slot.', 'simple-hall-booking-manager');
				} else {
					$message = 'slot_error';
					$error_message = __('Could not update slot. Please try again.', 'simple-hall-booking-manager');
				}
			}
		} else {
			// Insert
			$result = $db->insert_slot($data);

			if ($result) {
				$message = 'slot_added';
			} else {
				// Failed
				if ($db->check_slot_overlap($hall_id, $start_time, $end_time)) {
					$message = 'slot_error';
					$error_message = __('Time slot overlaps with an existing slot.', 'simple-hall-booking-manager');
				} else {
					$message = 'slot_error';
					$error_message = __('Could not add slot. Please try again.', 'simple-hall-booking-manager');
				}
			}
		}

		$redirect_args = array(
			'message' => $message,
			'action' => 'edit',
			'id' => $hall_id,
		);

		if (isset($error_message)) {
			$redirect_args['error_message'] = urlencode($error_message);
		}

		wp_safe_redirect(add_query_arg($redirect_args, admin_url('admin.php?page=shb-halls')));
		exit;
	}

	/**
	 * Handle delete slot
	 */
	private function handle_delete_slot()
	{
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Validated via check_admin_referer
		if (!isset($_GET['id'])) {
			wp_die(esc_html__('Invalid slot ID.', 'simple-hall-booking-manager'));
		}

		$slot_id = absint($_GET['id']);
		check_admin_referer('shb_delete_slot_' . $slot_id);

		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have permission to perform this action.', 'simple-hall-booking-manager'));
		}

		$hall_id = isset($_GET['hall_id']) ? absint($_GET['hall_id']) : 0;
		$db = shb()->db;
		$result = $db->delete_slot($slot_id);
		$message = $result ? 'slot_deleted' : 'error';

		wp_safe_redirect(add_query_arg(array('message' => $message, 'action' => 'edit', 'id' => $hall_id), admin_url('admin.php?page=shb-halls')));
		exit;
	}

	/**
	 * Handle save booking
	 */
	private function handle_save_booking()
	{
		check_admin_referer('shb_save_booking');

		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have permission to perform this action.', 'simple-hall-booking-manager'));
		}

		$booking_id = isset($_POST['booking_id']) ? absint($_POST['booking_id']) : 0;
		$old_status = isset($_POST['old_status']) ? sanitize_text_field(wp_unslash($_POST['old_status'])) : '';
		$new_status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : 'pending';

		$data = array(
			'status' => $new_status,
			'admin_notes' => isset($_POST['admin_notes']) ? wp_kses_post(wp_unslash($_POST['admin_notes'])) : '',
		);

		$db = shb()->db;
		$result = $db->update_booking($booking_id, $data);

		// Auto-cancel conflicting bookings when approving
		$cancelled_bookings = array();
		if ($result && 'confirmed' === $new_status && 'confirmed' !== $old_status) {
			$cancelled_bookings = $db->auto_cancel_conflicts($booking_id);
		}

		// Send email if status changed
		if ($result && $old_status !== $new_status) {
			$emails = shb()->emails;
			if ('confirmed' === $new_status) {
				$emails->send_guest_confirmed($booking_id);
			} elseif ('cancelled' === $new_status) {
				$emails->send_guest_cancelled($booking_id);
			}
		}

		$message = $result ? 'booking_updated' : 'error';

		// Add info about cancelled conflicts
		if (!empty($cancelled_bookings)) {
			$message .= '&cancelled=' . count($cancelled_bookings);
		}

		wp_safe_redirect(add_query_arg(array('message' => $message), admin_url('admin.php?page=shb-bookings')));
		exit;
	}

	/**
	 * Handle delete booking
	 */
	private function handle_delete_booking()
	{
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Validated via check_admin_referer
		if (!isset($_GET['id'])) {
			wp_die(esc_html__('Invalid booking ID.', 'simple-hall-booking-manager'));
		}

		$booking_id = absint($_GET['id']);
		check_admin_referer('shb_delete_booking_' . $booking_id);

		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have permission to perform this action.', 'simple-hall-booking-manager'));
		}

		$db = shb()->db;
		$result = $db->delete_booking($booking_id);
		$message = $result ? 'booking_deleted' : 'error';

		wp_safe_redirect(add_query_arg(array('message' => $message), admin_url('admin.php?page=shb-bookings')));
		exit;
	}

	/**
	 * Render halls page
	 */
	public function render_halls_page()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter used only for page routing
		$action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : 'list';

		if ('edit' === $action) {
			include SHB_PLUGIN_DIR . 'admin/views/view-hall-edit.php';
		} elseif ('new' === $action) {
			include SHB_PLUGIN_DIR . 'admin/views/view-hall-create.php';
		} else {
			include SHB_PLUGIN_DIR . 'admin/views/view-halls-list.php';
		}
	}

	/**
	 * Render bookings page
	 */
	public function render_bookings_page()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter used only for page routing
		$action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : 'list';

		if ('edit' === $action) {
			include SHB_PLUGIN_DIR . 'admin/views/view-booking-edit.php';
		} else {
			include SHB_PLUGIN_DIR . 'admin/views/view-bookings-list.php';
		}
	}

	/**
	 * Render calendar page
	 */
	public function render_calendar_page()
	{
		include SHB_PLUGIN_DIR . 'admin/views/view-calendar.php';
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page()
	{
		include SHB_PLUGIN_DIR . 'admin/views/view-settings.php';
	}
}