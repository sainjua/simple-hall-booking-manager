<?php
/**
 * Admin view partial: Hall Form
 * Shared between Create and Edit views.
 *
 * @package SimpleHallBookingManager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="post" action="">
    <?php wp_nonce_field('shb_save_hall'); ?>
    <input type="hidden" name="hall_id" value="<?php echo esc_attr($hall_id); ?>">

    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="title">
                    <?php esc_html_e('Hall Name', 'simple-hall-booking-manager'); ?> <span class="required">*</span>
                </label>
            </th>
            <td>
                <input type="text" name="title" id="title" value="<?php echo esc_attr($title); ?>"
                    class="regular-text" required>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="description">
                    <?php esc_html_e('Description', 'simple-hall-booking-manager'); ?>
                </label>
            </th>
            <td>
                <textarea name="description" id="description" rows="4"
                    class="large-text"><?php echo esc_textarea($description); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="capacity">
                    <?php esc_html_e('Capacity', 'simple-hall-booking-manager'); ?>
                </label>
            </th>
            <td>
                <input type="number" name="capacity" id="capacity" value="<?php echo esc_attr($capacity); ?>" min="0"
                    class="small-text">
                <p class="description">
                    <?php esc_html_e('Maximum number of attendees', 'simple-hall-booking-manager'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="cleaning_buffer">
                    <?php esc_html_e('Cleaning Buffer', 'simple-hall-booking-manager'); ?>
                </label>
            </th>
            <td>
                <input type="number" name="cleaning_buffer" id="cleaning_buffer"
                    value="<?php echo esc_attr($cleaning_buffer); ?>" min="0" class="small-text">
                <span>
                    <?php esc_html_e('minutes', 'simple-hall-booking-manager'); ?>
                </span>
                <p class="description">
                    <?php esc_html_e('Time required between bookings for cleaning', 'simple-hall-booking-manager'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="status">
                    <?php esc_html_e('Status', 'simple-hall-booking-manager'); ?>
                </label>
            </th>
            <td>
                <select name="status" id="status">
                    <option value="active" <?php selected($status, 'active'); ?>>
                        <?php esc_html_e('Active', 'simple-hall-booking-manager'); ?>
                    </option>
                    <option value="inactive" <?php selected($status, 'inactive'); ?>>
                        <?php esc_html_e('Inactive', 'simple-hall-booking-manager'); ?>
                    </option>
                </select>
            </td>
        </tr>
    </table>

    <p class="submit">
        <input type="submit" name="shb_save_hall" class="button button-primary"
            value="<?php echo $hall_id ? esc_attr__('Save Changes', 'simple-hall-booking-manager') : esc_attr__('Create Hall', 'simple-hall-booking-manager'); ?>">
        <a href="<?php echo esc_url(admin_url('admin.php?page=shb-halls')); ?>" class="button">
            <?php esc_html_e('Cancel', 'simple-hall-booking-manager'); ?>
        </a>
    </p>
</form>