# Plugin Check Report

**Plugin:** Simple Hall Booking Manager
**Generated at:** 2026-01-16 19:50:03


## `includes/class-shb-db.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 249 | 33 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;get_results($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t&quot;SHOW COLUMNS FROM {$table_bookings} LIKE %s&quot;,\n\t\t\t\t&#039;booking_type&#039;\n\t\t\t))\n$table_bookings assigned unsafely at line 246:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 250 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 250 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 250 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 251 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;SHOW COLUMNS FROM {$table_bookings} LIKE %s&quot; |  |
| 258 | 17 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;query(&quot;ALTER TABLE {$table_bookings} \n\t\t\t\tADD COLUMN booking_type enum(&#039;single&#039;,&#039;multiday&#039;) NOT NULL DEFAULT &#039;single&#039; \n\t\t\t\tAFTER slot_id&quot;)\n$table_bookings assigned unsafely at line 246:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 259 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;ALTER TABLE {$table_bookings} \n |  |
| 265 | 17 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;query(&quot;ALTER TABLE {$table_bookings} \n\t\t\t\tMODIFY COLUMN booking_date date DEFAULT NULL \n\t\t\t\tCOMMENT &#039;Used for single bookings only&#039;&quot;)\n$table_bookings assigned unsafely at line 246:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 266 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;ALTER TABLE {$table_bookings} \n |  |
| 282 | 33 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;get_results($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t&quot;SHOW COLUMNS FROM {$table_bookings} LIKE %s&quot;,\n\t\t\t\t&#039;pin&#039;\n\t\t\t))\n$table_bookings assigned unsafely at line 279:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 283 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 283 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 283 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 284 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;SHOW COLUMNS FROM {$table_bookings} LIKE %s&quot; |  |
| 291 | 17 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;query(&quot;ALTER TABLE {$table_bookings} \n\t\t\t\tADD COLUMN pin varchar(6) NOT NULL DEFAULT &#039;&#039; COMMENT &#039;Format: AA1111 (2 letters + 4 digits)&#039; \n\t\t\t\tAFTER access_token,\n\t\t\t\tADD UNIQUE KEY pin (pin)&quot;)\n$table_bookings assigned unsafely at line 279:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 292 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;ALTER TABLE {$table_bookings} \n |  |
| 299 | 29 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;get_results(&quot;SELECT id FROM {$table_bookings} WHERE pin = &#039;&#039;&quot;)\n$table_bookings assigned unsafely at line 279:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 300 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;SELECT id FROM {$table_bookings} WHERE pin = &#039;&#039;&quot; |  |
| 327 | 39 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;get_results($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t&quot;SHOW COLUMNS FROM {$table_bookings} LIKE %s&quot;,\n\t\t\t\t&#039;booking_date&#039;\n\t\t\t))\n$table_bookings assigned unsafely at line 323:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 328 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 328 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 328 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 329 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;SHOW COLUMNS FROM {$table_bookings} LIKE %s&quot; |  |
| 336 | 36 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;get_results(&quot;SELECT id, booking_date, slot_id \n\t\t\t\tFROM {$table_bookings} \n\t\t\t\tWHERE booking_type = &#039;single&#039; \n\t\t\t\tAND booking_date IS NOT NULL&quot;)\n$table_bookings assigned unsafely at line 323:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 338 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at \t\t\t\tFROM {$table_bookings} \n |  |
| 345 | 30 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_booking_dates used in $wpdb-&gt;get_var($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t\t\t&quot;SELECT COUNT(*) FROM {$table_booking_dates} WHERE booking_id = %d&quot;,\n\t\t\t\t\t\t$booking-&gt;id\n\t\t\t\t\t))\n$table_booking_dates assigned unsafely at line 324:\n $table_booking_dates = $this-&gt;get_table_booking_dates() |  |
| 346 | 6 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 346 | 13 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 346 | 19 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 347 | 7 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_booking_dates} at &quot;SELECT COUNT(*) FROM {$table_booking_dates} WHERE booking_id = %d&quot; |  |
| 348 | 7 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $booking |  |
| 348 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found id |  |
| 367 | 17 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;query(&quot;ALTER TABLE {$table_bookings} \n\t\t\t\tDROP INDEX booking_date,\n\t\t\t\tDROP COLUMN booking_date&quot;)\n$table_bookings assigned unsafely at line 323:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 368 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;ALTER TABLE {$table_bookings} \n |  |
| 375 | 34 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;get_results($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t&quot;SHOW COLUMNS FROM {$table_bookings} LIKE %s&quot;,\n\t\t\t\t&#039;slot_id&#039;\n\t\t\t))\n$table_bookings assigned unsafely at line 323:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 376 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 376 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 376 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 377 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;SHOW COLUMNS FROM {$table_bookings} LIKE %s&quot; |  |
| 384 | 17 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;query(&quot;ALTER TABLE {$table_bookings} \n\t\t\t\tDROP INDEX slot_id,\n\t\t\t\tDROP COLUMN slot_id&quot;)\n$table_bookings assigned unsafely at line 323:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 385 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;ALTER TABLE {$table_bookings} \n |  |
| 490 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($this-&gt;wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, absint($id)))\n$table assigned unsafely at line 489:\n $table = $this-&gt;get_table_halls() |  |
| 491 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 491 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 491 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 491 | 25 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |
| 529 | 23 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $sql used in $wpdb->get_results($sql)\n$sql assigned unsafely at line 527:\n $sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$order_by} {$limit_sql}"\n$table assigned unsafely at line 512:\n $table = $this->get_table_halls()\n$limit_sql assigned unsafely at line 521:\n $limit_sql = ''\n$args['limit'] used without escaping.\n$args['offset'] used without escaping. |  |
| 529 | 35 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $sql |  |
| 587 | 40 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $sql |  |
| 588 | 27 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $prepared_sql used in $wpdb->get_var($prepared_sql)\n$prepared_sql assigned unsafely at line 587:\n $prepared_sql = $this->wpdb->prepare($sql, $params)\n$sql assigned unsafely at line 583:\n $sql .= " AND id != %d"\n$params[] used without escaping. |  |
| 588 | 35 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $prepared_sql |  |
| 739 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($this-&gt;wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, absint($id)))\n$table assigned unsafely at line 738:\n $table = $this-&gt;get_table_slots() |  |
| 740 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 740 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 740 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 740 | 25 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |
| 773 | 23 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $sql used in $wpdb->get_results($sql)\n$sql assigned unsafely at line 771:\n $sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY sort_order ASC, start_time ASC"\n$table assigned unsafely at line 759:\n $table = $this->get_table_slots() |  |
| 773 | 35 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $sql |  |
| 934 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($this-&gt;wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, absint($id)))\n$table assigned unsafely at line 933:\n $table = $this-&gt;get_table_bookings() |  |
| 935 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 935 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 935 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 935 | 25 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |
| 948 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($this-&gt;wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE access_token = %s&quot;, sanitize_text_field($token)))\n$table assigned unsafely at line 947:\n $table = $this-&gt;get_table_bookings() |  |
| 949 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 949 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 949 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 949 | 25 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE access_token = %s&quot; |  |
| 949 | 75 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found sanitize_text_field |  |
| 949 | 95 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $token |  |
| 1050 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1053 | 27 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $sql used in $wpdb->get_results($sql)\n$sql assigned unsafely at line 1045:\n $sql = "SELECT * FROM {$table_bookings} b WHERE {$where_sql} ORDER BY {$order_by} {$limit_sql}"\n$table_bookings assigned unsafely at line 975:\n $table_bookings = $this->get_table_bookings()\n$limit_sql assigned unsafely at line 1022:\n $limit_sql = ''\n$filters['limit'] used without escaping.\n$filters['offset'] used without escaping. |  |
| 1053 | 39 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $sql |  |
| 1057 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1276 | 27 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_results($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t&quot;SELECT * FROM {$table} WHERE booking_id = %d ORDER BY booking_date ASC&quot;,\n\t\t\t\t$booking_id\n\t\t\t))\n$table assigned unsafely at line 1274:\n $table = $this-&gt;get_table_booking_dates() |  |
| 1277 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 1277 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 1277 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 1278 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE booking_id = %d ORDER BY booking_date ASC&quot; |  |
| 1279 | 5 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $booking_id |  |
| 1341 | 37 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;get_results($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t&quot;SELECT b.id, b.hall_id, d.slot_id, d.booking_date, b.customer_name, \n\t\t\t b.customer_email, b.customer_phone, b.event_purpose, \n\t\t\t b.attendees_count, b.status, b.access_token, b.pin, \n\t\t\t b.admin_notes, b.created_at, b.booking_type\n\t\t\tFROM {$table_bookings} b\n\t\t\tINNER JOIN {$table_booking_dates} d ON b.id = d.booking_id\n\t\t\tWHERE b.hall_id = %d \n\t\t\tAND d.booking_date = %s\n\t\t\tAND b.booking_type = &#039;multiday&#039;\n\t\t\tAND b.status != &#039;cancelled&#039;&quot;,\n\t\t\t\t$hall_id,\n\t\t\t\t$date\n\t\t\t))\n$table_bookings assigned unsafely at line 1335:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 1342 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 1342 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 1342 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 1347 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at \t\t\tFROM {$table_bookings} b\n |  |
| 1348 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_booking_dates} at \t\t\tINNER JOIN {$table_booking_dates} d ON b.id = d.booking_id\n |  |
| 1353 | 5 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $hall_id |  |
| 1354 | 5 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $date |  |
| 1360 | 35 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;get_results($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t&quot;SELECT b.id, b.hall_id, d.slot_id, d.booking_date, b.customer_name, \n\t\t\t b.customer_email, b.customer_phone, b.event_purpose, \n\t\t\t b.attendees_count, b.status, b.access_token, b.pin, \n\t\t\t b.admin_notes, b.created_at, b.booking_type\n\t\t\tFROM {$table_bookings} b\n\t\t\tINNER JOIN {$table_booking_dates} d ON b.id = d.booking_id\n\t\t\tWHERE b.hall_id = %d \n\t\t\tAND d.booking_date = %s\n\t\t\tAND b.booking_type = &#039;single&#039;\n\t\t\tAND b.status != &#039;cancelled&#039;&quot;,\n\t\t\t\t$hall_id,\n\t\t\t\t$date\n\t\t\t))\n$table_bookings assigned unsafely at line 1335:\n $table_bookings = $this-&gt;get_table_bookings() |  |
| 1361 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 1361 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 1361 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 1366 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at \t\t\tFROM {$table_bookings} b\n |  |
| 1367 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_booking_dates} at \t\t\tINNER JOIN {$table_booking_dates} d ON b.id = d.booking_id\n |  |
| 1372 | 5 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $hall_id |  |
| 1373 | 5 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $date |  |
| 1403 | 18 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 1527 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1533 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1534 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1542 | 6 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1549 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 1571 | 25 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $sql used in $wpdb->get_var($this->wpdb->prepare($sql, $hall_id))\n$sql assigned unsafely at line 1565:\n $sql = "SELECT COUNT(*) FROM {$table} WHERE hall_id = %d AND slot_type = 'full_day'"\n$table assigned unsafely at line 1564:\n $table = $this->get_table_slots() |  |
| 1571 | 33 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 1571 | 40 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 1571 | 46 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 1571 | 54 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $sql |  |
| 1571 | 60 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $hall_id |  |
| 1676 | 21 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to __() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 1676 | 24 | ERROR | WordPress.WP.I18n.UnorderedPlaceholdersText | Multiple placeholders in translatable strings should be ordered. Expected "%1$s, %2$s, %3$s", but got "%s, %s, %s" in 'Time slot overlaps with existing slot: %s (%s - %s)'. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#variables) |
| 1678 | 6 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 1679 | 6 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 1863 | 27 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $this-&gt;get_table_bookings() used in $wpdb-&gt;get_var($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t\t&quot;SELECT COUNT(*) FROM {$this-&gt;get_table_bookings()} WHERE pin = %s&quot;,\n\t\t\t\t\t$pin\n\t\t\t\t)) |  |
| 1864 | 5 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 1864 | 12 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 1864 | 18 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 1865 | 6 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$this-&gt;get_table_bookings()} at &quot;SELECT COUNT(*) FROM {$this-&gt;get_table_bookings()} WHERE pin = %s&quot; |  |
| 1866 | 6 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $pin |  |
| 1890 | 20 | ERROR | WordPress.WP.AlternativeFunctions.rand_rand | rand() is discouraged. Use the far less predictable wp_rand() instead. |  |
| 1894 | 21 | ERROR | WordPress.WP.AlternativeFunctions.rand_rand | rand() is discouraged. Use the far less predictable wp_rand() instead. |  |
| 1909 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($this-&gt;wpdb-&gt;prepare(\n\t\t\t\t&quot;SELECT * FROM {$table} WHERE pin = %s&quot;,\n\t\t\t\tstrtoupper(sanitize_text_field($pin))\n\t\t\t))\n$table assigned unsafely at line 1907:\n $table = $this-&gt;get_table_bookings() |  |
| 1910 | 4 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $this |  |
| 1910 | 11 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found wpdb |  |
| 1910 | 17 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found prepare |  |
| 1911 | 5 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE pin = %s&quot; |  |
| 1912 | 5 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found strtoupper |  |
| 1912 | 16 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found sanitize_text_field |  |
| 1912 | 36 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $pin |  |

## `includes/class-shb-emails.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 111 | 56 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 111 | 110 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 342 | 50 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 342 | 104 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 424 | 33 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to esc_html__() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 446 | 63 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 446 | 63 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '_n'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 452 | 54 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 452 | 54 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 463 | 62 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 463 | 122 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 483 | 50 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 483 | 104 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 553 | 33 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to esc_html__() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 579 | 50 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 579 | 104 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 643 | 33 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to esc_html__() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 669 | 50 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 669 | 104 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |

## `admin/views/view-booking-edit.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 14 | 22 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 14 | 46 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 127 | 44 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 127 | 102 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 152 | 57 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 152 | 57 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '_n'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 172 | 37 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 176 | 56 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 176 | 119 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 219 | 78 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |

## `admin/views/view-bookings-list.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 17 | 23 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 17 | 55 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 21 | 24 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 21 | 70 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 21 | 70 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;filter_status&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 22 | 25 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 22 | 56 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 23 | 22 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 23 | 56 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 23 | 56 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;s&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 67 | 18 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 67 | 58 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 67 | 58 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;message&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 100 | 138 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$cancelled_count'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 229 | 66 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 252 | 72 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 274 | 108 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$conflict_count'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 302 | 32 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '_n'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 302 | 107 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'number_format_i18n'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 319 | 66 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$page_links'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `public/partials/user-booking.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 19 | 87 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;shb_pin_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 19 | 87 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;shb_pin_nonce&#039;] |  |
| 20 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;booking_pin&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 22 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;pin&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 26 | 54 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;token&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 99 | 60 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;shb_cancel_nonce&#039;]. Check that the array index exists before using it. |  |
| 99 | 60 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;shb_cancel_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 99 | 60 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;shb_cancel_nonce&#039;] |  |
| 206 | 42 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to _n() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |
| 206 | 42 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '_n'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 225 | 59 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 225 | 116 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 241 | 68 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 243 | 75 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 250 | 35 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 250 | 89 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 266 | 69 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |

## `simple-hall-booking-manager.zip`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | compressed_files | Compressed files are not permitted. |  |

## `simple-hall-booking-manager.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | plugin_header_invalid_author_uri_domain | The "Author URI" header in the plugin file is not valid. Discouraged domain "example.com" found. This is the author's website or profile on another website. | [Docs](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/#header-fields) |

## `admin/views/view-settings.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 44 | 57 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;tab&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 59 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;from_name&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 62 | 47 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;from_email&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 65 | 48 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;admin_email&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 71 | 68 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;admin_notification_subject&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 74 | 58 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;admin_notification_body&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 78 | 63 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;guest_pending_subject&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 81 | 53 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;guest_pending_body&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 85 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;guest_confirmed_subject&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 88 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;guest_confirmed_body&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 92 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;guest_cancelled_subject&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 95 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;guest_cancelled_body&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 114 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;date_format&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 117 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;time_format&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 126 | 62 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;recaptcha_site_key&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 129 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;recaptcha_secret_key&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 243 | 47 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$confirmation_page'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 244 | 55 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 449 | 100 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$content'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `admin/views/view-hall-edit.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 14 | 18 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 14 | 40 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 19 | 12 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '__'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 34 | 18 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 34 | 58 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 34 | 58 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;message&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 35 | 24 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 35 | 80 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 35 | 80 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;error_message&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 93 | 30 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 95 | 30 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |

## `admin/views/partials/_slot-form.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 90 | 127 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$checked'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `includes/class-shb-plugin.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 141 | 3 | ERROR | PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound | load_plugin_textdomain() has been discouraged since WordPress version 4.6. When your plugin is hosted on WordPress.org, you no longer need to manually include this function call for translations under your plugin slug. WordPress will automatically load the translations for you as needed. | [Docs](https://make.wordpress.org/core/2016/07/06/i18n-improvements-in-4-6/) |

## `includes/class-shb-ajax.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 73 | 20 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_SERVER[&#039;REMOTE_ADDR&#039;]. Check that the array index exists before using it. |  |
| 73 | 20 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_SERVER[&#039;REMOTE_ADDR&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 73 | 20 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_SERVER[&#039;REMOTE_ADDR&#039;] |  |
| 80 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 89 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 89 | 56 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_print_r | print_r() found. Debug code should not normally be used in production. |  |
| 95 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 101 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 114 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 114 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 124 | 55 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 125 | 99 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;dates&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 180 | 21 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 181 | 19 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 230 | 22 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 231 | 20 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 254 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 254 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 265 | 99 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;dates&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 333 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 334 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 334 | 34 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_print_r | print_r() found. Debug code should not normally be used in production. |  |
| 334 | 42 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 338 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 338 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 340 | 5 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 350 | 77 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;recaptcha_token&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 353 | 5 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 365 | 71 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;booking_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 366 | 81 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;date_slots&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 366 | 81 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;date_slots&#039;] |  |
| 368 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;customer_name&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 369 | 70 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;customer_email&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 370 | 75 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;customer_phone&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 371 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;event_purpose&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 383 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 385 | 5 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 417 | 5 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 504 | 6 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 509 | 7 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 516 | 9 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 524 | 7 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 535 | 7 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 542 | 9 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 553 | 5 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 554 | 5 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 554 | 36 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_print_r | print_r() found. Debug code should not normally be used in production. |  |
| 559 | 6 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 571 | 6 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 582 | 6 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 612 | 16 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 722 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 722 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 732 | 69 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;slot_type&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 789 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 789 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;nonce&#039;] |  |
| 799 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;start_time&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 800 | 63 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;end_time&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |

## `admin/views/view-calendar.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 15 | 26 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 15 | 53 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 20 | 15 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 21 | 13 | ERROR | WordPress.DateTime.RestrictedFunctions.date_date | date() is affected by runtime timezone changes which can cause date/time to be incorrectly displayed. Use gmdate() instead. |  |
| 72 | 1 | ERROR | WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet | Stylesheets must be registered/enqueued via wp_enqueue_style() |  |
| 72 | 1 | ERROR | PluginCheck.CodeAnalysis.Offloading.OffloadedContent | Offloading images, js, css, and other scripts to your servers or any remote service is disallowed. |  |
| 74 | 1 | ERROR | WordPress.WP.EnqueuedResources.NonEnqueuedScript | Scripts must be registered/enqueued via wp_enqueue_script() |  |
| 74 | 1 | ERROR | PluginCheck.CodeAnalysis.Offloading.OffloadedContent | Offloading images, js, css, and other scripts to your servers or any remote service is disallowed. |  |

## `readme.txt`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | ERROR | outdated_tested_upto_header | Tested up to: 6.7 < 6.9. The "Tested up to" value in your plugin is not set to the current version of WordPress. This means your plugin will not show up in searches, as we require plugins to be compatible and documented as tested up to the most recent version of WordPress. | [Docs](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/#readme-header-information) |
| 0 | 0 | ERROR | readme_restricted_contributors | The "Contributors" header in the readme file contains restricted username(s). Found: "yourusername" | [Docs](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/#readme-header-information) |

## `.gitignore`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | hidden_files | Hidden files are not permitted. |  |

## `plugin.md`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | unexpected_markdown_file | Unexpected markdown file "plugin.md" detected in plugin root. Only specific markdown files are expected in production plugins. |  |

## `includes/functions-helpers.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 186 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |
| 186 | 24 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_print_r | print_r() found. Debug code should not normally be used in production. |  |
| 188 | 4 | WARNING | WordPress.PHP.DevelopmentFunctions.error_log_error_log | error_log() found. Debug code should not normally be used in production. |  |

## `includes/class-shb-admin.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 256 | 14 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 256 | 49 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 256 | 49 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;page&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 256 | 49 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;page&#039;] |  |
| 261 | 13 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 265 | 13 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 265 | 51 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 265 | 76 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 270 | 13 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 274 | 13 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 274 | 51 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 274 | 76 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 279 | 13 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 283 | 13 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 283 | 54 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 283 | 79 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 301 | 60 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;title&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 302 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;description&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 304 | 62 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;status&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 329 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_GET[&#039;id&#039;]. Check that the array index exists before using it. |  |
| 335 | 21 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_GET[&#039;id&#039;]. Check that the array index exists before using it. |  |
| 363 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;start_time&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 364 | 63 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;end_time&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 376 | 68 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;slot_type&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 377 | 60 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;label&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 470 | 51 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_GET[&#039;id&#039;]. Check that the array index exists before using it. |  |
| 476 | 21 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_GET[&#039;id&#039;]. Check that the array index exists before using it. |  |
| 498 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;old_status&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 499 | 63 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;status&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 503 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;admin_notes&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 541 | 54 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_GET[&#039;id&#039;]. Check that the array index exists before using it. |  |
| 547 | 24 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_GET[&#039;id&#039;]. Check that the array index exists before using it. |  |
| 561 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 561 | 58 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 561 | 58 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;action&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 577 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 577 | 58 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 577 | 58 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;action&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |

## `admin/views/view-hall-create.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 25 | 18 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 25 | 58 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 25 | 58 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;message&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 26 | 24 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 26 | 80 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 26 | 80 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;error_message&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |

## `admin/views/view-halls-list.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 17 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 17 | 61 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 17 | 61 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;message&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 35 | 26 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 35 | 85 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 35 | 85 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;error_message&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |

## `uninstall.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 29 | 1 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 29 | 1 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 29 | 8 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_bookings used in $wpdb-&gt;query(&quot;DROP TABLE IF EXISTS {$table_bookings}&quot;)\n$table_bookings assigned unsafely at line 27:\n $table_bookings = $wpdb-&gt;prefix . &#039;shb_bookings&#039; |  |
| 29 | 15 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_bookings} at &quot;DROP TABLE IF EXISTS {$table_bookings}&quot; |  |
| 29 | 15 | WARNING | WordPress.DB.DirectDatabaseQuery.SchemaChange | Attempting a database schema change is discouraged. |  |
| 30 | 1 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 30 | 1 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 30 | 8 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_slots used in $wpdb-&gt;query(&quot;DROP TABLE IF EXISTS {$table_slots}&quot;)\n$table_slots assigned unsafely at line 26:\n $table_slots = $wpdb-&gt;prefix . &#039;shb_slots&#039;\n$table_bookings assigned unsafely at line 27:\n $table_bookings = $wpdb-&gt;prefix . &#039;shb_bookings&#039; |  |
| 30 | 15 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_slots} at &quot;DROP TABLE IF EXISTS {$table_slots}&quot; |  |
| 30 | 15 | WARNING | WordPress.DB.DirectDatabaseQuery.SchemaChange | Attempting a database schema change is discouraged. |  |
| 31 | 1 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 31 | 1 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 31 | 8 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_halls used in $wpdb-&gt;query(&quot;DROP TABLE IF EXISTS {$table_halls}&quot;)\n$table_halls assigned unsafely at line 25:\n $table_halls = $wpdb-&gt;prefix . &#039;shb_halls&#039;\n$table_slots assigned unsafely at line 26:\n $table_slots = $wpdb-&gt;prefix . &#039;shb_slots&#039;\n$table_bookings assigned unsafely at line 27:\n $table_bookings = $wpdb-&gt;prefix . &#039;shb_bookings&#039; |  |
| 31 | 15 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_halls} at &quot;DROP TABLE IF EXISTS {$table_halls}&quot; |  |
| 31 | 15 | WARNING | WordPress.DB.DirectDatabaseQuery.SchemaChange | Attempting a database schema change is discouraged. |  |

## `public/partials/booking-form.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 15 | 24 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 15 | 51 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `includes/class-shb-frontend.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 54 | 17 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_script(). This means new versions of the script may not always be loaded due to browser caching. |  |
