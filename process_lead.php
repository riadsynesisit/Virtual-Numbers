<?php

header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

require("./dashboard/includes/config.inc.php");
require("./dashboard/includes/db.inc.php");
require("./dashboard/classes/Assigned_DID.class.php");
require("./dashboard/classes/AST_RT_Extensions.class.php");
require("./dashboard/classes/Leads.class.php");
require("./dashboard/classes/Lead_accounts.class.php");
require("./dashboard/classes/User.class.php");
require("/var/www/html/stickynumber/dashboard/classes/Client_notifications.class.php");
require("./dashboard/libs/ast_realtime_dialplan.lib.php");
require("/var/www/html/stickynumber/dashboard/libs/utilities.lib.php");

//Validate inputs
if(isset($_POST["txtsnname"])==false || trim($_POST["txtsnname"])==""){
	echo "Error: Required field 'Name' not passed";
	exit;
}else{
	$name = trim($_POST["txtsnname"]);
}

if(isset($_POST["txtsnemail"])==false || trim($_POST["txtsnemail"])==""){
	echo "Error: Required field 'Email' not passed";
	exit;
}elseif(filter_var(trim($_POST["txtsnemail"]), FILTER_VALIDATE_EMAIL)==false){
	echo "Error: Invalid Email id passed.";
	exit;
}else{
	$email = trim($_POST["txtsnemail"]);
}

if(isset($_POST["txtsnphone1"])==false || trim($_POST["txtsnphone1"])==""){
	echo "Error: Required field 'Phone Number' not passed";
	exit;
}elseif(filter_var(trim($_POST["txtsnphone1"]),FILTER_VALIDATE_INT)==false){
	if(substr(trim($_POST["txtsnphone1"]),0,1)=="0" || substr(trim($_POST["txtsnphone1"]),0,s)=="00"){
		if(substr(trim($_POST["txtsnphone1"]),0,1)=="0"){
			$phone_number = substr(trim($_POST["txtsnphone1"]),1);
		}elseif(substr(trim($_POST["txtsnphone1"]),0,2)=="00"){
			$phone_number = substr(trim($_POST["txtsnphone1"]),2);
		}
		if(filter_var($phone_number,FILTER_VALIDATE_INT)==false){
			echo "Error: Invalid number for Phone Number";
			exit;
		}else{
			$phone1 = trim($_POST["txtsnphone1"]);
		}
	}else{
		echo "Error: Invalid number for Phone Number";
		exit;
	}
}else{
	$phone1 = trim($_POST["txtsnphone1"]);
}

//Check if Phone 2 is submitted
if(isset($_POST["txtsnphone2"])==true && trim($_POST["txtsnphone2"])!=""){
	$phone2 = trim($_POST["txtsnphone2"]);
	if(filter_var($phone2,FILTER_VALIDATE_INT)==false){
		echo "Error: Invalid number for Phone 2";
		exit;
	}
}else{
	$phone2 = "NULL";
}

if(isset($_POST["sn_user_id"])==false || trim($_POST["sn_user_id"])==""){
	echo "Error: Required parameter 'sn_user_id' not passed";
	exit;
}elseif(filter_var(trim($_POST["sn_user_id"]),FILTER_VALIDATE_INT)==false){
	echo "Error: Invalid sn_user_id number";
	exit;
}else{
	$user_id = trim($_POST["sn_user_id"]);
}

$ipaddress = $_SERVER["REMOTE_ADDR"];

//Create objects
$assinged_dids = new Assigned_DID();
if(is_object($assinged_dids)==false){
	echo "Error: Failed to create Assigned_DID object.";
	exit;
}

$leads = new Leads();
if(is_object($leads)==false){
	echo "Error: Failed to create Leads object.";
	exit;
}

$lead_accounts_obj = new Lead_accounts();	
if(is_object($lead_accounts_obj)==false){
	echo "Error: Failed to create Lead_accounts object.";
	exit;
}

$user_obj = new User();
if(is_object($user_obj)==false){
	echo "Error: Failed to create User object.";
	exit;
}

$user = $user_obj->getUserInfoById($user_id);
if(is_array($user)==false && substr($user,0,6)=="Error:"){
	echo $user;
	exit;
}
if(is_array($user)==false && $user==0){
	echo "Error: No user account exists with user id $user_id.";
	exit;
}
if(is_array($user)===false){
	echo "Error: Unexpected result returned - User is not an array.";
	exit;
}
$client_email = trim($user["email"]);

//Check if the submitted User ID is a lead plan user and the lead plan is currently acive
$lead_accounts = $lead_accounts_obj->get_lead_accounts($user_id);
if(is_array($lead_accounts)==false && substr($lead_accounts,0,6)=="Error:"){
	echo $lead_accounts;
	exit;
}
if(is_array($lead_accounts)==false && $lead_accounts==0){
	echo "Error: No user account exists.";
	exit;
}
if(is_array($lead_accounts)===false){
	echo "Error: Unexpected result returned - Lead Accounts is not an array.";
	exit;
}
if($lead_accounts["secs_to_expire"] <= 0){
	echo "Error: Customer plan expired.";
	exit;
}

$lead_reporting_email = trim($lead_accounts["lead_reporting_email"]);
if($lead_reporting_email==""){
	$lead_reporting_email = $client_email;//If no lead reporting email set up, then fall back on client email id
}

//Now assign a lead DID Number which is not in use and not expired - if such a thing is available
$lead_did = $assinged_dids->assign_lead_did($user_id);
if(is_array($lead_did)==false && substr($lead_did,0,6)=="Error:"){
	echo $lead_did;
	exit;
}
if(is_array($lead_did)==false && $lead_did==0){
	echo "Sorry! No DID Numbers available at this time. Please check back soon.";
	//Send email to the client from here
	$status = send_no_dids_email_to_client($user_id,$lead_reporting_email,$name,$email,$phone1,$phone2,$ipaddress);
	exit;
}
if(is_array($lead_did)===false){
	echo "Error: Unexpected result returned - Lead DID is not an array.";
	exit;
}

//Default the forwarding number to AU
if(substr($phone1,0,4)=="0061"){
	$forwarding_number = substr($phone1,2);
}elseif(substr($phone1,0,2)=="61"){
	$forwarding_number = $phone1;
}elseif(substr($phone1,0,1)=="0"){
	$forwarding_number = "61".substr($phone1,1);
}else{
	$forwarding_number = "61".$phone1;
}

//Now insert the lead in database table
$lead_id = $leads->insert_lead($lead_did["id"], $name, $email, $forwarding_number, $phone2, $ipaddress);
if(substr($status,0,6)=="Error:"){
	echo $lead_id;
	exit;
}

//Now mark the lead as in use
$status = $assinged_dids->mark_lead_did_in_use($lead_did["id"]);
if(is_bool($status) && $status===false){
	//Delete the lead record since the DID could not be marked as in use
	$leads->delete_lead($lead_id);
}elseif(is_bool($status)==false && substr($status,0,6)=="Error:"){
	echo $status;
	exit;
}elseif(is_bool($status) && $status===true){
	//Now send the lead received email
	$status = send_lead_received_email($user_id,$lead_reporting_email,$name,$email,$phone1,$phone2,$ipaddress,$lead_did["did_number"]);
}else{
	echo "Error: Unexpected result while marking the lead as in use. Result is: ".$status;
	exit;
}

//Now update the forwarding number and build the ast_rt_extensions record for the DID to work
$status = $assinged_dids->update_route($lead_did["id"],$forwarding_number);
if(is_bool($status)==false && substr($status,0,6)=="Error:"){
	echo $status;
	exit;
}elseif(is_bool($status)==true && $status===true){
	//Now build or rebuild the record in ast_rt_extension
	$status = dialplan_did_route("vssp", $lead_did["did_number"], $forwarding_number, $user_id);
	if($status==false){
		echo "Error: Failed to create dialplan route.";
		exit;
	}
}else{
	echo "Error: Unexpected results while updating forwarding number of DID. ".$status;
	exit;
}

//Now send the response back to the browser

if(substr($lead_did['did_number'],0,3)=="070" || substr($lead_did['did_number'],0,3)=="087"){
	$did_number = "44".substr($lead_did["did_number"],1);
}else{
	$did_number = $lead_did["did_number"];
}

echo "<h4 style='text-align:center;'>Thanks for submitting your information.</h4><h4 style='text-align:center;'>Your DID Number is: </h4><h3 style='text-align:center;'>+".$did_number."</h3>";
exit;

function send_lead_received_email($user_id,$lead_reporting_email,$name,$email,$phone1,$phone2,$ipaddress,$did_number){
	
	$email_message = "A new lead received with the following details:<br /><br />";
	$email_message .= "Name: ".$name."<br />";
	$email_message .= "Email: ".$email."<br />";
	$email_message .= "Phone(Forwards to): ".$phone1."<br />";
	$email_message .= "DID Number Assigned: ".$did_number."<br />";
	//$email_message .= "Phone 2: ".$phone2."<br />";
	$email_message .= "IP Address: ".$ipaddress."<br /><br />";
	$email_message .= "Kind Regards,<br /><br />";
	$email_message .= "Sticky Number,<br />Customer Support<br />";
	$email_message .= SERVER_WEB_NAME."<br />";
	
	$to_email = $lead_reporting_email;
	$subject_line = "New Lead Received on StickyNumber";
	$from_email = ACCOUNT_CHANGE_EMAIL;
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
	$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
	$status = mail($to_email, $subject_line, $email_message, $headers, $addl_params);
	if($status==true){
		$status = create_client_notification($user_id, $from_email, $to_email, $subject_line, $email_message);
		if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
			echo $status;
			return false;
		}else{
			return true;
		}
	}
	
}

function send_no_dids_email_to_client($user_id,$lead_reporting_email,$name,$email,$phone1,$phone2,$ipaddress){
	
	$email_message = "<font color='red'>NO DIDs AVAILABLE to assign to the new lead received with the following details:</font><br /><br />";
	$email_message .= "Name: ".$name."<br />";
	$email_message .= "Email: ".$email."<br />";
	$email_message .= "Phone(Forwards to): ".$phone1."<br />";
	$email_message .= "DID Number Assigned: NOT AVAILABLE<br />";
	//$email_message .= "Phone 2: ".$phone2."<br />";
	$email_message .= "IP Address: ".$ipaddress."<br /><br />";
	$email_message .= "<b>Please ensure that you have enough DID numbers available in your account.</b><br /><br />";
	$email_message .= "Kind Regards,<br /><br />";
	$email_message .= "Sticky Number,<br />Customer Support<br />";
	$email_message .= SERVER_WEB_NAME."<br />";
	
	$to_email = $lead_reporting_email;
	$subject_line = "NO DID AVAILABLE to Assign to New Lead Received on StickyNumber";
	$from_email = ACCOUNT_CHANGE_EMAIL;
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
	$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
	$status = mail($to_email, $subject_line, $email_message, $headers, $addl_params);
	if($status==true){
		$status = create_client_notification($user_id, $from_email, $to_email, $subject_line, $email_message);
		if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
			echo $status;
			return false;
		}else{
			return true;
		}
	}
	
}