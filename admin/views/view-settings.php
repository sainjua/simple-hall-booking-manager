<?php
/**
 * Admin view: Settings
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

$email_settings = get_option('shb_email_settings', array());
$general_settings = get_option('shb_general_settings', array());

// General Settings Defaults
$delete_data = isset($general_settings['delete_data_on_uninstall']) ? $general_settings['delete_data_on_uninstall'] : 0;
$date_format = isset($general_settings['date_format']) ? $general_settings['date_format'] : 'Y-m-d';
$time_format = isset($general_settings['time_format']) ? $general_settings['time_format'] : 'H:i';
$confirmation_page = isset($general_settings['confirmation_page']) ? $general_settings['confirmation_page'] : '';
$recaptcha_enabled = isset($general_settings['recaptcha_enabled']) ? $general_settings['recaptcha_enabled'] : 'no';
$recaptcha_site_key = isset($general_settings['recaptcha_site_key']) ? $general_settings['recaptcha_site_key'] : '';
$recaptcha_secret_key = isset($general_settings['recaptcha_secret_key']) ? $general_settings['recaptcha_secret_key'] : '';
$recaptcha_threshold = isset($general_settings['recaptcha_threshold']) ? $general_settings['recaptcha_threshold'] : 0.5;

// Email Configuration Defaults
$from_name = isset($email_settings['from_name']) ? $email_settings['from_name'] : get_bloginfo('name');
$from_email = isset($email_settings['from_email']) ? $email_settings['from_email'] : get_bloginfo('admin_email');
$admin_email = isset($email_settings['admin_email']) ? $email_settings['admin_email'] : get_bloginfo('admin_email');

// Email Templates Defaults
$admin_notification_subject = isset($email_settings['admin_notification_subject']) ? $email_settings['admin_notification_subject'] : __('[New Booking] Booking Request #{booking_id}', 'simple-hall-booking-manager');
$admin_notification_body = isset($email_settings['admin_notification_body']) ? $email_settings['admin_notification_body'] : '';

$guest_pending_subject = isset($email_settings['guest_pending_subject']) ? $email_settings['guest_pending_subject'] : __('Booking Request Received - #{booking_id}', 'simple-hall-booking-manager');
$guest_pending_body = isset($email_settings['guest_pending_body']) ? $email_settings['guest_pending_body'] : '';

$guest_confirmed_subject = isset($email_settings['guest_confirmed_subject']) ? $email_settings['guest_confirmed_subject'] : __('Booking Confirmed - #{booking_id}', 'simple-hall-booking-manager');
$guest_confirmed_body = isset($email_settings['guest_confirmed_body']) ? $email_settings['guest_confirmed_body'] : '';

$guest_cancelled_subject = isset($email_settings['guest_cancelled_subject']) ? $email_settings['guest_cancelled_subject'] : __('Booking Cancelled - #{booking_id}', 'simple-hall-booking-manager');
$guest_cancelled_body = isset($email_settings['guest_cancelled_body']) ? $email_settings['guest_cancelled_body'] : '';

$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

// Handle form submission
if (isset($_POST['shb_save_settings'])) {
	check_admin_referer('shb_settings');

	if (!current_user_can('manage_options')) {
		wp_die(esc_html__('You do not have permission to perform this action.', 'simple-hall-booking-manager'));
	}

	// Save email settings
	$email_data = $email_settings; // Start with existing

	if ('email_config' === $active_tab) {
		if (isset($_POST['from_name'])) {
			$email_data['from_name'] = sanitize_text_field($_POST['from_name']);
		}
		if (isset($_POST['from_email'])) {
			$email_data['from_email'] = sanitize_email($_POST['from_email']);
		}
		if (isset($_POST['admin_email'])) {
			$email_data['admin_email'] = sanitize_email($_POST['admin_email']);
		}
	}

	if ('notification_templates' === $active_tab) {
		if (isset($_POST['admin_notification_subject'])) {
			$email_data['admin_notification_subject'] = sanitize_text_field($_POST['admin_notification_subject']);
		}
		if (isset($_POST['admin_notification_body'])) {
			$email_data['admin_notification_body'] = wp_kses_post($_POST['admin_notification_body']);
		}

		if (isset($_POST['guest_pending_subject'])) {
			$email_data['guest_pending_subject'] = sanitize_text_field($_POST['guest_pending_subject']);
		}
		if (isset($_POST['guest_pending_body'])) {
			$email_data['guest_pending_body'] = wp_kses_post($_POST['guest_pending_body']);
		}

		if (isset($_POST['guest_confirmed_subject'])) {
			$email_data['guest_confirmed_subject'] = sanitize_text_field($_POST['guest_confirmed_subject']);
		}
		if (isset($_POST['guest_confirmed_body'])) {
			$email_data['guest_confirmed_body'] = wp_kses_post($_POST['guest_confirmed_body']);
		}

		if (isset($_POST['guest_cancelled_subject'])) {
			$email_data['guest_cancelled_subject'] = sanitize_text_field($_POST['guest_cancelled_subject']);
		}
		if (isset($_POST['guest_cancelled_body'])) {
			$email_data['guest_cancelled_body'] = wp_kses_post($_POST['guest_cancelled_body']);
		}
	}

	if (isset($_POST['shb_email_settings_nonce'])) { // Only update if we are on an email tab
		update_option('shb_email_settings', $email_data);
	}

	// Save general settings
	if ('general' === $active_tab) {
		$general_data = $general_settings; // Start with existing

		if (isset($_POST['delete_data_on_uninstall'])) {
			$general_data['delete_data_on_uninstall'] = 1;
		} else {
			$general_data['delete_data_on_uninstall'] = 0; // Checkbox unchecked
		}

		if (isset($_POST['date_format'])) {
			$general_data['date_format'] = sanitize_text_field($_POST['date_format']);
		}
		if (isset($_POST['time_format'])) {
			$general_data['time_format'] = sanitize_text_field($_POST['time_format']);
		}
		if (isset($_POST['confirmation_page'])) {
			$general_data['confirmation_page'] = absint($_POST['confirmation_page']);
		}

		$general_data['recaptcha_enabled'] = isset($_POST['recaptcha_enabled']) ? 'yes' : 'no';

		if (isset($_POST['recaptcha_site_key'])) {
			$general_data['recaptcha_site_key'] = sanitize_text_field($_POST['recaptcha_site_key']);
		}
		if (isset($_POST['recaptcha_secret_key'])) {
			$general_data['recaptcha_secret_key'] = sanitize_text_field($_POST['recaptcha_secret_key']);
		}
		if (isset($_POST['recaptcha_threshold'])) {
			$general_data['recaptcha_threshold'] = floatval($_POST['recaptcha_threshold']);
		}

		if (isset($_POST['shb_general_settings_nonce'])) {
			update_option('shb_general_settings', $general_data);
		}
	}

	// Refetch for display (Unified save for simplicity, though splitting update_option by tab check helps avoid overwrites if fields are missing)
	// Actually, preserving existing data is key if we are only saving one tab.
	// Let's refine the save logic above:
	// The form submits ALL fields if they are present. But with tabs, fields from other tabs might be missing.
	// Since we are using a single form wrapped around all content OR separate forms per tab?
	// The implementation below wraps the form around the specific tab content.
	// So we need to only update the valid option for that tab.

	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully.', 'simple-hall-booking-manager') . '</p></div>';

	// Refresh variables from updated options
	$email_settings = get_option('shb_email_settings', array());
	$general_settings = get_option('shb_general_settings', array());

	// Refresh locals
	extract(shortcode_atts(array(
		'from_name' => get_bloginfo('name'),
		'from_email' => get_bloginfo('admin_email'),
		'admin_email' => get_bloginfo('admin_email'),
		'admin_notification_subject' => '',
		'admin_notification_body' => '',
		'guest_pending_subject' => '',
		'guest_pending_body' => '',
		'guest_confirmed_subject' => '',
		'guest_confirmed_body' => '',
		'guest_cancelled_subject' => '',
		'guest_cancelled_body' => '',
	), $email_settings));

	extract(shortcode_atts(array(
		'delete_data_on_uninstall' => 0,
		'date_format' => 'Y-m-d',
		'time_format' => 'H:i',
		'confirmation_page' => '',
		'recaptcha_enabled' => 'no',
		'recaptcha_site_key' => '',
		'recaptcha_secret_key' => '',
		'recaptcha_threshold' => 0.5,
	), $general_settings));

	$delete_data = $delete_data_on_uninstall; // mapping fix
}
?>

<div class="wrap">
	<h1><?php esc_html_e('Hall Booking Settings', 'simple-hall-booking-manager'); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="?page=shb-settings&tab=general"
			class="nav-tab <?php echo 'general' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('General', 'simple-hall-booking-manager'); ?></a>
		<a href="?page=shb-settings&tab=email_config"
			class="nav-tab <?php echo 'email_config' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Email Configuration', 'simple-hall-booking-manager'); ?></a>
		<a href="?page=shb-settings&tab=notification_templates"
			class="nav-tab <?php echo 'notification_templates' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Notification Templates', 'simple-hall-booking-manager'); ?></a>
		<a href="?page=shb-settings&tab=shortcodes"
			class="nav-tab <?php echo 'shortcodes' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Shortcodes', 'simple-hall-booking-manager'); ?></a>
	</h2>

	<form method="post" action="">
		<?php wp_nonce_field('shb_settings'); ?>

		<?php if ('general' === $active_tab): ?>
			<input type="hidden" name="shb_general_settings_nonce" value="1">
			<h2><?php esc_html_e('General Settings', 'simple-hall-booking-manager'); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="date_format"><?php esc_html_e('Date Format', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<input type="text" name="date_format" id="date_format" value="<?php echo esc_attr($date_format); ?>"
							class="regular-text">
						<p class="description">
							<?php
							printf(
								/* translators: %s: PHP date format documentation URL */
								esc_html__('PHP date format. See %s', 'simple-hall-booking-manager'),
								'<a href="https://www.php.net/manual/en/datetime.format.php" target="_blank">documentation</a>'
							);
							?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="time_format"><?php esc_html_e('Time Format', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<input type="text" name="time_format" id="time_format" value="<?php echo esc_attr($time_format); ?>"
							class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label
							for="confirmation_page"><?php esc_html_e('Booking Confirmation Page', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<?php
						wp_dropdown_pages(
							array(
								'name' => 'confirmation_page',
								'id' => 'confirmation_page',
								'selected' => $confirmation_page,
								'show_option_none' => esc_html__('— Select Page —', 'simple-hall-booking-manager'),
								'option_none_value' => '',
							)
						);
						?>
						<p class="description">
							<?php esc_html_e('Page to redirect to after successful booking.', 'simple-hall-booking-manager'); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e('Data Management', 'simple-hall-booking-manager'); ?></th>
					<td>
						<label>
							<input type="checkbox" name="delete_data_on_uninstall" value="1" <?php checked($delete_data, 1); ?>>
							<?php esc_html_e('Delete all plugin data when uninstalling', 'simple-hall-booking-manager'); ?>
						</label>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e('Google reCAPTCHA v3', 'simple-hall-booking-manager'); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e('Enable reCAPTCHA', 'simple-hall-booking-manager'); ?></th>
					<td>
						<label>
							<input type="checkbox" name="recaptcha_enabled" value="yes" <?php checked($recaptcha_enabled, 'yes'); ?>>
							<?php esc_html_e('Enable on booking forms', 'simple-hall-booking-manager'); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label
							for="recaptcha_site_key"><?php esc_html_e('Site Key', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td><input type="text" name="recaptcha_site_key" id="recaptcha_site_key"
							value="<?php echo esc_attr($recaptcha_site_key); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label
							for="recaptcha_secret_key"><?php esc_html_e('Secret Key', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td><input type="text" name="recaptcha_secret_key" id="recaptcha_secret_key"
							value="<?php echo esc_attr($recaptcha_secret_key); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label
							for="recaptcha_threshold"><?php esc_html_e('Score Threshold', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td><input type="number" name="recaptcha_threshold" id="recaptcha_threshold"
							value="<?php echo esc_attr($recaptcha_threshold); ?>" min="0" max="1" step="0.1"
							class="small-text"></td>
				</tr>
			</table>

		<?php elseif ('email_config' === $active_tab): ?>
			<input type="hidden" name="shb_email_settings_nonce" value="1">
			<h2><?php esc_html_e('Email Configuration', 'simple-hall-booking-manager'); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="from_name"><?php esc_html_e('From Name', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<input type="text" name="from_name" id="from_name" value="<?php echo esc_attr($from_name); ?>"
							class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="from_email"><?php esc_html_e('From Email', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<input type="email" name="from_email" id="from_email" value="<?php echo esc_attr($from_email); ?>"
							class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label
							for="admin_email"><?php esc_html_e('Admin Notification Email', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<input type="email" name="admin_email" id="admin_email"
							value="<?php echo esc_attr($admin_email); ?>" class="regular-text">
					</td>
				</tr>
			</table>

		<?php elseif ('notification_templates' === $active_tab): ?>
			<input type="hidden" name="shb_email_settings_nonce" value="1">
			<h2><?php esc_html_e('Notification Templates', 'simple-hall-booking-manager'); ?></h2>
			<p class="description">
				<?php esc_html_e('Customize the email notifications sent to admins and guests. Use the placeholders below to insert dynamic content.', 'simple-hall-booking-manager'); ?>
			</p>

			<?php
			$placeholders = array(
				'{customer_name}' => 'Customer Name',
				'{booking_id}' => 'Booking ID',
				'{hall_title}' => 'Hall Title',
				'{booking_date}' => 'Booking Date',
				'{slot_time}' => 'Slot Time',
				'{status}' => 'Status',
				'{pin}' => 'PIN',
				'{access_url}' => 'Access URL',
				'{admin_email}' => 'Admin Email',
				'{customer_email}' => 'Customer Email',
				'{customer_phone}' => 'Customer Phone',
				'{event_purpose}' => 'Event Purpose',
				'{attendees_count}' => 'Attendees Count',
			);

			// Define Default Templates
			$defaults = array(
				'admin_notification' => '<h2>New Booking Request</h2>
<p>You have received a new booking request:</p>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Booking ID:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">#{booking_id}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Hall:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{hall_title}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Date:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{booking_date}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Time:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{slot_time}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Customer:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{customer_name}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Email:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><a href="mailto:{customer_email}">{customer_email}</a></td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Phone:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{customer_phone}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Event Purpose:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{event_purpose}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Attendees:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{attendees_count}</td></tr>
</table>
<p><a href="' . admin_url('admin.php?page=shb-bookings') . '" style="display: inline-block; padding: 10px 20px; background: #0073aa; color: #fff; text-decoration: none; border-radius: 3px;">Review Bookings</a></p>',

				'guest_pending' => '<h2>Booking Request Received</h2>
<p>Dear {customer_name},</p>
<p>Thank you for your booking request. We have received your request and will review it shortly.</p>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Booking ID:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">#{booking_id}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Hall:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{hall_title}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Date:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{booking_date}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Time:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{slot_time}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Status:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><strong style="color: #d63638;">Pending</strong></td></tr>
</table>
<p>You will receive another email once your booking has been confirmed.</p>
<div style="background: #f0f7ff; border: 2px solid #0073aa; border-radius: 5px; padding: 15px; margin: 20px 0;">
	<p style="margin: 0 0 10px 0;"><strong>Your Booking Access PIN:</strong></p>
	<p style="font-size: 32px; font-family: monospace; background: #fff; padding: 15px; border-radius: 3px; margin: 10px 0; letter-spacing: 4px; text-align: center; color: #0073aa; font-weight: bold;">{pin}</p>
	<p style="margin: 10px 0 0 0; font-size: 13px; color: #666; text-align: center;">Use this 6-digit PIN and your email to view your booking at: <a href="{access_url}">{access_url}</a></p>
</div>',

				'guest_confirmed' => '<h2>Booking Confirmed!</h2>
<p>Dear {customer_name},</p>
<p>Great news! Your booking has been confirmed.</p>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Booking ID:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">#{booking_id}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Hall:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{hall_title}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Date:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{booking_date}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Time:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{slot_time}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Status:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><strong style="color: #00a32a;">Confirmed</strong></td></tr>
</table>
<div style="background: #d4edda; border: 2px solid #28a745; border-radius: 5px; padding: 15px; margin: 20px 0;">
	<p style="margin: 0 0 10px 0;"><strong>Your Booking Access PIN:</strong></p>
	<p style="font-size: 32px; font-family: monospace; background: #fff; padding: 15px; border-radius: 3px; margin: 10px 0; letter-spacing: 4px; text-align: center; color: #28a745; font-weight: bold;">{pin}</p>
	<p style="margin: 10px 0 0 0; font-size: 13px; color: #155724; text-align: center;">Use this 6-digit PIN and your email to view your booking at: <a href="{access_url}">{access_url}</a></p>
</div>',

				'guest_cancelled' => '<h2>Booking Cancelled</h2>
<p>Dear {customer_name},</p>
<p>Your booking has been cancelled.</p>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Booking ID:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">#{booking_id}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Hall:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{hall_title}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Date:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{booking_date}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Time:</strong></td><td style="padding: 10px; border: 1px solid #ddd;">{slot_time}</td></tr>
	<tr><td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Status:</strong></td><td style="padding: 10px; border: 1px solid #ddd;"><strong style="color: #d63638;">Cancelled</strong></td></tr>
</table>
<p>If you have any questions, please feel free to contact us.</p>'
			);

			// Populate empty bodies with defaults for display
			if (empty($admin_notification_body)) {
				$admin_notification_body = $defaults['admin_notification'];
			}
			if (empty($guest_pending_body)) {
				$guest_pending_body = $defaults['guest_pending'];
			}
			if (empty($guest_confirmed_body)) {
				$guest_confirmed_body = $defaults['guest_confirmed'];
			}
			if (empty($guest_cancelled_body)) {
				$guest_cancelled_body = $defaults['guest_cancelled'];
			}

			// Helper to render toolbar
			$render_toolbar = function ($target_id, $default_key) use ($placeholders) {
				echo '<div class="shb-template-toolbar" style="margin-bottom: 10px;">';
				echo '<p style="margin-bottom: 5px;"><strong>' . esc_html__('Insert Placeholder:', 'simple-hall-booking-manager') . '</strong></p>';
				foreach ($placeholders as $ph => $label) {
					echo '<button type="button" class="button button-secondary shb-insert-placeholder" data-target="' . esc_attr($target_id) . '" data-value="' . esc_attr($ph) . '" style="margin-right: 5px; margin-bottom: 5px;">' . esc_html($label) . '</button>';
				}
				echo '<button type="button" class="button shb-reset-template" data-target="' . esc_attr($target_id) . '" data-default-container="default-' . esc_attr($default_key) . '" style="margin-left: 10px; color: #b32d2e; border-color: #b32d2e;">' . esc_html__('Reset to Default', 'simple-hall-booking-manager') . '</button>';
				echo '</div>';
			};
			?>

			<!-- Hidden Default Containers -->
			<?php foreach ($defaults as $key => $content): ?>
				<script type="text/template"
					id="default-<?php echo esc_attr($key); ?>"><?php echo wp_kses_post($content); ?></script>
			<?php endforeach; ?>

			<hr>

			<h3><?php esc_html_e('Admin Notification (New Booking)', 'simple-hall-booking-manager'); ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label
							for="admin_notification_subject"><?php esc_html_e('Subject', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td><input type="text" name="admin_notification_subject" id="admin_notification_subject"
							value="<?php echo esc_attr($admin_notification_subject); ?>" class="large-text"></td>
				</tr>
				<tr>
					<th scope="row"><label
							for="admin_notification_body"><?php esc_html_e('Body (HTML)', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<?php $render_toolbar('admin_notification_body', 'admin_notification'); ?>
						<?php
						wp_editor(
							$admin_notification_body,
							'admin_notification_body',
							array('textarea_name' => 'admin_notification_body', 'textarea_rows' => 12, 'media_buttons' => false)
						);
						?>

					</td>
				</tr>
			</table>

			<hr>

			<h3><?php esc_html_e('Guest Notification (Pending)', 'simple-hall-booking-manager'); ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label
							for="guest_pending_subject"><?php esc_html_e('Subject', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td><input type="text" name="guest_pending_subject" id="guest_pending_subject"
							value="<?php echo esc_attr($guest_pending_subject); ?>" class="large-text"></td>
				</tr>
				<tr>
					<th scope="row"><label
							for="guest_pending_body"><?php esc_html_e('Body (HTML)', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<?php $render_toolbar('guest_pending_body', 'guest_pending'); ?>
						<?php
						wp_editor(
							$guest_pending_body,
							'guest_pending_body',
							array('textarea_name' => 'guest_pending_body', 'textarea_rows' => 12, 'media_buttons' => false)
						);
						?>
					</td>
				</tr>
			</table>

			<hr>

			<h3><?php esc_html_e('Guest Notification (Confirmed)', 'simple-hall-booking-manager'); ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label
							for="guest_confirmed_subject"><?php esc_html_e('Subject', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td><input type="text" name="guest_confirmed_subject" id="guest_confirmed_subject"
							value="<?php echo esc_attr($guest_confirmed_subject); ?>" class="large-text"></td>
				</tr>
				<tr>
					<th scope="row"><label
							for="guest_confirmed_body"><?php esc_html_e('Body (HTML)', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<?php $render_toolbar('guest_confirmed_body', 'guest_confirmed'); ?>
						<?php
						wp_editor(
							$guest_confirmed_body,
							'guest_confirmed_body',
							array('textarea_name' => 'guest_confirmed_body', 'textarea_rows' => 12, 'media_buttons' => false)
						);
						?>
					</td>
				</tr>
			</table>

			<hr>

			<h3><?php esc_html_e('Guest Notification (Cancelled)', 'simple-hall-booking-manager'); ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label
							for="guest_cancelled_subject"><?php esc_html_e('Subject', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td><input type="text" name="guest_cancelled_subject" id="guest_cancelled_subject"
							value="<?php echo esc_attr($guest_cancelled_subject); ?>" class="large-text"></td>
				</tr>
				<tr>
					<th scope="row"><label
							for="guest_cancelled_body"><?php esc_html_e('Body (HTML)', 'simple-hall-booking-manager'); ?></label>
					</th>
					<td>
						<?php $render_toolbar('guest_cancelled_body', 'guest_cancelled'); ?>
						<?php
						wp_editor(
							$guest_cancelled_body,
							'guest_cancelled_body',
							array('textarea_name' => 'guest_cancelled_body', 'textarea_rows' => 12, 'media_buttons' => false)
						);
						?>
					</td>
				</tr>
			</table>
		<?php elseif ('shortcodes' === $active_tab): ?>
			<h2><?php esc_html_e('Shortcodes', 'simple-hall-booking-manager'); ?></h2>
			<table class="form-table">
				<tr>
					<th><?php esc_html_e('Available Shortcodes', 'simple-hall-booking-manager'); ?></th>
					<td>
						<p><code>[shb_hall_list columns="3"]</code> -
							<?php esc_html_e('Display a list of available halls', 'simple-hall-booking-manager'); ?>
						</p>
						<p><code>[shb_booking_form hall_id="123"]</code> -
							<?php esc_html_e('Display booking form (hall_id is optional)', 'simple-hall-booking-manager'); ?>
						</p>
						<p><code>[shb_user_bookings]</code> -
							<?php esc_html_e('Display booking details for guests (uses token from URL)', 'simple-hall-booking-manager'); ?>
						</p>
					</td>
				</tr>
			</table>
		<?php endif; ?>

		<?php if ('shortcodes' !== $active_tab): ?>
			<p class="submit">
				<input type="submit" name="shb_save_settings" class="button button-primary"
					value="<?php esc_attr_e('Save Settings', 'simple-hall-booking-manager'); ?>">
			</p>
		<?php endif; ?>
	</form>
</div>