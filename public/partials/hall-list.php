<?php
/**
 * Template: Hall list
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$db      = shb()->db;
$halls   = $db->get_halls( array( 'status' => 'active' ) );
$columns = isset( $atts['columns'] ) ? absint( $atts['columns'] ) : 3;
?>

<div class="shb-hall-list shb-columns-<?php echo esc_attr( $columns ); ?>">
	<?php if ( empty( $halls ) ) : ?>
		<p class="shb-no-halls"><?php esc_html_e( 'No halls available at the moment.', 'simple-hall-booking-manager' ); ?></p>
	<?php else : ?>
		<?php foreach ( $halls as $hall ) : ?>
			<div class="shb-hall-card">
				<div class="shb-hall-header">
					<h3 class="shb-hall-title"><?php echo esc_html( $hall->title ); ?></h3>
				</div>
				<div class="shb-hall-body">
					<?php if ( $hall->description ) : ?>
						<div class="shb-hall-description">
							<?php echo wp_kses_post( $hall->description ); ?>
						</div>
					<?php endif; ?>
					<div class="shb-hall-meta">
						<span class="shb-hall-capacity">
							<strong><?php esc_html_e( 'Capacity:', 'simple-hall-booking-manager' ); ?></strong>
							<?php echo esc_html( $hall->capacity ); ?>
						</span>
					</div>
				</div>
				<div class="shb-hall-footer">
					<a href="<?php echo esc_url( add_query_arg( 'hall_id', $hall->id, get_permalink() ) ); ?>" class="shb-btn shb-btn-primary">
						<?php esc_html_e( 'Book Now', 'simple-hall-booking-manager' ); ?>
					</a>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

