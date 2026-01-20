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
$current_hall_id = isset($_GET['hall_id']) ? absint($_GET['hall_id']) : 0;

// Get bookings for the calendar
// Optimized fetch using get_hall_booked_dates to avoid N+1 queries
$start_date = wp_date('Y-m-d', strtotime('-1 year'));
$end_date = wp_date('Y-m-d', strtotime('+2 years'));

$events = array();

if (true) {
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
	/* FullCalendar Overrides - Google Calendar Style */
	.fc {
		font-family: "Roboto", "HelveticaNeue", "Helvetica", "Arial", sans-serif;
	}

	.fc-toolbar-title {
		font-size: 22px !important;
		font-weight: 400 !important;
		color: #3c4043;
	}

	/* Buttons (Navigation & Views) */
	.fc-button {
		background-color: transparent !important;
		border: 1px solid #dadce0 !important;
		color: #3c4043 !important;
		font-weight: 500 !important;
		font-size: 14px !important;
		padding: 7px 16px !important;
		border-radius: 4px !important;
		box-shadow: none !important;
		text-transform: none !important;
		transition: background-color 0.2s, box-shadow 0.2s !important;
	}

	.fc-button:hover {
		background-color: #f1f3f4 !important;
		border-color: #dadce0 !important;
		color: #3c4043 !important;
	}

	.fc-button-active {
		background-color: #e8f0fe !important;
		color: #1967d2 !important;
		border-color: #e8f0fe !important;
	}

	.fc-button-primary:not(:disabled).fc-button-active:focus,
	.fc-button-primary:not(:disabled):active:focus {
		box-shadow: none !important;
	}

	/* Button Groups */
	.fc-button-group>.fc-button {
		border-radius: 0 !important;
		margin: 0 !important;
	}

	.fc-button-group>.fc-button:first-child {
		border-top-left-radius: 4px !important;
		border-bottom-left-radius: 4px !important;
	}

	.fc-button-group>.fc-button:last-child {
		border-top-right-radius: 4px !important;
		border-bottom-right-radius: 4px !important;
	}

	/* Grid & Headers */
	.fc-col-header-cell-cushion {
		text-transform: uppercase;
		font-size: 11px;
		font-weight: 500;
		color: #70757a;
		padding: 10px 0 !important;
		text-decoration: none !important;
	}

	.fc-scrollgrid {
		border: 1px solid #dadce0 !important;
		border-radius: 8px;
		overflow: hidden;
	}

	.fc-theme-standard td,
	.fc-theme-standard th {
		border-color: #dadce0 !important;
	}

	/* Day Numbers */
	.fc-daygrid-day-top {
		justify-content: center !important;
		padding-top: 4px;
	}

	.fc-daygrid-day-number {
		font-size: 12px;
		color: #3c4043;
		font-weight: 500;
		width: 24px;
		height: 24px;
		line-height: 24px;
		text-align: center;
		border-radius: 50%;
		text-decoration: none !important;
		margin-top: 2px;
	}

	.fc-daygrid-day-number:hover {
		background-color: #f1f3f4;
		border-radius: 50%;
	}

	/* Today Highlight */
	.fc-day-today {
		background-color: transparent !important;
	}

	.fc-day-today .fc-daygrid-day-number {
		background-color: #1a73e8;
		color: #fff;
	}

	.fc-day-today .fc-daygrid-day-number:hover {
		background-color: #1a73e8;
	}

	/* Events */
	.fc-event {
		border: none !important;
		border-radius: 4px;
		padding: 2px 6px;
		font-size: 12px;
		font-weight: 500;
		box-shadow: 0 1px 2px rgba(60, 64, 67, 0.3);
		margin: 1px 4px !important;
	}

	.fc-h-event .fc-event-main {
		color: #fff !important;
		/* Ensure text is white on colored events */
	}

	/* Legend */
	.shb-legend {
		display: flex;
		align-items: center;
		justify-content: flex-start;
		background: transparent;
		border: none;
		padding: 0;
		margin-bottom: 24px;
		gap: 24px;
	}

	.shb-legend-item {
		display: flex;
		align-items: center;
		gap: 8px;
		font-family: inherit;
		font-weight: 500;
		color: #3c4043;
		font-size: 14px;
	}

	.shb-dot {
		width: 12px;
		height: 12px;
		border-radius: 50%;
		display: inline-block;
	}

	/* Print Styles Start Here */
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

	@media print {

		/* Hide WordPress Admin Elements */
		#adminmenumain,
		#wpadminbar,
		#adminmenuback,
		#wpfooter,
		.notice,
		.updated,
		.shb-calendar-controls,
		.shb-print-btn,
		.wp-heading-inline,
		.wp-header-end,
		#wpcontent>.wrap>h1,
		#screen-meta,
		#screen-meta-links {
			display: none !important;
		}

		/* Reset Layout for Print */
		html,
		body {
			background: #fff !important;
			margin: 0 !important;
			padding: 0 !important;
			height: 100%;
			overflow: visible !important;
			font-size: 10pt !important;
		}

		#wpcontent {
			margin-left: 0 !important;
			padding: 0 !important;
			background: #fff !important;
		}

		.shb-calendar-wrapper {
			margin: 0 !important;
			padding: 5px !important;
			box-shadow: none !important;
			border: none !important;
			width: 100% !important;
			max-width: 100% !important;
		}

		/* FullCalendar Corrections for Print */
		.fc-theme-standard .fc-scrollgrid {
			border: 1px solid #ddd !important;
		}

		.fc-header-toolbar {
			display: none !important;
		}

		.fc-scroller {
			overflow: visible !important;
			height: auto !important;
		}

		/* Ensure colors are printed */
		* {
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
		}

		/* Avoid page breaks inside events */
		.fc-event {
			break-inside: avoid;
			page-break-inside: avoid;
		}

		/* Compact Events */
		.fc-event-title,
		.fc-event-time {
			font-size: 9px !important;
			padding: 0 1px !important;
		}

		/* List View Compact */
		.fc-list-table {
			font-size: 10px !important;
		}

		/* Custom Header for Print */
		.shb-calendar-wrapper::before {
			content: "Hall Booking Calendar";
			display: block;
			font-size: 18px;
			font-weight: bold;
			margin-bottom: 10px;
			text-align: center;
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
						<option value="0" <?php selected($current_hall_id, 0); ?>>
							<?php esc_html_e('All Halls', 'simple-hall-booking-manager'); ?>
						</option>
						<?php if (empty($halls)): ?>
							<option value="" disabled>
								<?php esc_html_e('No halls available', 'simple-hall-booking-manager'); ?>
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
					right: 'dayGridMonth,timeGridWeek,listMonth,listYear'
				},
				buttonText: {
					today: '<?php esc_html_e('Today', 'simple-hall-booking-manager'); ?>',
					month: '<?php esc_html_e('Month', 'simple-hall-booking-manager'); ?>',
					week: '<?php esc_html_e('Week', 'simple-hall-booking-manager'); ?>',
					list: '<?php esc_html_e('List', 'simple-hall-booking-manager'); ?>',
					listYear: '<?php esc_html_e('Year', 'simple-hall-booking-manager'); ?>'
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