<?php
/**
 * Admin view: Hall Create
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$db = shb()->db;
$hall_id = 0;
// Initialize empty/default values
$title = '';
$description = '';
$capacity = 50;
$status = 'active';
$cleaning_buffer = 30;

$page_title = __('Add New Hall', 'simple-hall-booking-manager');

// Handle messages
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$error_message = isset($_GET['error_message']) ? urldecode(sanitize_text_field($_GET['error_message'])) : '';
?>

<div class="wrap">
    <h1>
        <?php echo esc_html($page_title); ?>
    </h1>

    <?php if ($message && 'slot_error' === $message && $error_message): ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php echo esc_html($error_message); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php include SHB_PLUGIN_DIR . 'admin/views/partials/_hall-form.php'; ?>
</div>