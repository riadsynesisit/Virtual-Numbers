<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){

	if(isset($_POST['new_password'])){
		process_change_password($this_page);
	}else{
		show_change_password_page($this_page);
	}
	
}


function show_change_password_page($this_page){
	
	global $action_message;
	
	$page_vars = array();
	$page_vars['user_login'] = $_SESSION['user_login'];
	$page_vars['action_message'] = $action_message;
	$page_vars['menu_item'] = 'index';
	
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	$this_page->content_area = "";
	$this_page->content_area .= getChangePasswordPage($this_page);
	
	common_header($this_page, "Changing Your Password");
	
}

function process_change_password($this_page){
	
	global $action_message;
	
	$page_vars = array();
	
	//Make sure current password matches
	$this_user = new User();
	$user_info = $user->getUserInfo($_SESSION['user_login'], SITE_COUNTRY);
	//$isValidPwd = $this_user->isValidLogin($_SESSION['user_login'],$_POST['password']);
	
	//if($isValidPwd!=false){
		//check if the new password is empty
		if(trim($_POST['new_password'])!=""){
			//Save the new password here
			$status = $this_user->updatePassword($_SESSION['user_login'],trim($_POST['new_password']),SITE_COUNTRY);
			if($status==true){
				//send an e-mail to the user
				account_change_mailer($user_info["id"], $_SESSION['user_login'], $_SESSION['user_name'], trim($_POST['new_password']), 'update');
				$action_message .= "<p>Your changes to your login information have been saved successfully.  You'll get emails shortly about the changes.\n";
				//Show the change password done page
				
			}else{//new password could not be saved
				$action_message .= "<p>We found some problems when trying to save your changes.  <a href='change_password.php'>Please try again </a> or contact technical support if the problem persists.</p>\n";
			}
		}else{//new password is empty
			$action_message .= "<p>Your new password was blank so we could not save your new password.  <a href='change_password.php'>Please try again.</a></p>\n";
		}
	/*}else{//current password does not match
		$action_message .= "There was an error verifying your existing password.  <a href='change_password.php'>Please try again</a>.\n";
	}*/
	
	//build the page
	$page_vars['action_message'] = $action_message;
	$page_vars['menu_item'] = 'index';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	$this_page->content_area .= getChangePasswordDone($this_page);
	common_header($this_page,"Your Password Change Results");
}

require("includes/DRY_authentication.inc.php");

?>