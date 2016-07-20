<?php
ini_set("display_errors",0);

include_once('../commons/ip2c/ip2country.php');
$ip2c               =   new ip2country();
$ip2c->mysql_host   =   SQLHOST;
$ip2c->db_user      =   SQLUSER;
$ip2c->db_pass      =   SQLPASS;
$ip2c->db_name      =   SQLDB;
$ip2c->table_name   =   'ip_address_details';

$country_from_IP    =   $ip2c->get_country_name($ip_address);
require "./includes/DRY_setup.inc.php";
$_SESSION['IP_Country'] =   $country_from_IP;

function get_page_content($this_page){
	
	if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
		if(isset($_POST['Submit'])){
			if(trim($_POST['destinationnumber'])=="+" || trim($_POST['destinationnumber'])==""){
				noDestinationNumber($this_page);
			}else{
				process_wizard_attempt($this_page);
			}
		}elseif(isset($_POST['pymt_type']) && trim($_POST['pymt_type'])!=""){
			process_purchase($this_page);
		}else{
			show_setup_page($this_page);
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

function show_setup_page($this_page){
	
	$page_vars = array();
	
	$assigned_dids = new Assigned_DID();
	$assigned_dids->user_id = $_SESSION['user_logins_id'];
	if(isset($_POST["did_id"]) && trim($_POST["did_id"])!="" ||
		(isset($_POST["activate_did_id"]) && trim($_POST["activate_did_id"])!="")){
		$page_vars['menu_item'] = 'edit_routes';
	}else{
		$page_vars['menu_item'] = 'add_new_number';
	}
	
	$page_vars["country_codes"]=$assigned_dids->getCountry_Codes();
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);

	//if(SITE_COUNTRY=="AU"){
		if(isset($_POST["number"]) && trim($_POST["number"])!=""){
			$this_page->content_area .= add_number_1_Page_AU_choose_plan();//This function is in dashboard/pages/add-number-1.inc.php page
		}elseif(isset($_POST["chosen_plan"]) && trim($_POST["chosen_plan"])!=""){
			$this_page->content_area .= add_number_1_Page_AU_choose_pymt();//This function is in dashboard/pages/add-number-1.inc.php page
		}else{
			$this_page->content_area .= add_number_1_Page_AU();//This function is in dashboard/pages/add-number-1.inc.php page
		}
	//}else{
	//	$this_page->content_area .= add_number_1_Page();//This function is in dashboard/pages/add-number-1.inc.php page
	//}	
	common_header($this_page,"Add Number Wizard Step 1 of 3");
}

function process_wizard_attempt($this_page){
	
	checkSimilarDestinations($this_page,trim($_POST['destinationnumber']));
	
	$_SESSION['destinationnumber'] = trim($_POST['destinationnumber']);
	
	$block_destination = new Block_Destination();
	if($block_destination->isDestinationBlocked($_SESSION['destinationnumber'])==true){
		$page_vars['action_message'] = "<font color='red'>Your destination is detected as fraud, <a href='mailto:support@stickynumber.com'>click here</a> to ask for an exception.</font>";
		$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
		show_setup_page($this_page);
	}else{
		$unassigned_087did = new Unassigned_087DID();
		if($unassigned_087did->isCallCostOK($_SESSION['destinationnumber'])==true){
			header("Location: add-number-2.php");
		}else{
			$page_vars['action_message'] = "<font color='red'>Your destination is too expensive and not currently allowed.</font>";
			$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
			show_setup_page($this_page);		
		}
	}
}

function noDestinationNumber($this_page){
	
	$page_vars = array();
	$page_vars['menu_item'] = 'add_new_number';
	$meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS+4;
	$meta_refresh_page = "add-number-1.php";
	$action_message .= "<h2>Problems...</h2>\n";
	$action_message .= "<p>We encountered some errors in your information when saving your changes to your Virtual Number.</p>\n";
	$action_message .= "<p>Destination number is required. Please enter destination number.</p>\n";
	$action_message .= "<p>Please try again or contact us if the problem persists.</p>\n";
	$page_vars['action_message'] = $action_message;
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	$this_page->content_area .= getWizardProgressPage();
			
	common_header($this_page,"Add Number Wizard Step 1 of 3",false,$meta_refresh_sec,$meta_refresh_page);
	
}

function checkSimilarDestinations($this_page,$fwd_num){
	
	$assigned_dids = new Assigned_DID();
	if(!is_object($assigned_dids)){
		die("Error: Failed to create Assigned_DID object.");
	}
	$similar_numbers = $assigned_dids->getSimilarFwdNumCount($_SESSION['user_logins_id'],$fwd_num);
	
	$fraud_det = new FraudDetection();
	if(!is_object($fraud_det)){
		die("Error: Failed to create FraudDetection object.");
	}
	$fraud_data = $fraud_det->getFraudDetection();
	$max_similar = $fraud_data["max_forward"];

	if(intval($similar_numbers)>=intval($max_similar)){
		//Suspend User Account
		$user_logins = new User();
		$user_logins->suspendUser($_SESSION['user_logins_id']);
		// update access logs
		$this_access = new Access();
		$this_access->updateAccessLogs("Auto suspended due to fraud detection - Max Number of similar destination numbers in 24 hours:",false,$fwd_num);
		//Send Email to system admin
		$numbers_info = $assigned_dids->getSimilarNumbersInfo($_SESSION['user_logins_id'],$fwd_num);
		send_email($_SESSION['user_logins_id'],$numbers_info);
		$page_vars['action_message'] = "<font color='red'>Our system detected a fraud and your account is suspended. Please contact us at ".ACCOUNT_CHANGE_EMAIL." for assistance..</font>";
		$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
		show_setup_page($this_page);
	}else{
		return true;
	}
}

function send_email($user_id, $numbers_info){
	
	$from_email = "info@stickynumber.com";
	$to_email = "steve.s@stickynumber.com";
	$subject_line = "User ID: ".$user_id." | Account auto suspended";
	$email_hmtl_start = '<html> 
  	<body style="padding: 10px 15px; background: #FDFFC0; border: 1px dotted #999; color: #666; margin-bottom: 15px;">'; 
	$email_top = "
	Hello,<br />
	<br />
	Account with User ID: $user_id was auto suspended due to its exceeding the maximum number of similar destinations in past 7 days. 
	<br /><br />
	-------------------------------------------------------------------------------------------------------
	<br /><br />
	<b>$numbers_info</b>
	<br /><br />
	-------------------------------------------------------------------------------------------------------
	<br /><br />";
	$email_body = '<br />
	Sticky Number <br />
	System Generated Email.<br />';
	$email_hmtl_end = '
	<br />This email is sent by StickyNumber.com server on detection of a fraud case.
  	</body> 
	</html>';

	$email_message  = $email_hmtl_start . $email_top . $email_body . $email_hmtl_end;
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
	return mail($to_email, $subject_line, $email_message, $headers);
	
}

require("includes/DRY_authentication.inc.php");

?>