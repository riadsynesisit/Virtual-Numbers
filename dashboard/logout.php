<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
	global $action_message;
	
	$this_page->content_area = "";
	$action_message = "";
	process_logout_attempt();
	
}

function process_logout_attempt(){
	
	unset($_SESSION['user_logins_id']);
	unset($_SESSION['user_name']);
	unset($_SESSION['user_login']);
	unset($_SESSION['is_authenticated']);
	unset($_SESSION['admin_flag']);
	unset($_SESSION['added_dids_list']);
	unset($_SESSION['admin_access_login']);
	unset($_SESSION['admin_access_logins_id']);
	session_unset();
	$status = session_destroy();
	
	//build the page
	$this_page->content_area .= getLogoutPage();
	
	if($status==true){
		//$this_page->content_area .= "<h3>Success!<h3>\n";
		//$this_page->content_area .= "<p>We will now try to automatically take you to the index page.  If nothing seems to happen in a few seconds, just click <a href='index.php'>this link</a> and we will take you there.</p>";
		//$meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS + 4;
		//$meta_refresh_page = "../index.php";
		header("Location: ../index.php");
		exit;
	}else{
		$this_page->content_area = "<h3>Failure!<h3>\n";
		$this_page->content_area .= "<p>There has been an issue with your logout attempt. Please <a href='logout.php'>try again by clicking here</a>.</p><br>\n";
		common_header($this_page, "Logout Page", false,$meta_refresh_sec,$meta_refresh_page);  
	}
	
}

require("includes/DRY_authentication.inc.php");

?>