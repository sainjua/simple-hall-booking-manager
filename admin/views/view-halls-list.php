<?php
/**
 * Admin view: Halls list
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$db    = shb()->db;
$halls = $db->get_halls();

// Handle messages
$message = isset( $_GET['message'] ) ? sanitize_text_field( $_GET['message'] ) : '';
?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Halls', 'simple-hall-booking-manager' ); ?>
	</h1>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=shb-halls&action=new' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Add New', 'simple-hall-booking-manager' ); ?>
	</a>

	<hr class="wp-header-end">

	<?php if ( $message ) : ?>
		<?php if ( 'slot_error' === $message ) : ?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					$error_msg = isset( $_GET['error_message'] ) ? urldecode( sanitize_text_field( $_GET['error_message'] ) ) : __( 'Failed to save slot.', 'simple-hall-booking-manager' );
					echo esc_html( $error_msg );
					?>
				</p>
			</div>
		<?php else : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					switch ( $message ) {
						case 'created':
							esc_html_e( 'Hall created successfully.', 'simple-hall-booking-manager' );
							break;
						case 'updated':
							esc_html_e( 'Hall updated successfully.', 'simple-hall-booking-manager' );
							break;
						case 'deleted':
							esc_html_e( 'Hall deleted successfully.', 'simple-hall-booking-manager' );
							break;
						case 'slot_saved':
							esc_html_e( 'Slot saved successfully.', 'simple-hall-booking-manager' );
							break;
						case 'slot_deleted':
							esc_html_e( 'Slot deleted successfully.', 'simple-hall-booking-manager' );
							break;
						default:
							esc_html_e( 'Operation completed.', 'simple-hall-booking-manager' );
					}
					?>
				</p>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( empty( $halls ) ) : ?>
		<div class="notice notice-info">
			<p><?php esc_html_e( 'No halls found. Click "Add New" to create your first hall.', 'simple-hall-booking-manager' ); ?></p>
		</div>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Title', 'simple-hall-booking-manager' ); ?></th>
					<th><?php esc_html_e( 'Capacity', 'simple-hall-booking-manager' ); ?></th>
					<th><?php esc_html_e( 'Status', 'simple-hall-booking-manager' ); ?></th>
					<th><?php esc_html_e( 'Cleaning Buffer', 'simple-hall-booking-manager' ); ?></th>
					<th><?php esc_html_e( 'Shortcode', 'simple-hall-booking-manager' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'simple-hall-booking-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $halls as $hall ) : ?>
					<tr>
						<td>
							<strong>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=shb-halls&action=edit&id=' . $hall->id ) ); ?>">
									<?php echo esc_html( $hall->title ); ?>
								</a>
							</strong>
						</td>
						<td><?php echo esc_html( $hall->capacity ); ?></td>
						<td>
							<span class="shb-status-badge shb-status-<?php echo esc_attr( $hall->status ); ?>">
								<?php echo esc_html( ucfirst( $hall->status ) ); ?>
							</span>
						</td>
						<td><?php echo esc_html( $hall->cleaning_buffer . ' ' . __( 'minutes', 'simple-hall-booking-manager' ) ); ?></td>
						<td>
							<code class="shb-shortcode" style="background: #f0f0f1; padding: 3px 8px; border-radius: 3px; font-size: 12px; cursor: pointer;" 
							      onclick="shbCopyShortcode(this)" 
							      title="<?php esc_attr_e( 'Click to copy', 'simple-hall-booking-manager' ); ?>">
								[shb_booking_form hall_id="<?php echo esc_attr( $hall->id ); ?>"]
							</code>
						</td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=shb-halls&action=edit&id=' . $hall->id ) ); ?>">
								<?php esc_html_e( 'Edit', 'simple-hall-booking-manager' ); ?>
							</a>
							|
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=shb-halls&action=delete_hall&id=' . $hall->id ), 'shb_delete_hall_' . $hall->id ) ); ?>" 
							   class="delete-link" 
							   onclick="return confirm('<?php esc_attr_e( 'Are you sure? This will also delete all slots for this hall.', 'simple-hall-booking-manager' ); ?>');">
								<?php esc_html_e( 'Delete', 'simple-hall-booking-manager' ); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<script>
		function shbCopyShortcode(element) {
			// Get the shortcode text
			var shortcode = element.textContent;
			
			// Create temporary textarea to copy from
			var textarea = document.createElement('textarea');
			textarea.value = shortcode;
			textarea.style.position = 'fixed';
			textarea.style.opacity = '0';
			document.body.appendChild(textarea);
			textarea.select();
			
			try {
				// Copy to clipboard
				document.execCommand('copy');
				
				// Visual feedback
				var originalBg = element.style.background;
				element.style.background = '#46b450';
				element.style.color = '#fff';
				element.textContent = '<?php esc_html_e( 'Copied!', 'simple-hall-booking-manager' ); ?>';
				
				// Reset after 1.5 seconds
				setTimeout(function() {
					element.style.background = originalBg;
					element.style.color = '';
					element.textContent = shortcode;
				}, 1500);
			} catch (err) {
				alert('<?php esc_html_e( 'Failed to copy. Please copy manually.', 'simple-hall-booking-manager' ); ?>');
			}
			
			document.body.removeChild(textarea);
		}
		</script>
	<?php endif; ?>
</div>

