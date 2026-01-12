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

	<?php if ($message && 'slot_error' === $message && $error_message): ?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_html($error_message); ?></p>
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

		<!-- Slot Edit Modal (simplified inline form) -->
		<div id="shb-slot-form" class="shb-modal" style="display: none;">
			<div class="shb-modal-content">
				<h3 id="shb-slot-modal-title"><?php esc_html_e('Add Slot', 'simple-hall-booking-manager'); ?></h3>

				<div class="shb-slot-form-errors"
					style="display: none; padding: 10px; margin-bottom: 15px; background: #f8d7da; color: #721c24; border-left: 4px solid #d63638; border-radius: 3px;">
				</div>

				<form method="post" action="" id="shb-slot-form-element">
					<?php wp_nonce_field('shb_save_slot'); ?>
					<input type="hidden" name="slot_id" id="slot_id" value="">
					<input type="hidden" name="hall_id" value="<?php echo esc_attr($hall_id); ?>">

					<table class="form-table">
						<tr>
							<th><label
									for="slot_type"><?php esc_html_e('Slot Type', 'simple-hall-booking-manager'); ?></label>
							</th>
							<td>
								<select name="slot_type" id="slot_type" required>
									<option value="partial">
										<?php esc_html_e('Partial (Morning/Day/Evening)', 'simple-hall-booking-manager'); ?>
									</option>
									<?php if (!$has_full_day_slot): ?>
										<option value="full_day">
											<?php esc_html_e('Full Day', 'simple-hall-booking-manager'); ?>
										</option>
									<?php endif; ?>
								</select>
								<?php if ($has_full_day_slot): ?>
									<p class="description"
										style="color: #856404; background: #fff3cd; padding: 8px; margin-top: 8px; border-radius: 3px;">
										<?php esc_html_e('Full Day option is hidden because this hall already has a Full Day slot. Only one Full Day slot is allowed per hall.', 'simple-hall-booking-manager'); ?>
									</p>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><label for="label"><?php esc_html_e('Label', 'simple-hall-booking-manager'); ?></label>
							</th>
							<td><input type="text" name="label" id="label" class="regular-text" required></td>
						</tr>
						<tr>
							<th><label
									for="start_time"><?php esc_html_e('Start Time', 'simple-hall-booking-manager'); ?></label>
							</th>
							<td><input type="time" name="start_time" id="start_time" required></td>
						</tr>
						<tr>
							<th><label
									for="end_time"><?php esc_html_e('End Time', 'simple-hall-booking-manager'); ?></label>
							</th>
							<td><input type="time" name="end_time" id="end_time" required></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Enabled Days', 'simple-hall-booking-manager'); ?></th>
							<td>
								<?php
								$days = array(
									0 => __('Sunday', 'simple-hall-booking-manager'),
									1 => __('Monday', 'simple-hall-booking-manager'),
									2 => __('Tuesday', 'simple-hall-booking-manager'),
									3 => __('Wednesday', 'simple-hall-booking-manager'),
									4 => __('Thursday', 'simple-hall-booking-manager'),
									5 => __('Friday', 'simple-hall-booking-manager'),
									6 => __('Saturday', 'simple-hall-booking-manager'),
								);
								foreach ($days as $day_num => $day_name):
									?>
									<label>
										<input type="checkbox" name="days_enabled[]" value="<?php echo esc_attr($day_num); ?>"
											checked>
										<?php echo esc_html($day_name); ?>
									</label><br>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e('Status', 'simple-hall-booking-manager'); ?></th>
							<td>
								<label>
									<input type="checkbox" name="is_active" value="1" checked>
									<?php esc_html_e('Active', 'simple-hall-booking-manager'); ?>
								</label>
							</td>
						</tr>
					</table>

					<p class="submit">
						<input type="submit" name="shb_save_slot" class="button button-primary"
							value="<?php esc_attr_e('Save Slot', 'simple-hall-booking-manager'); ?>">
						<button type="button"
							class="button shb-cancel-slot"><?php esc_html_e('Cancel', 'simple-hall-booking-manager'); ?></button>
					</p>
				</form>
			</div>
		</div>
	<?php endif; ?>
</div>

<script>
	jQuery(document).ready(function ($) {
		// Existing slots data for validation
		var existingSlots = <?php echo wp_json_encode($slots); ?>;
		var hasFullDaySlot = <?php echo $has_full_day_slot ? 'true' : 'false'; ?>;
		var currentEditingSlotId = null;

		$('.shb-add-slot-btn').on('click', function (e) {
			e.preventDefault();
			currentEditingSlotId = null;
			$('#shb-slot-form').show();
			$('#shb-slot-modal-title').text('<?php esc_html_e('Add Slot', 'simple-hall-booking-manager'); ?>');
			$('#slot_id').val('');
			$('#shb-slot-form-element')[0].reset();
			$('.shb-slot-form-errors').hide().html('');
			// Reset slot type select (in case FullDay option was hidden dynamically)
			$('#slot_type option[value="full_day"]').prop('disabled', false);
		});

		// Edit slot button
		$('.shb-edit-slot-btn').on('click', function (e) {
			e.preventDefault();
			var slotId = $(this).data('slot-id');
			currentEditingSlotId = slotId;

			// Find slot data
			var slot = existingSlots.find(function (s) {
				return s.id == slotId;
			});

			if (slot) {
				$('#shb-slot-modal-title').text('<?php esc_html_e('Edit Slot', 'simple-hall-booking-manager'); ?>');
				$('#slot_id').val(slot.id);
				$('#slot_type').val(slot.slot_type);
				$('#label').val(slot.label);
				$('#start_time').val(slot.start_time);
				$('#end_time').val(slot.end_time);

				// Handle checkbox for is_active
				if (parseInt(slot.is_active) === 1) {
					$('input[name="is_active"]').prop('checked', true);
				} else {
					$('input[name="is_active"]').prop('checked', false);
				}

				// Handle days enabled
				$('input[name="days_enabled[]"]').prop('checked', false);
				try {
					var days = JSON.parse(slot.days_enabled);
					if (Array.isArray(days)) {
						days.forEach(function (day) {
							$('input[name="days_enabled[]"][value="' + day + '"]').prop('checked', true);
						});
					}
				} catch (e) {
					// Fallback if not valid JSON or array (e.g., all checked)
					$('input[name="days_enabled[]"]').prop('checked', true);
				}

				// Show modal
				$('#shb-slot-form').show();
				$('.shb-slot-form-errors').hide().html('');
			}
		});

		$('.shb-cancel-slot').on('click', function () {
			$('#shb-slot-form').hide();
			$('.shb-slot-form-errors').hide().html('');
		});

		// Validate slot times before submission
		$('#shb-slot-form-element').on('submit', function (e) {
			var slotType = $('#slot_type').val();
			var startTime = $('#start_time').val();
			var endTime = $('#end_time').val();
			var label = $('#label').val();
			var errors = [];

			// Clear previous errors
			$('.shb-slot-form-errors').hide().html('');

			// Validate required fields
			if (!label.trim()) {
				errors.push('<?php esc_html_e('Label is required.', 'simple-hall-booking-manager'); ?>');
			}

			if (!startTime || !endTime) {
				errors.push('<?php esc_html_e('Start time and end time are required.', 'simple-hall-booking-manager'); ?>');
			}

			// Validate end time is after start time
			if (startTime && endTime) {
				var start = new Date('2000-01-01 ' + startTime);
				var end = new Date('2000-01-01 ' + endTime);

				if (end <= start) {
					errors.push('<?php esc_html_e('End time must be after start time.', 'simple-hall-booking-manager'); ?>');
				}

				// Check for overlaps ONLY for partial slots with other partial slots
				// Full Day slots don't check time overlap - they only check for duplicate Full Day (handled by hiding option)
				// Check for overlaps ONLY for partial slots with other partial slots
				// Full Day slots don't check time overlap - they only check for duplicate Full Day (handled by hiding option)
				if (slotType === 'partial') {
					var editingId = $('#slot_id').val();

					for (var i = 0; i < existingSlots.length; i++) {
						var existingSlot = existingSlots[i];

						// Only check overlap with other PARTIAL slots
						if (existingSlot.slot_type !== 'partial') {
							continue;
						}

						// If we are editing, skip the current slot
						// Use the hidden input value which is reliable
						if (editingId && String(existingSlot.id) === String(editingId)) {
							continue;
						}

						var existingStart = new Date('2000-01-01 ' + existingSlot.start_time);
						var existingEnd = new Date('2000-01-01 ' + existingSlot.end_time);

						// Check overlap
						if (start < existingEnd && end > existingStart) {
							errors.push('<?php esc_html_e('Time slot overlaps with existing partial slot:', 'simple-hall-booking-manager'); ?> ' + existingSlot.label + ' (' + formatTime(existingSlot.start_time) + ' - ' + formatTime(existingSlot.end_time) + ')');
							break;
						}
					}
				}
			}

			// Check full day slot restriction
			if (slotType === 'full_day' && hasFullDaySlot && !$('#slot_id').val()) { // Use $('#slot_id').val() to check if adding new
				errors.push('<?php esc_html_e('This hall already has a Full Day slot. Only one Full Day slot is allowed per hall.', 'simple-hall-booking-manager'); ?>');
			}

			// Display errors or submit
			if (errors.length > 0) {
				e.preventDefault();
				var errorHtml = '<ul style="margin: 0; padding-left: 20px;">';
				for (var i = 0; i < errors.length; i++) {
					errorHtml += '<li>' + errors[i] + '</li>';
				}
				errorHtml += '</ul>';
				$('.shb-slot-form-errors').html(errorHtml).show();

				// Scroll to errors
				$('.shb-modal-content').animate({
					scrollTop: 0
				}, 300);

				return false;
			}

			return true;
		});

		// Helper function to format time
		function formatTime(time) {
			var parts = time.split(':');
			var hours = parseInt(parts[0]);
			var minutes = parts[1];
			var ampm = hours >= 12 ? 'PM' : 'AM';
			hours = hours % 12;
			hours = hours ? hours : 12; // 0 should be 12
			return hours + ':' + minutes + ' ' + ampm;
		}

		// Auto-dismiss success/error notices
		<?php if ($message && 'slot_error' === $message): ?>
			setTimeout(function () {
				$('.notice-error').fadeOut(300);
			}, 8000);
		<?php endif; ?>
	});
</script>