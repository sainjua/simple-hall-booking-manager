<?php
/**
 * Admin view partial: Slot Form
 *
 * @package SimpleHallBookingManager
 */

if (!defined('ABSPATH')) {
    exit;
}

// Variables passed to this partial:
// $slot_id, $hall_id, $slot_type, $label, $start_time, $end_time, $days_enabled, $is_active, $has_full_day_slot
?>

<div class="shb-slot-form-wrapper">
    <div class="shb-slot-form-errors"
        style="display: none; padding: 10px; margin-bottom: 15px; background: #f8d7da; color: #721c24; border-left: 4px solid #d63638; border-radius: 3px;">
    </div>

    <form method="post" action="" id="shb-slot-form-element">
        <?php wp_nonce_field('shb_save_slot'); ?>
        <input type="hidden" name="slot_id" value="<?php echo esc_attr($slot_id); ?>">
        <input type="hidden" name="hall_id" value="<?php echo esc_attr($hall_id); ?>">

        <table class="form-table">
            <tr>
                <th><label for="slot_type">
                        <?php esc_html_e('Slot Type', 'simple-hall-booking-manager'); ?>
                    </label></th>
                <td>
                    <select name="slot_type" id="slot_type" required>
                        <option value="partial" <?php selected($slot_type, 'partial'); ?>>
                            <?php esc_html_e('Partial (Morning/Day/Evening)', 'simple-hall-booking-manager'); ?>
                        </option>
                        <?php if (!$has_full_day_slot || $slot_type === 'full_day'): ?>
                            <option value="full_day" <?php selected($slot_type, 'full_day'); ?>>
                                <?php esc_html_e('Full Day', 'simple-hall-booking-manager'); ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <?php if ($has_full_day_slot && $slot_type !== 'full_day'): ?>
                        <p class="description"
                            style="color: #856404; background: #fff3cd; padding: 8px; margin-top: 8px; border-radius: 3px;">
                            <?php esc_html_e('Full Day option is hidden because this hall already has a Full Day slot.', 'simple-hall-booking-manager'); ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="label">
                        <?php esc_html_e('Label', 'simple-hall-booking-manager'); ?>
                    </label></th>
                <td><input type="text" name="label" id="label" value="<?php echo esc_attr($label); ?>"
                        class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="start_time">
                        <?php esc_html_e('Start Time', 'simple-hall-booking-manager'); ?>
                    </label></th>
                <td><input type="time" name="start_time" id="start_time" value="<?php echo esc_attr($start_time); ?>"
                        required></td>
            </tr>
            <tr>
                <th><label for="end_time">
                        <?php esc_html_e('End Time', 'simple-hall-booking-manager'); ?>
                    </label></th>
                <td><input type="time" name="end_time" id="end_time" value="<?php echo esc_attr($end_time); ?>"
                        required></td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e('Enabled Days', 'simple-hall-booking-manager'); ?>
                </th>
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
                        $checked = in_array((string) $day_num, (array) $days_enabled) ? 'checked' : '';
                        ?>
                        <label>
                            <input type="checkbox" name="days_enabled[]" value="<?php echo esc_attr($day_num); ?>" <?php echo $checked; ?>>
                            <?php echo esc_html($day_name); ?>
                        </label><br>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e('Status', 'simple-hall-booking-manager'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="is_active" value="1" <?php checked($is_active, 1); ?>>
                        <?php esc_html_e('Active', 'simple-hall-booking-manager'); ?>
                    </label>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="shb_save_slot" class="button button-primary"
                value="<?php esc_attr_e('Save Slot', 'simple-hall-booking-manager'); ?>">
            <button type="button" class="button shb-check-slot-overlap"><?php esc_html_e('Check Overlap', 'simple-hall-booking-manager'); ?></button>
            <button type="button" class="button shb-cancel-slot">
                <?php esc_html_e('Cancel', 'simple-hall-booking-manager'); ?>
            </button>
            <span class="shb-overlap-check-spinner spinner" style="float: none; margin: 0 5px; vertical-align: middle;"></span>
        </p>
        <div class="shb-overlap-check-result" style="margin-top: 10px; display: none; padding: 10px; border-radius: 3px;"></div>
    </form>
</div>