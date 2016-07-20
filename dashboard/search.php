<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){

	global $action_message;
	global $this_user;
	
	//check if the user is an admin or basicsupport, otherwise redirect to index page
	if($_SESSION['admin_flag']==1||$_SESSION['admin_flag']==2){
		if(isset($_REQUEST['Submit']) ){
		process_search();
	}else{
		show_index_page();
	}
	
		
	}else{
		not_admin_user();
	}
	
}

function show_index_page(){
	
	global $action_message;
	global $this_page;
	$page_vars = array();
	
	$page_vars['action_message'] = $action_message;
	$page_vars['menu_item'] = 'member_search';
	
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	# => build the page
	$this_page->content_area .= getSearchPage();
	
	common_header($this_page,"Member Search");
	
}

function process_search()
{

    global $action_message;
	
	global $this_page;
	
	$page_vars = array();
	$this_member = new Member();
	
	
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
	if(isset($_POST['paginate'])){
	 $safe_page_select = $_POST['paginate'];
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
	$page_vars['ip_address'] = $_REQUEST['ip_address'];
	$page_vars['id'] = $_REQUEST['id'];
	$page_vars['email'] = $_REQUEST['email'];
	$page_vars['did'] = $_REQUEST['did'];
	$page_vars['dest'] = $_REQUEST['dest'];
	$page_vars['Submit'] = $_REQUEST['Submit'];
	$page_vars['menu_item'] = 'member_search';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	$this_page->content_area .= getSearchMembersList();//In pages/search.inc.php
	
	common_header($this_page,"'Member Search List'");





}
require("includes/DRY_authentication.inc.php");

?>