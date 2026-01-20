<?php
/**
 * Admin view: Calendar
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

$db = shb()->db;
$halls = $db->get_halls(array('status' => 'active'));
$current_hall_id = isset($_GET['hall_id']) ? absint($_GET['hall_id']) : (!empty($halls) ? $halls[0]->id : 0);

// Get bookings for the calendar
// Optimized fetch using get_hall_booked_dates to avoid N+1 queries
$start_date = wp_date('Y-m-d', strtotime('-1 year'));
$end_date = wp_date('Y-m-d', strtotime('+2 years'));

$events = array();

if ($current_hall_id) {
	// Fetch all dates (with slots and booking info) in one query
	$booked_dates = $db->get_hall_booked_dates($current_hall_id, $start_date, $end_date);

	foreach ($booked_dates as $b_date) {
		$color = '#3788d8'; // Default blue
		if ('pending' === $b_date->status) {
			$color = '#fbc02d'; // Yellow
		} elseif ('cancelled' === $b_date->status) {
			$color = '#d32f2f'; // Red
		} elseif ('confirmed' === $b_date->status) {
			$color = '#4caf50'; // Green
		}

		$slot_label = !empty($b_date->slot_label) ? $b_date->slot_label : 'Slot';
		$start_time = !empty($b_date->start_time) ? $b_date->start_time : '00:00:00';
		$end_time = !empty($b_date->end_time) ? $b_date->end_time : '23:59:59';

		$events[] = array(
			'title' => '#' . $b_date->booking_id . ' - ' . $b_date->customer_name . ' (' . $slot_label . ')',
			'start' => $b_date->booking_date . 'T' . $start_time,
			'end' => $b_date->booking_date . 'T' . $end_time,
			'url' => admin_url('admin.php?page=shb-bookings&action=edit&id=' . $b_date->booking_id),
			'color' => $color,
		);
	}
}

// FullCalendar is enqueued via wp_enqueue_style/wp_enqueue_script in class-shb-admin.php
?>

<style>
	/* Calendar page styling */
	.shb-calendar-wrapper {
		background: #fff;
		border-radius: 8px;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
		padding: 24px;
		margin-top: 20px;
	}

	.shb-calendar-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 24px;
		flex-wrap: wrap;
		gap: 16px;
	}

	.shb-calendar-controls form {
		display: flex;
		align-items: center;
		gap: 12px;
	}

	.shb-calendar-controls select {
		padding: 6px 32px 6px 12px;
		border-radius: 4px;
		border: 1px solid #dcdcde;
		font-size: 14px;
		min-width: 200px;
	}

	.shb-print-btn {
		background: #fff;
		color: #2271b1;
		border: 1px solid #2271b1;
		padding: 6px 16px;
		border-radius: 4px;
		cursor: pointer;
		font-weight: 500;
		transition: all 0.2s;
		display: inline-flex;
		align-items: center;
		gap: 8px;
		text-decoration: none;
	}

	.shb-print-btn:hover {
		background: #f0f6fc;
		color: #135e96;
		border-color: #135e96;
	}

	.shb-print-btn:before {
		content: "\f469";
		/* dashicons-printer */
		font-family: dashicons;
		font-size: 18px;
	}

	/* FullCalendar Overrides */
	.fc-toolbar-title {
		font-size: 1.5em !important;
		font-weight: 600;
		color: #1d2327;
	}

	.fc-button-primary {
		background-color: #2271b1 !important;
		border-color: #2271b1 !important;
		text-transform: capitalize;
		font-weight: 500;
		transition: all 0.2s;
	}

	.fc-button-primary:hover {
		background-color: #135e96 !important;
		border-color: #135e96 !important;
	}

	.fc-button-active {
		background-color: #135e96 !important;
		border-color: #135e96 !important;
	}

	.fc-event {
		border: none;
		border-radius: 4px;
		padding: 2px 4px;
		font-size: 0.85em;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		cursor: pointer;
		transition: transform 0.1s;
	}

	.fc-event:hover {
		transform: translateY(-1px);
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
	}

	/* Legend */
	.shb-legend {
		display: flex;
		gap: 20px;
		margin-bottom: 20px;
		padding: 12px;
		background: #f8f9fa;
		border-radius: 6px;
		border: 1px solid #e0e0e0;
	}

	.shb-legend-item {
		display: flex;
		align-items: center;
		gap: 8px;
		font-size: 13px;
		color: #50575e;
	}

	.shb-dot {
		width: 10px;
		height: 10px;
		border-radius: 50%;
	}

	/* Print Styles */
	@media print {

		/* Global resets for print */
		body,
		html {
			background: white !important;
			width: 100% !important;
			height: auto !important;
			overflow: visible !important;
			font-size: 10pt !important;
		}

		/* Hide standard WordPress admin elements */
		#adminmenumain,
		#wpadminbar,
		#wpfooter,
		.update-nag,
		.notice,
		#wp-admin-bar-root-default,
		#screen-meta,
		#screen-meta-links,
		.wp-heading-inline,
		.wp-header-end,
		.shb-calendar-controls,
		.shb-print-btn {
			display: none !important;
		}

		/* Reset the wrapper positioning */
		.wrap {
			margin: 0 !important;
			padding: 0 !important;
			width: 100% !important;
			max-width: none !important;
		}

		.shb-calendar-wrapper {
			position: static !important;
			background: none !important;
			box-shadow: none !important;
			padding: 0 !important;
			margin: 0 !important;
			width: 100% !important;
			border: none !important;
		}

		.shb-calendar-header {
			margin-bottom: 10px !important;
		}

		/* Title for print */
		.shb-calendar-wrapper:before {
			content: "Booking Calendar";
			display: block;
			font-size: 24px;
			font-weight: bold;
			margin-bottom: 20px;
			text-align: center;
		}

		/* FullCalendar Specifics for Print */
		#calendar {
			max-width: 100% !important;
		}

		.fc {
			max-width: 100% !important;
		}

		.fc-header-toolbar {
			display: none !important;
			/* Hide calendar navigation buttons on print */
		}

		.fc-view-harness {
			height: auto !important;
			overflow: visible !important;
		}

		.fc-scroller {
			height: auto !important;
			overflow: visible !important;
		}

		.fc-daygrid-body {
			width: 100% !important;
		}

		.fc-theme-standard th {
			background: #f0f0f1 !important;
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
			padding: 5px !important;
		}

		.fc-event {
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
			border: 1px solid #ccc !important;
			break-inside: avoid;
			page-break-inside: avoid;
		}

		/* Ensure grid lines show */
		.fc-theme-standard td,
		.fc-theme-standard th {
			border: 1px solid #ccc !important;
		}
	}
</style>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e('Booking Calendar', 'simple-hall-booking-manager'); ?></h1>
	<hr class="wp-header-end">

	<div class="shb-calendar-wrapper">
		<div class="shb-calendar-header">
			<div class="shb-calendar-controls">
				<form method="get" action="">
					<input type="hidden" name="page" value="shb-calendar">
					<select name="hall_id" id="filter_hall" onchange="this.form.submit()">
						<?php if (empty($halls)): ?>
							<option value=""><?php esc_html_e('No halls available', 'simple-hall-booking-manager'); ?>
							</option>
						<?php else: ?>
							<?php foreach ($halls as $hall): ?>
								<option value="<?php echo esc_attr($hall->id); ?>" <?php selected($current_hall_id, $hall->id); ?>>
									<?php echo esc_html($hall->title); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</form>
			</div>

			<button class="shb-print-btn" onclick="window.print()">
				<?php esc_html_e('Print Calendar', 'simple-hall-booking-manager'); ?>
			</button>
		</div>

		<div class="shb-legend">
			<div class="shb-legend-item">
				<span class="shb-dot" style="background-color: #fbc02d;"></span>
				<span><?php esc_html_e('Pending', 'simple-hall-booking-manager'); ?></span>
			</div>
			<div class="shb-legend-item">
				<span class="shb-dot" style="background-color: #4caf50;"></span>
				<span><?php esc_html_e('Confirmed', 'simple-hall-booking-manager'); ?></span>
			</div>
			<div class="shb-legend-item">
				<span class="shb-dot" style="background-color: #d32f2f;"></span>
				<span><?php esc_html_e('Cancelled', 'simple-hall-booking-manager'); ?></span>
			</div>
		</div>

		<div id="calendar"></div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function () {
			var calendarEl = document.getElementById('calendar');
			var calendar = new FullCalendar.Calendar(calendarEl, {
				initialView: 'dayGridMonth',
				themeSystem: 'standard',
				headerToolbar: {
					left: 'prev,next today',
					center: 'title',
					right: 'dayGridMonth,timeGridWeek,listMonth'
				},
				buttonText: {
					today: '<?php esc_html_e('Today', 'simple-hall-booking-manager'); ?>',
					month: '<?php esc_html_e('Month', 'simple-hall-booking-manager'); ?>',
					week: '<?php esc_html_e('Week', 'simple-hall-booking-manager'); ?>',
					list: '<?php esc_html_e('List', 'simple-hall-booking-manager'); ?>'
				},
				events: <?php echo json_encode($events); ?>,
				eventClick: function (info) {
					if (info.event.url) {
						window.location.href = info.event.url;
						info.jsEvent.preventDefault();
					}
				},
				eventTimeFormat: { // like '2:30 PM'
					hour: 'numeric',
					minute: '2-digit',
					meridiem: 'short'
				},
				height: 'auto',
				aspectRatio: 1.8,
				displayEventTime: true,
			});
			calendar.render();
		});
	</script>
</div>