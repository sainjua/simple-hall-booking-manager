<?php
/**
 * Admin view: Booking edit (Elegant Professional Design - Unified Columns)
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

// Calculate status variables
$status_label = shb_get_status_label($booking->status);
$status_class = 'status-' . $booking->status;

// Check for conflicts
$conflicts = array();
if ('pending' === $booking->status) {
	$conflict_ids = $db->get_conflicting_bookings($booking->id, true);
	if (!empty($conflict_ids)) {
		foreach ($conflict_ids as $conflict_id) {
			$conflict_booking = $db->get_booking($conflict_id);
			if ($conflict_booking) {
				$conflicts[] = $conflict_booking;
			}
		}
	}
}
?>

<style>
	:root {
		--shb-primary: #2563eb;
		--shb-primary-dark: #1d4ed8;
		--shb-text-main: #1f2937;
		--shb-text-muted: #6b7280;
		--shb-text-light: #9ca3af;
		--shb-bg-card: #ffffff;
		--shb-border-subtle: #e2e8f0;
		--shb-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
		--shb-shadow-md: 0 1px 3px 0 rgba(0, 0, 0, 0.02), 0 1px 2px -1px rgba(0, 0, 0, 0.02);
	}

	.shb-container {
		max-width: 1200px;
		margin: 32px 20px 32px 0; /* Adjust for WP admin sidebar */
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
		color: var(--shb-text-main);
	}

	/* Header */
	.shb-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 24px;
	}

	.shb-back-link {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		color: var(--shb-text-muted);
		text-decoration: none;
		font-size: 13px;
		font-weight: 500;
		transition: color 0.2s;
	}

	.shb-back-link:hover {
		color: var(--shb-primary);
	}

	.shb-title-group h1 {
		font-size: 24px;
		font-weight: 600;
		color: #111827;
		margin: 0;
		letter-spacing: -0.01em;
		display: flex;
		align-items: center;
		gap: 12px;
	}

	.shb-id-pill {
		font-size: 13px;
		font-weight: 500;
		background: #f1f5f9;
		color: #64748b;
		padding: 2px 8px;
		border-radius: 4px;
		vertical-align: middle;
        letter-spacing: normal;
        border: 1px solid #e2e8f0;
	}

	/* Grid Layout */
	.shb-grid {
		display: grid;
		grid-template-columns: 2fr 1fr;
		gap: 24px;
		align-items: stretch; /* Stretch to keep equal height */
	}

    .shb-main, .shb-sidebar {
        display: flex;
        flex-direction: column;
    }

	/* Elegant Card Styling */
	.shb-card {
		background: var(--shb-bg-card);
		border-radius: 6px;
		box-shadow: var(--shb-shadow-md);
		border: 1px solid var(--shb-border-subtle);
		overflow: hidden;
        height: 100%; /* Fill the column height */
        display: flex;
        flex-direction: column;
	}

    .shb-section {
        padding: 24px;
        border-bottom: 1px solid var(--shb-border-subtle);
    }
    .shb-section:last-child {
        border-bottom: none;
    }

	.shb-section-title {
		font-size: 13px;
		font-weight: 600;
		color: var(--shb-text-muted);
		margin: 0 0 16px 0;
		text-transform: uppercase;
		letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 8px;
	}

	.shb-section-title .dashicons {
		color: var(--shb-text-light);
		font-size: 16px;
		width: 16px;
		height: 16px;
        vertical-align:text-bottom;
	}

	/* Data Display */
	.shb-data-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
		gap: 24px;
	}

	.shb-data-item {
		display: flex;
		flex-direction: column;
		gap: 4px;
	}

	.shb-label {
		font-size: 12px;
		font-weight: 500;
		color: var(--shb-text-muted);
		text-transform: uppercase;
		letter-spacing: 0.02em;
	}

	.shb-value {
		font-size: 14px;
		font-weight: 500;
		color: #111827;
        line-height: 1.5;
	}

	.shb-value a {
		color: var(--shb-primary);
		text-decoration: none;
	}

	.shb-value a:hover {
		text-decoration: underline;
	}

	/* Status Badge */
	.shb-status {
		padding: 4px 12px;
		border-radius: 9999px;
		font-size: 12px;
		font-weight: 600;
		letter-spacing: 0.025em;
		text-transform: capitalize;
		display: inline-flex;
		align-items: center;
		gap: 6px;
	}

	.status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fcd34d; }
	.status-confirmed { background: #ecfdf5; color: #047857; border: 1px solid #6ee7b7; }
	.status-cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

	/* PIN & Token */
	.shb-access-box {
		background: #f8fafc;
		border-radius: 6px;
		padding: 16px;
		text-align: center;
		border: 1px solid #e2e8f0;
	}

	.shb-pin {
		font-family: 'SF Mono', 'Roboto Mono', 'Courier New', monospace;
		font-size: 24px;
		font-weight: 700;
		color: #334155;
		letter-spacing: 2px;
		margin: 8px 0;
		display: block;
	}

    .shb-token-code {
        background: #e2e8f0;
        color: #64748b;
        font-family: monospace;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        display: inline-block;
        margin-top: 4px;
    }

	/* Buttons */
	.shb-button {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: 8px;
		padding: 8px 16px;
		border-radius: 6px;
		font-size: 13px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s ease;
		border: 1px solid transparent;
		text-decoration: none;
        box-sizing: border-box;
	}

	.btn-primary {
		background: var(--shb-primary);
		color: #fff;
        border: 1px solid var(--shb-primary-dark);
	}
	.btn-primary:hover {
		background-color: var(--shb-primary-dark);
        color: #fff;
		transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
	}

	.btn-secondary {
		background: #fff;
		border-color: #d1d5db;
		color: #374151;
	}
	.btn-secondary:hover {
		background: #f9fafb;
		border-color: #9ca3af;
        color: #111827;
	}

	.btn-link {
        color: var(--shb-primary);
        font-weight: 500;
        text-decoration: none;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 16px;
    }
    .btn-link:hover { text-decoration: underline; }

	/* Forms */
    textarea.shb-form-control,
	select.shb-form-control {
		display: block;
		width: 100%;
		padding: 8px 12px;
		border: 1px solid #d1d5db;
		border-radius: 6px;
		font-size: 13px;
		color: #111827;
		margin-top: 6px;
		transition: border-color 0.15s;
        box-sizing: border-box;
        background: #fff;
	}
    
	textarea.shb-form-control:focus,
    select.shb-form-control:focus {
		border-color: var(--shb-primary);
		outline: none;
		box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
	}

    .shb-conflict-box {
        background-color: #fff7ed;
        border: 1px solid #fed7aa;
        padding: 16px;
        border-radius: 6px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .shb-conflict-icon {
        color: #ea580c;
        flex-shrink: 0;
        margin-top: 2px;
    }

	@media (max-width: 1024px) {
		.shb-grid { grid-template-columns: 1fr; }
        .shb-container { margin-right: 20px; }
	}
</style>

<div class="wrap shb-container">
    
    <!-- Header -->
	<div class="shb-header">
		<div class="shb-title-group">
			<a href="<?php echo esc_url(admin_url('admin.php?page=shb-bookings')); ?>" class="shb-back-link">
                <span class="dashicons dashicons-arrow-left-alt"></span> <?php esc_html_e('Back to Bookings', 'simple-hall-booking-manager'); ?>
            </a>
			<h1 style="margin-top: 12px; font-size: 24px;">
				<?php esc_html_e('Booking Details', 'simple-hall-booking-manager'); ?>
				<span class="shb-id-pill">#<?php echo esc_html($booking->id); ?></span>
			</h1>
		</div>
		<div class="shb-status-pill">
			<span class="shb-status <?php echo esc_attr($status_class); ?>">
				<span class="dashicons dashicons-marker" style="font-size:16px; width:16px; height:16px; vertical-align:middle;"></span>
				<?php echo esc_html($status_label); ?>
			</span>
		</div>
	</div>

    <?php if (isset($_GET['message'])) : 
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $msg = sanitize_text_field(wp_unslash($_GET['message']));
        $notice_class = ($msg === 'email_resent') ? 'notice-success' : 'notice-error';
        $notice_text = ($msg === 'email_resent') ? __('Email resent successfully.', 'simple-hall-booking-manager') : __('Email failed.', 'simple-hall-booking-manager');
    ?>
    <div class="notice <?php echo esc_attr($notice_class); ?> is-dismissible" style="margin-left: 0; margin-bottom: 32px;"><p><?php echo esc_html($notice_text); ?></p></div>
    <?php endif; ?>

    <?php if (!empty($conflicts)): ?>
        <div class="shb-conflict-box">
            <span class="dashicons dashicons-warning shb-conflict-icon"></span>
            <div>
                <h4 style="margin: 0 0 4px 0; color: #9a3412; font-size: 14px; font-weight: 600;"><?php esc_html_e('Scheduling Conflicts Detected', 'simple-hall-booking-manager'); ?></h4>
                <p style="margin: 0; color: #7c2d12; font-size: 13px; line-height: 1.5;">
                    <?php printf(esc_html__('This booking conflicts with %d potentially pending booking(s). Approving will automatically cancel overlapping pending requests.', 'simple-hall-booking-manager'), count($conflicts)); ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

	<form method="post" action="">
		<?php wp_nonce_field('shb_save_booking'); ?>
		<input type="hidden" name="booking_id" value="<?php echo esc_attr($booking_id); ?>">
		<input type="hidden" name="old_status" value="<?php echo esc_attr($booking->status); ?>">

		<div class="shb-grid">
			<!-- Main Content Column -->
			<div class="shb-main">
				<div class="shb-card">
                    <!-- Customer Section -->
                    <div class="shb-section">
                        <div class="shb-section-title">
                            <span class="dashicons dashicons-admin-users"></span>
                            <?php esc_html_e('Customer Profile', 'simple-hall-booking-manager'); ?>
                        </div>
                        <div class="shb-data-grid">
                            <div class="shb-data-item">
                                <span class="shb-label"><?php esc_html_e('Full Name', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-value"><?php echo esc_html($booking->customer_name); ?></span>
                            </div>
                            <div class="shb-data-item">
                                <span class="shb-label"><?php esc_html_e('Email Address', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-value">
                                    <a href="mailto:<?php echo esc_attr($booking->customer_email); ?>">
                                        <?php echo esc_html($booking->customer_email); ?>
                                    </a>
                                </span>
                            </div>
                            <?php if ($booking->customer_phone): ?>
                            <div class="shb-data-item">
                                <span class="shb-label"><?php esc_html_e('Phone', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-value"><?php echo esc_html($booking->customer_phone); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($booking->customer_organization)): ?>
                            <div class="shb-data-item">
                                <span class="shb-label"><?php esc_html_e('Organization', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-value"><?php echo esc_html($booking->customer_organization); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Schedule Section -->
                    <div class="shb-section">
                        <div class="shb-section-title">
                            <span class="dashicons dashicons-calendar-alt"></span>
                            <?php esc_html_e('Schedule & Timing', 'simple-hall-booking-manager'); ?>
                        </div>
                        <?php if ('multiday' === $booking->booking_type): ?>
                            <div style="margin-bottom: 16px;">
                                <span class="shb-status" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:12px;">
                                    <span class="dashicons dashicons-calendar-alt" style="font-size:14px; width:14px; height:14px;"></span>
                                    <?php esc_html_e('Multi-Day Event', 'simple-hall-booking-manager'); ?>
                                </span>
                            </div>
                            <table class="wp-list-table widefat fixed striped" style="border:none; box-shadow:none;">
                                <thead>
                                    <tr>
                                        <th style="font-weight:600; color:#6b7280; font-size:12px;"><?php esc_html_e('Date', 'simple-hall-booking-manager'); ?></th>
                                        <th style="font-weight:600; color:#6b7280; font-size:12px;"><?php esc_html_e('Day', 'simple-hall-booking-manager'); ?></th>
                                        <th style="font-weight:600; color:#6b7280; font-size:12px;"><?php esc_html_e('Time Slot', 'simple-hall-booking-manager'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($booking_dates as $date_record): 
                                        $d_slot = $db->get_slot($date_record->slot_id);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo esc_html(shb_format_date($date_record->booking_date)); ?></strong></td>
                                        <td><?php echo esc_html(wp_date('l', strtotime($date_record->booking_date))); ?></td>
                                        <td><?php echo $d_slot ? esc_html($d_slot->label . ' (' . wp_date('g:i A', strtotime($d_slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($d_slot->end_time)) . ')') : '-'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="shb-data-grid">
                                <div class="shb-data-item">
                                    <span class="shb-label"><?php esc_html_e('Event Date', 'simple-hall-booking-manager'); ?></span>
                                    <span class="shb-value" style="font-size:18px;">
                                        <?php echo !empty($booking_dates) ? esc_html(shb_format_date($booking_dates[0]->booking_date)) : '-'; ?>
                                    </span>
                                </div>
                                <div class="shb-data-item">
                                    <span class="shb-label"><?php esc_html_e('Time Slot', 'simple-hall-booking-manager'); ?></span>
                                    <span class="shb-value">
                                        <?php if ($slot) echo esc_html($slot->label . ' (' . wp_date('g:i A', strtotime($slot->start_time)) . ' - ' . wp_date('g:i A', strtotime($slot->end_time)) . ')'); else echo '-'; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Event Details Section -->
                    <div class="shb-section">
                        <div class="shb-section-title">
                            <span class="dashicons dashicons-tickets-alt"></span>
                            <?php esc_html_e('Event Specifics', 'simple-hall-booking-manager'); ?>
                        </div>
                        <div class="shb-data-grid">
                            <div class="shb-data-item">
                                <span class="shb-label"><?php esc_html_e('Venue Hall', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-value"><?php echo $hall ? esc_html($hall->title) : '-'; ?></span>
                            </div>
                            <div class="shb-data-item">
                                <span class="shb-label"><?php esc_html_e('Event Purpose', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-value"><?php echo esc_html($booking->event_purpose ?: '-'); ?></span>
                            </div>
                            <div class="shb-data-item">
                                <span class="shb-label"><?php esc_html_e('Expected Attendees', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-value"><?php echo esc_html($booking->attendees_count ?: '-'); ?></span>
                            </div>
                            <div class="shb-data-item">
                                <span class="shb-label"><?php esc_html_e('Booked On', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-value" style="color:#6b7280; font-size:13px;">
                                    <?php echo esc_html(shb_format_date($booking->created_at) . ' at ' . wp_date('g:i A', strtotime($booking->created_at))); ?>
                                </span>
                            </div>
                        </div>
                    </div>
				</div>
			</div>

			<!-- Sidebar Column -->
			<div class="shb-sidebar">
                <div class="shb-card">
                    <!-- Access Control Section -->
                    <div class="shb-section">
                        <div class="shb-section-title">
                            <span class="dashicons dashicons-lock"></span>
                            <?php esc_html_e('Access Control', 'simple-hall-booking-manager'); ?>
                        </div>
                        <div style="text-align: center;">
                            <div class="shb-access-box">
                                <span class="shb-label" style="display:block; margin-bottom:4px;"><?php esc_html_e('Entry PIN', 'simple-hall-booking-manager'); ?></span>
                                <span class="shb-pin"><?php echo esc_html($booking->pin); ?></span>
                                <div style="margin-top: 8px;">
                                    <span class="shb-token-code" title="<?php esc_attr_e('Security Token', 'simple-hall-booking-manager'); ?>">
                                        Token: <?php echo esc_html(substr($booking->access_token, 0, 12) . '...'); ?>
                                    </span>
                                </div>
                            </div>
                            <a href="<?php echo esc_url(shb_get_booking_access_url($booking->access_token)); ?>" target="_blank" class="btn-link">
                                <span class="dashicons dashicons-external" style="font-size:14px; width:14px; height: 14px; vertical-align:text-bottom;"></span>
                                <?php esc_html_e('View Guest Page', 'simple-hall-booking-manager'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Actions Section -->
                    <div class="shb-section" style="flex: 1; display: flex; flex-direction: column;">
                        <div class="shb-section-title">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e('Actions', 'simple-hall-booking-manager'); ?>
                        </div>
                        
                        <div style="flex-grow: 1;">
                            <div style="margin-bottom: 24px;">
                                <label class="shb-label" for="status"><?php esc_html_e('Status', 'simple-hall-booking-manager'); ?></label>
                                <select name="status" id="status" class="shb-form-control">
                                    <?php foreach (shb_get_booking_statuses() as $status_val => $status_lbl): ?>
                                        <option value="<?php echo esc_attr($status_val); ?>" <?php selected($booking->status, $status_val); ?>>
                                            <?php echo esc_html($status_lbl); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div style="margin-bottom: 24px;">
                                <label class="shb-label" for="admin_notes"><?php esc_html_e('Internal Notes', 'simple-hall-booking-manager'); ?></label>
                                <textarea name="admin_notes" id="admin_notes" rows="4" class="shb-form-control" placeholder="<?php esc_attr_e('Add private notes...', 'simple-hall-booking-manager'); ?>"><?php echo esc_textarea($booking->admin_notes); ?></textarea>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 12px; margin-top: auto;">
                            <button type="submit" name="shb_save_booking" class="shb-button btn-primary" style="width:100%;">
                                <span class="dashicons dashicons-saved" style="color:#fff;"></span>
                                <?php esc_html_e('Update Booking', 'simple-hall-booking-manager'); ?>
                            </button>
                            <button type="submit" name="shb_resend_email" class="shb-button btn-secondary" style="width:100%;" onclick="return confirm('<?php esc_attr_e('Resend email?', 'simple-hall-booking-manager'); ?>');">
                                <span class="dashicons dashicons-email"></span>
                                <?php esc_html_e('Resend Email', 'simple-hall-booking-manager'); ?>
                            </button>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</form>
</div>