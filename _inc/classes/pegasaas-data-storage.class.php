<?php
class PegasaasDataStorage {
	
	function __construct() { 
			// so we can use session variables
			if (session_id() == "") { 
				// don't need this right now, but it should be disabled with session_write_close() according to site-health.php
				//session_start();
				
			}
	}
	
	function is_valid($name) {
		
		$debug = $_GET['pegasaas_debug'] == "get_all_pages_and_posts";
		$object = get_option("pegasaas_data__".$name, NULL);
		if (isset($object) && isset($object['when_updated']) && isset($object['value'])) {
			
			$when_updated 	= $object['when_updated'];
			$duration 		= $object['duration'];
			
			if ($when_updated > time() - $duration) {
				
				return true;
					
			} 
		} 
		return false;
	}
	
	function get($name) {
		
		if ($this->is_valid($name)) {
			$object = get_option("pegasaas_data__".$name, NULL);
			return $object["value"];
		} else {
			return NULL;
		}
	}
	
	function set($name, $value, $expires = 60) {
		$critical_logging = true;
		if ($critical_logging) {
				PegasaasUtils::log("PegasaasDataStorage::set() '{$name}'");
		}		
		$object = array("when_updated" => time(), 
								   "value" => $value,
								   "duration" => $expires);
		update_option("pegasaas_data__".$name, $object, false);
		/*
		return;
		
		
		$_SESSION["$name"] = array("when_updated" => time(), 
								   "value" => $value,
								   "duration" => $expires);
								   */
	}
	
	function unset_object($name) {
		$critical_logging = true;
		if ($critical_logging) {
				PegasaasUtils::log("PegasaasDataStorage::unset_object() '{$name}'");
		}				
		delete_option("pegasaas_data__".$name);
		//unset($_SESSION["$name"]);
	}
	
	function update($variable, $refresh_expiry = true) {
		
	}
}
?>