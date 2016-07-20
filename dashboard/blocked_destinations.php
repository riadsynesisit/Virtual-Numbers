<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){

	global $action_message;
	global $this_user;
	
	//check if the user is an admin, otherwise redirect to index page
	if($_SESSION['admin_flag']==1){
		if(isset($_POST['Edit']) && $_POST['Edit']=='Edit')
		{
		 show_edit_page($this_page);
		}
		else if(isset($_POST['Delete']) && $_POST['Delete']=='Delete')
		{
		  show_delete_page($this_page);
		
		}
		else if(isset($_POST['Continue']) && $_POST['Continue']=='Continue')
		{
		  
		  process_edit_page($this_page);
		
		}
		else
		{
		
		process_search($this_page);	
	   }
		
	}else{
		not_admin_user();
	}
	
}

function show_delete_page($this_page){

	global $action_message;
	$assigned_did = new Block_Destination();
	
	$status = $assigned_did->deletedestinationData($_POST['destiantion_number']);
	if($status==true)
	{
		$action_message .= "<h2>Success! </h2>\n";
			$action_message .= "<p>We successfully Deleted  your Outbound Number '".$_POST['destiantion_number']."'.</p>\n";
			process_search($this_page);
	}
	else
	{
	        $action_message .= "<h2>Problems...</h2>\n";
			$action_message .= "<p>We encountered some errors in your information when saving your changes to your Outbound Number '".$_POST['destiantion_number']."'.</p>\n";
			$action_message .= "<p>Please try again or contact us if the problem persists.</p>\n";
			process_search($this_page);
	
	}
	
	
}
function process_edit_page($this_page){

	global $action_message;
	$this_member = new Block_Destination();
	
	 $ck = $this_member->getupdate();
	
	
	if($ck!=0)
	{
	
	$action_message .= "<p>ERROR: Same Outbound Number Already Exists.</p>\n";
	show_edit_page($this_page);
	}
	else
	{
	
	$status = $this_member->getupdatenumber();
	
	if($status==true)
	{
		$action_message .= "<h2>Success! </h2>\n";
			$action_message .= "<p>We Successfully Updated  your Outbound Number '".$_POST['destiantion_number']."'.</p>\n";
			process_search($this_page);
	}
	else
	{
	        $action_message .= "<h2>Problems...</h2>\n";
			$action_message .= "<p>We encountered some errors in your information when saving your changes to your Outbound Number '".$_POST['destiantion_number']."'.</p>\n";
			$action_message .= "<p>Please try again or contact us if the problem persists.</p>\n";
			process_search($this_page);
	
	}
	}
	
}

function show_edit_page($this_page){
	
	global $action_message;
	$page_vars = array();
	
	$page_vars['menu_item'] = 'block_destination';
	$page_vars['destiantion_number'] = $_POST['destiantion_number'];
	$page_vars['destiantion_id'] = $_POST['destiantion_id'];
	$page_vars['action_message'] = $action_message;
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	$this_page->content_area .= getUpdateDestinationPage();
	
	common_header($this_page, "Edit Outbound Number - ".$_POST['destiantion_number']);
	
}

function process_search($this_page)
{
   
    global $action_message;
	
	global $this_page;
	
	$page_vars = array();
	$this_member = new Block_Destination();
	if($_REQUEST['Add']=='Add')
		{
		$ck = $this_member->getCountMembers();
		if($ck!=0)
		{
		 $action_message .= "<p>ERROR: Adding Outbound Number Already Exists.</p>\n";
		
		}
		else
		{
		$st = $this_member->insertDestiantionData();
		if($st==true)
		{
		 $action_message .= "<p>SUCCESS: Outbound Number Added Success.</p>\n";
		}
		else
		{
		 $action_message .= "<p>ERROR: Insert Failed Please Try Again.</p>\n";
		
		}
		$_REQUEST['destination']='';
		}
		}
	
			//get memebrs count
			$my_total_call_count = $this_member->getCountMembers();
			
			
			
	// sort out pagination
	// first, figure out the page selector data
	$records_per_page = 20;
	$page_count = ceil($my_total_call_count / $records_per_page);

	$page_deck = array();
	for($page=1;$page<=$page_count;$page++){
		$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
		$last_record_in_range = $page * $records_per_page;
		if($last_record_in_range > $my_total_call_count){
			$last_record_in_range = $my_total_call_count;
		}
		$page_deck[$page] = $first_record_in_range ." - ".$last_record_in_range;
	}
	
	//now figure out what page we are on
	$page_current = 1;
	if(isset($_REQUEST['page'])){
	 $safe_page_select = $_GET['page'];
	}
	else
	{
	$safe_page_select='';
	
	}
	
	if($safe_page_select!=""){
		$safe_page_select = intval($safe_page_select);
	}
	
	if($safe_page_select > 0 && $safe_page_select != $page_current){
		$page_current  = $safe_page_select;
	}

	$page_current_range_start = $page_current * $records_per_page - ($records_per_page -1 );
	$page_current_range_end = $page_current * $records_per_page;
	if ($page_current_range_end > $my_total_call_count){
		$page_current_range_end = $my_total_call_count;
	}
	
	//now update the variable stack
	$page_vars['page_count'] = $page_count;
	$page_vars['page_deck'] = $page_deck;
	
	$page_vars['page_current'] = $page_current;
	$page_vars['page_current_range_start'] = $page_current_range_start;
	$page_vars['page_current_range_end'] = $page_current_range_end;
	
	# => now, only get the records for this page
	
	$members = $this_member->for_page_range($records_per_page, $page_current_range_start - 1);
     
     if($members=='')
	 {
	    $page_vars['members'] = array();
	 }
	 else
	 {
	   $page_vars['members'] = $members;
	 
	 }
	
	
	
	$page_vars['my_total_members_count'] = $my_total_call_count;
	$page_vars['action_message'] = $action_message;
	$page_vars['destination'] = $_REQUEST['destination'];
	$page_vars['Submit'] = $_REQUEST['Submit'];
	$page_vars['menu_item'] = 'block_destination';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	$this_page->content_area .= getBlockDestinations();//In pages/block_destinations.inc.php
	
	common_header($this_page,"'Blocked Outbound List'");





}
require("includes/DRY_authentication.inc.php");

?>