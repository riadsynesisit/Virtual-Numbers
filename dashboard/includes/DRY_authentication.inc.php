<?php

$this_page = new WebPage();
$this_user = new User();

//these are needed everywhere
$action_message = "";
$this_page->content_area = "";

if(strtoupper(RUN_MODE) == "MAINTENANCE"){
	//Jump to login screen
	$this_page->meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS+12;
	$this_page->meta_refresh_page = "http://www.stickynumber.com/index.php";
	
	$this_page->content_area = "<img src='images/maintenance.gif' alt='Description: Site down for maintenance, please try again in an hour'> <br>";
	$this_page->content_area .= "We're sorry, this service is currently down for maintenance and upgrades. We're working as fast as we can, and we appreciate you patience.</ br>\n";
	$this_page->content_area .= "We will now try to automatically take you to the front page.  If nothing seems to happen in a few seconds, just click <a href='http://www.stickynumber.com/index.php'>this link</a> and we will take you there. </ br>\n";
	
	common_header($this_page, "Not Logged In - Login Required", false);
	
}else{
	if($_SESSION['is_authenticated']==true || $this_page->is_border_page == true){
		if($_SESSION['is_authenticated']==true){
			if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
				$user_id = $_SESSION['user_logins_id'];
				//Get the user data and load the variables we need for the left user bar
				$assigned_dids = new Assigned_DID();
				$users_dids = $assigned_dids->getUserDIDsCount($user_id);
				if(is_object($this_user)){
					//Need to interface with AsteriskSQLite for CDRs
					$cdr_connector = new AsteriskSQLiteCDR();
					$user_messages = new UserMessages();
					$voice_mail   =   new VoiceMail();
					$page_vars = array();
					
					$page_vars['my_messages_count'] = $user_messages->getUserMessagesCount($user_id);
					$page_vars['user_name'] = $_SESSION['user_name'];
					$page_vars['member_id'] = $user_id;
					$page_vars['my_DID_count'] = $users_dids;
					$page_vars['user_admin_flag'] = $_SESSION['admin_flag'];
					$page_vars['my_unread_voicemails_count'] = $voice_mail->get_total_unread_mails_for_user($user_id);
					$my_total_call_count = $cdr_connector->total_calls_for_user_id($user_id);
	
					$page_vars['my_total_call_count'] = $my_total_call_count;
		 			$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
					
				}else{
					echo "ERROR: this_user is not an object.";exit;	
				}
			}else{
				//Jump to login screen
				$this_page->meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS+12;
				$this_page->meta_refresh_page = "login.php";
				
				$this_page->content_area = "";
				$this_page->content_area .= "We're sorry, you must be logged in as a verified user to access this service. </ br>\n";
				$this_page->content_area .= "We will now  try to automatically take you to the login page.  If nothing seems to happen in a few seconds, just click <a href='login.php'>this link</a> and we will take you there. </ br>\n"; 
				
				common_header($this_page, "Not Logged In - Login Required", false, $meta_refresh_sec, $meta_refresh_page);
			}	
		}
		//show them what they came for
		get_page_content($this_page);
	}else{
		//Jump to login screen
		$this_page->meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS+12;
		$this_page->meta_refresh_page = "login.php";
		
		$this_page->content_area = "";
		$this_page->content_area .= "We're sorry, you must be logged in as a verified user to access this service. </ br>\n";
		$this_page->content_area .= "We will now  try to automatically take you to the login page.  If nothing seems to happen in a few seconds, just click <a href='login.php'>this link</a> and we will take you there. </ br>\n"; 
		
		common_header($this_page, "Not Authenticated - Login Required", false, $meta_refresh_sec, $meta_refresh_page);
	}
}

$this_page->footer = "";

if(strtoupper(RUN_MODE) == "DEBUG" && is_object($this_user) && isset($_SESSION['admin_flag']) && $_SESSION['admin_flag']==1){
	$this_page->footer .= "<div id='footer_center_small'>The following line shows the Paramaters that were passed for test purposes:<br><br> $this_page->variable_stack </div>";
} //RUN_MODE == "DEBUG"

$this_page->footer .= getFooterPage();

echo $this_page->footer;

/* CLOSE MYSQL CONNECTION AFTER PAGE DATA SHOWN :: ADDED ON 15TH JULY 2015 */

if($conn){
	mysql_close($conn);
}

?>
