<?php
class PegasaasDB {
	var $db_queries = 0;
	var $when_connected = 0;

	function __construct() {
		global $pegasaas;
		$this->when_connected = $pegasaas->microtime();
	}
	
	function assert_structure() {	
		global $wpdb;
		$this->assert_table_pegasaas_api_request();
		$this->assert_table_pegasaas_performance_scan();
		$this->assert_table_pegasaas_page_config();
		$this->assert_table_pegasaas_page_cache();
		$this->assert_table_pegasaas_static_asset();
		$this->assert_table_pegasaas_queued_task();
		$this->assert_table_pegasaas_semaphore();
	}
	
	function assert_table_pegasaas_api_request() {
		global $wpdb; 
		
		$table_name = $wpdb->prefix."pegasaas_api_request";
		$results = $wpdb->query("SELECT table_name FROM information_schema.tables WHERE table_name='{$table_name}' AND table_schema='".DB_NAME."'");
		
		if ($results == 0) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
  						request_id bigint(22) NOT NULL AUTO_INCREMENT,
						time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  						request_type varchar(50) DEFAULT '' NOT NULL,
						resource_id varchar(255) DEFAULT '' NOT NULL,
						nonce varchar(255) DEFAULT '' NOT NULL,
						advisory varchar(255) DEFAULT '' NOT NULL,
  						PRIMARY KEY  (request_id)
						) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		} else {
			$results = $wpdb->query("SHOW COLUMNS FROM `{$table_name}` LIKE 'nonce'");
			if ($results == 0) {
				$wpdb->query("ALTER TABLE `{$table_name}` ADD `nonce` varchar(255) DEFAULT '' NOT NULL");
			}
			$results = $wpdb->query("SHOW COLUMNS FROM `{$table_name}` LIKE 'advisory'");
			if ($results == 0) {
				
				$wpdb->query("ALTER TABLE `{$table_name}` ADD `advisory` varchar(255) DEFAULT '' NOT NULL");
			}			
			
		}
	}

	
	function assert_table_pegasaas_queued_task() {
		global $wpdb;
		
		$table_name = $wpdb->prefix."pegasaas_queued_task";
		$results = $wpdb->query("SELECT table_name FROM information_schema.tables WHERE table_name='{$table_name}' AND table_schema='".DB_NAME."'");
	
		if ($results == 0) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
  						task_id bigint(22) NOT NULL AUTO_INCREMENT,
						time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  						task_type varchar(50) DEFAULT '' NOT NULL,
						resource_id varchar(255) DEFAULT '' NOT NULL,
  						PRIMARY KEY  (task_id)
						) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		} 
	}	
	
	function assert_table_pegasaas_performance_scan() {
		global $wpdb;
		
		$table_name = $wpdb->prefix."pegasaas_performance_scan";
		$results = $wpdb->query("SELECT table_name FROM information_schema.tables WHERE table_name='{$table_name}' AND table_schema='".DB_NAME."'");
	
		if ($results == 0) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
  						scan_id bigint(22) NOT NULL AUTO_INCREMENT,
						time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  						scan_type varchar(50) DEFAULT '' NOT NULL,
						resource_id varchar(255) DEFAULT '' NOT NULL,
						data longtext NOT NULL,
  						PRIMARY KEY  (scan_id)
						) $charset_collate;";
			

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}
	
	
	function assert_table_pegasaas_static_asset() {
		global $wpdb;
		
		$table_name = $wpdb->prefix."pegasaas_static_asset";
		$results = $wpdb->query("SELECT table_name FROM information_schema.tables WHERE table_name='{$table_name}' AND table_schema='".DB_NAME."'");
	
		if ($results == 0) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
  						asset_id bigint(22) NOT NULL AUTO_INCREMENT,
						when_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  						asset_type varchar(50) DEFAULT '' NOT NULL,
						original_file_name varchar(255) DEFAULT '' NOT NULL,
						optimized_file_name varchar(255) DEFAULT '' NOT NULL,
						original_file_size int(11) DEFAULT 0 NOT NULL,
						optimized_file_size int(11) DEFAULT 0 NOT NULL,
						status smallint(2) DEFAULT 0 NOT NULL,
  						PRIMARY KEY  (asset_id)
						) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}	
	
	function assert_table_pegasaas_semaphore() {
		global $wpdb;
		
		$table_name = $wpdb->prefix."pegasaas_semaphore";
		$results = $wpdb->query("SELECT table_name FROM information_schema.tables WHERE table_name='{$table_name}' AND table_schema='".DB_NAME."'");
	
		if ($results == 0) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
  						semaphore_name varchar(100) NOT NULL,
						when_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  						PRIMARY KEY (semaphore_name)
						) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}		
	
	function assert_table_pegasaas_page_config() {
		global $wpdb;
		global $pegasaas;
		
		$table_name = $wpdb->prefix."pegasaas_page_config";
		$results = $wpdb->query("SELECT table_name FROM information_schema.tables WHERE table_name='{$table_name}' AND table_schema='".DB_NAME."'");
	
		if ($results == 0) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
  						resource_id varchar(190) NOT NULL,
						settings text NOT NULL,
  						PRIMARY KEY  (resource_id)
						) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
			// re-associated all data
			$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
			foreach ($accelerated_pages as $post_id => $page_info) {
					$page_level_settings = PegasaasUtils::get_object_meta($post_id, "accelerator_overrides");
					
						
						$pegasaas->utils->update_object_meta($post_id, "accelerator_overrides", $page_level_settings);
					
					
			}
			
			//delete_option("pegasaas_accelerated_pages");			
		} else {
			$results = $wpdb->query("SHOW COLUMNS FROM `{$table_name}` LIKE 'last_updated'");
			if ($results == 0) {
				$wpdb->query("ALTER TABLE `{$table_name}` ADD `last_updated` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
			}
		}
	}	
	
	
	function assert_table_pegasaas_page_cache() {
		global $wpdb;
		global $pegasaas;
		
		$table_name = $wpdb->prefix."pegasaas_page_cache";
		$results = $wpdb->query("SELECT table_name FROM information_schema.tables WHERE table_name='{$table_name}' AND table_schema='".DB_NAME."'");
	
		if ($results == 0) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
  						resource_id varchar(190) NOT NULL,
						data text NOT NULL,
  						PRIMARY KEY  (resource_id)
						) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
			// re-associated all data
			$accelerated_pages = get_option("pegasaas_accelerated_pages", array());
			foreach ($accelerated_pages as $post_id => $page_info) {
					$page_level_settings = PegasaasUtils::get_object_meta($post_id, "cached_html");
					if ($page_level_settings && sizeof($page_level_settings) > 0) {	
						$pegasaas->utils->update_object_meta($post_id, "cached_html", $page_level_settings);
					}
			}
			
						
		}
	}	
	
	function add_record($table, $fields) {
		global $wpdb;
		
		$table_name = $wpdb->prefix.$table;
		$wpdb->insert($table_name, $fields);
	
		return $wpdb->insert_id;
	}
	
	function update_record($table, $fields) {
		global $wpdb;
		
		$table_name = $wpdb->prefix.$table;
		$wpdb->replace($table_name, $fields);
	
		return $wpdb->insert_id;
	}	
	
	function delete($table, $fields = array()) {
		global $wpdb;
		
		$table_name = $wpdb->prefix.$table;
		if (sizeof($fields) == 0) {
			$query = "DELETE FROM {$table_name}";
			$wpdb->query($query);
		} else {
			$wpdb->delete($table_name, $fields);
		}
		//print $wpdb->func_call."<br>";
		$this->assert_fresh_db_connection();
	}

	function delete_comparison($table, $where = array()) {
		global $wpdb;
		
		$table_name = $wpdb->prefix.$table;
		

		if ( ! is_array( $where ) ) {
        	return false;
    	}
 
 		$query = "DELETE FROM {$table_name}";
		if (sizeof($where) > 0) { 
			$query .= " WHERE ";
		}
    	$conditions = $values = array();
		foreach ($where as $field_name => $field_value) {
			$comparison = "=";
			if (is_array($field_value) ){
				$comparison  = $field_value['comparison'];
				$field_value = $field_value['value'];
			} 
			if ($comparison == "") {
			  $comparison = "=";	
			}
			$query .= "{$field_name}{$comparison}'{$field_value}' AND ";
			
		}
		$query = preg_replace('/ AND $/', '', $query);
		
 			
				return $wpdb->query($query);

		
	}		
	
	function delete_last($table, $where = array(), $primary_key = "") {
		global $wpdb;
		
		$table_name = $wpdb->prefix.$table;
		

		if ( ! is_array( $where ) ) {
        	return false;
    	}
 
    	$where = $wpdb->process_fields( $table_name, $where, array() );
    	if ( false === $where ) {
        	return false;
		}
 
    	$conditions = $values = array();
    	foreach ( $where as $field => $value ) {
			if ( is_null( $value['value'] ) ) {
            	$conditions[] = "`$field` IS NULL";
            	continue;
			}
 
        	$conditions[] = "`$field` = " . $value['format'];
        	$values[] = $value['value'];
    	}
 
    	$conditions = implode( ' AND ', $conditions );
 
		$sql = "DELETE FROM `$table` WHERE $conditions ORDER BY $primary_key DESC LIMIT 1";
 
    	$this->check_current_query = false;
    	return $this->query( $this->prepare( $sql, $values ) );		
		
		
	}	
	
	function has_record($table, $fields) {
		global $wpdb;
		
		$table_name = $wpdb->prefix.$table;
		$query = "SELECT * FROM {$table_name} WHERE ";
		
		foreach ($fields as $field_name => $field_value) {
			$comparison = "=";
			if (is_array($field_value) ){
				$comparison  = $field_value['comparison'];
				$field_value = $field_value['value'];
			} 
			if ($comparison == "") {
			  $comparison = "=";	
			}
			$query .= "{$field_name}{$comparison}'{$field_value}' AND ";
			
		}

		$query = preg_replace('/ AND $/', '', $query);
		$query .= " LIMIT 1";
		
		$this->assert_fresh_db_connection();

		return $wpdb->query($query) > 0;
		
	}	
	
	
	function get_single_record($table, $fields = array(), $order_by = "", $group_by = "") {
		$results = $this->get_results($table, $fields, $order_by, $group_by, 1);
		$this->assert_fresh_db_connection();

		return $results[0];
		
	}
	
	function get_num_results($table, $fields = array()) {
		global $wpdb;
		global $pegasaas;

		$table_name = $wpdb->prefix.$table;
		$query = "SELECT count(*) total_results FROM {$table_name}";
		if (sizeof($fields) > 0) { 
			$query .= " WHERE ";
		}
		foreach ($fields as $field_name => $field_value) {
			$comparison = "=";
			if (is_array($field_value) ){
				$comparison  = $field_value['comparison'];
				$field_value = $field_value['value'];
			} 
			if ($comparison == "") {
			  $comparison = "=";	
			}
			$query .= "{$field_name}{$comparison}'{$field_value}' AND ";
			
		}
		$query = preg_replace('/ AND $/', '', $query);

		$this->db_queries++;
		
		$memory_usage = memory_get_usage();
		$memory_usage_k = $memory_usage / 1024;
		$memory_usage_m = number_format($memory_usage_k / 1024, 2, '.', ',');	
		
		$pegasaas->utils->log("Before PegasaasDB::get_results / Query #{$this->db_queries} / {$memory_usage_m}M / {$query}", "database");
		
		$results = $wpdb->get_results($query);

		$pegasaas->utils->log("After PegasaasDB::get_results / Query #{$this->db_queries} / {$memory_usage_m}M / {$query}", "database");

		$this->assert_fresh_db_connection();
		$record = $results[0];
		
		return $record['total_results'];

	}

	function get_results($table, $fields = array(), $order_by = "", $group_by = "", $limit = "") {
		global $wpdb;
		global $pegasaas;
		
		$table_name = $wpdb->prefix.$table;
		$query = "SELECT * FROM {$table_name}";
		if (sizeof($fields) > 0) { 
			$query .= " WHERE ";
		}
		foreach ($fields as $field_name => $field_value) {
			$comparison = "=";
			if (is_array($field_value) ){
				$comparison  = $field_value['comparison'];
				$field_value = $field_value['value'];
			} 
			if ($comparison == "") {
			  $comparison = "=";	
			}
			$query .= "{$field_name}{$comparison}'{$field_value}' AND ";
			
		}
		$query = preg_replace('/ AND $/', '', $query);
		
		if ($group_by != "") {
			$query .= " GROUP BY ".$group_by;	
		}
		
		if ($order_by != "") {
		 	$query .= " ORDER BY ".$order_by;	
		}
		
		if ($limit != "") {
			$query .= " LIMIT {$limit}";	
		}
		
		$this->db_queries++;
		
		$memory_usage = memory_get_usage();
		$memory_usage_k = $memory_usage / 1024;
		$memory_usage_m = number_format($memory_usage_k / 1024, 2, '.', ',');	
		
		$pegasaas->utils->log("Before PegasaasDB::get_results / Query #{$this->db_queries} / {$memory_usage_m}M / {$query}", "database");
		
		$results = $wpdb->get_results($query);

		$pegasaas->utils->log("After PegasaasDB::get_results / Query #{$this->db_queries} / {$memory_usage_m}M / {$query}", "database");

		$this->assert_fresh_db_connection();
		return $results;
		
	}	
	
	function assert_fresh_db_connection() {
		global $wpdb;
		global $pegasaas;
		
		$do_database_connection_refresh = false;
		$refresh_interval = 15; // seconds
		if (isset(PegasaasAccelerator::$settings['settings']['database_connection_refresh'])) {
			$do_database_connection_refresh = PegasaasAccelerator::$settings['settings']['database_connection_refresh']['status'] == 1;
			$refresh_interval = PegasaasAccelerator::$settings['settings']['database_connection_refresh']['interval'];
		//	print "{$do_database_connection_refresh}, it is {$refresh_interval}<br>";
		}
		
		if ($do_database_connection_refresh && $this->db_connected_time() >= $refresh_interval) {
			$pegasaas->utils->log("Asserting Fresh DB Connection at {$refresh_interval}s interval at ".$pegasaas->execution_time(), "database");
			$wpdb->close();
			if (!$wpdb->has_connected) {
				$pegasaas->utils->log("Have Successfully Disconnected from DB at ".$pegasaas->execution_time(), "database");

			} else {
				$pegasaas->utils->log("Did not Disconnect from DB at ".$pegasaas->execution_time(), "database");

			}
			$wpdb->db_connect();
			if ($wpdb->has_connected) {
				$pegasaas->utils->log("Have Successfully Reconnected to DB at ".$pegasaas->execution_time(), "database");

			} else {
				$pegasaas->utils->log("Experiencing Problem Reconnecting to DB at ".$pegasaas->execution_time(), "database");
			}
			$this->when_connected = $pegasaas->microtime();
		}		
		
	}
	
	function db_connected_time() {
		global $pegasaas;
		return $pegasaas->microtime() - $this->when_connected;
	}
	
	
	
}
?>