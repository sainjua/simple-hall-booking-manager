<?php
/**
 * Admin view: Hall edit
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

$db = shb()->db;
$hall_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
$hall = $hall_id ? $db->get_hall($hall_id) : null;
$slots = $hall_id ? $db->get_slots_by_hall($hall_id) : array();

if (!$hall) {
	wp_die(__('Hall not found.', 'simple-hall-booking-manager'));
}

$title = $hall->title;
$description = $hall->description;
$capacity = $hall->capacity;
$status = $hall->status;
$cleaning_buffer = $hall->cleaning_buffer;

$page_title = __('Edit Hall', 'simple-hall-booking-manager');

// Check if hall has full day slot
$has_full_day_slot = $db->hall_has_full_day_slot($hall_id);

// Handle messages
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$error_message = isset($_GET['error_message']) ? urldecode(sanitize_text_field($_GET['error_message'])) : '';
?>

<div class="wrap">
	<h1><?php echo esc_html($page_title); ?></h1>

	<?php if ($message && $error_message): ?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_html($error_message); ?></p>
		</div>
	<?php elseif ($message === 'slot_added'): ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e('Slot added successfully.', 'simple-hall-booking-manager'); ?></p>
		</div>
	<?php elseif ($message === 'slot_updated'): ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e('Slot updated successfully.', 'simple-hall-booking-manager'); ?></p>
		</div>
	<?php elseif ($message === 'slot_deleted'): ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e('Slot deleted successfully.', 'simple-hall-booking-manager'); ?></p>
		</div>
	<?php endif; ?>

	<?php include SHB_PLUGIN_DIR . 'admin/views/partials/_hall-form.php'; ?>

	<?php if ($hall_id): ?>
		<hr>
		<h2><?php esc_html_e('Time Slots', 'simple-hall-booking-manager'); ?></h2>

		<div class="shb-slots-section">
			<a href="#" class="button shb-add-slot-btn" data-hall-id="<?php echo esc_attr($hall_id); ?>">
				<?php esc_html_e('Add Slot', 'simple-hall-booking-manager'); ?>
			</a>

			<?php if (empty($slots)): ?>
				<p><?php esc_html_e('No slots created yet. Add a slot to get started.', 'simple-hall-booking-manager'); ?></p>
			<?php else: ?>
				<table class="wp-list-table widefat fixed striped shb-slots-table">
					<thead>
						<tr>
							<th><?php esc_html_e('Type', 'simple-hall-booking-manager'); ?></th>
							<th><?php esc_html_e('Label', 'simple-hall-booking-manager'); ?></th>
							<th><?php esc_html_e('Time', 'simple-hall-booking-manager'); ?></th>
							<th><?php esc_html_e('Status', 'simple-hall-booking-manager'); ?></th>
							<th><?php esc_html_e('Actions', 'simple-hall-booking-manager'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($slots as $slot): ?>
							<tr>
								<td>
									<span class="shb-slot-type-badge shb-type-<?php echo esc_attr($slot->slot_type); ?>">
										<?php echo esc_html(shb_get_slot_type_label($slot->slot_type)); ?>
									</span>
								</td>
								<td><?php echo esc_html($slot->label); ?></td>
								<td>
									<?php echo esc_html(date('g:i A', strtotime($slot->start_time))); ?>
									&ndash;
									<?php echo esc_html(date('g:i A', strtotime($slot->end_time))); ?>
								</td>
								<td>
									<?php echo $slot->is_active ? esc_html__('Active', 'simple-hall-booking-manager') : esc_html__('Inactive', 'simple-hall-booking-manager'); ?>
								</td>
								<td>
									<a href="#" class="shb-edit-slot-btn" data-slot-id="<?php echo esc_attr($slot->id); ?>">
										<?php esc_html_e('Edit', 'simple-hall-booking-manager'); ?>
									</a>
									|
									<a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=shb-halls&action=delete_slot&id=' . $slot->id . '&hall_id=' . $hall_id), 'shb_delete_slot_' . $slot->id)); ?>"
										class="delete-link"
										onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this slot?', 'simple-hall-booking-manager'); ?>');">
										<?php esc_html_e('Delete', 'simple-hall-booking-manager'); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<!-- Slot Edit Modal -->
		<div id="shb-slot-form" class="shb-modal" style="display: none;">
			<div class="shb-modal-content">
				<h3 id="shb-slot-modal-title"></h3>
				<div id="shb-slot-form-container">
					<!-- AJAX loaded form goes here -->
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<script>
	jQuery(document).ready(function ($) {
		var hasFullDaySlot = <?php echo $has_full_day_slot ? 'true' : 'false'; ?>;

		$('.shb-add-slot-btn').on('click', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var hallId = $btn.data('hall-id');
			var slotType = $btn.data('slot-type') || 'partial';

			$btn.addClass('updating-slot').css('opacity', '0.5');

			window.shb_get_slot(0, hallId, slotType, function (data) {
				$btn.removeClass('updating-slot').css('opacity', '1');
				$('#shb-slot-modal-title').text(data.modal_title);
				$('#shb-slot-form-container').html(data.html);
				$('#shb-slot-form').show();
			});
		});

		$('.shb-edit-slot-btn').on('click', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var slotId = $btn.data('slot-id');
			var hallId = <?php echo (int) $hall_id; ?>;

			$btn.addClass('updating-slot').css('opacity', '0.5');

			window.shb_get_slot(slotId, hallId, '', function (data) {
				$btn.removeClass('updating-slot').css('opacity', '1');
				$('#shb-slot-modal-title').text(data.modal_title);
				$('#shb-slot-form-container').html(data.html);
				$('#shb-slot-form').show();
			});
		});

		$(document).on('click', '.shb-check-slot-overlap', function () {
			var $btn = $(this);
			var $form = $('#shb-slot-form-element');
			var $result = $('.shb-overlap-check-result');
			var $spinner = $('.shb-overlap-check-spinner');

			var startTime = $('#start_time').val();
			var endTime = $('#end_time').val();
			var hallId = $form.find('input[name="hall_id"]').val();
			var slotId = $form.find('input[name="slot_id"]').val();

			if (!startTime || !endTime) {
				alert('<?php esc_html_e('Please enter both start and end times.', 'simple-hall-booking-manager'); ?>');
				return;
			}

			$btn.prop('disabled', true);
			$spinner.addClass('is-active');
			$result.hide().removeClass('notice-success notice-error');

			$.ajax({
				url: shbAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'shb_check_slot_overlap',
					nonce: shbAdmin.nonce,
					hall_id: hallId,
					slot_id: slotId,
					start_time: startTime,
					end_time: endTime
				},
				success: function (response) {
					$btn.prop('disabled', false);
					$spinner.removeClass('is-active');
					$result.text(response.data.message).show();
					if (response.success) {
						$result.css({ 'background': '#d4edda', 'color': '#155724', 'border': '1px solid #c3e6cb' });
					} else {
						$result.css({ 'background': '#f8d7da', 'color': '#721c24', 'border': '1px solid #f5c6cb' });
					}
				},
				error: function () {
					$btn.prop('disabled', false);
					$spinner.removeClass('is-active');
					alert('<?php esc_html_e('Connection error while checking slot overlap.', 'simple-hall-booking-manager'); ?>');
				}
			});
		});

		$(document).on('click', '.shb-cancel-slot', function () {
			$('#shb-slot-form').hide();
			$('#shb-slot-form-container').empty();
		});

		// Validate slot times before submission
		$(document).on('submit', '#shb-slot-form-element', function (e) {
			var startTime = $('#start_time').val();
			var endTime = $('#end_time').val();
			var label = $('#label').val();
			var errors = [];

			if (!label.trim()) {
				errors.push('<?php esc_html_e('Label is required.', 'simple-hall-booking-manager'); ?>');
			}

			if (!startTime || !endTime) {
				errors.push('<?php esc_html_e('Start time and end time are required.', 'simple-hall-booking-manager'); ?>');
			}

			if (startTime && endTime) {
				var start = new Date('2000-01-01 ' + startTime);
				var end = new Date('2000-01-01 ' + endTime);
				if (end <= start) {
					errors.push('<?php esc_html_e('End time must be after start time.', 'simple-hall-booking-manager'); ?>');
				}
			}

			if (errors.length > 0) {
				e.preventDefault();
				var errorHtml = '<ul style="margin: 0; padding-left: 20px;">';
				for (var i = 0; i < errors.length; i++) {
					errorHtml += '<li>' + errors[i] + '</li>';
				}
				errorHtml += '</ul>';
				$('.shb-slot-form-errors').html(errorHtml).show();
				$('.shb-modal-content').animate({ scrollTop: 0 }, 300);
				return false;
			}
			return true;
		});

		// Auto-dismiss notices
		<?php if ($message): ?>
			setTimeout(function () {
				$('.notice').fadeOut(300);
			}, 5000);
		<?php endif; ?>
	});
</script>