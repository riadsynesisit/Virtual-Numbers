<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){

	global $action_message;
	global $this_user;
	
	//check if the user is an admin, otherwise redirect to index page
	if($_SESSION['admin_flag']==1){
		$action_message .= "This page will automatically refresh every minute.";
		show_index_page();
	}else{
		not_admin_user();
	}
	
}

function show_index_page(){
	
	global $action_message;
	global $this_page;
	$page_vars = array();
	
	$page_vars['action_message'] = $action_message;
	
	//load the display variables
	$page_vars['asterisk_online'] = is_app_online('asterisk');
	$page_vars['mysql_online'] = is_app_online('mysqld');
	$page_vars['apache_online'] = is_app_online('httpd');
	$page_vars['smtp_online'] = is_app_online('master');
	$page_vars['ntpd_online'] = is_app_online('ntpd');
	$page_vars['asterisk_hostname'] = get_asterisk_hostname();
	$page_vars['sql_hostname'] = get_sql_hostname();
		
	$load = check_load("web");
	$page_vars['cpu_usage'] = $load[1];//Take only minute by minute loads
	//Check load on asterisk server
	$ast_load = check_load("ast");
	$page_vars['cpu_ast_usage'] = $ast_load[1];//Take only minute by minute loads
	//Check load on MySQL server
	$sql_load = check_load("sql");
	$page_vars['cpu_sql_usage'] = $sql_load[1];//Take only minute by minute loads
	$page_vars['bandwidth_usage'] = get_bandwidth('eth0');
	
	$hdd_usage = array();
	
	$hdd_usage["web_root"] = disk_space('/','percent','web');
	$hdd_usage["web_content"] = disk_space('/var/www/html','percent','web');
	$hdd_usage["asterisk_root"] = disk_space('/','percent','asterisk');
	$hdd_usage["asterisk_content"] = disk_space('/etc/asterisk','percent','asterisk');
	$hdd_usage["sql_root"] = disk_space('/','percent','database');
	$hdd_usage["sql_data"] = disk_space('/var/lib/mysql','percent','database');
	
	$page_vars['hdd_usage'] = $hdd_usage;

	$calls = array();
	$calls['incoming'] = asterisk_check_calls("incoming","070");
	$calls['outgoing'] = asterisk_check_calls("outgoing","070");
	$calls['concurrent'] = asterisk_check_calls("concurrent","070");
	$calls['incoming_087'] = asterisk_check_calls("incoming","087");
	$calls['outgoing_087'] = asterisk_check_calls("outgoing","087");
	$calls['concurrent_087'] = asterisk_check_calls("concurrent","087");
	
	$page_vars['calls'] = $calls;
	
	// => p201009I17 change start
	$sco = new System_Config_Options();

	// => save config parameter changes, if any
	if(isset($_POST['reorder_threshold']) && $_POST['reorder_threshold']!=""){
		$new_reorder_threshold = intval($_POST['reorder_threshold']);
		$sco->updateDIDReorderThreshold($new_reorder_threshold);
	}else if(isset($_POST['did_070_start']) && $_POST['did_070_start']!=""){
		$new_did_070_start = ($_POST['did_070_start']);
		$sco->updateDIDSeries($new_did_070_start);
	}else if(isset($_POST['087reorder_threshold']) && $_POST['087reorder_threshold']!=""){
		$new_reorder_threshold = intval($_POST['087reorder_threshold']);
		$sco->update087DIDReorderThreshold($new_reorder_threshold);
	}

	$did_070_start = '';
	$scoData = $sco->getDIDReorderThreshold(", op_value");
	if(is_array($scoData)){
		$did_reorder_threshold_set = $scoData['config_value'];
		$did_070_start = $scoData['op_value'] ;
	}else{
		$did_reorder_threshold_set = $scoData ;
	}

	$did087_reorder_threshold_set = $sco->get087DIDReorderThreshold();
	
	$dids = array();
	$dids['reorder_threshold'] = $did_reorder_threshold_set;
	$dids['did_070_start'] = $did_070_start ;
	$dids['087reorder_threshold'] = $did087_reorder_threshold_set;
	
	// => p201009I17 change stop
	
	$unassigned_did = new Unassigned_DID();
	
	$dids_remaining_count = $unassigned_did->getUnassignedDIDsCount();
	
	$dids_remaining_result = array();
	if($dids_remaining_count >= 0 && $dids_remaining_count < intval($did_reorder_threshold_set)){
		// => low, red
		$dids_remaining_result = array("red", "LOW: $dids_remaining_count");
	}elseif($dids_remaining_count >= intval($did_reorder_threshold_set) && $dids_remaining_count < $did_reorder_threshold_set * 2){
		// => getting thin, yellow
		$dids_remaining_result = array("yellow", "WARN: $dids_remaining_count");
	}elseif($dids_remaining_count >= $did_reorder_threshold_set * 2){
		// => plenty left, green
		$dids_remaining_result = array("green", "OK: $dids_remaining_count");
	}else{
		// => something is broken, orange
		$dids_remaining_result = array("orange", "UNKNOWN: $dids_remaining_count");
	} //dids_remaining_count >= 0 and dids_remaining_count < 10
	
	$assigned_did = new Assigned_DID();
	
	$dids["remaining"] = $dids_remaining_result;
	$dids["in_service"] = array("blue",$assigned_did->get070DIDsCount());
	
	$unassigned_did = new Unassigned_087DID();
	
	$dids_remaining_count = $unassigned_did->getUnassignedDIDsCount();
	
	$dids_remaining_result = array();
	if($dids_remaining_count >= 0 && $dids_remaining_count < intval($did_reorder_threshold_set)){
		// => low, red
		$dids_remaining_result = array("red", "LOW: $dids_remaining_count");
	}elseif($dids_remaining_count >= intval($did_reorder_threshold_set) && $dids_remaining_count < $did_reorder_threshold_set * 2){
		// => getting thin, yellow
		$dids_remaining_result = array("yellow", "WARN: $dids_remaining_count");
	}elseif($dids_remaining_count >= $did_reorder_threshold_set * 2){
		// => plenty left, green
		$dids_remaining_result = array("green", "OK: $dids_remaining_count");
	}else{
		// => something is broken, orange
		$dids_remaining_result = array("orange", "UNKNOWN: $dids_remaining_count");
	} //dids_remaining_count >= 0 and dids_remaining_count < 10
	
	$dids["remaining_087"] = $dids_remaining_result;
	$dids["in_service_087"] = array("blue",$assigned_did->get087DIDsCount());
	
	$page_vars['dids'] = $dids;
	//echo '<pre>';print_r($page_vars);echo '</pre>';
	$cdr_connector = new AsteriskSQLiteCDR();
	$daily_total = array();
	$daily_total['calls'] = array("orange",$cdr_connector->number_of_calls_today());
	$daily_total['calls_087'] = array("orange",$cdr_connector->number_of_087calls_today());
	
	$sign_ups_user = new User();

	$signups_count = $sign_ups_user->sign_ups_today(SITE_COUNTRY);
	if($signups_count == 0){
		$signups_color = 'red';
	}else{
		$signups_color = 'green';
	} //signups

	$daily_total['signups'] = array($signups_color, $signups_count);
	
	$page_vars['daily_total'] = $daily_total;
	$page_vars['menu_item'] = 'admin_system_status';

	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	# => build the page
	$this_page->content_area .= getSystemStatusPage();//In pages/system_status.inc.php
	
	$meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS + 60;
	$meta_refresh_page = "admin_system_status.php";
	
	common_header($this_page,"System Management - System Status",false,$meta_refresh_sec,$meta_refresh_page);
}

require("includes/DRY_authentication.inc.php");

?>