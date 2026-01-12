<?php
/**
 * Admin view: Settings
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_settings   = get_option( 'shb_email_settings', array() );
$general_settings = get_option( 'shb_general_settings', array() );

$from_name  = isset( $email_settings['from_name'] ) ? $email_settings['from_name'] : get_bloginfo( 'name' );
$from_email = isset( $email_settings['from_email'] ) ? $email_settings['from_email'] : get_bloginfo( 'admin_email' );
$admin_email = isset( $email_settings['admin_email'] ) ? $email_settings['admin_email'] : get_bloginfo( 'admin_email' );

$delete_data = isset( $general_settings['delete_data_on_uninstall'] ) ? $general_settings['delete_data_on_uninstall'] : 0;
$date_format = isset( $general_settings['date_format'] ) ? $general_settings['date_format'] : 'Y-m-d';
$time_format = isset( $general_settings['time_format'] ) ? $general_settings['time_format'] : 'H:i';
$confirmation_page = isset( $general_settings['confirmation_page'] ) ? $general_settings['confirmation_page'] : '';
$recaptcha_enabled = isset( $general_settings['recaptcha_enabled'] ) ? $general_settings['recaptcha_enabled'] : 'no';
$recaptcha_site_key = isset( $general_settings['recaptcha_site_key'] ) ? $general_settings['recaptcha_site_key'] : '';
$recaptcha_secret_key = isset( $general_settings['recaptcha_secret_key'] ) ? $general_settings['recaptcha_secret_key'] : '';
$recaptcha_threshold = isset( $general_settings['recaptcha_threshold'] ) ? $general_settings['recaptcha_threshold'] : 0.5;

// Handle form submission
if ( isset( $_POST['shb_save_settings'] ) ) {
	check_admin_referer( 'shb_settings' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'simple-hall-booking-manager' ) );
	}

	// Save email settings
	$email_data = array(
		'from_name'   => sanitize_text_field( $_POST['from_name'] ),
		'from_email'  => sanitize_email( $_POST['from_email'] ),
		'admin_email' => sanitize_email( $_POST['admin_email'] ),
	);
	update_option( 'shb_email_settings', $email_data );

	// Save general settings
	$general_data = array(
		'delete_data_on_uninstall' => isset( $_POST['delete_data_on_uninstall'] ) ? 1 : 0,
		'date_format'              => sanitize_text_field( $_POST['date_format'] ),
		'time_format'              => sanitize_text_field( $_POST['time_format'] ),
		'confirmation_page'        => absint( $_POST['confirmation_page'] ),
		'recaptcha_enabled'        => isset( $_POST['recaptcha_enabled'] ) ? 'yes' : 'no',
		'recaptcha_site_key'       => sanitize_text_field( $_POST['recaptcha_site_key'] ),
		'recaptcha_secret_key'     => sanitize_text_field( $_POST['recaptcha_secret_key'] ),
		'recaptcha_threshold'      => floatval( $_POST['recaptcha_threshold'] ),
	);
	update_option( 'shb_general_settings', $general_data );

	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'simple-hall-booking-manager' ) . '</p></div>';

	// Refresh values
	$from_name   = $email_data['from_name'];
	$from_email  = $email_data['from_email'];
	$admin_email = $email_data['admin_email'];
	$delete_data = $general_data['delete_data_on_uninstall'];
	$date_format = $general_data['date_format'];
	$time_format = $general_data['time_format'];
	$confirmation_page = $general_data['confirmation_page'];
	$recaptcha_enabled = $general_data['recaptcha_enabled'];
	$recaptcha_site_key = $general_data['recaptcha_site_key'];
	$recaptcha_secret_key = $general_data['recaptcha_secret_key'];
	$recaptcha_threshold = $general_data['recaptcha_threshold'];
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Hall Booking Settings', 'simple-hall-booking-manager' ); ?></h1>

	<form method="post" action="">
		<?php wp_nonce_field( 'shb_settings' ); ?>

		<h2><?php esc_html_e( 'Email Settings', 'simple-hall-booking-manager' ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="from_name"><?php esc_html_e( 'From Name', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<input type="text" name="from_name" id="from_name" value="<?php echo esc_attr( $from_name ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'The name that appears in outgoing emails', 'simple-hall-booking-manager' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="from_email"><?php esc_html_e( 'From Email', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<input type="email" name="from_email" id="from_email" value="<?php echo esc_attr( $from_email ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'The email address that appears in outgoing emails', 'simple-hall-booking-manager' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="admin_email"><?php esc_html_e( 'Admin Notification Email', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<input type="email" name="admin_email" id="admin_email" value="<?php echo esc_attr( $admin_email ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'Email address to receive booking notifications', 'simple-hall-booking-manager' ); ?></p>
				</td>
			</tr>
		</table>

		<h2><?php esc_html_e( 'General Settings', 'simple-hall-booking-manager' ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="date_format"><?php esc_html_e( 'Date Format', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<input type="text" name="date_format" id="date_format" value="<?php echo esc_attr( $date_format ); ?>" class="regular-text">
					<p class="description">
						<?php
						printf(
							/* translators: %s: PHP date format documentation URL */
							esc_html__( 'PHP date format. See %s', 'simple-hall-booking-manager' ),
							'<a href="https://www.php.net/manual/en/datetime.format.php" target="_blank">documentation</a>'
						);
						?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="time_format"><?php esc_html_e( 'Time Format', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<input type="text" name="time_format" id="time_format" value="<?php echo esc_attr( $time_format ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'PHP time format (e.g., H:i for 24-hour, g:i A for 12-hour)', 'simple-hall-booking-manager' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="confirmation_page"><?php esc_html_e( 'Booking Confirmation Page', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<?php
					wp_dropdown_pages(
						array(
							'name'             => 'confirmation_page',
							'id'               => 'confirmation_page',
							'selected'         => $confirmation_page,
							'show_option_none' => __( '— Select Page —', 'simple-hall-booking-manager' ),
							'option_none_value' => '',
						)
					);
					?>
					<p class="description">
						<?php esc_html_e( 'Select the page where customers can view their booking details. Add the shortcode [shb_user_bookings] to this page.', 'simple-hall-booking-manager' ); ?>
						<?php if ( $confirmation_page ) : ?>
							<br>
							<a href="<?php echo esc_url( get_permalink( $confirmation_page ) ); ?>" target="_blank">
								<?php esc_html_e( 'View page', 'simple-hall-booking-manager' ); ?> →
							</a>
						<?php endif; ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Data Management', 'simple-hall-booking-manager' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="delete_data_on_uninstall" value="1" <?php checked( $delete_data, 1 ); ?>>
						<?php esc_html_e( 'Delete all plugin data when uninstalling', 'simple-hall-booking-manager' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Warning: This will permanently delete all halls, slots, and bookings when you uninstall the plugin.', 'simple-hall-booking-manager' ); ?></p>
				</td>
			</tr>
		</table>

		<h2><?php esc_html_e( 'Google reCAPTCHA v3 (Spam Protection)', 'simple-hall-booking-manager' ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable reCAPTCHA', 'simple-hall-booking-manager' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="recaptcha_enabled" value="yes" <?php checked( $recaptcha_enabled, 'yes' ); ?>>
						<?php esc_html_e( 'Enable Google reCAPTCHA v3 on booking forms', 'simple-hall-booking-manager' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'Protect your booking forms from spam and bot submissions. Get your keys from', 'simple-hall-booking-manager' ); ?>
						<a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="recaptcha_site_key"><?php esc_html_e( 'Site Key', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<input type="text" name="recaptcha_site_key" id="recaptcha_site_key" value="<?php echo esc_attr( $recaptcha_site_key ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'Your reCAPTCHA v3 Site Key (public key)', 'simple-hall-booking-manager' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="recaptcha_secret_key"><?php esc_html_e( 'Secret Key', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<input type="text" name="recaptcha_secret_key" id="recaptcha_secret_key" value="<?php echo esc_attr( $recaptcha_secret_key ); ?>" class="regular-text">
					<p class="description"><?php esc_html_e( 'Your reCAPTCHA v3 Secret Key (private key)', 'simple-hall-booking-manager' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="recaptcha_threshold"><?php esc_html_e( 'Score Threshold', 'simple-hall-booking-manager' ); ?></label>
				</th>
				<td>
					<input type="number" name="recaptcha_threshold" id="recaptcha_threshold" value="<?php echo esc_attr( $recaptcha_threshold ); ?>" min="0" max="1" step="0.1" class="small-text">
					<p class="description">
						<?php esc_html_e( 'Minimum score to accept (0.0 - 1.0). Default: 0.5. Lower = more strict, Higher = more lenient.', 'simple-hall-booking-manager' ); ?>
						<br>
						<?php esc_html_e( '1.0 = Very likely human, 0.0 = Very likely bot', 'simple-hall-booking-manager' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<h2><?php esc_html_e( 'Shortcodes', 'simple-hall-booking-manager' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Available Shortcodes', 'simple-hall-booking-manager' ); ?></th>
				<td>
					<p><code>[shb_hall_list columns="3"]</code> - <?php esc_html_e( 'Display a list of available halls', 'simple-hall-booking-manager' ); ?></p>
					<p><code>[shb_booking_form hall_id="123"]</code> - <?php esc_html_e( 'Display booking form (hall_id is optional)', 'simple-hall-booking-manager' ); ?></p>
					<p><code>[shb_user_bookings]</code> - <?php esc_html_e( 'Display booking details for guests (uses token from URL)', 'simple-hall-booking-manager' ); ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="shb_save_settings" class="button button-primary" value="<?php esc_attr_e( 'Save Settings', 'simple-hall-booking-manager' ); ?>">
		</p>
	</form>
</div>

