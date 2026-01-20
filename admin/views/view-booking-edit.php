<?php
/**
 * Admin view: Booking edit (Professional Design)
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

// Get status badge color
$status_colors = array(
	'pending' => '#f59e0b',
	'confirmed' => '#10b981',
	'cancelled' => '#ef4444',
);
$status_color = isset($status_colors[$booking->status]) ? $status_colors[$booking->status] : '#6b7280';
?>

<style>
	.shb-booking-edit-container {
		max-width: 1400px;
		margin: 20px 0;
	}

	.shb-page-header {
		background: #fff;
		padding: 24px;
		margin-bottom: 20px;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.shb-page-header h1 {
		margin: 0;
		font-size: 24px;
		font-weight: 600;
		color: #1e293b;
		display: inline-flex;
		align-items: center;
		gap: 12px;
	}

	.shb-booking-id-badge {
		background: #f1f5f9;
		color: #475569;
		padding: 4px 12px;
		border-radius: 6px;
		font-size: 18px;
		font-weight: 600;
		vertical-align: middle;
	}

	.shb-status-badge {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 6px 12px;
		border-radius: 20px;
		font-size: 13px;
		font-weight: 600;
		color: #fff;
		text-transform: capitalize;
	}

	.shb-status-badge::before {
		content: '';
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: currentColor;
		opacity: 0.8;
	}

	.shb-grid-2col {
		display: grid;
		grid-template-columns: 2fr 1fr;
		gap: 20px;
		margin-bottom: 20px;
	}

	.shb-card {
		background: #fff;
		border-radius: 8px;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		overflow: hidden;
	}

	.shb-card-header {
		padding: 20px 24px;
		border-bottom: 1px solid #e2e8f0;
		background: #f8fafc;
	}

	.shb-card-header h3 {
		margin: 0;
		font-size: 16px;
		font-weight: 600;
		color: #1e293b;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.shb-card-body {
		padding: 24px;
	}

	.shb-info-row {
		display: flex;
		padding: 12px 0;
		border-bottom: 1px solid #f1f5f9;
	}

	.shb-info-row:last-child {
		border-bottom: none;
	}

	.shb-info-label {
		flex: 0 0 140px;
		font-weight: 600;
		color: #64748b;
		font-size: 13px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.shb-info-value {
		flex: 1;
		color: #1e293b;
		font-size: 14px;
	}

	.shb-pin-display {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: #fff;
		padding: 16px 20px;
		border-radius: 8px;
		text-align: center;
		margin: 16px 0;
	}

	.shb-pin-label {
		font-size: 12px;
		opacity: 0.9;
		margin-bottom: 8px;
		text-transform: uppercase;
		letter-spacing: 1px;
	}

	.shb-pin-code {
		font-size: 32px;
		font-weight: 700;
		letter-spacing: 4px;
		font-family: 'Courier New', monospace;
	}

	.shb-multiday-table {
		width: 100%;
		border-collapse: collapse;
		margin-top: 12px;
	}

	.shb-multiday-table th {
		background: #f8fafc;
		padding: 10px 12px;
		text-align: left;
		font-size: 12px;
		font-weight: 600;
		color: #64748b;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		border-bottom: 2px solid #e2e8f0;
	}

	.shb-multiday-table td {
		padding: 12px;
		border-bottom: 1px solid #f1f5f9;
		font-size: 14px;
	}

	.shb-multiday-table tr:last-child td {
		border-bottom: none;
	}

	.shb-conflict-alert {
		background: #fef3c7;
		border-left: 4px solid #f59e0b;
		padding: 16px 20px;
		margin-bottom: 20px;
		border-radius: 8px;
	}

	.shb-conflict-alert h4 {
		margin: 0 0 8px 0;
		color: #92400e;
		font-size: 15px;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.shb-conflict-list {
		list-style: none;
		margin: 12px 0 0 0;
		padding: 0;
	}

	.shb-conflict-list li {
		background: #fff;
		padding: 10px 12px;
		margin-bottom: 8px;
		border-radius: 6px;
		font-size: 13px;
	}

	.shb-admin-controls {
		background: #f8fafc;
		padding: 24px;
		border-radius: 8px;
		border: 1px solid #e2e8f0;
	}

	.shb-form-group {
		margin-bottom: 20px;
	}

	.shb-form-group label {
		display: block;
		font-weight: 600;
		color: #475569;
		margin-bottom: 8px;
		font-size: 14px;
	}

	.shb-form-group select,
	.shb-form-group textarea {
		width: 100%;
		padding: 10px 12px;
		border: 1px solid #cbd5e1;
		border-radius: 6px;
		font-size: 14px;
		transition: all 0.2s;
	}

	.shb-form-group select:focus,
	.shb-form-group textarea:focus {
		outline: none;
		border-color: #3b82f6;
		box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
	}

	.shb-form-group .description {
		margin-top: 6px;
		font-size: 13px;
		color: #64748b;
	}

	.shb-action-buttons {
		display: flex;
		gap: 12px;
		padding-top: 20px;
		border-top: 1px solid #e2e8f0;
	}

	.shb-btn {
		padding: 10px 20px;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.2s;
		border: none;
		text-decoration: none;
		display: inline-flex;
		align-items: center;
		gap: 6px;
	}

	.shb-btn-primary {
		background: #3b82f6;
		color: #fff;
	}

	.shb-btn-primary:hover {
		background: #2563eb;
		transform: translateY(-1px);
		box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
	}

	.shb-btn-secondary {
		background: #64748b;
		color: #fff;
	}

	.shb-btn-secondary:hover {
		background: #475569;
	}

	.shb-btn-outline {
		background: #fff;
		color: #64748b;
		border: 1px solid #cbd5e1;
	}

	.shb-btn-outline:hover {
		background: #f8fafc;
		border-color: #94a3b8;
	}

	.shb-icon {
		width: 16px;
		height: 16px;
	}

	@media (max-width: 1024px) {
		.shb-grid-2col {
			grid-template-columns: 1fr;
		}
	}
</style>

<div class="wrap shb-booking-edit-container">
	<div class="shb-page-header">
		<a href="<?php echo esc_url(admin_url('admin.php?page=shb-bookings')); ?>" class="shb-btn shb-btn-outline">
			← <?php esc_html_e('Back to Bookings', 'simple-hall-booking-manager'); ?>
		</a>

		<div style="display: flex; align-items: center; gap: 12px;">
			<h1>
				<?php esc_html_e('Edit Booking', 'simple-hall-booking-manager'); ?>
				<span class="shb-booking-id-badge">#<?php echo esc_html($booking->id); ?></span>
			</h1>
			<span class="shb-status-badge" style="background-color: <?php echo esc_attr($status_color); ?>">
				<?php echo esc_html(shb_get_status_label($booking->status)); ?>
			</span>
		</div>
	</div>

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
		<div class="shb-conflict-alert">
			<h4>⚠️ <?php esc_html_e('Booking Conflicts Detected', 'simple-hall-booking-manager'); ?></h4>
			<p>
				<?php
				/* translators: %d: number of conflicts */
				printf(esc_html__('This booking conflicts with %d other booking(s). If you approve this booking, the conflicting pending bookings will be automatically cancelled.', 'simple-hall-booking-manager'), absint(count($conflicts)));
				?>
			</p>
			<ul class="shb-conflict-list">
				<?php foreach ($conflict_details as $detail): ?>
					<li>
						<strong><?php echo esc_html(sprintf('#%d - %s', $detail['id'], $detail['name'])); ?></strong>
						<br>
						<small style="color: #64748b;">
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

		<div class="shb-grid-2col">
			<!-- Left Column -->
			<div>
				<!-- Customer Information Card -->
				<div class="shb-card">
					<div class="shb-card-header">
						<h3>👤 <?php esc_html_e('Customer Information', 'simple-hall-booking-manager'); ?></h3>
					</div>
					<div class="shb-card-body">
						<div class="shb-info-row">
							<div class="shb-info-label"><?php esc_html_e('Name', 'simple-hall-booking-manager'); ?>
							</div>
							<div class="shb-info-value">
								<strong><?php echo esc_html($booking->customer_name); ?></strong>
							</div>
						</div>
						<div class="shb-info-row">
							<div class="shb-info-label"><?php esc_html_e('Email', 'simple-hall-booking-manager'); ?>
							</div>
							<div class="shb-info-value">
								<a href="mailto:<?php echo esc_attr($booking->customer_email); ?>"
									style="color: #3b82f6;">
									<?php echo esc_html($booking->customer_email); ?>
								</a>
							</div>
						</div>
						<?php if ($booking->customer_phone): ?>
							<div class="shb-info-row">
								<div class="shb-info-label"><?php esc_html_e('Phone', 'simple-hall-booking-manager'); ?>
								</div>
								<div class="shb-info-value"><?php echo esc_html($booking->customer_phone); ?></div>
							</div>
						<?php endif; ?>
						<?php if (!empty($booking->customer_organization)): ?>
							<div class="shb-info-row">
								<div class="shb-info-label">
									<?php esc_html_e('Organization', 'simple-hall-booking-manager'); ?>
								</div>
								<div class="shb-info-value"><?php echo esc_html($booking->customer_organization); ?></div>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Event Details Card -->
				<div class="shb-card" style="margin-top: 20px;">
					<div class="shb-card-header">
						<h3>📅 <?php esc_html_e('Event Details', 'simple-hall-booking-manager'); ?></h3>
					</div>
					<div class="shb-card-body">
						<div class="shb-info-row">
							<div class="shb-info-label"><?php esc_html_e('Hall', 'simple-hall-booking-manager'); ?>
							</div>
							<div class="shb-info-value">
								<strong><?php echo $hall ? esc_html($hall->title) : '-'; ?></strong>
							</div>
						</div>
						<div class="shb-info-row">
							<div class="shb-info-label">
								<?php esc_html_e('Booking Type', 'simple-hall-booking-manager'); ?>
							</div>
							<div class="shb-info-value">
								<?php if ('multiday' === $booking->booking_type): ?>
									<span
										style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
										📅 <?php esc_html_e('Multi-Day', 'simple-hall-booking-manager'); ?>
									</span>
								<?php else: ?>
									<?php esc_html_e('Single Day', 'simple-hall-booking-manager'); ?>
								<?php endif; ?>
							</div>
						</div>

						<?php if ('multiday' === $booking->booking_type): ?>
							<div class="shb-info-row">
								<div class="shb-info-label"><?php esc_html_e('Dates', 'simple-hall-booking-manager'); ?>
								</div>
								<div class="shb-info-value">
									<strong><?php
									/* translators: %d: number of days */
									echo esc_html(sprintf(_n('%d day', '%d days', absint(count($booking_dates)), 'simple-hall-booking-manager'), absint(count($booking_dates)))); ?></strong>
									<?php if (!empty($booking_dates)): ?>
										<table class="shb-multiday-table">
											<thead>
												<tr>
													<th style="width: 40px;">#</th>
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
														<td><?php echo esc_html(wp_date('l', strtotime($date_record->booking_date))); ?>
														</td>
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
									<?php endif; ?>
								</div>
							</div>
						<?php else: ?>
							<div class="shb-info-row">
								<div class="shb-info-label"><?php esc_html_e('Date', 'simple-hall-booking-manager'); ?>
								</div>
								<div class="shb-info-value">
									<strong><?php echo !empty($booking_dates) ? esc_html(shb_format_date($booking_dates[0]->booking_date)) : '-'; ?></strong>
								</div>
							</div>
							<div class="shb-info-row">
								<div class="shb-info-label"><?php esc_html_e('Time Slot', 'simple-hall-booking-manager'); ?>
								</div>
								<div class="shb-info-value">
									<?php
									if ($slot) {
										echo esc_html($slot->label . ' (' . wp_date('g:i A', strtotime($slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($slot->end_time)) . ')');
									} else {
										echo '-';
									}
									?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($booking->event_purpose): ?>
							<div class="shb-info-row">
								<div class="shb-info-label"><?php esc_html_e('Purpose', 'simple-hall-booking-manager'); ?>
								</div>
								<div class="shb-info-value"><?php echo esc_html($booking->event_purpose); ?></div>
							</div>
						<?php endif; ?>
						<?php if ($booking->attendees_count): ?>
							<div class="shb-info-row">
								<div class="shb-info-label"><?php esc_html_e('Attendees', 'simple-hall-booking-manager'); ?>
								</div>
								<div class="shb-info-value"><?php echo esc_html($booking->attendees_count); ?></div>
							</div>
						<?php endif; ?>
						<div class="shb-info-row">
							<div class="shb-info-label"><?php esc_html_e('Booked On', 'simple-hall-booking-manager'); ?>
							</div>
							<div class="shb-info-value">
								<?php echo esc_html(shb_format_date($booking->created_at) . ' ' . wp_date('g:i A', strtotime($booking->created_at))); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Right Column -->
			<div>
				<!-- PIN Card -->
				<div class="shb-card">
					<div class="shb-card-header">
						<h3>🔑 <?php esc_html_e('Access Information', 'simple-hall-booking-manager'); ?></h3>
					</div>
					<div class="shb-card-body">
						<div class="shb-pin-display">
							<div class="shb-pin-label">
								<?php esc_html_e('Booking PIN', 'simple-hall-booking-manager'); ?>
							</div>
							<div class="shb-pin-code"><?php echo esc_html($booking->pin); ?></div>
						</div>
						<p style="font-size: 13px; color: #64748b; text-align: center; margin: 0;">
							<?php esc_html_e('Customer can use this PIN to access their booking', 'simple-hall-booking-manager'); ?>
						</p>
						<div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
							<div class="shb-info-row">
								<div class="shb-info-label" style="flex: 0 0 100px;">
									<?php esc_html_e('Token', 'simple-hall-booking-manager'); ?>
								</div>
								<div class="shb-info-value">
									<code
										style="font-size: 11px; word-break: break-all; background: #f1f5f9; padding: 4px 6px; border-radius: 4px;">
								<?php echo esc_html(substr($booking->access_token, 0, 20) . '...'); ?>
							</code>
								</div>
							</div>
							<a href="<?php echo esc_url(shb_get_booking_access_url($booking->access_token)); ?>"
								target="_blank" class="shb-btn shb-btn-outline"
								style="width: 100%; justify-content: center; margin-top: 16px;">
								🔗 <?php esc_html_e('View Guest Page', 'simple-hall-booking-manager'); ?>
							</a>
						</div>
					</div>
				</div>

				<!-- Admin Controls Card -->
				<div class="shb-card" style="margin-top: 20px;">
					<div class="shb-card-header">
						<h3>⚙️ <?php esc_html_e('Admin Controls', 'simple-hall-booking-manager'); ?></h3>
					</div>
					<div class="shb-card-body">
						<div class="shb-form-group">
							<label
								for="status"><?php esc_html_e('Booking Status', 'simple-hall-booking-manager'); ?></label>
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
						</div>

						<div class="shb-form-group">
							<label
								for="admin_notes"><?php esc_html_e('Admin Notes', 'simple-hall-booking-manager'); ?></label>
							<textarea name="admin_notes" id="admin_notes"
								rows="4"><?php echo esc_textarea($booking->admin_notes); ?></textarea>
							<p class="description">
								<?php esc_html_e('Internal notes (not visible to customer)', 'simple-hall-booking-manager'); ?>
							</p>
						</div>

						<div class="shb-action-buttons">
							<button type="submit" name="shb_save_booking" class="shb-btn shb-btn-primary">
								💾 <?php esc_attr_e('Update Booking', 'simple-hall-booking-manager'); ?>
							</button>
							<button type="submit" name="shb_resend_email" class="shb-btn shb-btn-secondary"
								onclick="return confirm('<?php esc_attr_e('Are you sure you want to resend the status email to the customer?', 'simple-hall-booking-manager'); ?>');">
								📧 <?php esc_attr_e('Resend Email', 'simple-hall-booking-manager'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>