<?php
class System_Config_Options{
	
	function getDIDReorderThreshold( $op_value = ""){
		$query = "select config_value $op_value from system_config_options where config_option='did_reorder_threshold'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.getDIDReorderThreshold. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.getDIDReorderThreshold");
		}
		$row = mysql_fetch_array($result);
		if($op_value){
			return array("is_active" => $row['is_active'], "config_value" => $row['config_value'], "op_value" => $row['op_value']);
		}else{
			return $row['config_value'];
		}
	}
	
	function updateDIDReorderThreshold($config_value){
		
		$query = "update system_config_options set config_value='$config_value' where config_option='did_reorder_threshold'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.updateDIDReorderThreshold. ".mysql_error());
		}
		return true;
	}
	
	function updateDIDSeries($op_value){
		
		$query = "update system_config_options set op_value='$op_value', is_active = 'Y' where config_option='did_reorder_threshold'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.updateDIDSeries. ".mysql_error());
		}
		return true;
	}
	
	function updateActiveStatus($config_option, $status='N'){
		$query = "update system_config_options set is_active='$status' where config_option='$config_option'";
		mysql_query($query);
		if(mysql_error()!=""){
			echo $query;
			return false;
		}
		return true;
	}
	
	function get087DIDReorderThreshold(){
		$query = "select config_value from system_config_options where config_option='087did_reorder_threshold'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.get087DIDReorderThreshold. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.get087DIDReorderThreshold");
		}
		$row = mysql_fetch_array($result);
		return $row['config_value'];
	}
	
	function update087DIDReorderThreshold($config_value){
		
		$query = "update system_config_options set config_value='$config_value' where config_option='087did_reorder_threshold'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.update087DIDReorderThreshold. ".mysql_error());
		}
		return true;
	}
	
	function get087DIDCallCostLimit(){
		$query = "select config_value from system_config_options where config_option='087call_cost_limit'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.get087DIDCallCostLimit. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.get087DIDCallCostLimit");
		}
		$row = mysql_fetch_array($result);
		return $row['config_value'];
	}
	
	function update087DIDCallCostLimit($config_value){
		
		$query = "update system_config_options set config_value='$config_value' where config_option='087call_cost_limit'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.update087DIDCallCostLimit. ".mysql_error());
		}
		return true;
	}
	
	function getAUDIDReorderThreshold(){
		$query = "select config_value from system_config_options where config_option='audid_reorder_threshold'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.getAUDIDReorderThreshold. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.getAUDIDReorderThreshold");
		}
		$row = mysql_fetch_array($result);
		return $row['config_value'];
	}
	
	function getDIDWWRegionsLastRequestGMT(){
		$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='didww_regions_last_request_gmt'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.getDIDWWRegionsLastRequestGMT. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.getDIDWWRegionsLastRequestGMT");
		}
		$row = mysql_fetch_array($result);
		return $row['config_value'];
	}
	
	function updateDIDWWRegionsLastRequestGMT($config_value){
		
		$query = "update system_config_options set config_value='$config_value' where config_option='didww_regions_last_request_gmt'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.updateDIDWWRegionsLastRequestGMT. ".mysql_error());
		}
		return true;
	}
	
	function getSIPMarkUpCost(){
		$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='sip_mark_up_cost'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.getSIPMarkUpCost. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.getSIPMarkUpCost");
		}
		$row = mysql_fetch_array($result);
		return $row['config_value'];
	}
	
	function getTollFreeMarkUpCost(){
		$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='tollfree_mark_up_cost'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.getTollFreeMarkUpCost. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.getTollFreeMarkUpCost");
		}
		$row = mysql_fetch_array($result);
		return $row['config_value'];
	}
	
	function updateSIPMarkUpCost($config_value){
		$query = "update system_config_options set config_value='$config_value' where config_option='sip_mark_up_cost'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.updateSIPMarkUpCost. ".mysql_error());
		}
		return true;
	}
	
	function getRateGBPToAUD(){
		$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='currency_rate_gbp_to_aud'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.getRateGBPToAUD. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.getRateGBPToAUD");
		}
		$row = mysql_fetch_array($result);
		return $row['config_value'];
	}
	
	function updateRateGBPToAUD($config_value){
		$query = "update system_config_options set config_value='$config_value' where config_option='currency_rate_gbp_to_aud'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.updateRateGBPToAUD. ".mysql_error());
		}
		return true;
	}
	
	function getRateUSDToAUD(){
		$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='currency_rate_usd_to_aud'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.getRateUSDToAUD. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options.getRateUSDToAUD");
		}
		$row = mysql_fetch_array($result);
		return $row['config_value'];
	}
	
	function updateRateUSDToAUD($config_value){
		$query = "update system_config_options set config_value='$config_value' where config_option='currency_rate_usd_to_aud'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in System_Config_Options.updateRateUSDToAUD. ".mysql_error());
		}
		return true;
	}
	
	function getConfigOption($config_option){
		$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='$config_option'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting config option $config_option. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No System Config record found in System_Config_Options for $config_option");
		}
		$row = mysql_fetch_array($result);
		return $row["config_value"];
	}
	
	function updateConfigOption($config_option,$config_value){
		$query = "update system_config_options set config_value='$config_value' where config_option='$config_option'";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while updating config option $config_option. ".mysql_error());
		}
		return true;
	}
	
}
?>