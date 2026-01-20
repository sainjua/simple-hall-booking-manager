<?php
/**
 * Admin view: Booking edit
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

$db = shb()->db;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET operation to display booking data
$booking_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
$booking = $booking_id ? $db->get_booking($booking_id) : null;

if (!$booking) {
	wp_die(esc_html__('Booking not found.', 'simple-hall-booking-manager'));
}

$hall = $db->get_hall($booking->hall_id);

if (!$hall) {
	wp_die(esc_html__('Hall not found.', 'simple-hall-booking-manager'));
}

// Get booking dates (works for both single and multi-day bookings)
$booking_dates = $db->get_booking_dates($booking->id);
$slot = null;
if (!empty($booking_dates)) {
	$first_booking_date = reset($booking_dates);
	$slot = $db->get_slot($first_booking_date->slot_id);
}

// Check for conflicts
$conflicts = array();
$conflict_details = array();
if ('pending' === $booking->status) {
	$conflict_ids = $db->get_conflicting_bookings($booking->id, true);
	if (!empty($conflict_ids)) {
		foreach ($conflict_ids as $conflict_id) {
			$conflict_booking = $db->get_booking($conflict_id);
			if ($conflict_booking) {
				$conflicts[] = $conflict_booking;

				// Get conflict booking dates
				$conflict_dates = $db->get_booking_dates($conflict_id);
				$conflict_slot = null;
				$conflict_date_display = '-';

				if (!empty($conflict_dates)) {
					$conflict_slot = $db->get_slot($conflict_dates[0]->slot_id);

					if ('multiday' === $conflict_booking->booking_type) {
						$conflict_date_display = __('Multi-day', 'simple-hall-booking-manager');
					} else {
						$conflict_date_display = shb_format_date($conflict_dates[0]->booking_date);
					}
				}

				$conflict_details[] = array(
					'id' => $conflict_booking->id,
					'name' => $conflict_booking->customer_name,
					'status' => $conflict_booking->status,
					'date' => $conflict_date_display,
					'slot' => $conflict_slot ? $conflict_slot->label : '-',
				);
			}
		}
	}
}
?>

<div class="wrap">
	<h1><?php esc_html_e('Edit Booking', 'simple-hall-booking-manager'); ?></h1>

	<?php
	// Display admin notices
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET operation to display message
	if (isset($_GET['message'])) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Read-only GET operation
		$message = sanitize_text_field(wp_unslash($_GET['message']));

		if ('email_resent' === $message) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e('Status email has been resent to the customer successfully.', 'simple-hall-booking-manager'); ?>
				</p>
			</div>
			<?php
		} elseif ('email_failed' === $message) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e('Failed to send email. Please check your email settings.', 'simple-hall-booking-manager'); ?>
				</p>
			</div>
			<?php
		}
	}
	?>

	<?php if (!empty($conflicts)): ?>
		<div class="shb-conflict-notice">
			<h4>⚠️ <?php esc_html_e('Booking Conflicts Detected', 'simple-hall-booking-manager'); ?></h4>
			<p>
				<?php
				/* translators: %d: number of conflicts */
				printf(esc_html__('This booking conflicts with %d other booking(s). If you approve this booking, the conflicting pending bookings will be automatically cancelled.', 'simple-hall-booking-manager'), absint(count($conflicts)));
				?>
			</p>
			<ul>
				<?php foreach ($conflict_details as $detail): ?>
					<li>
						<strong><?php echo esc_html(sprintf('#%d - %s', $detail['id'], $detail['name'])); ?></strong>
						<br>
						<small>
							<?php echo esc_html(sprintf('%s | %s | %s', $detail['date'], $detail['slot'], shb_get_status_label($detail['status']))); ?>
						</small>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<form method="post" action="">
		<?php wp_nonce_field('shb_save_booking'); ?>
		<input type="hidden" name="booking_id" value="<?php echo esc_attr($booking_id); ?>">
		<input type="hidden" name="old_status" value="<?php echo esc_attr($booking->status); ?>">

		<h2><?php esc_html_e('Booking Details', 'simple-hall-booking-manager'); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e('Booking ID', 'simple-hall-booking-manager'); ?></th>
				<td><strong>#<?php echo esc_html($booking->id); ?></strong></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Booking PIN', 'simple-hall-booking-manager'); ?></th>
				<td>
					<code
						style="font-size: 16px; font-weight: bold; background: #f0f7ff; padding: 5px 10px; border-radius: 3px; letter-spacing: 2px;">
						<?php echo esc_html($booking->pin); ?>
					</code>
					<p class="description">
						<?php esc_html_e('Customer can use this PIN to access their booking details', 'simple-hall-booking-manager'); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e('Hall', 'simple-hall-booking-manager'); ?></th>
				<td><?php echo $hall ? esc_html($hall->title) : '-'; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Slot', 'simple-hall-booking-manager'); ?></th>
				<td>
					<?php
					if ($slot) {
						echo esc_html($slot->label . ' (' . wp_date('g:i A', strtotime($slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($slot->end_time)) . ')');
					} else {
						echo '-';
					}
					?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e('Booking Type', 'simple-hall-booking-manager'); ?></th>
				<td>
					<?php if ('multiday' === $booking->booking_type): ?>
						<span class="shb-multiday-badge"
							style="background: #2271b1; color: #fff; padding: 3px 8px; border-radius: 3px;">
							📅 <?php esc_html_e('Multi-Day Booking', 'simple-hall-booking-manager'); ?>
						</span>
					<?php else: ?>
						<?php esc_html_e('Single Day', 'simple-hall-booking-manager'); ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php if ('multiday' === $booking->booking_type): ?>
				<?php $booking_dates = $db->get_booking_dates($booking->id); ?>
				<tr>
					<th><?php esc_html_e('Booking Dates', 'simple-hall-booking-manager'); ?></th>
					<td>
						<div style="margin-bottom: 10px;">
							<strong><?php
							/* translators: %d: number of days */
							echo esc_html(sprintf(_n('%d day', '%d days', absint(count($booking_dates)), 'simple-hall-booking-manager'), absint(count($booking_dates)))); ?></strong>
						</div>
						<?php if (!empty($booking_dates)): ?>
							<table class="wp-list-table widefat fixed striped" style="max-width: 800px;">
								<thead>
									<tr>
										<th style="width: 60px;">#</th>
										<th><?php esc_html_e('Date', 'simple-hall-booking-manager'); ?></th>
										<th><?php esc_html_e('Day', 'simple-hall-booking-manager'); ?></th>
										<th><?php esc_html_e('Time Slot', 'simple-hall-booking-manager'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($booking_dates as $index => $date_record): ?>
										<?php
										$date_slot = $db->get_slot($date_record->slot_id);
										?>
										<tr>
											<td><?php echo esc_html($index + 1); ?></td>
											<td><strong><?php echo esc_html(shb_format_date($date_record->booking_date)); ?></strong>
											</td>
											<td><?php echo esc_html(wp_date('l', strtotime($date_record->booking_date))); ?></td>
											<td>
												<?php
												if ($date_slot) {
													echo esc_html($date_slot->label . ' (' . wp_date('g:i A', strtotime($date_slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($date_slot->end_time)) . ')');
												} else {
													echo '-';
												}
												?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<p class="description">
								<?php esc_html_e('No dates found for this booking.', 'simple-hall-booking-manager'); ?>
							</p>
						<?php endif; ?>
					</td>
				</tr>
			<?php else: ?>
				<tr>
					<th><?php esc_html_e('Date', 'simple-hall-booking-manager'); ?></th>
					<td><?php echo !empty($booking_dates) ? esc_html(shb_format_date($booking_dates[0]->booking_date)) : '-'; ?>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<th><?php esc_html_e('Customer Name', 'simple-hall-booking-manager'); ?></th>
				<td><?php echo esc_html($booking->customer_name); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Customer Email', 'simple-hall-booking-manager'); ?></th>
				<td><a
						href="mailto:<?php echo esc_attr($booking->customer_email); ?>"><?php echo esc_html($booking->customer_email); ?></a>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e('Customer Phone', 'simple-hall-booking-manager'); ?></th>
				<td><?php echo esc_html($booking->customer_phone); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Event Purpose', 'simple-hall-booking-manager'); ?></th>
				<td><?php echo esc_html($booking->event_purpose); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Number of Attendees', 'simple-hall-booking-manager'); ?></th>
				<td><?php echo esc_html($booking->attendees_count); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Created At', 'simple-hall-booking-manager'); ?></th>
				<td><?php echo esc_html(shb_format_date($booking->created_at) . ' ' . wp_date('g:i A', strtotime($booking->created_at))); ?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e('Access Token', 'simple-hall-booking-manager'); ?></th>
				<td>
					<code><?php echo esc_html($booking->access_token); ?></code>
					<br>
					<a href="<?php echo esc_url(shb_get_booking_access_url($booking->access_token)); ?>"
						target="_blank">
						<?php esc_html_e('View Guest Page', 'simple-hall-booking-manager'); ?>
					</a>
				</td>
			</tr>
		</table>

		<h2><?php esc_html_e('Admin Controls', 'simple-hall-booking-manager'); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="status"><?php esc_html_e('Status', 'simple-hall-booking-manager'); ?></label>
				</th>
				<td>
					<select name="status" id="status">
						<?php foreach (shb_get_booking_statuses() as $status_val => $status_label): ?>
							<option value="<?php echo esc_attr($status_val); ?>" <?php selected($booking->status, $status_val); ?>>
								<?php echo esc_html($status_label); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						<?php esc_html_e('Changing the status will send an email to the customer.', 'simple-hall-booking-manager'); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="admin_notes"><?php esc_html_e('Admin Notes', 'simple-hall-booking-manager'); ?></label>
				</th>
				<td>
					<textarea name="admin_notes" id="admin_notes" rows="4"
						class="large-text"><?php echo esc_textarea($booking->admin_notes); ?></textarea>
					<p class="description">
						<?php esc_html_e('Internal notes (not visible to customer)', 'simple-hall-booking-manager'); ?>
					</p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="shb_save_booking" class="button button-primary"
				value="<?php esc_attr_e('Update Booking', 'simple-hall-booking-manager'); ?>">
			<input type="submit" name="shb_resend_email" class="button button-secondary"
				value="<?php esc_attr_e('Resend Status Email', 'simple-hall-booking-manager'); ?>"
				onclick="return confirm('<?php esc_attr_e('Are you sure you want to resend the status email to the customer?', 'simple-hall-booking-manager'); ?>');">
			<a href="<?php echo esc_url(admin_url('admin.php?page=shb-bookings')); ?>" class="button">
				<?php esc_html_e('Back to Bookings', 'simple-hall-booking-manager'); ?>
			</a>
		</p>
	</form>
</div>