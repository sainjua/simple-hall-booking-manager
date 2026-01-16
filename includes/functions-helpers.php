<?php
/**
 * Helper functions
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Get plugin instance
 *
 * @return SHB_Plugin
 */
function shb()
{
	return SHB_Plugin::get_instance();
}

/**
 * Generate a secure random token
 *
 * @param int $length Token length.
 * @return string
 */
function shb_generate_token($length = 32)
{
	return bin2hex(random_bytes($length));
}

/**
 * Format date for display
 *
 * @param string $date Date string.
 * @param string $format Date format.
 * @return string
 */
function shb_format_date($date, $format = null)
{
	if (null === $format) {
		$format = get_option('date_format');
	}
	return date_i18n($format, strtotime($date));
}

/**
 * Format time for display
 *
 * @param string $time Time string.
 * @param string $format Time format.
 * @return string
 */
function shb_format_time($time, $format = null)
{
	if (null === $format) {
		$format = get_option('time_format');
	}
	return date_i18n($format, strtotime($time));
}

/**
 * Get booking status label
 *
 * @param string $status Booking status.
 * @return string
 */
function shb_get_status_label($status)
{
	$statuses = array(
		'pending' => __('Pending', 'simple-hall-booking-manager'),
		'confirmed' => __('Confirmed', 'simple-hall-booking-manager'),
		'cancelled' => __('Cancelled', 'simple-hall-booking-manager'),
	);

	return isset($statuses[$status]) ? $statuses[$status] : $status;
}

/**
 * Get all booking statuses
 *
 * @return array
 */
function shb_get_booking_statuses()
{
	return array(
		'pending' => __('Pending', 'simple-hall-booking-manager'),
		'confirmed' => __('Confirmed', 'simple-hall-booking-manager'),
		'cancelled' => __('Cancelled', 'simple-hall-booking-manager'),
	);
}

/**
 * Get slot type label
 *
 * @param string $type Slot type.
 * @return string
 */
function shb_get_slot_type_label($type)
{
	$types = array(
		'full_day' => __('Full Day', 'simple-hall-booking-manager'),
		'partial' => __('Partial', 'simple-hall-booking-manager'),
	);

	return isset($types[$type]) ? $types[$type] : $type;
}

/**
 * Sanitize slot label
 *
 * @param string $label Slot label.
 * @return string
 */
function shb_sanitize_slot_label($label)
{
	return sanitize_text_field($label);
}

/**
 * Validate email address
 *
 * @param string $email Email address.
 * @return bool
 */
function shb_is_valid_email($email)
{
	return is_email($email);
}

/**
 * Validate phone number (basic)
 *
 * @param string $phone Phone number.
 * @return bool
 */
function shb_is_valid_phone($phone)
{
	// Basic validation - you can make this more sophisticated
	return preg_match('/^[0-9\s\+\-\(\)]+$/', $phone);
}

/**
 * Get access token URL for a booking
 *
 * @param string $token Access token.
 * @param int    $page_id Page ID containing the [shb_user_bookings] shortcode.
 * @return string
 */
function shb_get_booking_access_url($token, $page_id = null)
{
	if (null === $page_id) {
		// Try to find a page with the shortcode
		$pages = get_posts(
			array(
				'post_type' => 'page',
				'post_status' => 'publish',
				's' => '[shb_user_bookings]',
				'numberposts' => 1,
			)
		);

		if (!empty($pages)) {
			$page_id = $pages[0]->ID;
		}
	}

	if ($page_id) {
		return add_query_arg('token', $token, get_permalink($page_id));
	}

	return home_url('?token=' . $token);
}



/**
 * Get a single slot
 *
 * @param int $id Slot ID.
 * @return object|null
 */
function shb_get_slot($id)
{
	return shb()->db->get_slot($id);
}

/**
 * Get a single hall
 *
 * @param int $id Hall ID.
 * @return object|null
 */
function shb_get_hall($id)
{
	return shb()->db->get_hall($id);
}

