<?php
/**
 * AJAX handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * AJAX class
 */
class SHB_AJAX
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Check availability (both logged-in and guests)
		add_action('wp_ajax_shb_check_availability', array($this, 'check_availability'));
		add_action('wp_ajax_nopriv_shb_check_availability', array($this, 'check_availability'));

		// Check multi-day availability
		add_action('wp_ajax_shb_check_multiday_availability', array($this, 'check_multiday_availability'));
		add_action('wp_ajax_nopriv_shb_check_multiday_availability', array($this, 'check_multiday_availability'));

		// Submit booking (both logged-in and guests)
		add_action('wp_ajax_shb_submit_booking', array($this, 'submit_booking'));
		add_action('wp_ajax_nopriv_shb_submit_booking', array($this, 'submit_booking'));

		// Get slot details (admin only)
		add_action('wp_ajax_shb_get_slot', array($this, 'get_slot'));

		// Check slot overlap (admin only)
		add_action('wp_ajax_shb_check_slot_overlap', array($this, 'check_slot_overlap'));
	}

	/**
	 * Verify Google reCAPTCHA v3 token
	 *
	 * @param string $token The reCAPTCHA token from the frontend.
	 * @return bool True if valid, false otherwise.
	 */
	private function verify_recaptcha($token)
	{
		$general_settings = get_option('shb_general_settings', array());
		$recaptcha_enabled = isset($general_settings['recaptcha_enabled']) ? $general_settings['recaptcha_enabled'] : 'no';
		$recaptcha_secret_key = isset($general_settings['recaptcha_secret_key']) ? $general_settings['recaptcha_secret_key'] : '';
		$recaptcha_threshold = isset($general_settings['recaptcha_threshold']) ? floatval($general_settings['recaptcha_threshold']) : 0.5;

		// If reCAPTCHA is not enabled, skip verification
		if ('yes' !== $recaptcha_enabled || empty($recaptcha_secret_key)) {
			return true;
		}

		// If no token provided, fail
		if (empty($token)) {
			return false;
		}

		// Verify token with Google API
		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret' => $recaptcha_secret_key,
					'response' => $token,
					'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
				),
			)
		);

		// Handle API errors (fail-open: allow submission if API is down)
		if (is_wp_error($response)) {
			return true; // Fail-open
		}

		$body = wp_remote_retrieve_body($response);
		$result = json_decode($body);

		// Check if verification was successful
		if (!isset($result->success) || !$result->success) {
			return false;
		}

		// Check score (v3 only)
		if (isset($result->score) && $result->score < $recaptcha_threshold) {
			return false;
		}

		// Check action (optional but recommended)
		if (isset($result->action) && 'booking_submit' !== $result->action) {
			return false;
		}

		return true;
	}

	/**
	 * Check availability for a hall on a specific date or multiple dates
	 */
	public function check_availability()
	{
		// Verify nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shb_frontend_nonce')) {
			wp_send_json_error(
				array(
					'message' => __('Security verification failed.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Get parameters
		$hall_id = isset($_POST['hall_id']) ? absint($_POST['hall_id']) : 0;
		$date = isset($_POST['date']) ? sanitize_text_field(wp_unslash($_POST['date'])) : '';
		$dates = isset($_POST['dates']) && is_array($_POST['dates']) ? array_map('sanitize_text_field', array_map('wp_unslash', $_POST['dates'])) : array();

		// If multiple dates provided, handle batch request
		if (!empty($dates)) {
			$this->check_batch_availability($hall_id, $dates);
			return;
		}

		// Validate inputs for single date
		if (!$hall_id || !$date) {
			wp_send_json_error(
				array(
					'message' => __('Invalid parameters. Please select both hall and date.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Validate date format
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
			wp_send_json_error(
				array(
					'message' => __('Invalid date format.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Check if date is in the past
		if (strtotime($date) < strtotime('today')) {
			wp_send_json_error(
				array(
					'message' => __('Cannot book dates in the past.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Get available slots
		$db = shb()->db;
		$available_slots = $db->get_available_slots($hall_id, $date);

		if (empty($available_slots)) {
			wp_send_json_success(
				array(
					'slots' => array(),
					'message' => __('No slots available for this date.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Format slots for response
		$slots = array();
		foreach ($available_slots as $slot) {
			$slots[] = array(
				'id' => $slot->id,
				'label' => $slot->label,
				'type' => $slot->slot_type,
				'start_time' => wp_date('g:i A', strtotime($slot->start_time)),
				'end_time' => wp_date('g:i A', strtotime($slot->end_time)),
			);
		}

		wp_send_json_success(
			array(
				'slots' => $slots,
			)
		);
	}

	/**
	 * Check availability for multiple dates (batch request)
	 */
	private function check_batch_availability($hall_id, $dates)
	{
		// Validate hall_id
		if (!$hall_id) {
			wp_send_json_error(
				array(
					'message' => __('Invalid hall ID.', 'simple-hall-booking-manager'),
				)
			);
		}

		$db = shb()->db;
		$dates_response = array();

		foreach ($dates as $date) {
			// Validate date format
			if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
				continue;
			}

			// Skip past dates
			if (strtotime($date) < strtotime('today')) {
				continue;
			}

			// Get available slots
			$available_slots = $db->get_available_slots($hall_id, $date);

			// Format slots for response
			$slots = array();
			foreach ($available_slots as $slot) {
				$slots[] = array(
					'id' => $slot->id,
					'label' => $slot->label,
					'type' => $slot->slot_type,
					'start_time' => wp_date('g:i A', strtotime($slot->start_time)),
					'end_time' => wp_date('g:i A', strtotime($slot->end_time)),
				);
			}

			$dates_response[$date] = array(
				'available_slots' => $slots,
				'slots_count' => count($slots),
			);
		}

		wp_send_json_success(
			array(
				'dates' => $dates_response,
			)
		);
	}

	/**
	 * Check multi-day availability for a hall and slot
	 */
	public function check_multiday_availability()
	{
		// Verify nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shb_frontend_nonce')) {
			wp_send_json_error(
				array(
					'message' => __('Security verification failed.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Get parameters
		$hall_id = isset($_POST['hall_id']) ? absint($_POST['hall_id']) : 0;
		$slot_id = isset($_POST['slot_id']) ? absint($_POST['slot_id']) : 0;
		$dates = isset($_POST['dates']) && is_array($_POST['dates']) ? array_map('sanitize_text_field', $_POST['dates']) : array();

		// Validate inputs
		if (!$hall_id || !$slot_id || empty($dates)) {
			wp_send_json_error(
				array(
					'message' => __('Invalid parameters.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Validate each date format and check not in past
		foreach ($dates as $date) {
			if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
				wp_send_json_error(
					array(
						'message' => __('Invalid date format.', 'simple-hall-booking-manager'),
					)
				);
			}

			if (strtotime($date) < strtotime('today')) {
				wp_send_json_error(
					array(
						'message' => __('Cannot book dates in the past.', 'simple-hall-booking-manager'),
					)
				);
			}
		}

		// Check availability
		$db = shb()->db;
		$unavailable_dates = $db->check_multiday_availability($hall_id, $slot_id, $dates);

		if (!empty($unavailable_dates)) {
			wp_send_json_error(
				array(
					'available' => false,
					'unavailable_dates' => $unavailable_dates,
					'message' => sprintf(
						/* translators: %s: number of unavailable dates */
						_n(
							'%s date is not available.',
							'%s dates are not available.',
							count($unavailable_dates),
							'simple-hall-booking-manager'
						),
						count($unavailable_dates)
					),
				)
			);
		}

		wp_send_json_success(
			array(
				'available' => true,
				'message' => __('All selected dates are available!', 'simple-hall-booking-manager'),
			)
		);
	}

	/**
	 * Submit a booking
	 */
	public function submit_booking()
	{
		// Log for debugging (Removed for production)

		// Verify nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shb_frontend_nonce')) {
			// Nonce failed
			wp_send_json_error(
				array(
					'message' => __('Security verification failed. Please refresh the page and try again.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Verify reCAPTCHA if enabled
		$recaptcha_token = isset($_POST['recaptcha_token']) ? sanitize_text_field(wp_unslash($_POST['recaptcha_token'])) : '';
		if (!$this->verify_recaptcha($recaptcha_token)) {
			// reCAPTCHA failed
			wp_send_json_error(
				array(
					'message' => __('Spam protection verification failed. Please try again or contact us if the problem persists.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Get and validate parameters
		$hall_id = isset($_POST['hall_id']) ? absint($_POST['hall_id']) : 0;
		$slot_id = isset($_POST['slot_id']) ? absint($_POST['slot_id']) : 0;
		$booking_date = isset($_POST['booking_date']) ? sanitize_text_field(wp_unslash($_POST['booking_date'])) : '';
		$date_slots = isset($_POST['date_slots']) && is_array($_POST['date_slots']) ? array_map('sanitize_text_field', array_map('wp_unslash', $_POST['date_slots'])) : array();
		$is_multiday = !empty($date_slots);
		$customer_name = isset($_POST['customer_name']) ? sanitize_text_field(wp_unslash($_POST['customer_name'])) : '';
		$customer_email = isset($_POST['customer_email']) ? sanitize_email(wp_unslash($_POST['customer_email'])) : '';
		$customer_phone = isset($_POST['customer_phone']) ? sanitize_text_field(wp_unslash($_POST['customer_phone'])) : '';
		$customer_organization = isset($_POST['customer_organization']) ? sanitize_text_field(wp_unslash($_POST['customer_organization'])) : '';
		$event_purpose = isset($_POST['event_purpose']) ? sanitize_text_field(wp_unslash($_POST['event_purpose'])) : '';
		$attendees_count = isset($_POST['attendees_count']) ? absint($_POST['attendees_count']) : 0;

		// For multiday, extract dates and validate
		$booking_dates = array();
		if ($is_multiday) {
			foreach ($date_slots as $date => $slot) {
				$booking_dates[] = sanitize_text_field($date);
			}
		}


		// Validate required fields
		$date_required = $is_multiday ? !empty($booking_dates) : !empty($booking_date);
		$slot_required = $is_multiday ? !empty($date_slots) : !empty($slot_id);
		if (!$hall_id || !$slot_required || !$date_required || !$customer_name || !$customer_email) {
			$missing_fields = array();
			if (!$hall_id) {
				$missing_fields[] = __('Hall', 'simple-hall-booking-manager');
			}
			if (!$slot_id) {
				$missing_fields[] = __('Time Slot', 'simple-hall-booking-manager');
			}
			if (!$booking_date) {
				$missing_fields[] = __('Booking Date', 'simple-hall-booking-manager');
			}
			if (!$customer_name) {
				$missing_fields[] = __('Name', 'simple-hall-booking-manager');
			}
			if (!$customer_email) {
				$missing_fields[] = __('Email', 'simple-hall-booking-manager');
			}

			$error_message = sprintf(
				/* translators: %s: comma-separated list of missing fields */
				__('Please fill in the following required fields: %s', 'simple-hall-booking-manager'),
				implode(', ', $missing_fields)
			);


			wp_send_json_error(
				array(
					'message' => $error_message,
				)
			);
		}

		// Validate email
		if (!is_email($customer_email)) {
			wp_send_json_error(
				array(
					'message' => __('Please provide a valid email address.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Validate date format(s)
		if ($is_multiday) {
			// Validate each date in array
			foreach ($booking_dates as $date) {
				if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
					wp_send_json_error(
						array(
							'message' => __('Invalid date format.', 'simple-hall-booking-manager'),
						)
					);
				}

				if (strtotime($date) < strtotime('today')) {
					wp_send_json_error(
						array(
							'message' => __('Cannot book dates in the past.', 'simple-hall-booking-manager'),
						)
					);
				}
			}
		} else {
			// Validate single date
			if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $booking_date)) {
				wp_send_json_error(
					array(
						'message' => __('Invalid date format.', 'simple-hall-booking-manager'),
					)
				);
			}

			if (strtotime($booking_date) < strtotime('today')) {
				wp_send_json_error(
					array(
						'message' => __('Cannot book dates in the past.', 'simple-hall-booking-manager'),
					)
				);
			}
		}

		// Validate phone number (if provided)
		if ($customer_phone && !shb_is_valid_phone($customer_phone)) {
			wp_send_json_error(
				array(
					'message' => __('Please provide a valid phone number.', 'simple-hall-booking-manager'),
				)
			);
		}

		$db = shb()->db;

		// Verify hall exists
		$hall = $db->get_hall($hall_id);
		if (!$hall || 'active' !== $hall->status) {
			wp_send_json_error(
				array(
					'message' => __('Selected hall is not available.', 'simple-hall-booking-manager'),
				)
			);
		}

		// Verify slots exist and belong to the hall
		if ($is_multiday) {
			// Validate each slot in the date_slots array
			foreach ($date_slots as $date => $s_id) {
				$s_id = absint($s_id);
				$slot = $db->get_slot($s_id);


				if (!$slot) {
					wp_send_json_error(
						array(
							'message' => sprintf(
								/* translators: %s: date */
								__('Selected time slot for %s does not exist. Please refresh and try again.', 'simple-hall-booking-manager'),
								wp_date('F j, Y', strtotime($date))
							),
						)
					);
				}

				if (absint($slot->hall_id) !== absint($hall_id)) {
					wp_send_json_error(
						array(
							'message' => __('One or more selected time slots do not belong to the selected hall. Please refresh and try again.', 'simple-hall-booking-manager'),
						)
					);
				}

				if (!$slot->is_active) {
					wp_send_json_error(
						array(
							'message' => sprintf(
								/* translators: %s: date */
								__('Selected time slot for %s is currently inactive. Please choose another slot.', 'simple-hall-booking-manager'),
								wp_date('F j, Y', strtotime($date))
							),
						)
					);
				}
			}
		} else {
			// Verify single slot for single-day booking
			$slot = $db->get_slot($slot_id);


			if (!$slot) {
				wp_send_json_error(
					array(
						'message' => __('Selected time slot does not exist. Please refresh and try again.', 'simple-hall-booking-manager'),
					)
				);
			}

			// Convert both to integers for comparison
			if (absint($slot->hall_id) !== absint($hall_id)) {
				wp_send_json_error(
					array(
						'message' => __('Selected time slot does not belong to the selected hall. Please refresh and try again.', 'simple-hall-booking-manager'),
					)
				);
			}

			if (!$slot->is_active) {
				wp_send_json_error(
					array(
						'message' => __('Selected time slot is currently inactive. Please choose another slot.', 'simple-hall-booking-manager'),
					)
				);
			}
		}

		// Re-check availability
		if ($is_multiday) {
			// Check availability for each date with its specific slot
			$unavailable_dates = array();
			foreach ($date_slots as $date => $slot) {
				$date = sanitize_text_field($date);
				$slot = absint($slot);

				if (!$db->is_slot_available($hall_id, $slot, $date)) {
					$unavailable_dates[] = $date;
				}
			}

			if (!empty($unavailable_dates)) {
				wp_send_json_error(
					array(
						'message' => sprintf(
							/* translators: %s: comma-separated list of unavailable dates */
							__('Sorry, the following dates are no longer available: %s', 'simple-hall-booking-manager'),
							implode(', ', array_map(function ($date) {
								return wp_date('F j, Y', strtotime($date));
							}, $unavailable_dates))
						),
						'unavailable_dates' => $unavailable_dates,
					)
				);
			}
		} else {
			// Check single date availability
			if (!$db->is_slot_available($hall_id, $slot_id, $booking_date)) {
				wp_send_json_error(
					array(
						'message' => __('Sorry, this slot is no longer available. Please select another slot.', 'simple-hall-booking-manager'),
					)
				);
			}
		}

		// Create booking
		$booking_data = array(
			'hall_id' => $hall_id,
			'customer_name' => $customer_name,
			'customer_email' => $customer_email,
			'customer_phone' => $customer_phone,
			'customer_organization' => $customer_organization,
			'event_purpose' => $event_purpose,
			'attendees_count' => $attendees_count,
			'status' => 'pending',
		);

		if ($is_multiday) {
			// Create multi-day booking with different slots per date
			$booking_id = $db->create_multiday_booking_with_slots($booking_data, $date_slots);

			if (!$booking_id) {
				wp_send_json_error(
					array(
						'message' => __('Failed to create multi-day booking. Please try again.', 'simple-hall-booking-manager'),
					)
				);
			}
		} else {
			// Create single-day booking
			// Now we need to create the booking and then add the date+slot to booking_dates
			$booking_id = $db->create_booking_with_token($booking_data);

			if (!$booking_id) {
				wp_send_json_error(
					array(
						'message' => __('Failed to create booking. Please try again.', 'simple-hall-booking-manager'),
					)
				);
			}

			// Insert the single date and slot into booking_dates table
			$date_result = $db->insert_booking_date(
				array(
					'booking_id' => $booking_id,
					'booking_date' => $booking_date,
					'slot_id' => $slot_id,
				)
			);

			if (!$date_result) {
				// Rollback - delete the booking
				$db->delete_booking($booking_id);
				wp_send_json_error(
					array(
						'message' => __('Failed to create booking. Please try again.', 'simple-hall-booking-manager'),
					)
				);
			}
		}

		// Send email notifications
		$emails = shb()->emails;
		$emails->send_admin_new_booking($booking_id);
		$emails->send_guest_pending($booking_id);

		// Get booking details for response
		$booking = $db->get_booking($booking_id);

		// Check for custom confirmation page
		$general_settings = get_option('shb_general_settings', array());
		$confirmation_page_id = isset($general_settings['confirmation_page']) ? absint($general_settings['confirmation_page']) : 0;
		$redirect_url = '';

		if ($confirmation_page_id) {
			$redirect_url = get_permalink($confirmation_page_id);
			if ($redirect_url) {
				$redirect_url = add_query_arg('token', $booking->access_token, $redirect_url);
			}
		}

		wp_send_json_success(
			array(
				'message' => __('Booking submitted successfully! Please check your email for confirmation.', 'simple-hall-booking-manager'),
				'booking_id' => $booking_id,
				'access_token' => $booking->access_token,
				'access_url' => shb_get_booking_access_url($booking->access_token),
				'redirect_url' => $redirect_url,
			)
		);
	}

	/**
	 * Get slot details for popup
	 */
	public function get_slot()
	{
		// Verify nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shb_admin_nonce')) {
			wp_send_json_error(array('message' => __('Security verification failed.', 'simple-hall-booking-manager')));
		}

		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('Permission denied.', 'simple-hall-booking-manager')));
		}

		$slot_id = isset($_POST['slot_id']) ? absint($_POST['slot_id']) : 0;
		$hall_id = isset($_POST['hall_id']) ? absint($_POST['hall_id']) : 0;
		$slot_type_req = isset($_POST['slot_type']) ? sanitize_text_field(wp_unslash($_POST['slot_type'])) : 'partial';

		$db = shb()->db;
		$slot = null;

		if ($slot_id) {
			$slot = $db->get_slot($slot_id);
		}

		// Defaults for new slot
		$data = array(
			'slot_id' => 0,
			'hall_id' => $hall_id,
			'slot_type' => $slot_type_req,
			'label' => '',
			'start_time' => '',
			'end_time' => '',
			'days_enabled' => array('0', '1', '2', '3', '4', '5', '6'),
			'is_active' => 1,
			'is_new' => true,
			'modal_title' => __('Add Slot', 'simple-hall-booking-manager')
		);

		if ($slot) {
			$data['slot_id'] = $slot->id;
			$data['hall_id'] = $slot->hall_id;
			$data['slot_type'] = $slot->slot_type;
			$data['label'] = $slot->label;
			$data['start_time'] = substr($slot->start_time, 0, 5);
			$data['end_time'] = substr($slot->end_time, 0, 5);
			$data['days_enabled'] = json_decode($slot->days_enabled);
			$data['is_active'] = (int) $slot->is_active;
			$data['is_new'] = false;
			$data['modal_title'] = __('Edit Slot', 'simple-hall-booking-manager');
		}

		// Check if hall has full day slot
		$full_day_slots = $db->get_slots_by_hall($hall_id, array('slot_type' => 'full_day'));
		$data['has_full_day_slot'] = !empty($full_day_slots);

		// Render form HTML
		ob_start();
		extract($data);
		include SHB_PLUGIN_DIR . 'admin/views/partials/_slot-form.php';
		$html = ob_get_clean();

		$data['html'] = $html;

		wp_send_json_success($data);
	}

	/**
	 * AJAX handler to check slot overlap
	 */
	public function check_slot_overlap()
	{
		// Verify nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shb_admin_nonce')) {
			wp_send_json_error(array('message' => __('Security verification failed.', 'simple-hall-booking-manager')));
		}

		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('Permission denied.', 'simple-hall-booking-manager')));
		}

		$hall_id = isset($_POST['hall_id']) ? absint($_POST['hall_id']) : 0;
		$slot_id = isset($_POST['slot_id']) ? absint($_POST['slot_id']) : 0;
		$start_time = isset($_POST['start_time']) ? sanitize_text_field(wp_unslash($_POST['start_time'])) : '';
		$end_time = isset($_POST['end_time']) ? sanitize_text_field(wp_unslash($_POST['end_time'])) : '';

		if (!$hall_id || !$start_time || !$end_time) {
			wp_send_json_error(array('message' => __('Missing required fields.', 'simple-hall-booking-manager')));
		}

		// Ensure time format includes seconds for DB check
		if (strlen($start_time) === 5) {
			$start_time .= ':00';
		}
		if (strlen($end_time) === 5) {
			$end_time .= ':00';
		}

		$db = shb()->db;
		$has_overlap = $db->check_slot_overlap($hall_id, $start_time, $end_time, $slot_id);

		if ($has_overlap) {
			wp_send_json_error(array(
				'message' => __('This time slot overlaps with an existing slot.', 'simple-hall-booking-manager')
			));
		} else {
			wp_send_json_success(array(
				'message' => __('No overlap found. You can use this time slot.', 'simple-hall-booking-manager')
			));
		}
	}
}

