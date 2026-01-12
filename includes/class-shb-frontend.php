<?php
/**
 * Frontend handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend class
 */
class SHB_Frontend {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_assets() {
		// Only enqueue if shortcode is present (we'll check in shortcode class)
		global $post;
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		$has_shortcode = has_shortcode( $post->post_content, 'shb_hall_list' ) ||
						has_shortcode( $post->post_content, 'shb_booking_form' ) ||
						has_shortcode( $post->post_content, 'shb_user_bookings' );

		if ( ! $has_shortcode ) {
			return;
		}

		// Get reCAPTCHA settings
		$general_settings = get_option( 'shb_general_settings', array() );
		$recaptcha_enabled = isset( $general_settings['recaptcha_enabled'] ) ? $general_settings['recaptcha_enabled'] : 'no';
		$recaptcha_site_key = isset( $general_settings['recaptcha_site_key'] ) ? $general_settings['recaptcha_site_key'] : '';

		// Enqueue Google reCAPTCHA if enabled and has booking form
		if ( 'yes' === $recaptcha_enabled && ! empty( $recaptcha_site_key ) && has_shortcode( $post->post_content, 'shb_booking_form' ) ) {
			wp_enqueue_script(
				'google-recaptcha',
				'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $recaptcha_site_key ),
				array(),
				null,
				true
			);
		}

		// CSS
		wp_enqueue_style(
			'shb-frontend-css',
			SHB_PLUGIN_URL . 'public/css/shb-frontend.css',
			array(),
			SHB_VERSION
		);

		// JS
		wp_enqueue_script(
			'shb-frontend-js',
			SHB_PLUGIN_URL . 'public/js/shb-frontend.js',
			array( 'jquery' ),
			SHB_VERSION,
			true
		);

		// Localize script
		wp_localize_script(
			'shb-frontend-js',
			'shbFrontend',
			array(
				'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
				'nonce'             => wp_create_nonce( 'shb_frontend_nonce' ),
				'recaptchaEnabled'  => $recaptcha_enabled,
				'recaptchaSiteKey'  => $recaptcha_site_key,
				'i18n'              => array(
					'loading'          => __( 'Loading...', 'simple-hall-booking-manager' ),
					'error'            => __( 'An error occurred. Please try again.', 'simple-hall-booking-manager' ),
					'selectSlot'       => __( 'Please select a time slot.', 'simple-hall-booking-manager' ),
					'confirmCancel'    => __( 'Are you sure you want to cancel this booking?', 'simple-hall-booking-manager' ),
					'noSlotsAvailable' => __( 'No slots available for this date.', 'simple-hall-booking-manager' ),
				),
			)
		);
	}
}

