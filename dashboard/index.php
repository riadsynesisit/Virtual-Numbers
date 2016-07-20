<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	$page_vars = $this_page->variable_stack;
	//if($page_vars['my_DID_count']==0 && $_SESSION['admin_flag']==0){
	//	send_to_wizard_start($this_page);
	//}else{
		show_index_page($this_page);
	//}
	
}

function show_index_page($this_page){
	
	global $this_user;
	global $page_vars;//This is declared in DRY_authentication.inc.php
	
	$um = new UserMessages();
	
	// => ... if the user has deleted messages, honor request first
	if(isset($_POST['submit']) && trim($_POST['del_msg_id'])!=""){ 
		$um->id = trim($_POST['del_msg_id']);
		$um->deleteSelectedUserMessage();
	}//end of if deleting messages 
	
	$user_id = $_SESSION['user_logins_id'];
	$email = $_SESSION['user_login'];
	
	$user_info = $this_user->getUserInfo($email,SITE_COUNTRY);
	$page_vars['user_last_login'] = $user_info['user_last_login'];
	
	$my_messages = $um->getUserMessages($user_id);
	if($my_messages===false){
		echo "Error in retrieving user messages. Please try again or contact support if the problem persists.";
		exit;
	}
	
	$page_vars['my_messages'] = $my_messages;
	$page_vars['menu_item'] = 'index';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	$this_page->content_area = "";
	$this_page->content_area .= getIndexPage($this_page);
	
	common_header($this_page, "Main Page");
	
	//set the message status as viewed
	$um->updateUserMessagesAsViewed($user_id);
}

function send_to_wizard_start($this_page){
	
	$this_page->content_area = '<div class="title-bg"><h1>Main Page Welcome to the '.APP_TITLE_SHORT.' server. </h1></div><div class="mid-section">
	               <div class="date-bar">
					<div class="left-info"><p>We see that you do not yet have a phone-number (called a DID) chosen for your account.</p>  
					<p>We will now try to automatically take you to the Setup Wizard.  If nothing seems to happen in a few seconds, just click <a href="add-number-1.php">this link</a> and we will take you there.</p>
					</div>
										</div>
					<!-- Date section end here -->';				
	$this_page->content_area .='</div>';				
	/*$this_page->content_area = "<h2>"."Main Page"."</h2><p>Welcome to the ".APP_TITLE_SHORT." server.</p>\n"; 
	$this_page->content_area .= "<p>We see that you do not yet have a phone-number (called a DID) chosen for your account.</p>  \n";
	$this_page->content_area .= "<p>We will now try to automatically take you to the Setup Wizard.  If nothing seems to happen in a few seconds, just click <a href='add-number-step1.php'>this link</a> and we will take you there.</p>\n "; */
	
	$meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS + 4;
	$meta_refresh_page = "add-number-1.php";
	
	common_header($this_page, "Main Page", false, $meta_refresh_sec, $meta_refresh_page);
}

require("includes/DRY_authentication.inc.php");

?>
