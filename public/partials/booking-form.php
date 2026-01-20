<?php
/**
 * Template: Booking form
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

$db = shb()->db;
$hall_id = isset($atts['hall_id']) ? absint($atts['hall_id']) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter used only for pre-filling form field, no data processing
$selected_hall = isset($_GET['hall_id']) ? absint($_GET['hall_id']) : $hall_id;

$halls = $db->get_halls(array('status' => 'active'));
?>

<div class="shb-booking-form-wrapper">
	<form id="shb-booking-form" class="shb-booking-form">
		<?php wp_nonce_field('shb_frontend_nonce', 'nonce'); ?>

		<div class="shb-form-messages"></div>

		<!-- Hall Selection -->
		<div class="shb-form-group">
			<label for="shb_hall_id">
				<?php esc_html_e('Select Hall', 'simple-hall-booking-manager'); ?>
				<span class="required">*</span>
			</label>
			<?php if ($hall_id): ?>
				<?php
				$hall = $db->get_hall($hall_id);
				if ($hall) {
					echo '<p><strong>' . esc_html($hall->title) . '</strong></p>';
				}
				?>
				<input type="hidden" name="hall_id" id="shb_hall_id" value="<?php echo esc_attr($hall_id); ?>">
			<?php else: ?>
				<select name="hall_id" id="shb_hall_id" required>
					<option value="">
						<?php esc_html_e('Choose a hall...', 'simple-hall-booking-manager'); ?>
					</option>
					<?php foreach ($halls as $hall): ?>
						<option value="<?php echo esc_attr($hall->id); ?>" <?php selected($selected_hall, $hall->id); ?>>
							<?php echo esc_html($hall->title); ?>
							(
							<?php esc_html_e('Capacity:', 'simple-hall-booking-manager'); ?>
							<?php echo esc_html($hall->capacity); ?>)
						</option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</div>

		<!-- Date Selection -->
		<div id="shb-date-selection-container" class="shb-form-group">
			<label>
				<?php esc_html_e('Select Date(s)', 'simple-hall-booking-manager'); ?>
				<span class="required">*</span>
			</label>
			<p class="description">
				<?php esc_html_e('Click on dates to select. Select one date for single-day booking or multiple dates for multi-day booking.', 'simple-hall-booking-manager'); ?>
			</p>
			<div id="shb-calendar" class="shb-calendar">
				<!-- Calendar will be rendered by JavaScript -->
			</div>
			<div id="shb-selected-dates-display" class="shb-selected-dates-display" style="display: none;">
				<p class="shb-dates-label">
					<?php esc_html_e('Selected dates:', 'simple-hall-booking-manager'); ?>
				</p>
				<div id="shb-selected-dates-with-slots" class="shb-selected-dates-with-slots">
					<!-- Dates with slot selection will be rendered here -->
				</div>
				<p class="shb-dates-count">
					<strong>
						<?php esc_html_e('Total:', 'simple-hall-booking-manager'); ?>
					</strong> <span id="shb-dates-count">0</span>
					<?php esc_html_e('day(s)', 'simple-hall-booking-manager'); ?>
				</p>
			</div>
		</div>

		<!-- Customer Information -->
		<h3>
			<?php esc_html_e('Your Information', 'simple-hall-booking-manager'); ?>
		</h3>

		<div class="shb-form-group">
			<label for="shb_customer_name">
				<?php esc_html_e('Full Name', 'simple-hall-booking-manager'); ?>
				<span class="required">*</span>
			</label>
			<input type="text" name="customer_name" id="shb_customer_name" required>
		</div>

		<div class="shb-form-group">
			<label for="shb_customer_email">
				<?php esc_html_e('Email Address', 'simple-hall-booking-manager'); ?>
				<span class="required">*</span>
			</label>
			<input type="email" name="customer_email" id="shb_customer_email" required>
		</div>

		<div class="shb-form-group">
			<label for="shb_customer_phone">
				<?php esc_html_e('Phone Number', 'simple-hall-booking-manager'); ?>
			</label>
			<input type="tel" name="customer_phone" id="shb_customer_phone">
		</div>

		<div class="shb-form-group">
			<label for="shb_customer_organization">
				<?php esc_html_e('Organization', 'simple-hall-booking-manager'); ?>
			</label>
			<input type="text" name="customer_organization" id="shb_customer_organization">
		</div>

		<!-- Event Details -->
		<h3>
			<?php esc_html_e('Event Details', 'simple-hall-booking-manager'); ?>
		</h3>

		<div class="shb-form-group">
			<label for="shb_event_purpose">
				<?php esc_html_e('Event Purpose', 'simple-hall-booking-manager'); ?>
			</label>
			<input type="text" name="event_purpose" id="shb_event_purpose"
				placeholder="<?php esc_attr_e('e.g., Birthday Party, Meeting, Wedding', 'simple-hall-booking-manager'); ?>">
		</div>

		<div class="shb-form-group">
			<label for="shb_attendees_count">
				<?php esc_html_e('Number of Attendees', 'simple-hall-booking-manager'); ?>
			</label>
			<input type="number" name="attendees_count" id="shb_attendees_count" min="1">
		</div>

		<!-- Submit Button -->
		<div class="shb-form-group">
			<button type="submit" class="shb-btn shb-btn-primary shb-btn-large" id="shb-submit-booking">
				<?php esc_html_e('Submit Booking Request', 'simple-hall-booking-manager'); ?>
			</button>
		</div>

		<?php
		$general_settings = get_option('shb_general_settings', array());
		$recaptcha_enabled = isset($general_settings['recaptcha_enabled']) ? $general_settings['recaptcha_enabled'] : 'no';
		$recaptcha_site_key = isset($general_settings['recaptcha_site_key']) ? $general_settings['recaptcha_site_key'] : '';

		if ('yes' === $recaptcha_enabled && !empty($recaptcha_site_key)):
			?>
			<div class="shb-recaptcha-notice" style="font-size: 12px; color: #666; margin-top: 10px; text-align: center;">
				<?php esc_html_e('This site is protected by reCAPTCHA and the Google', 'simple-hall-booking-manager'); ?>
				<a href="https://policies.google.com/privacy" target="_blank">
					<?php esc_html_e('Privacy Policy', 'simple-hall-booking-manager'); ?>
				</a>
				<?php esc_html_e('and', 'simple-hall-booking-manager'); ?>
				<a href="https://policies.google.com/terms" target="_blank">
					<?php esc_html_e('Terms of Service', 'simple-hall-booking-manager'); ?>
				</a>
				<?php esc_html_e('apply.', 'simple-hall-booking-manager'); ?>
			</div>
		<?php endif; ?>
	</form>
</div>