<?php
    require "./includes/DRY_setup.inc.php";
    
    function get_page_content($this_page){

	global $action_message;
	global $users_dids;
	$page_vars = array();
	
	//$action_message = "Use the 'edit' button to make changes to a selected Virtual Number route.";
	
	// sort out pagination
	// first, figure out the page selector data
        $users_dids =   mysql_fetch_object(mysql_query("SELECT count(*) as total FROM assigned_dids WHERE did_block_status LIKE 'Y'"))->total;
        
	$records_per_page = RECORDS_PER_PAGE;
	$page_count = ceil($users_dids / $records_per_page);

	$page_deck = array();
	for($page=1;$page<=$page_count;$page++){
		$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
		$last_record_in_range = $page * $records_per_page;
		if($last_record_in_range > $users_dids){
			$last_record_in_range = $users_dids;
		}
		$page_deck[$page] = $first_record_in_range ." - ".$last_record_in_range;
	}
	
	//now figure out what page we are on
	$page_current = 1;
	$safe_page_select = $_GET['page'];
	if($safe_page_select!=""){
		$safe_page_select = intval($safe_page_select);
	}
	
	if($safe_page_select > 0 && $safe_page_select != $page_current){
		$page_current  = $safe_page_select;
	}

	$page_current_range_start = $page_current * $records_per_page - ($records_per_page -1 );
	$page_current_range_end = $page_current * $records_per_page;
	if ($page_current_range_end > $users_dids){
		$page_current_range_end = $users_dids;
	}
	
	//now update the variable stack
	$page_vars['page_count'] = $page_count;
	$page_vars['page_deck'] = $page_deck;
	$page_vars['menu_item'] = 'edit_routes';
	
	$page_vars['page_current'] = $page_current;
	$page_vars['page_current_range_start'] = $page_current_range_start;
	$page_vars['page_current_range_end'] = $page_current_range_end;
	
	$page_vars['my_DIDs_count'] = $users_dids;
	
	//now, only get the records for this page
	$assigned_dids = new Assigned_DID();
	$page_vars['my_DIDs'] = $assigned_dids->getBlockDIDs($_SESSION['user_logins_id'],$page_current_range_start-1);
	
	$page_vars['action_message'] = $action_message;
	
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	//build the page
	$this_page->content_area .= getFraudDetectionNoticesPage();
	
	common_header($this_page,"'Fraud Notices'");
}

    require("includes/DRY_authentication.inc.php");
?>