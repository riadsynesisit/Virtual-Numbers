<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){

	global $this_user;
	
	//check if the user is an admin, otherwise redirect to index page
	if($_SESSION['admin_flag']==1){
		//must be a clean start, show the index
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
	
	$assigned_did = new Assigned_DID();
	
	$list_of_dids = $assigned_did->getAllUsersDIDs();
	$page_vars['grouped_by_user'] = $list_of_dids;
	$page_vars['menu_item'] = 'did_numbers';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	# => build the page
	$this_page->content_area .= getAllAssigned_DIDs();
	
	common_header($this_page,"System Management - DID Numbers");
	
}

require("includes/DRY_authentication.inc.php");

?>