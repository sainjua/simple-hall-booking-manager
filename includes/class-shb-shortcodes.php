<?php
/**
 * Shortcodes handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcodes class
 */
class SHB_Shortcodes {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'shb_hall_list', array( $this, 'render_hall_list' ) );
		add_shortcode( 'shb_booking_form', array( $this, 'render_booking_form' ) );
		add_shortcode( 'shb_user_bookings', array( $this, 'render_user_bookings' ) );
	}

	/**
	 * Render hall list shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_hall_list( $atts ) {
		$atts = shortcode_atts(
			array(
				'columns' => 3,
			),
			$atts,
			'shb_hall_list'
		);

		ob_start();
		include SHB_PLUGIN_DIR . 'public/partials/hall-list.php';
		return ob_get_clean();
	}

	/**
	 * Render booking form shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_booking_form( $atts ) {
		$atts = shortcode_atts(
			array(
				'hall_id' => '',
			),
			$atts,
			'shb_booking_form'
		);

		ob_start();
		include SHB_PLUGIN_DIR . 'public/partials/booking-form.php';
		return ob_get_clean();
	}

	/**
	 * Render user bookings shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_user_bookings( $atts ) {
		ob_start();
		include SHB_PLUGIN_DIR . 'public/partials/user-booking.php';
		return ob_get_clean();
	}
}

