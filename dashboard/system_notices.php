<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
	global $action_message;
	
	$page_vars = array();
	
	$action_message = "";
	$this_page->content_area = "";

	//show personal messages in the system
	$user_messages = new UserMessages();
	$my_messages = $user_messages->getUserMessages($_SESSION['user_logins_id']);
	
	//check for ::keyword:: subsitutions
	//This logic moved into system_notices_show.inc.php
	
	$page_vars['my_messages'] = $my_messages;
	
	//show existing messages in the system, in order of valid date, creation date and severity
	$msg = new SystemMessages();
	$page_vars['old_message_list'] = $msg->get_old_issues();
	$page_vars['current_message_list'] = $msg->get_current_issues();
	$page_vars['future_message_list'] = $msg->get_future_issues();
	$page_vars['menu_item'] = 'system_notices';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	//build the page
	//$this_page->content_area = "<p class='action_message'>$action_message</p>\n";
	$this_page->content_area .= getSystemNotices();
	
	common_header($this_page,"My Stuff - System Notices & Messages");
	
}

require("includes/DRY_authentication.inc.php");

?>