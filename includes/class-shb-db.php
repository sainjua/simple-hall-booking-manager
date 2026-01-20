<?php
/**
 * Database handler class
 *
 * @package SimpleHallBookingManager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Database class
 */
class SHB_DB
{

	/**
	 * WordPress database object
	 *
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Get halls table name
	 *
	 * @return string
	 */
	public function get_table_halls()
	{
		return $this->wpdb->prefix . 'shb_halls';
	}

	/**
	 * Get slots table name
	 *
	 * @return string
	 */
	public function get_table_slots()
	{
		return $this->wpdb->prefix . 'shb_slots';
	}

	/**
	 * Get bookings table name
	 *
	 * @return string
	 */
	public function get_table_bookings()
	{
		return $this->wpdb->prefix . 'shb_bookings';
	}

	/**
	 * Get booking dates table name
	 *
	 * @return string
	 */
	public function get_table_booking_dates()
	{
		return $this->wpdb->prefix . 'shb_booking_dates';
	}

	/**
	 * Create database tables
	 */
	public function create_tables()
	{
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $this->wpdb->get_charset_collate();

		// Create halls table
		$sql_halls = $this->get_schema_halls();
		dbDelta($sql_halls);

		// Create slots table
		$sql_slots = $this->get_schema_slots();
		dbDelta($sql_slots);

		// Create bookings table
		$sql_bookings = $this->get_schema_bookings();
		dbDelta($sql_bookings);

		// Create booking dates table (for multi-day bookings)
		$sql_booking_dates = $this->get_schema_booking_dates();
		dbDelta($sql_booking_dates);

		// Run migrations if needed
		$this->check_and_run_migrations();
	}

	/**
	 * Get halls table schema
	 *
	 * @return string
	 */
	public function get_schema_halls()
	{
		$table_name = $this->get_table_halls();
		$charset_collate = $this->wpdb->get_charset_collate();

		return "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			title varchar(255) NOT NULL,
			description text,
			capacity int(11) NOT NULL DEFAULT 0,
			status enum('active','inactive') NOT NULL DEFAULT 'active',
			cleaning_buffer int(11) NOT NULL DEFAULT 0 COMMENT 'Minutes',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY status (status)
		) {$charset_collate};";
	}

	/**
	 * Get slots table schema
	 *
	 * @return string
	 */
	public function get_schema_slots()
	{
		$table_name = $this->get_table_slots();
		$charset_collate = $this->wpdb->get_charset_collate();

		return "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			hall_id bigint(20) unsigned NOT NULL,
			slot_type enum('full_day','partial') NOT NULL DEFAULT 'partial',
			label varchar(100) NOT NULL,
			start_time time NOT NULL,
			end_time time NOT NULL,
			days_enabled text COMMENT 'JSON array of enabled days',
			is_active tinyint(1) NOT NULL DEFAULT 1,
			sort_order int(11) NOT NULL DEFAULT 0,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY hall_id (hall_id),
			KEY is_active (is_active)
		) {$charset_collate};";
	}

	/**
	 * Get bookings table schema
	 *
	 * @return string
	 */
	public function get_schema_bookings()
	{
		$table_name = $this->get_table_bookings();
		$charset_collate = $this->wpdb->get_charset_collate();

		return "CREATE TABLE {$table_name} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		hall_id bigint(20) unsigned NOT NULL,
		booking_type enum('single','multiday') NOT NULL DEFAULT 'single',
		customer_name varchar(255) NOT NULL,
		customer_email varchar(255) NOT NULL,
		customer_phone varchar(50),
		customer_organization varchar(255),
		event_purpose varchar(255),
		attendees_count int(11) NOT NULL DEFAULT 0,
		status enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
		access_token varchar(64) NOT NULL,
		pin varchar(6) NOT NULL COMMENT 'Format: AA1111 (2 letters + 4 digits)',
		admin_notes text,
		created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY access_token (access_token),
		UNIQUE KEY pin (pin),
		KEY hall_id (hall_id),
		KEY booking_type (booking_type),
		KEY status (status),
		KEY customer_email (customer_email)
	) {$charset_collate};";
	}

	/**
	 * Get booking dates table schema (for multi-day bookings)
	 *
	 * @return string
	 */
	public function get_schema_booking_dates()
	{
		$table_name = $this->get_table_booking_dates();
		$charset_collate = $this->wpdb->get_charset_collate();

		return "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			booking_id bigint(20) unsigned NOT NULL,
			booking_date date NOT NULL,
			slot_id bigint(20) unsigned NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY booking_id (booking_id),
			KEY booking_date (booking_date),
			KEY slot_id (slot_id),
			KEY booking_date_slot (booking_date, slot_id)
		) {$charset_collate};";
	}

	/**
	 * Check and run database migrations
	 */
	private function check_and_run_migrations()
	{
		$db_version = get_option('shb_db_version', '1.0.0');

		// Migration for v1.1.0 (multi-day bookings)
		if (version_compare($db_version, '1.1.0', '<')) {
			$this->migrate_to_v110();
			update_option('shb_db_version', '1.1.0');
		}

		// Migration for v1.2.0 (booking PIN system)
		if (version_compare($db_version, '1.2.0', '<')) {
			$this->migrate_to_v120();
			update_option('shb_db_version', '1.2.0');
		}

		// Migration for v1.3.0 (remove booking_date from bookings table)
		if (version_compare($db_version, '1.3.0', '<')) {
			$this->migrate_to_v130();
			update_option('shb_db_version', '1.3.0');
		}

		// Migration for v1.4.0 (add customer_organization column)
		if (version_compare($db_version, '1.4.0', '<')) {
			$this->migrate_to_v140();
			update_option('shb_db_version', '1.4.0');
		}
	}

	/**
	 * Migrate database to v1.1.0
	 * Adds booking_type column if it doesn't exist
	 */
	private function migrate_to_v110()
	{
		$table_bookings = $this->get_table_bookings();

		// Check if booking_type column exists
		// Check if booking_type column exists
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SHOW COLUMNS FROM {$table_bookings} LIKE %s";
		$column_exists = $this->wpdb->get_results(
			$this->wpdb->prepare($sql, 'booking_type') // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		// Add booking_type column if it doesn't exist
		if (empty($column_exists)) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->wpdb->query(
				"ALTER TABLE {$table_bookings} 
				ADD COLUMN booking_type enum('single','multiday') NOT NULL DEFAULT 'single' 
				AFTER slot_id"
			);

			// Make booking_date nullable for multiday bookings
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->wpdb->query(
				"ALTER TABLE {$table_bookings} 
				MODIFY COLUMN booking_date date DEFAULT NULL 
				COMMENT 'Used for single bookings only'"
			);
		}
	}

	/**
	 * Migrate database to v1.2.0
	 * Adds PIN column to bookings table
	 */
	private function migrate_to_v120()
	{
		$table_bookings = $this->get_table_bookings();

		// Check if pin column exists
		// Check if pin column exists
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SHOW COLUMNS FROM {$table_bookings} LIKE %s";
		$column_exists = $this->wpdb->get_results(
			$this->wpdb->prepare($sql, 'pin') // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		// Add pin column if it doesn't exist
		if (empty($column_exists)) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->wpdb->query(
				"ALTER TABLE {$table_bookings} 
				ADD COLUMN pin varchar(6) NOT NULL DEFAULT '' COMMENT 'Format: AA1111 (2 letters + 4 digits)' 
				AFTER access_token,
				ADD UNIQUE KEY pin (pin)"
			);

			// Generate PINs for existing bookings
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$bookings = $this->wpdb->get_results("SELECT id FROM {$table_bookings} WHERE pin = ''");

			foreach ($bookings as $booking) {
				$pin = $this->generate_unique_pin();
				$this->wpdb->update(
					$table_bookings,
					array('pin' => $pin),
					array('id' => $booking->id),
					array('%s'),
					array('%d')
				);
			}
		}
	}

	/**
	 * Migrate database to v1.3.0
	 * Removes booking_date and slot_id columns from bookings table
	 * Migrates single-day booking dates and slots to booking_dates table
	 */
	private function migrate_to_v130()
	{
		$table_bookings = $this->get_table_bookings();
		$table_booking_dates = $this->get_table_booking_dates();

		// Check if booking_date column still exists
		// Check if booking_date column still exists
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SHOW COLUMNS FROM {$table_bookings} LIKE %s";
		$has_booking_date_column = $this->wpdb->get_results(
			$this->wpdb->prepare($sql, 'booking_date') // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		if (!empty($has_booking_date_column)) {
			// Migrate single-day bookings to use booking_dates table
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$single_bookings = $this->wpdb->get_results(
				"SELECT id, booking_date, slot_id 
				FROM {$table_bookings} 
				WHERE booking_type = 'single' 
				AND booking_date IS NOT NULL"
			);

			foreach ($single_bookings as $booking) {
				// Check if this booking already has an entry in booking_dates
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
				$sql = "SELECT COUNT(*) FROM {$table_booking_dates} WHERE booking_id = %d";
				$has_dates = $this->wpdb->get_var(
					$this->wpdb->prepare($sql, $booking->id) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				);

				// Only insert if not already present
				if (0 == $has_dates) {
					$this->wpdb->insert(
						$table_booking_dates,
						array(
							'booking_id' => $booking->id,
							'booking_date' => $booking->booking_date,
							'slot_id' => $booking->slot_id,
						),
						array('%d', '%s', '%d')
					);
				}
			}

			// Drop the booking_date column and its index
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->wpdb->query(
				"ALTER TABLE {$table_bookings} 
				DROP INDEX booking_date,
				DROP COLUMN booking_date"
			);
		}

		// Check if slot_id column still exists
		// Check if slot_id column still exists
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SHOW COLUMNS FROM {$table_bookings} LIKE %s";
		$column_exists = $this->wpdb->get_results(
			$this->wpdb->prepare($sql, 'slot_id') // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		if (!empty($column_exists)) {
			// Drop the slot_id column and its index
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->wpdb->query(
				"ALTER TABLE {$table_bookings} 
				DROP INDEX slot_id,
				DROP COLUMN slot_id"
			);
		}
	}

	/**
	 * Migrate database to v1.4.0
	 * Adds customer_organization column to bookings table
	 */
	private function migrate_to_v140()
	{
		$table_bookings = $this->get_table_bookings();

		// Check if customer_organization column exists
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SHOW COLUMNS FROM {$table_bookings} LIKE %s";
		$column_exists = $this->wpdb->get_results(
			$this->wpdb->prepare($sql, 'customer_organization') // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		// Add customer_organization column if it doesn't exist
		if (empty($column_exists)) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->wpdb->query(
				"ALTER TABLE {$table_bookings} 
				ADD COLUMN customer_organization varchar(255) DEFAULT NULL 
				AFTER customer_phone"
			);
		}
	}

	// ============================================================
	// HALLS CRUD
	// ============================================================

	/**
	 * Insert a new hall
	 *
	 * @param array $data Hall data.
	 * @return int|false Hall ID or false on failure.
	 */
	public function insert_hall($data)
	{
		$defaults = array(
			'title' => '',
			'description' => '',
			'capacity' => 0,
			'status' => 'active',
			'cleaning_buffer' => 0,
		);

		$data = wp_parse_args($data, $defaults);

		$result = $this->wpdb->insert(
			$this->get_table_halls(),
			array(
				'title' => sanitize_text_field($data['title']),
				'description' => wp_kses_post($data['description']),
				'capacity' => absint($data['capacity']),
				'status' => in_array($data['status'], array('active', 'inactive'), true) ? $data['status'] : 'active',
				'cleaning_buffer' => absint($data['cleaning_buffer']),
			),
			array('%s', '%s', '%d', '%s', '%d')
		);

		return $result ? $this->wpdb->insert_id : false;
	}

	/**
	 * Update a hall
	 *
	 * @param int   $id Hall ID.
	 * @param array $data Hall data.
	 * @return bool
	 */
	public function update_hall($id, $data)
	{
		$update_data = array();
		$format = array();

		if (isset($data['title'])) {
			$update_data['title'] = sanitize_text_field($data['title']);
			$format[] = '%s';
		}

		if (isset($data['description'])) {
			$update_data['description'] = wp_kses_post($data['description']);
			$format[] = '%s';
		}

		if (isset($data['capacity'])) {
			$update_data['capacity'] = absint($data['capacity']);
			$format[] = '%d';
		}

		if (isset($data['status'])) {
			$update_data['status'] = in_array($data['status'], array('active', 'inactive'), true) ? $data['status'] : 'active';
			$format[] = '%s';
		}

		if (isset($data['cleaning_buffer'])) {
			$update_data['cleaning_buffer'] = absint($data['cleaning_buffer']);
			$format[] = '%d';
		}

		if (empty($update_data)) {
			return false;
		}

		$result = $this->wpdb->update(
			$this->get_table_halls(),
			$update_data,
			array('id' => absint($id)),
			$format,
			array('%d')
		);

		return false !== $result;
	}

	/**
	 * Get a single hall
	 *
	 * @param int $id Hall ID.
	 * @return object|null
	 */
	public function get_hall($id)
	{
		$table = $this->get_table_halls();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SELECT * FROM {$table} WHERE id = %d";
		return $this->wpdb->get_row(
			$this->wpdb->prepare($sql, absint($id)) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);
	}

	/**
	 * Get halls with optional filters
	 *
	 * @param array $args Query arguments.
	 * @return array
	 */
	public function get_halls($args = array())
	{
		$defaults = array(
			'status' => '',
			'orderby' => 'title',
			'order' => 'ASC',
			'limit' => -1,
			'offset' => 0,
		);

		$args = wp_parse_args($args, $defaults);
		$table = $this->get_table_halls();
		$where = array('1=1');

		if (!empty($args['status'])) {
			$where[] = $this->wpdb->prepare('status = %s', $args['status']);
		}

		$where_sql = implode(' AND ', $where);
		$order_by = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
		$limit_sql = '';

		if ($args['limit'] > 0) {
			$limit_sql = $this->wpdb->prepare('LIMIT %d OFFSET %d', absint($args['limit']), absint($args['offset']));
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
		$sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$order_by} {$limit_sql}";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $this->wpdb->get_results($sql);
	}

	/**
	 * Delete a hall
	 *
	 * @param int $id Hall ID.
	 * @return bool
	 */
	public function delete_hall($id)
	{
		// Delete related slots first
		$this->wpdb->delete(
			$this->get_table_slots(),
			array('hall_id' => absint($id)),
			array('%d')
		);

		// Delete the hall
		$result = $this->wpdb->delete(
			$this->get_table_halls(),
			array('id' => absint($id)),
			array('%d')
		);

		return false !== $result;
	}

	// ============================================================
	// SLOTS CRUD
	// ============================================================

	/**
	 * Check for slot time overlap within a hall.
	 * 
	 * @param int    $hall_id    ID of the hall.
	 * @param string $start_time Start time (H:i:s).
	 * @param string $end_time   End time (H:i:s).
	 * @param int    $exclude_id Optional. Slot ID to exclude (for edit validation).
	 * @return bool True if overlap exists, false otherwise.
	 */
	public function check_slot_overlap($hall_id, $start_time, $end_time, $exclude_id = 0)
	{
		$table = $this->get_table_slots();
		$sql = "SELECT id FROM {$table} 
				WHERE hall_id = %d 
				AND slot_type = 'partial' 
				AND (
					(start_time < %s AND end_time > %s)
				)";

		$params = array($hall_id, $end_time, $start_time);

		if ($exclude_id > 0) {
			$sql .= " AND id != %d";
			$params[] = $exclude_id;
		}


		$overlap = $this->wpdb->get_var(
			$this->wpdb->prepare($sql, $params) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		return !empty($overlap);
	}

	/**
	 * Insert a new slot
	 *
	 * @param array $data Slot data.
	 * @return int|false Slot ID or false on failure.
	 */
	public function insert_slot($data)
	{
		$defaults = array(
			'hall_id' => 0,
			'slot_type' => 'partial',
			'label' => '',
			'start_time' => '00:00:00',
			'end_time' => '23:59:59',
			'days_enabled' => wp_json_encode(array(0, 1, 2, 3, 4, 5, 6)),
			'is_active' => 1,
			'sort_order' => 0,
		);

		$data = wp_parse_args($data, $defaults);

		// Check for overlap if partial
		if ('partial' === $data['slot_type']) {
			if ($this->check_slot_overlap($data['hall_id'], $data['start_time'], $data['end_time'])) {
				return false; // Time overlap
			}
		}

		// Ensure days_enabled is JSON
		if (is_array($data['days_enabled'])) {
			$data['days_enabled'] = wp_json_encode($data['days_enabled']);
		}

		$result = $this->wpdb->insert(
			$this->get_table_slots(),
			array(
				'hall_id' => absint($data['hall_id']),
				'slot_type' => in_array($data['slot_type'], array('full_day', 'partial'), true) ? $data['slot_type'] : 'partial',
				'label' => sanitize_text_field($data['label']),
				'start_time' => sanitize_text_field($data['start_time']),
				'end_time' => sanitize_text_field($data['end_time']),
				'days_enabled' => $data['days_enabled'],
				'is_active' => absint($data['is_active']),
				'sort_order' => absint($data['sort_order']),
			),
			array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d')
		);

		return $result ? $this->wpdb->insert_id : false;
	}

	/**
	 * Update a slot
	 *
	 * @param int   $id Slot ID.
	 * @param array $data Slot data.
	 * @return bool
	 */
	public function update_slot($id, $data)
	{
		// Get existing slot to find hall_id and current values
		$existing_slot = $this->get_slot($id);
		if (!$existing_slot) {
			return false;
		}

		$hall_id = $existing_slot->hall_id;
		$slot_type = $existing_slot->slot_type; // Type usually doesn't change via this form, but good to know

		// If valid start/end time provided, use them; otherwise use existing
		$start_time = isset($data['start_time']) ? $data['start_time'] : $existing_slot->start_time;
		$end_time = isset($data['end_time']) ? $data['end_time'] : $existing_slot->end_time;

		// Check for overlap if partial (and verify slot type is actually partial)
		// Note: The form might allow changing slot_type, but typically we edit existing type. 
		// If data has slot_type, use it.
		if (isset($data['slot_type'])) {
			$slot_type = $data['slot_type'];
		}

		if ('partial' === $slot_type) {
			if ($this->check_slot_overlap($hall_id, $start_time, $end_time, $id)) {
				return false; // Time overlap
			}
		}

		$update_data = array();
		$format = array();

		if (isset($data['label'])) {
			$update_data['label'] = sanitize_text_field($data['label']);
			$format[] = '%s';
		}

		if (isset($data['start_time'])) {
			$update_data['start_time'] = sanitize_text_field($data['start_time']);
			$format[] = '%s';
		}

		if (isset($data['end_time'])) {
			$update_data['end_time'] = sanitize_text_field($data['end_time']);
			$format[] = '%s';
		}

		if (isset($data['days_enabled'])) {
			if (is_array($data['days_enabled'])) {
				$data['days_enabled'] = wp_json_encode($data['days_enabled']);
			}
			$update_data['days_enabled'] = $data['days_enabled'];
			$format[] = '%s';
		}

		if (isset($data['is_active'])) {
			$update_data['is_active'] = absint($data['is_active']);
			$format[] = '%d';
		}

		if (isset($data['sort_order'])) {
			$update_data['sort_order'] = absint($data['sort_order']);
			$format[] = '%d';
		}

		if (empty($update_data)) {
			return false;
		}

		$result = $this->wpdb->update(
			$this->get_table_slots(),
			$update_data,
			array('id' => absint($id)),
			$format,
			array('%d')
		);

		return false !== $result;
	}

	/**
	 * Get a single slot
	 *
	 * @param int $id Slot ID.
	 * @return object|null
	 */
	public function get_slot($id)
	{
		$table = $this->get_table_slots();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SELECT * FROM {$table} WHERE id = %d";
		return $this->wpdb->get_row(
			$this->wpdb->prepare($sql, absint($id)) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);
	}

	/**
	 * Get slots by hall ID
	 *
	 * @param int   $hall_id Hall ID.
	 * @param array $args Additional arguments.
	 * @return array
	 */
	public function get_slots_by_hall($hall_id, $args = array(), $current_slot = NULL)
	{
		$defaults = array(
			'is_active' => '',
			'slot_type' => '',
		);

		$args = wp_parse_args($args, $defaults);
		$table = $this->get_table_slots();
		$where = array($this->wpdb->prepare('hall_id = %d', absint($hall_id)));

		if ('' !== $args['is_active']) {
			$where[] = $this->wpdb->prepare('is_active = %d', absint($args['is_active']));
		}

		if (!empty($args['slot_type'])) {
			$where[] = $this->wpdb->prepare('slot_type = %s', $args['slot_type']);
		}

		$where_sql = implode(' AND ', $where);
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
		$sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY sort_order ASC, start_time ASC";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $this->wpdb->get_results($sql);
	}

	/**
	 * Delete a slot
	 *
	 * @param int $id Slot ID.
	 * @return bool
	 */
	public function delete_slot($id)
	{
		$result = $this->wpdb->delete(
			$this->get_table_slots(),
			array('id' => absint($id)),
			array('%d')
		);

		return false !== $result;
	}

	// ============================================================
	// BOOKINGS CRUD
	// ============================================================

	/**
	 * Insert a new booking
	 *
	 * @param array $data Booking data.
	 * @return int|false Booking ID or false on failure.
	 */
	public function insert_booking($data)
	{
		$defaults = array(
			'hall_id' => 0,
			'customer_name' => '',
			'customer_email' => '',
			'customer_phone' => '',
			'customer_organization' => '',
			'event_purpose' => '',
			'attendees_count' => 0,
			'status' => 'pending',
			'access_token' => '',
			'pin' => '',
			'booking_type' => 'single',
			'admin_notes' => '',
		);

		$data = wp_parse_args($data, $defaults);

		$result = $this->wpdb->insert(
			$this->get_table_bookings(),
			array(
				'hall_id' => absint($data['hall_id']),
				'booking_type' => in_array($data['booking_type'], array('single', 'multiday'), true) ? $data['booking_type'] : 'single',
				'customer_name' => sanitize_text_field($data['customer_name']),
				'customer_email' => sanitize_email($data['customer_email']),
				'customer_phone' => sanitize_text_field($data['customer_phone']),
				'customer_organization' => sanitize_text_field($data['customer_organization']),
				'event_purpose' => sanitize_text_field($data['event_purpose']),
				'attendees_count' => absint($data['attendees_count']),
				'status' => in_array($data['status'], array('pending', 'confirmed', 'cancelled'), true) ? $data['status'] : 'pending',
				'access_token' => sanitize_text_field($data['access_token']),
				'pin' => strtoupper(sanitize_text_field($data['pin'])),
				'admin_notes' => wp_kses_post($data['admin_notes']),
			),
			array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
		);

		return $result ? $this->wpdb->insert_id : false;
	}

	/**
	 * Create booking with auto-generated token and PIN
	 *
	 * @param array $data Booking data.
	 * @return int|false Booking ID or false on failure.
	 */
	public function create_booking_with_token($data)
	{
		// Generate unique token
		$data['access_token'] = shb_generate_token(32);

		// Generate unique PIN
		$data['pin'] = $this->generate_unique_pin();

		return $this->insert_booking($data);
	}

	/**
	 * Update a booking
	 *
	 * @param int   $id Booking ID.
	 * @param array $data Booking data.
	 * @return bool
	 */
	public function update_booking($id, $data)
	{
		$update_data = array();
		$format = array();

		$allowed_fields = array(
			'customer_name' => '%s',
			'customer_email' => '%s',
			'customer_phone' => '%s',
			'customer_organization' => '%s',
			'event_purpose' => '%s',
			'attendees_count' => '%d',
			'status' => '%s',
			'admin_notes' => '%s',
		);

		foreach ($allowed_fields as $field => $field_format) {
			if (isset($data[$field])) {
				if ('status' === $field) {
					$update_data[$field] = in_array($data[$field], array('pending', 'confirmed', 'cancelled'), true) ? $data[$field] : 'pending';
				} elseif ('customer_email' === $field) {
					$update_data[$field] = sanitize_email($data[$field]);
				} elseif ('admin_notes' === $field) {
					$update_data[$field] = wp_kses_post($data[$field]);
				} elseif ('attendees_count' === $field) {
					$update_data[$field] = absint($data[$field]);
				} else {
					$update_data[$field] = sanitize_text_field($data[$field]);
				}
				$format[] = $field_format;
			}
		}

		if (empty($update_data)) {
			return false;
		}

		$result = $this->wpdb->update(
			$this->get_table_bookings(),
			$update_data,
			array('id' => absint($id)),
			$format,
			array('%d')
		);

		return false !== $result;
	}

	/**
	 * Update booking status
	 *
	 * @param int    $id Booking ID.
	 * @param string $status New status.
	 * @return bool
	 */
	public function update_booking_status($id, $status)
	{
		return $this->update_booking($id, array('status' => $status));
	}

	/**
	 * Get a single booking
	 *
	 * @param int $id Booking ID.
	 * @return object|null
	 */
	public function get_booking($id)
	{
		$table = $this->get_table_bookings();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SELECT * FROM {$table} WHERE id = %d";
		return $this->wpdb->get_row(
			$this->wpdb->prepare($sql, absint($id)) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);
	}

	/**
	 * Get booking by access token
	 *
	 * @param string $token Access token.
	 * @return object|null
	 */
	public function get_booking_by_token($token)
	{
		$table = $this->get_table_bookings();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SELECT * FROM {$table} WHERE access_token = %s";
		return $this->wpdb->get_row(
			$this->wpdb->prepare($sql, sanitize_text_field($token)) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);
	}

	/**
	 * Get bookings with filters
	 *
	 * @param array $filters Query filters.
	 * @return array
	 */
	public function get_bookings($filters = array())
	{
		$defaults = array(
			'hall_id' => '',
			'slot_id' => '',
			'date_from' => '',
			'date_to' => '',
			'customer_email' => '',
			'orderby' => 'created_at',
			'order' => 'DESC',
			'limit' => -1,
			'offset' => 0,
		);

		$filters = wp_parse_args($filters, $defaults);
		$table_bookings = $this->get_table_bookings();
		$table_booking_dates = $this->get_table_booking_dates();

		// If filtering by slot_id or date range, we need to JOIN with booking_dates
		$needs_join = !empty($filters['slot_id']) || !empty($filters['date_from']) || !empty($filters['date_to']);

		$where = array('1=1');

		if (!empty($filters['hall_id'])) {
			$where[] = $this->wpdb->prepare('b.hall_id = %d', absint($filters['hall_id']));
		}

		if (!empty($filters['status'])) {
			$where[] = $this->wpdb->prepare('b.status = %s', $filters['status']);
		}

		if (!empty($filters['customer_email'])) {
			$where[] = $this->wpdb->prepare('b.customer_email = %s', sanitize_email($filters['customer_email']));
		}

		// Date and slot filters require JOIN
		if (!empty($filters['slot_id'])) {
			$where[] = $this->wpdb->prepare('d.slot_id = %d', absint($filters['slot_id']));
		}

		if (!empty($filters['date_from'])) {
			$where[] = $this->wpdb->prepare('d.booking_date >= %s', $filters['date_from']);
		}

		if (!empty($filters['date_to'])) {
			$where[] = $this->wpdb->prepare('d.booking_date <= %s', $filters['date_to']);
		}

		$where_sql = implode(' AND ', $where);

		// Build ORDER BY - if ordering by booking_date, we need the JOIN
		if ('booking_date' === $filters['orderby']) {
			$needs_join = true;
			// Don't use sanitize_sql_orderby with table aliases, build it manually
			$order_by = 'd.booking_date ' . esc_sql($filters['order']) . ', b.id ' . esc_sql($filters['order']);
		} else {
			// Build orderby manually to preserve table alias
			$orderby_field = 'b.' . esc_sql($filters['orderby']);
			$order_dir = esc_sql($filters['order']);
			$order_by = $orderby_field . ' ' . $order_dir;
		}

		$limit_sql = '';
		if ($filters['limit'] > 0) {
			$limit_sql = $this->wpdb->prepare('LIMIT %d OFFSET %d', absint($filters['limit']), absint($filters['offset']));
		}

		// Search filter
		if (!empty($filters['search'])) {
			$search_term = '%' . $this->wpdb->esc_like($filters['search']) . '%';
			$where[] = $this->wpdb->prepare('(b.customer_name LIKE %s OR b.customer_email LIKE %s)', $search_term, $search_term);
		}

		// Build query with or without JOIN
		if ($needs_join) {
			// Use INNER JOIN when filtering by date/slot since we require matches
			// GROUP BY b.id ensures we get unique bookings even if they match multiple dates
			$sql = "SELECT b.* 
					FROM {$table_bookings} b
					INNER JOIN {$table_booking_dates} d ON b.id = d.booking_id
					WHERE {$where_sql} 
					GROUP BY b.id
					ORDER BY {$order_by} 
					{$limit_sql}";
		} else {
			// Simple query without JOIN
			$sql = "SELECT * FROM {$table_bookings} b WHERE {$where_sql} ORDER BY {$order_by} {$limit_sql}";
		}





		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $this->wpdb->get_results($sql);

		return $results;
	}

	/**
	 * Delete a booking
	 *
	 * @param int $id Booking ID.
	 * @return bool
	 */
	public function delete_booking($id)
	{
		// If multiday booking, delete all associated dates first
		$booking = $this->get_booking($id);
		if ($booking && 'multiday' === $booking->booking_type) {
			$this->delete_booking_dates($id);
		}

		$result = $this->wpdb->delete(
			$this->get_table_bookings(),
			array('id' => absint($id)),
			array('%d')
		);

		return false !== $result;
	}

	// ============================================================
	// Multi-Day Booking Methods (v1.1.0+)
	// ============================================================

	/**
	 * Create multi-day booking with different slots per date
	 *
	 * @param array $booking_data Booking data.
	 * @param array $date_slots Array of date => slot_id pairs.
	 * @return int|false Booking ID or false on failure.
	 */
	public function create_multiday_booking_with_slots($booking_data, $date_slots)
	{
		// Validate date_slots array
		if (empty($date_slots) || !is_array($date_slots)) {
			return false;
		}

		// Check availability for all date/slot combinations
		$hall_id = absint($booking_data['hall_id']);

		foreach ($date_slots as $date => $slot_id) {
			$date = sanitize_text_field($date);
			$slot_id = absint($slot_id);

			if (!$this->is_slot_available($hall_id, $slot_id, $date)) {
				return false;
			}
		}

		// Generate unique token and PIN
		$booking_data['access_token'] = shb_generate_token(32);
		$booking_data['pin'] = $this->generate_unique_pin();
		$booking_data['booking_type'] = 'multiday';

		// Insert main booking record
		$result = $this->wpdb->insert(
			$this->get_table_bookings(),
			array(
				'hall_id' => $hall_id,
				'booking_type' => 'multiday',
				'customer_name' => sanitize_text_field($booking_data['customer_name']),
				'customer_email' => sanitize_email($booking_data['customer_email']),
				'customer_phone' => sanitize_text_field($booking_data['customer_phone'] ?? ''),
				'event_purpose' => sanitize_text_field($booking_data['event_purpose'] ?? ''),
				'attendees_count' => absint($booking_data['attendees_count'] ?? 0),
				'status' => 'pending',
				'access_token' => $booking_data['access_token'],
				'pin' => $booking_data['pin'],
				'admin_notes' => '',
			),
			array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
		);

		if (!$result) {
			return false;
		}

		$booking_id = $this->wpdb->insert_id;

		// Insert all booking dates with their specific slots
		foreach ($date_slots as $date => $slot_id) {
			$date_result = $this->insert_booking_date(
				array(
					'booking_id' => $booking_id,
					'booking_date' => $date,
					'slot_id' => $slot_id,
				)
			);

			// If any date fails, rollback by deleting the booking
			if (!$date_result) {
				$this->delete_booking($booking_id);
				return false;
			}
		}

		return $booking_id;
	}

	/**
	 * Create multi-day booking with multiple dates (same slot for all)
	 *
	 * @param array $booking_data Booking data.
	 * @param array $dates_array Array of dates (Y-m-d format).
	 * @return int|false Booking ID or false on failure.
	 */
	public function create_multiday_booking($booking_data, $dates_array)
	{
		// Validate dates array
		if (empty($dates_array) || !is_array($dates_array)) {
			return false;
		}

		// Check availability for all dates first
		$hall_id = absint($booking_data['hall_id']);
		$slot_id = absint($booking_data['slot_id']);

		$unavailable_dates = $this->check_multiday_availability($hall_id, $slot_id, $dates_array);
		if (!empty($unavailable_dates)) {
			// Some dates are not available
			return false;
		}

		// Generate unique token
		$booking_data['access_token'] = shb_generate_token(32);
		$booking_data['booking_type'] = 'multiday';

		// Generate unique PIN
		$pin = $this->generate_unique_pin();

		// Insert main booking record
		$result = $this->wpdb->insert(
			$this->get_table_bookings(),
			array(
				'hall_id' => $hall_id,
				'booking_type' => 'multiday',
				'customer_name' => sanitize_text_field($booking_data['customer_name']),
				'customer_email' => sanitize_email($booking_data['customer_email']),
				'customer_phone' => sanitize_text_field($booking_data['customer_phone'] ?? ''),
				'event_purpose' => sanitize_text_field($booking_data['event_purpose'] ?? ''),
				'attendees_count' => absint($booking_data['attendees_count'] ?? 0),
				'status' => 'pending',
				'access_token' => $booking_data['access_token'],
				'pin' => $pin,
				'admin_notes' => '',
			),
			array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
		);

		if (!$result) {
			return false;
		}

		$booking_id = $this->wpdb->insert_id;

		// Insert all booking dates
		foreach ($dates_array as $date) {
			$date_result = $this->insert_booking_date(
				array(
					'booking_id' => $booking_id,
					'booking_date' => $date,
					'slot_id' => $slot_id,
				)
			);

			// If any date fails, rollback by deleting the booking
			if (!$date_result) {
				$this->delete_booking($booking_id);
				return false;
			}
		}

		return $booking_id;
	}

	/**
	 * Insert a booking date record
	 *
	 * @param array $data Date data.
	 * @return int|false Insert ID or false on failure.
	 */
	public function insert_booking_date($data)
	{
		$result = $this->wpdb->insert(
			$this->get_table_booking_dates(),
			array(
				'booking_id' => absint($data['booking_id']),
				'booking_date' => sanitize_text_field($data['booking_date']),
				'slot_id' => absint($data['slot_id']),
			),
			array('%d', '%s', '%d')
		);

		return $result ? $this->wpdb->insert_id : false;
	}

	/**
	 * Get all dates for a booking
	 *
	 * @param int $booking_id Booking ID.
	 * @return array Array of date objects.
	 */
	public function get_booking_dates($booking_id)
	{
		$table = $this->get_table_booking_dates();

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SELECT * FROM {$table} WHERE booking_id = %d ORDER BY booking_date ASC";
		$results = $this->wpdb->get_results(
			$this->wpdb->prepare($sql, $booking_id) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		return $results ? $results : array();
	}

	/**
	 * Delete all dates for a booking
	 *
	 * @param int $booking_id Booking ID.
	 * @return bool
	 */
	public function delete_booking_dates($booking_id)
	{
		$result = $this->wpdb->delete(
			$this->get_table_booking_dates(),
			array('booking_id' => absint($booking_id)),
			array('%d')
		);

		return false !== $result;
	}

	/**
	 * Check if multiple dates are available for booking
	 *
	 * @param int   $hall_id Hall ID.
	 * @param int   $slot_id Slot ID.
	 * @param array $dates_array Array of dates to check.
	 * @return array Array of unavailable dates (empty if all available).
	 */
	public function check_multiday_availability($hall_id, $slot_id, $dates_array)
	{
		$unavailable_dates = array();

		foreach ($dates_array as $date) {
			if (!$this->is_slot_available($hall_id, $slot_id, $date)) {
				$unavailable_dates[] = $date;
			}
		}

		return $unavailable_dates;
	}

	/**
	 * Get all booking dates for a specific date range
	 * Used for availability checking
	 *
	 * @param int    $hall_id Hall ID.
	 * @param string $date Date to check.
	 * @return array Array of booking IDs that have this date.
	 */
	public function get_bookings_for_date($hall_id, $date)
	{
		$table_booking_dates = $this->get_table_booking_dates();
		$table_bookings = $this->get_table_bookings();

		// Get multiday bookings for this date
		// IMPORTANT: Use d.slot_id and d.booking_date (from booking_dates table)
		// NOT b.slot_id or b.booking_date (from bookings table)
		// because each date in a multi-day booking can have a different slot
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names must be interpolated
		$sql = "SELECT b.id, b.hall_id, d.slot_id, d.booking_date, b.customer_name, 
			       b.customer_email, b.customer_phone, b.event_purpose, 
			       b.attendees_count, b.status, b.access_token, b.pin, 
			       b.admin_notes, b.created_at, b.booking_type
			FROM {$table_bookings} b
			INNER JOIN {$table_booking_dates} d ON b.id = d.booking_id
			WHERE b.hall_id = %d 
			AND d.booking_date = %s
			AND b.booking_type = 'multiday'
			AND b.status != 'cancelled'";
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names must be interpolated
		$multiday_bookings = $this->wpdb->get_results(
			$this->wpdb->prepare($sql, $hall_id, $date) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		// Get single day bookings for this date
		// Now also using booking_dates table for consistency
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names must be interpolated
		$sql = "SELECT b.id, b.hall_id, d.slot_id, d.booking_date, b.customer_name, 
			       b.customer_email, b.customer_phone, b.event_purpose, 
			       b.attendees_count, b.status, b.access_token, b.pin, 
			       b.admin_notes, b.created_at, b.booking_type
			FROM {$table_bookings} b
			INNER JOIN {$table_booking_dates} d ON b.id = d.booking_id
			WHERE b.hall_id = %d 
			AND d.booking_date = %s
			AND b.booking_type = 'single'
			AND b.status != 'cancelled'";
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names must be interpolated
		$single_bookings = $this->wpdb->get_results(
			$this->wpdb->prepare($sql, $hall_id, $date) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);

		// Merge both arrays
		return array_merge((array) $single_bookings, (array) $multiday_bookings);
	}

	// ============================================================
	// AVAILABILITY & CONFLICTS
	// ============================================================

	/**
	 * Get available slots for a hall on a specific date
	 *
	 * @param int    $hall_id Hall ID.
	 * @param string $date Date in Y-m-d format.
	 * @return array
	 */
	public function get_available_slots($hall_id, $date)
	{
		$hall = $this->get_hall($hall_id);
		if (!$hall) {
			return array();
		}

		// Get all active slots for this hall
		$all_slots = $this->get_slots_by_hall($hall_id, array('is_active' => 1));

		// Check day of week
		$day_of_week = wp_date('w', strtotime($date));

		// Filter slots by enabled days
		$slots = array_filter(
			$all_slots,
			function ($slot) use ($day_of_week) {
				$days_enabled = json_decode($slot->days_enabled, true);
				return is_array($days_enabled) && in_array((int) $day_of_week, $days_enabled, true);
			}
		);

		// Get existing bookings for this date
		// This includes both single-day and multi-day bookings
		$all_bookings = $this->get_bookings_for_date($hall_id, $date);

		// For exact slot blocking: ONLY consider confirmed bookings
		// Pending bookings should NOT block slots (they're just requests)
		$confirmed_bookings = array_filter(
			$all_bookings,
			function ($booking) {
				return 'confirmed' === $booking->status;
			}
		);

		// For full day vs partial blocking: ALSO only consider confirmed bookings
		// Pending bookings should not prevent booking of other slot types
		$booked_slot_ids = array();
		$has_full_day = false;
		$has_partial = false;

		// Check ONLY confirmed bookings for slot blocking
		// This allows multiple pending requests for the same slot
		// Admin can then choose which one to approve
		foreach ($confirmed_bookings as $booking) {
			// Add to booked slots list for exact slot blocking
			$booked_slot_ids[] = $booking->slot_id;

			// Track full day vs partial for cross-blocking logic
			$slot = $this->get_slot($booking->slot_id);
			if ($slot) {
				if ('full_day' === $slot->slot_type) {
					$has_full_day = true;
				} elseif ('partial' === $slot->slot_type) {
					$has_partial = true;
				}
			}
		}

		$available_slots = array();

		foreach ($slots as $slot) {
			// Skip if already booked (only confirmed bookings block exact same slot)
			if (in_array(absint($slot->id), array_map('absint', $booked_slot_ids), true)) {
				continue;
			}

			// FULL DAY vs PARTIAL LOGIC (considers pending + confirmed bookings):

			// If there's a full_day booking (pending or confirmed), no partial slots are available
			if ($has_full_day && 'partial' === $slot->slot_type) {
				continue;
			}

			// If there are partial bookings (pending or confirmed) and this is full_day, it's not available
			if ($has_partial && 'full_day' === $slot->slot_type) {
				continue;
			}

			// Check cleaning buffer if there are any confirmed bookings
			if (!empty($confirmed_bookings) && $hall->cleaning_buffer > 0) {
				$conflicts = $this->check_cleaning_buffer_conflict($slot, $confirmed_bookings, $hall->cleaning_buffer);
				if ($conflicts) {
					continue;
				}
			}

			$available_slots[] = $slot;
		}

		return $available_slots;
	}

	/**
	 * Check if a slot conflicts with existing bookings due to cleaning buffer
	 *
	 * @param object $slot Slot object.
	 * @param array  $bookings Existing bookings.
	 * @param int    $buffer_minutes Cleaning buffer in minutes.
	 * @return bool True if there's a conflict.
	 */
	private function check_cleaning_buffer_conflict($slot, $bookings, $buffer_minutes)
	{
		$slot_start = strtotime($slot->start_time);
		$slot_end = strtotime($slot->end_time);

		foreach ($bookings as $booking) {
			$booked_slot = $this->get_slot($booking->slot_id);
			if (!$booked_slot) {
				continue;
			}

			$booked_start = strtotime($booked_slot->start_time);
			$booked_end = strtotime($booked_slot->end_time) + ($buffer_minutes * 60);

			// Check if times overlap (including buffer)
			if ($slot_start < $booked_end && $slot_end > $booked_start) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a specific slot is available
	 *
	 * @param int    $hall_id Hall ID.
	 * @param int    $slot_id Slot ID.
	 * @param string $date Date in Y-m-d format.
	 * @return bool
	 */
	public function is_slot_available($hall_id, $slot_id, $date)
	{

		$available_slots = $this->get_available_slots($hall_id, $date);

		foreach ($available_slots as $slot) {
			if (absint($slot->id) === absint($slot_id)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if hall has a full day slot
	 *
	 * @param int $hall_id Hall ID.
	 * @param int $exclude_slot_id Slot ID to exclude from check (for editing).
	 * @return bool
	 */
	public function hall_has_full_day_slot($hall_id, $exclude_slot_id = 0)
	{
		$table = $this->get_table_slots();
		$sql = "SELECT COUNT(*) FROM {$table} WHERE hall_id = %d AND slot_type = 'full_day'";

		if ($exclude_slot_id > 0) {
			$sql .= $this->wpdb->prepare(' AND id != %d', $exclude_slot_id);
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name interpolated above, params prepared
		$count = $this->wpdb->get_var($this->wpdb->prepare($sql, $hall_id));

		return $count > 0;
	}

	/**
	 * Check if slot times overlap with existing slots
	 *
	 * Note: Only checks overlap between PARTIAL slots. Full Day slots don't check time overlap.
	 *
	 * @param int    $hall_id Hall ID.
	 * @param string $start_time Start time.
	 * @param string $end_time End time.
	 * @param string $slot_type Slot type ('full_day' or 'partial').
	 * @param int    $exclude_slot_id Slot ID to exclude from check (for editing).
	 * @return bool|object False if no overlap, slot object if overlap found.
	 */
	public function check_slot_time_overlap($hall_id, $start_time, $end_time, $slot_type = 'partial', $exclude_slot_id = 0)
	{
		// Full Day slots don't need time overlap checking
		// They only need to check that there's not another Full Day slot (handled separately)
		if ('full_day' === $slot_type) {
			return false;
		}

		// Get only PARTIAL slots for overlap checking
		$slots = $this->get_slots_by_hall($hall_id, array('slot_type' => 'partial'), $exclude_slot_id);

		$new_start = strtotime($start_time);
		$new_end = strtotime($end_time);

		foreach ($slots as $slot) {
			// Skip the slot being edited
			if ($exclude_slot_id > 0 && (int) $slot->id === (int) $exclude_slot_id) {
				continue;
			}

			$existing_start = strtotime($slot->start_time);
			$existing_end = strtotime($slot->end_time);

			// Check for overlap: (StartA < EndB) and (EndA > StartB)
			if ($new_start < $existing_end && $new_end > $existing_start) {
				return $slot;
			}
		}

		return false;
	}

	/**
	 * Validate slot data before insert/update
	 *
	 * @param array $data Slot data.
	 * @param int   $slot_id Slot ID (for updates).
	 * @return array Array with 'valid' (bool) and 'message' (string).
	 */
	public function validate_slot_data($data, $slot_id = 0)
	{
		$hall_id = isset($data['hall_id']) ? absint($data['hall_id']) : 0;
		$slot_type = isset($data['slot_type']) ? $data['slot_type'] : 'partial';
		$start_time = isset($data['start_time']) ? $data['start_time'] : '';
		$end_time = isset($data['end_time']) ? $data['end_time'] : '';

		// Validate hall exists
		$hall = $this->get_hall($hall_id);
		if (!$hall) {
			return array(
				'valid' => false,
				'message' => __('Invalid hall selected.', 'simple-hall-booking-manager'),
			);
		}

		// Validate time format
		if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $start_time) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $end_time)) {
			return array(
				'valid' => false,
				'message' => __('Invalid time format.', 'simple-hall-booking-manager'),
			);
		}

		// Validate end time is after start time
		if (strtotime($end_time) <= strtotime($start_time)) {
			return array(
				'valid' => false,
				'message' => __('End time must be after start time.', 'simple-hall-booking-manager'),
			);
		}

		// Check if trying to add a second full_day slot
		if ('full_day' === $slot_type) {
			if ($this->hall_has_full_day_slot($hall_id, $slot_id)) {
				return array(
					'valid' => false,
					'message' => __('This hall already has a Full Day slot. Only one Full Day slot is allowed per hall.', 'simple-hall-booking-manager'),
				);
			}
		}

		// Check for time overlaps (only for partial slots)
		$overlap = $this->check_slot_time_overlap($hall_id, $start_time, $end_time, $slot_type, $slot_id);
		if ($overlap) {
			return array(
				'valid' => false,
				/* translators: %s: overlapping slot label */
				'message' => sprintf(
					/* translators: 1: overlapping slot label, 2: start time, 3: end time */
					__('Time slot overlaps with existing slot: %1$s (%2$s - %3$s)', 'simple-hall-booking-manager'),
					$overlap->label,
					wp_date('g:i A', strtotime($overlap->start_time)),
					wp_date('g:i A', strtotime($overlap->end_time))
				),
			);
		}

		return array(
			'valid' => true,
			'message' => '',
		);
	}

	/**
	 * Find conflicting bookings for a given booking
	 *
	 * @param int $booking_id Booking ID to check.
	 * @param bool $exclude_cancelled Whether to exclude cancelled bookings.
	 * @return array Array of conflicting booking IDs.
	 */
	public function get_conflicting_bookings($booking_id, $exclude_cancelled = true)
	{
		$booking = $this->get_booking($booking_id);
		if (!$booking) {
			return array();
		}

		$conflicts = array();

		// Get booking dates (works for both single and multi-day bookings now)
		$booking_dates_data = $this->get_booking_dates($booking_id);

		if (empty($booking_dates_data)) {
			return array();
		}

		foreach ($booking_dates_data as $bd) {
			$date_conflicts = $this->find_date_slot_conflicts(
				$booking->hall_id,
				$bd->slot_id,
				$bd->booking_date,
				$booking_id,
				$exclude_cancelled
			);
			$conflicts = array_merge($conflicts, $date_conflicts);
		}

		return array_unique($conflicts);
	}

	/**
	 * Find bookings that conflict with a specific date/slot combination
	 *
	 * @param int    $hall_id Hall ID.
	 * @param int    $slot_id Slot ID.
	 * @param string $date Booking date.
	 * @param int    $exclude_booking_id Booking ID to exclude from results.
	 * @param bool   $exclude_cancelled Whether to exclude cancelled bookings.
	 * @return array Array of conflicting booking IDs.
	 */
	private function find_date_slot_conflicts($hall_id, $slot_id, $date, $exclude_booking_id = 0, $exclude_cancelled = true)
	{
		$slot = $this->get_slot($slot_id);
		if (!$slot) {
			return array();
		}

		$hall = $this->get_hall($hall_id);
		$cleaning_buffer = $hall ? $hall->cleaning_buffer : 0;

		// Get all bookings for this date
		$all_bookings = $this->get_bookings_for_date($hall_id, $date);

		$conflicts = array();

		foreach ($all_bookings as $other_booking) {
			// Skip self
			if (absint($other_booking->id) === absint($exclude_booking_id)) {
				continue;
			}

			// Skip cancelled if requested
			if ($exclude_cancelled && 'cancelled' === $other_booking->status) {
				continue;
			}

			$other_slot = $this->get_slot($other_booking->slot_id);
			if (!$other_slot) {
				continue;
			}

			// Check for conflicts
			$has_conflict = false;

			// Full day conflicts with anything
			if ('full_day' === $slot->slot_type || 'full_day' === $other_slot->slot_type) {
				$has_conflict = true;
			} else {
				// Check time overlap with cleaning buffer
				$slot_start = strtotime($slot->start_time);
				$slot_end = strtotime($slot->end_time);
				$other_slot_start = strtotime($other_slot->start_time);
				$other_slot_end = strtotime($other_slot->end_time) + ($cleaning_buffer * 60);

				if ($slot_start < $other_slot_end && $slot_end > $other_slot_start) {
					$has_conflict = true;
				}
			}

			if ($has_conflict) {
				$conflicts[] = $other_booking->id;
			}
		}

		return $conflicts;
	}

	/**
	 * Auto-cancel conflicting bookings when approving a booking
	 *
	 * @param int $approved_booking_id The booking ID being approved.
	 * @return array Array of cancelled booking IDs.
	 */
	public function auto_cancel_conflicts($approved_booking_id)
	{
		// Find all conflicts (excluding cancelled)
		$conflicts = $this->get_conflicting_bookings($approved_booking_id, true);

		$cancelled = array();

		foreach ($conflicts as $conflict_id) {
			$conflict = $this->get_booking($conflict_id);

			// Only auto-cancel pending bookings, not confirmed ones
			if ($conflict && 'pending' === $conflict->status) {
				$result = $this->update_booking(
					$conflict_id,
					array(
						'status' => 'cancelled',
						'admin_notes' => __('Auto-cancelled due to conflict with confirmed booking #', 'simple-hall-booking-manager') . $approved_booking_id,
					)
				);

				if ($result) {
					$cancelled[] = $conflict_id;

					// Send cancellation email
					if (class_exists('SHB_Emails')) {
						$emails = new SHB_Emails();
						$emails->send_guest_cancelled($conflict_id);
					}
				}
			}
		}

		return $cancelled;
	}

	/**
	 * Check if a booking has conflicts
	 *
	 * @param int $booking_id Booking ID.
	 * @return bool True if has conflicts.
	 */
	public function has_conflicts($booking_id)
	{
		$conflicts = $this->get_conflicting_bookings($booking_id, true);
		return !empty($conflicts);
	}

	/**
	 * Generate a unique 6-character PIN
	 * Format: 2 uppercase letters + 4 digits (e.g., AA1111)
	 *
	 * @return string 6-character PIN.
	 */
	public function generate_unique_pin()
	{
		$max_attempts = 100;
		$attempt = 0;

		do {
			$pin = $this->generate_pin();
			$attempt++;

			// Check if PIN already exists
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
			$sql = "SELECT COUNT(*) FROM {$this->get_table_bookings()} WHERE pin = %s";
			$exists = $this->wpdb->get_var(
				$this->wpdb->prepare($sql, $pin) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			);

			if (!$exists) {
				return $pin;
			}
		} while ($attempt < $max_attempts);

		// If we couldn't generate a unique PIN after max attempts,
		// throw an exception or add a timestamp suffix
		return $this->generate_pin();
	}

	/**
	 * Generate a PIN in format AA1111 (2 letters + 4 digits)
	 *
	 * @return string 6-character PIN.
	 */
	private function generate_pin()
	{
		// Generate 2 random uppercase letters
		$letters = '';
		for ($i = 0; $i < 2; $i++) {
			$letters .= chr(wp_rand(65, 90)); // A-Z ASCII codes
		}

		// Generate 4 random digits
		$digits = str_pad(wp_rand(0, 9999), 4, '0', STR_PAD_LEFT);

		return $letters . $digits;
	}

	/**
	 * Get booking by PIN
	 *
	 * @param string $pin The booking PIN.
	 * @return object|null Booking object or null if not found.
	 */
	public function get_booking_by_pin($pin)
	{
		$table = $this->get_table_bookings();

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name must be interpolated
		$sql = "SELECT * FROM {$table} WHERE pin = %s";
		return $this->wpdb->get_row(
			$this->wpdb->prepare($sql, strtoupper(sanitize_text_field($pin))) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		);
	}
}
