<?php
/**
 * Template: User booking (guest access via token or PIN)
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$db      = shb()->db;
$booking = null;
$error_message = '';

// Check for PIN (from POST or GET)
$pin = '';
if ( isset( $_POST['booking_pin'] ) && isset( $_POST['shb_pin_nonce'] ) && wp_verify_nonce( $_POST['shb_pin_nonce'], 'shb_pin_lookup' ) ) {
	$pin = sanitize_text_field( $_POST['booking_pin'] );
} elseif ( isset( $_GET['pin'] ) ) {
	$pin = sanitize_text_field( $_GET['pin'] );
}

// Check for token (from GET)
$token = isset( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] ) : '';

// Try to find booking by PIN first, then token
if ( $pin ) {
	$booking = $db->get_booking_by_pin( $pin );
	if ( ! $booking ) {
		$error_message = __( 'Invalid PIN. Please check your PIN and try again.', 'simple-hall-booking-manager' );
	}
} elseif ( $token ) {
	$booking = $db->get_booking_by_token( $token );
	if ( ! $booking ) {
		$error_message = __( 'Invalid access token. Please check your link.', 'simple-hall-booking-manager' );
	}
}

// If no booking found and no PIN/token provided, show PIN input form
if ( ! $booking && ! $pin && ! $token ) {
	?>
	<div class="shb-pin-lookup-form">
		<h2><?php esc_html_e( 'View Your Booking', 'simple-hall-booking-manager' ); ?></h2>
		<p><?php esc_html_e( 'Enter your 6-digit booking PIN to view your booking details.', 'simple-hall-booking-manager' ); ?></p>
		
		<form method="post" class="shb-pin-form">
			<?php wp_nonce_field( 'shb_pin_lookup', 'shb_pin_nonce' ); ?>
			
			<div class="shb-form-group">
				<label for="booking_pin">
					<?php esc_html_e( 'Booking PIN', 'simple-hall-booking-manager' ); ?>
					<span class="shb-required">*</span>
				</label>
				<input 
					type="text" 
					name="booking_pin" 
					id="booking_pin" 
					class="shb-pin-input" 
					placeholder="AA1111" 
					maxlength="6" 
					pattern="[A-Za-z]{2}[0-9]{4}"
					required
					style="text-transform: uppercase; font-size: 20px; letter-spacing: 3px; font-family: 'Courier New', monospace; text-align: center;"
				>
				<p class="shb-form-help"><?php esc_html_e( 'Format: 2 letters followed by 4 numbers (e.g., AA1111)', 'simple-hall-booking-manager' ); ?></p>
			</div>
			
			<button type="submit" class="shb-btn shb-btn-primary">
				<?php esc_html_e( 'View Booking', 'simple-hall-booking-manager' ); ?>
			</button>
		</form>
	</div>
	<?php
	return;
}

// If we have an error message, show it with option to try again
if ( $error_message ) {
	?>
	<div class="shb-notice shb-notice-error">
		<p><?php echo esc_html( $error_message ); ?></p>
	</div>
	<p>
		<a href="<?php echo esc_url( remove_query_arg( array( 'token', 'pin' ) ) ); ?>" class="shb-btn shb-btn-secondary">
			<?php esc_html_e( '← Try Another PIN', 'simple-hall-booking-manager' ); ?>
		</a>
	</p>
	<?php
	return;
}

$hall = $db->get_hall( $booking->hall_id );

// Get booking dates (works for both single and multi-day bookings)
$booking_dates = $db->get_booking_dates( $booking->id );
$slot = null;
if ( ! empty( $booking_dates ) ) {
	$slot = $db->get_slot( $booking_dates[0]->slot_id );
}

// Handle cancellation
if ( isset( $_POST['shb_cancel_booking'] ) && wp_verify_nonce( $_POST['shb_cancel_nonce'], 'shb_cancel_booking_' . $booking->id ) ) {
	if ( 'cancelled' !== $booking->status ) {
		$db->update_booking_status( $booking->id, 'cancelled' );
		$booking->status = 'cancelled';

		// Send cancellation email
		shb()->emails->send_guest_cancelled( $booking->id );

		echo '<div class="shb-notice shb-notice-success">';
		echo '<p>' . esc_html__( 'Your booking has been cancelled.', 'simple-hall-booking-manager' ) . '</p>';
		echo '</div>';
	}
}
?>

<div class="shb-user-booking">
	<h2><?php esc_html_e( 'Booking Details', 'simple-hall-booking-manager' ); ?></h2>

	<!-- Status Badge -->
	<div class="shb-booking-status">
		<span class="shb-status-badge shb-status-<?php echo esc_attr( $booking->status ); ?>">
			<?php echo esc_html( shb_get_status_label( $booking->status ) ); ?>
		</span>
	</div>

	<!-- Booking Information -->
	<div class="shb-booking-details">
		<div class="shb-detail-row">
			<span class="shb-detail-label"><?php esc_html_e( 'Booking ID:', 'simple-hall-booking-manager' ); ?></span>
			<span class="shb-detail-value">#<?php echo esc_html( $booking->id ); ?></span>
		</div>

		<div class="shb-detail-row">
			<span class="shb-detail-label"><?php esc_html_e( 'Booking PIN:', 'simple-hall-booking-manager' ); ?></span>
			<span class="shb-detail-value">
				<strong style="font-family: 'Courier New', monospace; font-size: 18px; letter-spacing: 2px; color: #0073aa;">
					<?php echo esc_html( $booking->pin ); ?>
				</strong>
				<br>
				<small style="color: #666;">
					<?php esc_html_e( 'Use this PIN to access your booking anytime', 'simple-hall-booking-manager' ); ?>
				</small>
			</span>
		</div>

		<div class="shb-detail-row">
			<span class="shb-detail-label"><?php esc_html_e( 'Hall:', 'simple-hall-booking-manager' ); ?></span>
			<span class="shb-detail-value"><?php echo $hall ? esc_html( $hall->title ) : '-'; ?></span>
		</div>

		<?php if ( 'multiday' === $booking->booking_type ) : ?>
			<div class="shb-detail-row">
				<span class="shb-detail-label"><?php esc_html_e( 'Booking Type:', 'simple-hall-booking-manager' ); ?></span>
				<span class="shb-detail-value">
					<span class="shb-multiday-badge">📅 <?php esc_html_e( 'Multi-Day Booking', 'simple-hall-booking-manager' ); ?></span>
				</span>
			</div>

			<div class="shb-detail-row shb-detail-full">
				<span class="shb-detail-label"><?php esc_html_e( 'Booking Dates:', 'simple-hall-booking-manager' ); ?></span>
				<div class="shb-detail-value">
					<p><strong><?php echo sprintf( _n( '%d day', '%d days', count( $booking_dates ), 'simple-hall-booking-manager' ), count( $booking_dates ) ); ?></strong></p>
					<?php if ( ! empty( $booking_dates ) ) : ?>
						<table class="shb-user-booking-dates-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Date', 'simple-hall-booking-manager' ); ?></th>
									<th><?php esc_html_e( 'Time Slot', 'simple-hall-booking-manager' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $booking_dates as $bd ) : ?>
									<?php $bd_slot = $db->get_slot( $bd->slot_id ); ?>
									<tr>
										<td><?php echo esc_html( shb_format_date( $bd->booking_date ) ); ?></td>
										<td>
											<?php
											if ( $bd_slot ) {
												echo esc_html( $bd_slot->label . ' (' . date( 'g:i A', strtotime( $bd_slot->start_time ) ) . ' - ' . date( 'g:i A', strtotime( $bd_slot->end_time ) ) . ')' );
											} else {
												echo '-';
											}
											?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
			</div>
		<?php else : ?>
			<div class="shb-detail-row">
				<span class="shb-detail-label"><?php esc_html_e( 'Date:', 'simple-hall-booking-manager' ); ?></span>
				<span class="shb-detail-value">
					<?php echo ! empty( $booking_dates ) ? esc_html( shb_format_date( $booking_dates[0]->booking_date ) ) : '-'; ?>
				</span>
			</div>

			<div class="shb-detail-row">
				<span class="shb-detail-label"><?php esc_html_e( 'Time Slot:', 'simple-hall-booking-manager' ); ?></span>
				<span class="shb-detail-value">
					<?php
					if ( $slot ) {
						echo esc_html( $slot->label . ' (' . date( 'g:i A', strtotime( $slot->start_time ) ) . ' - ' . date( 'g:i A', strtotime( $slot->end_time ) ) . ')' );
					} else {
						echo '-';
					}
					?>
				</span>
			</div>
		<?php endif; ?>

		<div class="shb-detail-row">
			<span class="shb-detail-label"><?php esc_html_e( 'Customer Name:', 'simple-hall-booking-manager' ); ?></span>
			<span class="shb-detail-value"><?php echo esc_html( $booking->customer_name ); ?></span>
		</div>

		<div class="shb-detail-row">
			<span class="shb-detail-label"><?php esc_html_e( 'Email:', 'simple-hall-booking-manager' ); ?></span>
			<span class="shb-detail-value"><?php echo esc_html( $booking->customer_email ); ?></span>
		</div>

		<?php if ( $booking->customer_phone ) : ?>
			<div class="shb-detail-row">
				<span class="shb-detail-label"><?php esc_html_e( 'Phone:', 'simple-hall-booking-manager' ); ?></span>
				<span class="shb-detail-value"><?php echo esc_html( $booking->customer_phone ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $booking->event_purpose ) : ?>
			<div class="shb-detail-row">
				<span class="shb-detail-label"><?php esc_html_e( 'Event Purpose:', 'simple-hall-booking-manager' ); ?></span>
				<span class="shb-detail-value"><?php echo esc_html( $booking->event_purpose ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $booking->attendees_count ) : ?>
			<div class="shb-detail-row">
				<span class="shb-detail-label"><?php esc_html_e( 'Attendees:', 'simple-hall-booking-manager' ); ?></span>
				<span class="shb-detail-value"><?php echo esc_html( $booking->attendees_count ); ?></span>
			</div>
		<?php endif; ?>

		<div class="shb-detail-row">
			<span class="shb-detail-label"><?php esc_html_e( 'Booked On:', 'simple-hall-booking-manager' ); ?></span>
			<span class="shb-detail-value"><?php echo esc_html( shb_format_date( $booking->created_at ) . ' ' . date( 'g:i A', strtotime( $booking->created_at ) ) ); ?></span>
		</div>
	</div>

	<!-- Actions -->
	<?php if ( 'pending' === $booking->status || 'confirmed' === $booking->status ) : ?>
		<div class="shb-booking-actions">
			<form method="post" onsubmit="return confirm('<?php esc_attr_e( 'Are you sure you want to cancel this booking?', 'simple-hall-booking-manager' ); ?>');">
				<?php wp_nonce_field( 'shb_cancel_booking_' . $booking->id, 'shb_cancel_nonce' ); ?>
				<button type="submit" name="shb_cancel_booking" class="shb-btn shb-btn-danger">
					<?php esc_html_e( 'Cancel Booking', 'simple-hall-booking-manager' ); ?>
				</button>
			</form>
		</div>
	<?php endif; ?>

	<?php if ( 'pending' === $booking->status ) : ?>
		<div class="shb-notice shb-notice-info">
			<p><?php esc_html_e( 'Your booking is pending approval. You will receive an email once it has been reviewed.', 'simple-hall-booking-manager' ); ?></p>
		</div>
	<?php endif; ?>
</div>

