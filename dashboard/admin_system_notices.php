<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){

	global $action_message;
	global $this_user;
	
	//check if the user is an admin, otherwise redirect to index page
	if($_SESSION['admin_flag']==1){
		if(isset($_POST['publish'])){
			if(trim($_POST['message_title'])!="" && trim($_POST['message_body'])){
				add_new_message();
			}else{
				$action_message .= "<h2>Problems... </h2>\n";
				$action_message .= "<p>Please enter both message title and message body and try again!</p>\n";
			}
		}elseif(isset($_GET['id']) && trim($_GET['id'])!=""){
			delete_message();
		}
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
	
	$systemmessages = new SystemMessages();
	
	$page_vars['severity'] = $systemmessages->severity;
	$page_vars['message_type'] = $systemmessages->message_type;
	
	$page_vars['months']=array('January','February','March','April','May','June','July','August','September','October','November','December');
	
	$page_vars['valid_from_day']=date("j");
	$page_vars['valid_from_month_num']=date("n");
	$page_vars['valid_from_year']=date("Y");
	
	$after7days = time()+(7*24*60*60);
	
	$page_vars['valid_until_day']=date("j",$after7days);
	$page_vars['valid_until_month_num']=date("n",$after7days);
	$page_vars['valid_until_year']=date("Y",$after7days);
	
	$page_vars['old_message_list'] = $systemmessages->get_old_issues();
	$page_vars['current_message_list'] = $systemmessages->get_current_issues();
	$page_vars['future_message_list'] = $systemmessages->get_future_issues();
	$page_vars['menu_item'] = 'admin_system_notices';
	 
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	//build the page 
	$this_page->content_area .= getSystemNoticesNew();//In pages/system_notices_new.inc.php
	$this_page->content_area .= getSystemNotices();//In pages/system_notices_show.inc.php
	
	common_header($this_page,"System Management - System Notices");
}

function add_new_message(){
	
	global $action_message;
	
	$msg = new SystemMessages();
	//set the properties
	$msg->message_title = $_POST['message_title'];
	$msg->message_body = $_POST['message_body'];
	$msg->message_creation = $_POST['valid_from_years']."-".$_POST['valid_from_months']."-".$_POST['valid_from_day'];
	$msg->valid_until = $_POST['valid_until_years']."-".$_POST['valid_until_months']."-".$_POST['valid_until_day'];
	$msg->msg_severity = $_POST['severity'];
	$msg->msg_type = $_POST['message_type'];
	$msg->user_id = $_SESSION['user_logins_id'];
	
	$status = $msg->saveMessage();
	
	if($status==true){
		$action_message .= "<h2>Success!</h2>\n";
		$action_message .= "<p>We successfully saved your changes to your System Message: ".$_POST['message_title'].".</p>\n";
	}else{
		$action_message .= "<h2>Problems... </h2>\n";
		$action_message .= "<p>We found some errors in your information when saving your System Message: ".$_POST['message_title'].".</p>\n";
		$action_message .= "<p>Please correct these issues and then re-publish your notice.</p>\n"; 
	}
	
}

function delete_message(){
	
	global $action_message;
	
	$msg = new SystemMessages();
	//set the properties
	$msg->id = $_GET['id'];
	
	$status = $msg->deleteMessage();
	
	if($status==true){
		$action_message .= "<h2>Success!</h2>\n";
		$action_message .= "<p>Successfully deleted the System Message.</p>\n";
	}else{
		$action_message .= "<h2>Problems... </h2>\n";
		$action_message .= "<p>We found some errors in your information while deleting your System Message.</p>\n";
		$action_message .= "<p>Please try again or contact technical support if the problem persists.</p>\n"; 
	}
	
}

require("includes/DRY_authentication.inc.php");

?>