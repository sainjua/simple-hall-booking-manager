<?php
/**
 * Admin view: Bookings list
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

$db = shb()->db;

// Pagination setup
$per_page = 20; // Items per page
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read‑only pagination GET
$current_page = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Handle filters
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Read‑only filter GET
$filter_status = isset($_GET['filter_status']) ? sanitize_text_field(wp_unslash($_GET['filter_status'])) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read‑only filter GET
$filter_hall_id = isset($_GET['filter_hall']) ? absint($_GET['filter_hall']) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Read‑only search GET
$search_term = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

// Handle Sorting
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read‑only sort GET
$orderby = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'created_at';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read‑only sort GET
$order = isset($_GET['order']) ? strtoupper(sanitize_text_field($_GET['order'])) : 'DESC';

// Whitelist orderby
$allowed_orderby = array('created_at', 'booking_date', 'customer_name', 'status', 'hall_id');
if (!in_array($orderby, $allowed_orderby, true)) {
	$orderby = 'created_at';
}
if (!in_array($order, array('ASC', 'DESC'), true)) {
	$order = 'DESC';
}

$filters = array(
	'limit' => $per_page,
	'offset' => $offset,
	'orderby' => $orderby,
	'order' => $order,
);

if ($filter_status) {
	$filters['status'] = $filter_status;
}
if ($filter_hall_id) {
	$filters['hall_id'] = $filter_hall_id;
}
if ($search_term) {
	$filters['search'] = $search_term;
}

$bookings = $db->get_bookings($filters);

// Get total count for pagination (without limit)
$count_filters = array();
if ($filter_status) {
	$count_filters['status'] = $filter_status;
}
if ($filter_hall_id) {
	$count_filters['hall_id'] = $filter_hall_id;
}
if ($search_term) {
	$count_filters['search'] = $search_term;
}
$total_bookings = count($db->get_bookings($count_filters));
$total_pages = ceil($total_bookings / $per_page);

$halls = $db->get_halls(array('status' => 'active'));

// Get counts for status links (All | Pending | Confirmed | Cancelled)
$status_links = array(
	'all' => __('All', 'simple-hall-booking-manager'),
	'pending' => __('Pending', 'simple-hall-booking-manager'),
	'confirmed' => __('Confirmed', 'simple-hall-booking-manager'),
	'cancelled' => __('Cancelled', 'simple-hall-booking-manager'),
);

// Handle messages
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Read‑only message GET
$message = isset($_GET['message']) ? sanitize_text_field(wp_unslash($_GET['message'])) : '';

/**
 * Helper to generate sortable column link
 */
function shb_sort_link($id, $label, $current_orderby, $current_order)
{
	$new_order = ($current_orderby === $id && $current_order === 'DESC') ? 'ASC' : 'DESC';
	$arrow = '';
	if ($current_orderby === $id) {
		$arrow = ($current_order === 'ASC') ? ' &uarr;' : ' &darr;';
	}
	$url = add_query_arg(array('orderby' => $id, 'order' => $new_order));
	return '<a href="' . esc_url($url) . '" style="color: inherit; text-decoration: none;">' . esc_html($label) . $arrow . '</a>';
}
?>

<style>
	/* Container */
	.shb-bookings-list-wrap {
		font-family: 'Roboto', "Helvetica Neue", Helvetica, Arial, sans-serif;
		max-width: 1200px;
		margin: 20px 0;
	}

	.shb-bookings-list-wrap h1 {
		font-weight: 400;
		color: #202124;
		margin-bottom: 20px;
	}

	/* Controls Grid */
	.shb-controls {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 15px;
		flex-wrap: wrap;
		gap: 15px;
	}

	.shb-filters-bar {
		display: flex;
		gap: 10px;
		align-items: center;
	}

	.shb-status-nav {
		margin: 0;
		display: flex;
		gap: 5px;
		list-style: none;
	}

	.shb-status-nav li a {
		text-decoration: none;
		color: #5f6368;
		padding: 5px 10px;
		border-radius: 4px;
		font-size: 13px;
		font-weight: 500;
		transition: background 0.2s;
	}

	.shb-status-nav li a:hover {
		background-color: #f1f3f4;
		color: #202124;
	}

	.shb-status-nav li a.current {
		background-color: #e8f0fe;
		color: #1967d2;
		font-weight: 600;
	}

	/* Table Design */
	.shb-table {
		width: 100%;
		border-collapse: separate;
		border-spacing: 0;
		background: #fff;
		/* box-shadow: 0 1px 2px rgba(60,64,67, 0.3); Removed per user request */
		border: 1px solid #dadce0;
		border-radius: 8px;
		overflow: hidden;
	}

	.shb-table thead {
		background-color: #f8f9fa;
	}

	.shb-table th {
		padding: 12px 16px;
		text-align: left;
		font-size: 13px;
		font-weight: 600;
		color: #5f6368;
		border-bottom: 1px solid #dadce0;
		user-select: none;
	}

	.shb-table td {
		padding: 12px 16px;
		border-bottom: 1px solid #f1f3f4;
		color: #3c4043;
		font-size: 14px;
		vertical-align: middle;
	}

	.shb-table tr:last-child td {
		border-bottom: none;
	}

	.shb-table tr:hover td {
		background-color: #f8f9fa;
	}

	/* Badges */
	.shb-status-badge {
		display: inline-flex;
		align-items: center;
		padding: 4px 8px;
		border-radius: 4px;
		font-size: 12px;
		font-weight: 500;
		text-transform: capitalize;
	}

	.shb-status-pending {
		background-color: #fef7e0;
		color: #b06000;
	}

	.shb-status-confirmed {
		background-color: #e6f4ea;
		color: #137333;
	}

	.shb-status-cancelled {
		background-color: #fce8e6;
		color: #c5221f;
	}

	/* Conflict Styles */
	.shb-booking-conflict td {
		background-color: #fff8f8;
	}

	.shb-table tr.shb-booking-conflict:hover td {
		background-color: #fff1f1;
	}

	.shb-conflict-badge {
		font-size: 14px;
		margin-left: 5px;
		cursor: help;
	}

	.shb-conflict-text {
		color: #d93025;
		font-weight: 500;
	}

	/* Actions */
	.shb-actions a {
		text-decoration: none;
		font-size: 13px;
		margin-right: 10px;
		font-weight: 500;
	}

	.shb-title-name {
		display: block;
		font-weight: 500;
		color: #202124;
		font-size: 14px;
	}

	.shb-sub-text {
		display: block;
		color: #70757a;
		font-size: 12px;
		margin-top: 2px;
	}

	/* Pagination */
	.shb-pagination {
		display: flex;
		justify-content: flex-end;
		margin-top: 15px;
		color: #5f6368;
		font-size: 13px;
	}

	.shb-pagination .page-numbers {
		padding: 4px 8px;
		text-decoration: none;
		color: #5f6368;
		border-radius: 4px;
	}

	.shb-pagination .page-numbers.current {
		background-color: #e8f0fe;
		color: #1967d2;
		font-weight: bold;
	}
</style>

<div class="wrap shb-bookings-list-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e('Bookings', 'simple-hall-booking-manager'); ?></h1>

	<?php if ($message): ?>
		<?php
		// Extract base message and cancelled count
		$message_parts = explode('&cancelled=', $message);
		$base_message = $message_parts[0];
		$cancelled_count = isset($message_parts[1]) ? absint($message_parts[1]) : 0;
		?>
		<div class="notice notice-success is-dismissible inline">
			<p>
				<?php
				switch ($base_message) {
					case 'booking_updated':
						esc_html_e('Booking updated successfully.', 'simple-hall-booking-manager');
						if ($cancelled_count > 0) {
							/* translators: %d: number of cancelled bookings */
							echo ' ' . sprintf(esc_html__('%d conflicting booking(s) were automatically cancelled.', 'simple-hall-booking-manager'), absint($cancelled_count));
						}
						break;
					case 'booking_deleted':
						esc_html_e('Booking deleted successfully.', 'simple-hall-booking-manager');
						break;
					default:
						esc_html_e('Operation completed.', 'simple-hall-booking-manager');
				}
				?>
			</p>
		</div>
	<?php endif; ?>

	<!-- Upper Controls -->
	<div class="shb-controls">
		<!-- Status Filter Tabs -->
		<ul class="shb-status-nav">
			<?php
			foreach ($status_links as $status_key => $status_label) {
				$class = '';
				if ($status_key === 'all' && empty($filter_status)) {
					$class = 'current';
				} elseif ($status_key === $filter_status) {
					$class = 'current';
				}

				$url_args = array('page' => 'shb-bookings');
				if ($status_key !== 'all') {
					$url_args['filter_status'] = $status_key;
				}
				if ($filter_hall_id)
					$url_args['filter_hall'] = $filter_hall_id;
				if ($search_term)
					$url_args['s'] = $search_term;
				if ($orderby)
					$url_args['orderby'] = $orderby;
				if ($order)
					$url_args['order'] = $order;

				$url = add_query_arg($url_args, admin_url('admin.php'));
				echo "<li><a href='" . esc_url($url) . "' class='" . esc_attr($class) . "'>" . esc_html($status_label) . "</a></li>";
			}
			?>
		</ul>

		<!-- Filters Form -->
		<form method="get" action="" class="shb-filters-bar">
			<input type="hidden" name="page" value="shb-bookings">
			<?php if ($filter_status)
				echo '<input type="hidden" name="filter_status" value="' . esc_attr($filter_status) . '">'; ?>
			<?php if ($orderby)
				echo '<input type="hidden" name="orderby" value="' . esc_attr($orderby) . '">'; ?>
			<?php if ($order)
				echo '<input type="hidden" name="order" value="' . esc_attr($order) . '">'; ?>

			<select name="filter_hall">
				<option value=""><?php esc_html_e('All Halls', 'simple-hall-booking-manager'); ?></option>
				<?php foreach ($halls as $hall): ?>
					<option value="<?php echo esc_attr($hall->id); ?>" <?php selected($filter_hall_id, $hall->id); ?>>
						<?php echo esc_html($hall->title); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<input type="search" name="s" placeholder="<?php esc_attr_e('Search...', 'simple-hall-booking-manager'); ?>"
				value="<?php echo esc_attr($search_term); ?>">
			<input type="submit" class="button" value="<?php esc_attr_e('Filter', 'simple-hall-booking-manager'); ?>">
		</form>
	</div>

	<?php if (empty($bookings)): ?>
		<div class="notice notice-info inline" style="margin: 0;">
			<p><?php esc_html_e('No bookings found.', 'simple-hall-booking-manager'); ?></p>
		</div>
	<?php else: ?>
		<table class="shb-table">
			<thead>
				<tr>
					<th><?php echo shb_sort_link('created_at', __('Created', 'simple-hall-booking-manager'), $orderby, $order); ?>
					</th>
					<th><?php echo shb_sort_link('booking_date', __('Date', 'simple-hall-booking-manager'), $orderby, $order); ?>
					</th>
					<th><?php echo shb_sort_link('hall_id', __('Hall', 'simple-hall-booking-manager'), $orderby, $order); ?>
					</th>
					<th><?php esc_html_e('Slot', 'simple-hall-booking-manager'); ?></th>
					<th><?php echo shb_sort_link('customer_name', __('Customer', 'simple-hall-booking-manager'), $orderby, $order); ?>
					</th>
					<th><?php esc_html_e('Status', 'simple-hall-booking-manager'); ?></th>
					<th><?php esc_html_e('Actions', 'simple-hall-booking-manager'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($bookings as $booking): ?>
					<?php
					$hall = $db->get_hall($booking->hall_id);

					// Get booking dates
					$booking_dates = $db->get_booking_dates($booking->id);
					$is_multiday = ('multiday' === $booking->booking_type);
					$date_display = '';
					$date_count = count($booking_dates);

					// Get first slot
					$slot = null;
					if (!empty($booking_dates)) {
						$first_booking_date = reset($booking_dates);
						$slot = $db->get_slot($first_booking_date->slot_id);
					}

					if ($is_multiday && $date_count > 0) {
						$first_date = reset($booking_dates)->booking_date;
						$last_date = end($booking_dates)->booking_date;
						$date_display = shb_format_date($first_date);
						if ($first_date !== $last_date) {
							$date_display .= ' - ' . shb_format_date($last_date);
						}
						$date_display .= '<br><span class="shb-sub-text">' . sprintf(_n('%d day', '%d days', $date_count, 'simple-hall-booking-manager'), $date_count) . '</span>';
					} elseif (!empty($booking_dates)) {
						$date_display = shb_format_date($booking_dates[0]->booking_date);
					} else {
						$date_display = '-';
					}

					// Check for conflicts
					$has_conflicts = false;
					$conflict_count = 0;
					if ('pending' === $booking->status) {
						$conflicts = $db->get_conflicting_bookings($booking->id, true);
						$has_conflicts = !empty($conflicts);
						$conflict_count = count($conflicts);
					}
					?>
					<tr <?php echo $has_conflicts ? 'class="shb-booking-conflict"' : ''; ?>>
						<td>
							<?php
							// display "2 hours ago" etc
							echo esc_html(human_time_diff(strtotime($booking->created_at), current_time('timestamp'))) . ' ' . esc_html__('ago', 'simple-hall-booking-manager');
							?>
						</td>
						<td>
							<?php echo wp_kses_post($date_display); ?>
							<?php if ($has_conflicts): ?>
								<span class="shb-conflict-badge"
									title="<?php echo esc_attr(sprintf(_n('%d conflict', '%d conflicts', $conflict_count, 'simple-hall-booking-manager'), $conflict_count)); ?>">⚠️</span>
							<?php endif; ?>
						</td>
						<td><?php echo $hall ? esc_html($hall->title) : '-'; ?></td>
						<td><?php echo $slot ? esc_html($slot->label) : '-'; ?></td>
						<td>
							<span class="shb-title-name"><?php echo esc_html($booking->customer_name); ?></span>
							<span
								class="shb-sub-text"><?php echo esc_html($booking->customer_organization ? $booking->customer_organization : $booking->customer_email); ?></span>
							<span class="shb-sub-text" style="font-family: monospace;">PIN:
								<?php echo esc_html($booking->pin); ?></span>
						</td>
						<td>
							<span class="shb-status-badge shb-status-<?php echo esc_attr($booking->status); ?>">
								<?php echo esc_html(shb_get_status_label($booking->status)); ?>
							</span>
							<?php if ($has_conflicts): ?>
								<div class="shb-conflict-text" style="font-size: 11px; margin-top: 4px;">
									<?php printf(esc_html__('%d conflict(s)', 'simple-hall-booking-manager'), absint($conflict_count)); ?>
								</div>
							<?php endif; ?>
						</td>
						<td class="shb-actions">
							<a
								href="<?php echo esc_url(admin_url('admin.php?page=shb-bookings&action=edit&id=' . $booking->id)); ?>">
								<?php esc_html_e('Edit', 'simple-hall-booking-manager'); ?>
							</a>
							<a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=shb-bookings&action=delete_booking&id=' . $booking->id), 'shb_delete_booking_' . $booking->id)); ?>"
								style="color: #c5221f;"
								onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this booking?', 'simple-hall-booking-manager'); ?>');">
								<?php esc_html_e('Delete', 'simple-hall-booking-manager'); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ($total_pages > 1): ?>
			<div class="shb-pagination">
				<?php
				$page_links = paginate_links(array(
					'base' => add_query_arg('paged', '%#%'),
					'format' => '',
					'prev_text' => __('&laquo;', 'simple-hall-booking-manager'),
					'next_text' => __('&raquo;', 'simple-hall-booking-manager'),
					'total' => $total_pages,
					'current' => $current_page,
					'type' => 'plain',
				));
				if ($page_links)
					echo $page_links;
				?>
			</div>
		<?php endif; ?>

	<?php endif; ?>
</div>