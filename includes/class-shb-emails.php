<?php
/**
 * Email handler
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Emails class
 */
class SHB_Emails
{

	/**
	 * Get email settings
	 *
	 * @return array
	 */
	private function get_email_settings()
	{
		return get_option(
			'shb_email_settings',
			array(
				'from_name' => get_bloginfo('name'),
				'from_email' => get_bloginfo('admin_email'),
				'admin_email' => get_bloginfo('admin_email'),
			)
		);
	}

	/**
	 * Get email headers
	 *
	 * @return array
	 */
	private function get_email_headers()
	{
		$settings = $this->get_email_settings();

		return array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $settings['from_name'] . ' <' . $settings['from_email'] . '>',
		);
	}

	/**
	 * Get booking details for email
	 *
	 * @param int $booking_id Booking ID.
	 * @return array|null
	 */
	private function get_booking_details($booking_id)
	{
		$db = shb()->db;
		$booking = $db->get_booking($booking_id);

		if (!$booking) {
			return null;
		}

		$hall = $db->get_hall($booking->hall_id);
		$slot = $db->get_slot($booking->slot_id);

		// Get dates for multiday bookings
		$booking_dates = array();
		if ('multiday' === $booking->booking_type) {
			$booking_dates = $db->get_booking_dates($booking->id);
		}

		return array(
			'booking' => $booking,
			'hall' => $hall,
			'slot' => $slot,
			'booking_dates' => $booking_dates,
		);
	}

	/**
	 * Replace placeholders in email content
	 *
	 * @param string $content Content to parse.
	 * @param object $booking Booking object.
	 * @param object $hall Hall object.
	 * @param object $slot Slot object.
	 * @param array $booking_dates Booking dates array.
	 * @return string
	 */
	private function replace_placeholders($content, $booking, $hall, $slot, $booking_dates = array())
	{
		$replacements = array(
			'{booking_id}' => $booking->id,
			'{hall_title}' => $hall->title,
			'{customer_name}' => $booking->customer_name,
			'{customer_email}' => $booking->customer_email,
			'{customer_phone}' => $booking->customer_phone,
			'{event_purpose}' => $booking->event_purpose,
			'{attendees_count}' => $booking->attendees_count,
			'{status}' => shb_get_status_label($booking->status),
			'{pin}' => $booking->pin,
			'{access_url}' => shb_get_booking_access_url($booking->access_token),
			'{admin_email}' => get_option('admin_email'),
		);

		// Format dates and times
		$replacements['{booking_date}'] = shb_format_date($booking->booking_date);
		$replacements['{slot_time}'] = $slot->label . ' (' . wp_date('g:i A', strtotime($slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($slot->end_time)) . ')';

		// Handle booking dates list
		$dates_list = '';
		if ('multiday' === $booking->booking_type && !empty($booking_dates)) {
			$dates_list .= '<ul>';
			foreach ($booking_dates as $date_record) {
				$dates_list .= '<li>' . shb_format_date($date_record->booking_date) . '</li>';
			}
			$dates_list .= '</ul>';
		} else {
			$dates_list .= shb_format_date($booking->booking_date);
		}
		$replacements['{booking_dates_list}'] = $dates_list;

		return str_replace(array_keys($replacements), array_values($replacements), $content);
	}

	/**
	 * Send new booking notification to admin
	 *
	 * @param int $booking_id Booking ID.
	 * @return bool
	 */
	public function send_admin_new_booking($booking_id)
	{
		$details = $this->get_booking_details($booking_id);
		if (!$details) {
			return false;
		}

		$booking = $details['booking'];
		$hall = $details['hall'];
		$slot = $details['slot'];
		$booking_dates = $details['booking_dates'];

		$settings = $this->get_email_settings();
		$to = $settings['admin_email'];

		// Subject
		if (!empty($settings['admin_notification_subject'])) {
			$subject = $this->replace_placeholders($settings['admin_notification_subject'], $booking, $hall, $slot, $booking_dates);
		} else {
			$subject = sprintf(
				/* translators: %s: booking ID */
				__('[New Booking] Booking Request #%s', 'simple-hall-booking-manager'),
				$booking->id
			);
		}

		// Body
		if (!empty($settings['admin_notification_body'])) {
			$message = $this->replace_placeholders($settings['admin_notification_body'], $booking, $hall, $slot, $booking_dates);
		} else {
			$message = $this->get_admin_new_booking_template($booking, $hall, $slot, $booking_dates);
		}

		return wp_mail($to, $subject, $message, $this->get_email_headers());
	}

	/**
	 * Send pending status email to guest
	 *
	 * @param int $booking_id Booking ID.
	 * @return bool
	 */
	public function send_guest_pending($booking_id)
	{
		$details = $this->get_booking_details($booking_id);
		if (!$details) {
			return false;
		}

		$booking = $details['booking'];
		$hall = $details['hall'];
		$slot = $details['slot'];
		$booking_dates = $details['booking_dates'];

		$to = $booking->customer_email;

		$settings = $this->get_email_settings();

		// Subject
		if (!empty($settings['guest_pending_subject'])) {
			$subject = $this->replace_placeholders($settings['guest_pending_subject'], $booking, $hall, $slot, $booking_dates);
		} else {
			$subject = sprintf(
				/* translators: %s: booking ID */
				__('Booking Request Received - #%s', 'simple-hall-booking-manager'),
				$booking->id
			);
		}

		// Body
		if (!empty($settings['guest_pending_body'])) {
			$message = $this->replace_placeholders($settings['guest_pending_body'], $booking, $hall, $slot, $booking_dates);
		} else {
			$message = $this->get_guest_pending_template($booking, $hall, $slot, $booking_dates);
		}

		return wp_mail($to, $subject, $message, $this->get_email_headers());
	}

	/**
	 * Send confirmed status email to guest
	 *
	 * @param int $booking_id Booking ID.
	 * @return bool
	 */
	public function send_guest_confirmed($booking_id)
	{
		$details = $this->get_booking_details($booking_id);
		if (!$details) {
			return false;
		}

		$booking = $details['booking'];
		$hall = $details['hall'];
		$slot = $details['slot'];

		$to = $booking->customer_email;
		$settings = $this->get_email_settings();

		// Subject
		if (!empty($settings['guest_confirmed_subject'])) {
			$subject = $this->replace_placeholders($settings['guest_confirmed_subject'], $booking, $hall, $slot);
		} else {
			$subject = sprintf(
				/* translators: %s: booking ID */
				__('Booking Confirmed - #%s', 'simple-hall-booking-manager'),
				$booking->id
			);
		}

		// Body
		if (!empty($settings['guest_confirmed_body'])) {
			$message = $this->replace_placeholders($settings['guest_confirmed_body'], $booking, $hall, $slot);
		} else {
			$message = $this->get_guest_confirmed_template($booking, $hall, $slot);
		}

		return wp_mail($to, $subject, $message, $this->get_email_headers());
	}

	/**
	 * Send cancelled status email to guest
	 *
	 * @param int $booking_id Booking ID.
	 * @return bool
	 */
	public function send_guest_cancelled($booking_id)
	{
		$details = $this->get_booking_details($booking_id);
		if (!$details) {
			return false;
		}

		$booking = $details['booking'];
		$hall = $details['hall'];
		$slot = $details['slot'];

		$to = $booking->customer_email;
		$settings = $this->get_email_settings();

		// Subject
		if (!empty($settings['guest_cancelled_subject'])) {
			$subject = $this->replace_placeholders($settings['guest_cancelled_subject'], $booking, $hall, $slot);
		} else {
			$subject = sprintf(
				/* translators: %s: booking ID */
				__('Booking Cancelled - #%s', 'simple-hall-booking-manager'),
				$booking->id
			);
		}

		// Body
		if (!empty($settings['guest_cancelled_body'])) {
			$message = $this->replace_placeholders($settings['guest_cancelled_body'], $booking, $hall, $slot);
		} else {
			$message = $this->get_guest_cancelled_template($booking, $hall, $slot);
		}

		return wp_mail($to, $subject, $message, $this->get_email_headers());
	}

	/**
	 * Admin new booking email template
	 *
	 * @param object $booking Booking object.
	 * @param object $hall Hall object.
	 * @param object $slot Slot object.
	 * @return string
	 */
	private function get_admin_new_booking_template($booking, $hall, $slot, $booking_dates = array())
	{
		ob_start();
		?>
		<!DOCTYPE html>
		<html>

		<head>
			<meta charset="UTF-8">
		</head>

		<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
			<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
				<h2 style="color: #0073aa;"><?php esc_html_e('New Booking Request', 'simple-hall-booking-manager'); ?></h2>

				<p><?php esc_html_e('You have received a new booking request:', 'simple-hall-booking-manager'); ?></p>

				<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Booking ID:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">#<?php echo esc_html($booking->id); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Hall:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($hall->title); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Date:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?php echo esc_html(shb_format_date($booking->booking_date)); ?>
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Time:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?php echo esc_html($slot->label . ' (' . wp_date('g:i A', strtotime($slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($slot->end_time)) . ')'); ?>
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Customer:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($booking->customer_name); ?>
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Email:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><a
								href="mailto:<?php echo esc_attr($booking->customer_email); ?>"><?php echo esc_html($booking->customer_email); ?></a>
						</td>
					</tr>
					<?php if ($booking->customer_phone): ?>
						<tr>
							<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
								<strong><?php esc_html_e('Phone:', 'simple-hall-booking-manager'); ?></strong>
							</td>
							<td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($booking->customer_phone); ?>
							</td>
						</tr>
					<?php endif; ?>
					<?php if ($booking->event_purpose): ?>
						<tr>
							<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
								<strong><?php esc_html_e('Event Purpose:', 'simple-hall-booking-manager'); ?></strong>
							</td>
							<td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($booking->event_purpose); ?>
							</td>
						</tr>
					<?php endif; ?>
					<?php if ($booking->attendees_count): ?>
						<tr>
							<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
								<strong><?php esc_html_e('Attendees:', 'simple-hall-booking-manager'); ?></strong>
							</td>
							<td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($booking->attendees_count); ?>
							</td>
						</tr>
					<?php endif; ?>
				</table>

				<p>
					<a href="<?php echo esc_url(admin_url('admin.php?page=shb-bookings&action=edit&id=' . $booking->id)); ?>"
						style="display: inline-block; padding: 10px 20px; background: #0073aa; color: #fff; text-decoration: none; border-radius: 3px;">
						<?php esc_html_e('Review Booking', 'simple-hall-booking-manager'); ?>
					</a>
				</p>
			</div>
		</body>

		</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * Guest pending email template
	 *
	 * @param object $booking Booking object.
	 * @param object $hall Hall object.
	 * @param object $slot Slot object.
	 * @return string
	 */
	private function get_guest_pending_template($booking, $hall, $slot, $booking_dates = array())
	{
		$access_url = shb_get_booking_access_url($booking->access_token);

		ob_start();
		?>
		<!DOCTYPE html>
		<html>

		<head>
			<meta charset="UTF-8">
		</head>

		<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
			<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
				<h2 style="color: #0073aa;"><?php esc_html_e('Booking Request Received', 'simple-hall-booking-manager'); ?>
				</h2>

				<p><?php
				/* translators: %s: customer name */
				printf(esc_html__('Dear %s,', 'simple-hall-booking-manager'), esc_html($booking->customer_name)); ?>
				</p>

				<p><?php esc_html_e('Thank you for your booking request. We have received your request and will review it shortly.', 'simple-hall-booking-manager'); ?>
				</p>

				<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Booking ID:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">#<?php echo esc_html($booking->id); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Hall:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($hall->title); ?></td>
					</tr>
					<?php if ('multiday' === $booking->booking_type && !empty($booking_dates)): ?>
						<tr>
							<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
								<strong><?php esc_html_e('Dates:', 'simple-hall-booking-manager'); ?></strong>
							</td>
							<td style="padding: 10px; border: 1px solid #ddd;">
								<p><strong><?php
								/* translators: %d: number of days */
								echo esc_html(sprintf(_n('%d day booking', '%d days booking', count($booking_dates), 'simple-hall-booking-manager'), count($booking_dates))); ?></strong>
								</p>
								<ul style="margin: 5px 0; padding-left: 20px;">
									<?php foreach ($booking_dates as $date_record): ?>
										<?php
										global $wpdb;
										$date_slot = $wpdb->get_row(
											$wpdb->prepare(
												"SELECT * FROM {$wpdb->prefix}shb_slots WHERE id = %d",
												$date_record->slot_id
											)
										);
										?>
										<li>
											<?php echo esc_html(shb_format_date($date_record->booking_date)); ?>
											<?php if ($date_slot): ?>
												<br><em style="font-size: 13px; color: #666;">
													<?php echo esc_html($date_slot->label . ' - ' . wp_date('g:i A', strtotime($date_slot->start_time)) . ' to ' . wp_date('g:i A', strtotime($date_slot->end_time))); ?>
												</em>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</td>
						</tr>
					<?php else: ?>
						<tr>
							<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
								<strong><?php esc_html_e('Date:', 'simple-hall-booking-manager'); ?></strong>
							</td>
							<td style="padding: 10px; border: 1px solid #ddd;">
								<?php echo esc_html(shb_format_date($booking->booking_date)); ?>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Time:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?php echo esc_html($slot->label . ' (' . wp_date('g:i A', strtotime($slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($slot->end_time)) . ')'); ?>
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Status:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><strong
								style="color: #d63638;"><?php esc_html_e('Pending', 'simple-hall-booking-manager'); ?></strong>
						</td>
					</tr>
				</table>

				<p><?php esc_html_e('You will receive another email once your booking has been confirmed or if any changes are made.', 'simple-hall-booking-manager'); ?>
				</p>

				<div style="background: #f0f7ff; border: 2px solid #0073aa; border-radius: 5px; padding: 15px; margin: 20px 0;">
					<p style="margin: 0 0 10px 0;">
						<strong><?php esc_html_e('Your Booking Access PIN:', 'simple-hall-booking-manager'); ?></strong>
					</p>
					<p
						style="font-size: 32px; font-family: 'Courier New', monospace; background: #fff; padding: 15px; border-radius: 3px; margin: 10px 0; letter-spacing: 4px; text-align: center; color: #0073aa; font-weight: bold;">
						<?php echo esc_html($booking->pin); ?>
					</p>
					<p style="margin: 10px 0 0 0; font-size: 13px; color: #666; text-align: center;">
						<?php esc_html_e('Use this 6-digit PIN to view your booking anytime', 'simple-hall-booking-manager'); ?>
					</p>
				</div>

				<p>
					<a href="<?php echo esc_url($access_url); ?>"
						style="display: inline-block; padding: 12px 24px; background: #0073aa; color: #fff; text-decoration: none; border-radius: 3px; font-weight: bold;">
						<?php esc_html_e('View Booking Details', 'simple-hall-booking-manager'); ?>
					</a>
				</p>

				<p style="color: #666; font-size: 12px;">
					<?php esc_html_e('You can use the link above or visit the booking page and enter your PIN to view details or cancel your booking if needed.', 'simple-hall-booking-manager'); ?>
				</p>
			</div>
		</body>

		</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * Guest confirmed email template
	 *
	 * @param object $booking Booking object.
	 * @param object $hall Hall object.
	 * @param object $slot Slot object.
	 * @return string
	 */
	private function get_guest_confirmed_template($booking, $hall, $slot)
	{
		$access_url = shb_get_booking_access_url($booking->access_token);

		ob_start();
		?>
		<!DOCTYPE html>
		<html>

		<head>
			<meta charset="UTF-8">
		</head>

		<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
			<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
				<h2 style="color: #00a32a;"><?php esc_html_e('Booking Confirmed!', 'simple-hall-booking-manager'); ?></h2>

				<p><?php
				/* translators: %s: customer name */
				printf(esc_html__('Dear %s,', 'simple-hall-booking-manager'), esc_html($booking->customer_name)); ?>
				</p>

				<p><?php esc_html_e('Great news! Your booking has been confirmed.', 'simple-hall-booking-manager'); ?></p>

				<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Booking ID:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">#<?php echo esc_html($booking->id); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Hall:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($hall->title); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Date:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?php echo esc_html(shb_format_date($booking->booking_date)); ?>
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Time:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?php echo esc_html($slot->label . ' (' . wp_date('g:i A', strtotime($slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($slot->end_time)) . ')'); ?>
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Status:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><strong
								style="color: #00a32a;"><?php esc_html_e('Confirmed', 'simple-hall-booking-manager'); ?></strong>
						</td>
					</tr>
				</table>

				<p><?php esc_html_e('We look forward to seeing you! If you have any questions, please don\'t hesitate to contact us.', 'simple-hall-booking-manager'); ?>
				</p>

				<div style="background: #d4edda; border: 2px solid #28a745; border-radius: 5px; padding: 15px; margin: 20px 0;">
					<p style="margin: 0 0 10px 0;">
						<strong><?php esc_html_e('Your Booking Access PIN:', 'simple-hall-booking-manager'); ?></strong>
					</p>
					<p
						style="font-size: 32px; font-family: 'Courier New', monospace; background: #fff; padding: 15px; border-radius: 3px; margin: 10px 0; letter-spacing: 4px; text-align: center; color: #28a745; font-weight: bold;">
						<?php echo esc_html($booking->pin); ?>
					</p>
					<p style="margin: 10px 0 0 0; font-size: 13px; color: #155724; text-align: center;">
						<?php esc_html_e('Use this 6-digit PIN to view your booking anytime', 'simple-hall-booking-manager'); ?>
					</p>
				</div>

				<p>
					<a href="<?php echo esc_url($access_url); ?>"
						style="display: inline-block; padding: 12px 24px; background: #28a745; color: #fff; text-decoration: none; border-radius: 3px; font-weight: bold;">
						<?php esc_html_e('View Booking Details', 'simple-hall-booking-manager'); ?>
					</a>
				</p>
			</div>
		</body>

		</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * Guest cancelled email template
	 *
	 * @param object $booking Booking object.
	 * @param object $hall Hall object.
	 * @param object $slot Slot object.
	 * @return string
	 */
	private function get_guest_cancelled_template($booking, $hall, $slot)
	{
		ob_start();
		?>
		<!DOCTYPE html>
		<html>

		<head>
			<meta charset="UTF-8">
		</head>

		<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
			<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
				<h2 style="color: #d63638;"><?php esc_html_e('Booking Cancelled', 'simple-hall-booking-manager'); ?></h2>

				<p><?php
				/* translators: %s: customer name */
				printf(esc_html__('Dear %s,', 'simple-hall-booking-manager'), esc_html($booking->customer_name)); ?>
				</p>

				<p><?php esc_html_e('Your booking has been cancelled.', 'simple-hall-booking-manager'); ?></p>

				<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Booking ID:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">#<?php echo esc_html($booking->id); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Hall:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($hall->title); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Date:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?php echo esc_html(shb_format_date($booking->booking_date)); ?>
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Time:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?php echo esc_html($slot->label . ' (' . wp_date('g:i A', strtotime($slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($slot->end_time)) . ')'); ?>
						</td>
					</tr>
					<tr>
						<td style="padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
							<strong><?php esc_html_e('Status:', 'simple-hall-booking-manager'); ?></strong>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;"><strong
								style="color: #d63638;"><?php esc_html_e('Cancelled', 'simple-hall-booking-manager'); ?></strong>
						</td>
					</tr>
				</table>

				<p><?php esc_html_e('If you have any questions, please feel free to contact us.', 'simple-hall-booking-manager'); ?>
				</p>
			</div>
		</body>

		</html>
		<?php
		return ob_get_clean();
	}
}

