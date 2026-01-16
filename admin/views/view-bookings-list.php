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
$current_page = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Handle filters
$filter_status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '';
$filter_hall_id = isset($_GET['filter_hall']) ? absint($_GET['filter_hall']) : '';
$search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

$filters = array(
	'limit' => $per_page,
	'offset' => $offset,
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
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e('Bookings', 'simple-hall-booking-manager'); ?></h1>

	<?php if ($search_term): ?>
		<span class="subtitle">
			<?php
			/* translators: %s: search term */
			printf(esc_html__('Search results for: %s', 'simple-hall-booking-manager'), '<strong>' . esc_html($search_term) . '</strong>');
			?>
		</span>
	<?php endif; ?>

	<hr class="wp-header-end">

	<?php if ($message): ?>
		<?php
		// Extract base message and cancelled count
		$message_parts = explode('&cancelled=', $message);
		$base_message = $message_parts[0];
		$cancelled_count = isset($message_parts[1]) ? absint($message_parts[1]) : 0;
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				switch ($base_message) {
					case 'booking_updated':
						esc_html_e('Booking updated successfully.', 'simple-hall-booking-manager');
						if ($cancelled_count > 0) {
							echo ' ';
							/* translators: %d: number of cancelled bookings */
							printf(esc_html__('%d conflicting booking(s) were automatically cancelled.', 'simple-hall-booking-manager'), $cancelled_count);
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

	<!-- Status Links -->
	<ul class="subsubsub">
		<?php
		$total_statuses = count($status_links);
		$i = 0;
		foreach ($status_links as $status_key => $status_label) {
			$i++;
			$class = '';
			if ($status_key === 'all' && empty($filter_status)) {
				$class = 'current';
			} elseif ($status_key === $filter_status) {
				$class = 'current';
			}

			$url_args = array(
				'page' => 'shb-bookings',
			);

			if ($status_key !== 'all') {
				$url_args['filter_status'] = $status_key;
			}

			if ($filter_hall_id) {
				$url_args['filter_hall'] = $filter_hall_id;
			}

			if ($search_term) {
				$url_args['s'] = $search_term;
			}

			$url = add_query_arg($url_args, admin_url('admin.php'));
			$separator = ($i < $total_statuses) ? ' |' : '';

			echo "<li><a href='" . esc_url($url) . "' class='" . esc_attr($class) . "'>" . esc_html($status_label) . "</a>" . esc_html($separator) . " </li>";
		}
		?>
	</ul>

	<!-- Filters & Search -->
	<div class="tablenav top">
		<form method="get" action="">
			<input type="hidden" name="page" value="shb-bookings">
			<?php if ($filter_status): ?>
				<input type="hidden" name="filter_status" value="<?php echo esc_attr($filter_status); ?>">
			<?php endif; ?>

			<div class="alignleft actions">
				<select name="filter_hall">
					<option value=""><?php esc_html_e('All Halls', 'simple-hall-booking-manager'); ?></option>
					<?php foreach ($halls as $hall): ?>
						<option value="<?php echo esc_attr($hall->id); ?>" <?php selected($filter_hall_id, $hall->id); ?>>
							<?php echo esc_html($hall->title); ?>
						</option>
					<?php endforeach; ?>
				</select>

				<input type="submit" class="button"
					value="<?php esc_attr_e('Filter', 'simple-hall-booking-manager'); ?>">
			</div>

			<div class="alignright">
				<p class="search-box">
					<label class="screen-reader-text"
						for="post-search-input"><?php esc_html_e('Search Bookings:', 'simple-hall-booking-manager'); ?></label>
					<input type="search" id="post-search-input" name="s" value="<?php echo esc_attr($search_term); ?>">
					<input type="submit" id="search-submit" class="button"
						value="<?php esc_attr_e('Search Bookings', 'simple-hall-booking-manager'); ?>">
				</p>
			</div>
		</form>
	</div>

	<?php if (empty($bookings)): ?>
		<div class="notice notice-info">
			<p><?php esc_html_e('No bookings found.', 'simple-hall-booking-manager'); ?></p>
		</div>
	<?php else: ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e('Date', 'simple-hall-booking-manager'); ?></th>
					<th><?php esc_html_e('Hall', 'simple-hall-booking-manager'); ?></th>
					<th><?php esc_html_e('Slot', 'simple-hall-booking-manager'); ?></th>
					<th><?php esc_html_e('Customer', 'simple-hall-booking-manager'); ?></th>
					<th><?php esc_html_e('PIN', 'simple-hall-booking-manager'); ?></th>
					<th><?php esc_html_e('Status', 'simple-hall-booking-manager'); ?></th>
					<th><?php esc_html_e('Actions', 'simple-hall-booking-manager'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($bookings as $booking): ?>
					<?php
					$hall = $db->get_hall($booking->hall_id);

					// Get booking dates (works for both single and multi-day bookings)
					$booking_dates = $db->get_booking_dates($booking->id);
					$is_multiday = ('multiday' === $booking->booking_type);
					$date_display = '';
					$date_count = count($booking_dates);

					// Get first slot for display (single-day has 1 slot, multi-day may have multiple)
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
						$date_display .= '<br><small>' . sprintf(
							/* translators: %d: number of days */
							_n('%d day', '%d days', $date_count, 'simple-hall-booking-manager'),
							$date_count
						) . '</small>';
					} elseif (!empty($booking_dates)) {
						// Single-day booking
						$date_display = shb_format_date($booking_dates[0]->booking_date);
					} else {
						$date_display = '-';
					}
					?>
					<?php
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
							<?php echo wp_kses_post($date_display); ?>
							<?php if ($has_conflicts): ?>
								<br><span class="shb-conflict-badge"
									title="<?php echo esc_attr(sprintf(
										/* translators: %d: number of conflicts */
										_n('%d conflict', '%d conflicts', $conflict_count, 'simple-hall-booking-manager'),
										$conflict_count
									)); ?>">⚠️</span>
							<?php endif; ?>
						</td>
						<td><?php echo $hall ? esc_html($hall->title) : '-'; ?></td>
						<td><?php echo $slot ? esc_html($slot->label) : '-'; ?></td>
						<td>
							<strong><?php echo esc_html($booking->customer_name); ?></strong><br>
							<small><?php echo esc_html($booking->customer_email); ?></small>
						</td>
						<td>
							<code style="font-weight: bold; letter-spacing: 1px;">
												<?php echo esc_html($booking->pin); ?>
											</code>
						</td>
						<td>
							<span class="shb-status-badge shb-status-<?php echo esc_attr($booking->status); ?>">
								<?php echo esc_html(shb_get_status_label($booking->status)); ?>
							</span>
							<?php if ($has_conflicts): ?>
								<br><small class="shb-conflict-text">
									<?php
									/* translators: %d: number of conflicts */
									printf(esc_html__('⚠️ %d conflict(s)', 'simple-hall-booking-manager'), $conflict_count);
									?>
								</small>
							<?php endif; ?>
						</td>
						<td>
							<a
								href="<?php echo esc_url(admin_url('admin.php?page=shb-bookings&action=edit&id=' . $booking->id)); ?>">
								<?php esc_html_e('Edit', 'simple-hall-booking-manager'); ?>
							</a>
							|
							<a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=shb-bookings&action=delete_booking&id=' . $booking->id), 'shb_delete_booking_' . $booking->id)); ?>"
								class="delete-link"
								onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this booking?', 'simple-hall-booking-manager'); ?>');">
								<?php esc_html_e('Delete', 'simple-hall-booking-manager'); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ($total_pages > 1): ?>
			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<span class="displaying-num">
						<?php
						/* translators: %s: number of items */
						printf(_n('%s item', '%s items', $total_bookings, 'simple-hall-booking-manager'), number_format_i18n($total_bookings));
						?>
					</span>
					<?php
					$page_links = paginate_links(
						array(
							'base' => add_query_arg('paged', '%#%'),
							'format' => '',
							'prev_text' => __('&laquo;', 'simple-hall-booking-manager'),
							'next_text' => __('&raquo;', 'simple-hall-booking-manager'),
							'total' => $total_pages,
							'current' => $current_page,
							'type' => 'plain',
						)
					);

					if ($page_links) {
						echo '<span class="pagination-links">' . $page_links . '</span>';
					}
					?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>