<?php
/*
    API interface for StickyNumber.com.au web application
 
    Input:
 
        1. Required paramters based on method are to be posted. (GET method is not supported).
        2. Only HTTPS posts are accepted in LIVE server environment.
        3. createAccount method does not need username and password inputs. All other methods need username and password.
 
    Output: A JSON object response over HTTP in local or DEV server and HTTPS in Live server
 
*/
 
// --- Step 1: Initialize variables and functions
 
/**
 * Deliver HTTP Response
 * @param string $api_response The desired HTTP response data
 * @return void
 **/
function deliver_response($api_response){
 
    // Set HTTP Response
    header('HTTP/1.1 200 OK');

    // Set HTTP Response Content Type
    header('Content-Type: application/json; charset=utf-8');
  
    // Format data into a JSON response
    $json_response = json_encode($api_response);

    // Deliver formatted data
    echo $json_response;
    
    // End script process
    exit;
 
}

// Set default HTTP response of 'Unknown Error'
$response['code'] = 300;
$response['status'] = 'Unknown Error';
$response['data'] = NULL;

// Define whether an HTTPS connection is required
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] != "dev.stickynumber.com" && $_SERVER["HTTP_HOST"] != "dev.stickynumber.com.au" &&
	(substr($_SERVER["HTTP_HOST"],-19)=="stickynumber.com.au" || substr($_SERVER["HTTP_HOST"],-16)=="stickynumber.com")){
	$HTTPS_required = TRUE;
}else{
	$HTTPS_required = FALSE;
}

if( $HTTPS_required && $_SERVER['HTTPS'] != 'on' ){
    $response['code'] = 2;
    $response['status'] = 'HTTPS Required';
    $response['data'] = NULL;
    deliver_response($response);
}

// --- Process Request
if(isset($_POST["method"]) && trim($_POST["method"])!=""){
	$method = $_POST["method"] ;
}else{
	$response['code'] = 3;
    $response['status'] = "API method parameter missing";
    $response['data'] = NULL;
    deliver_response($response);
}

// Define whether user authentication is required
$authentication_required = FALSE;//Initialize to FALSE
if($method=="changePassword" || 
	$method=="deleteCreditCard" || 
	$method=="deletePrimaryBackupSettingOnCC" || 
	$method=="deleteVoiceMessage" || 
	$method=="disableVoiceMail" || 
	$method=="enableVoiceMail" ||
	$method=="getAccountCredit" || 
	$method=="getAccountDetails" || 
	$method=="getCallRecords" || 
	$method=="getCallsCount" || 
	$method=="getCreditCards" || 
	$method=="getCreditCardsCount" || 
	$method=="getCreditCardsDetails" || 
	$method=="getDIDs" || 
	$method=="getDIDsCount" || 
	$method=="getInvoices" || 
	$method=="getInvoicesCount" || 
	$method=="getNewVoiceMailsCount" || 
	$method=="getNewVoiceMailsPerMailBoxCount" || 
	$method=="getTotalVoiceMailsPerMailBox" || 
	$method=="getVoiceMailBoxesCount" ||
	$method=="getVoiceMailBoxesInfo" || 
	$method=="getVoiceMailsInfo" || 
	$method=="getVoiceMessage" || 
	$method=="makeBackupCC" || 
	$method=="makePrimaryCC" || 
	$method=="payByAccountCreditNewDID" ||
	$method=="payByAccountCreditRenewDID" ||
	$method=="payByAccountCreditRestoreDID" || 
	$method=="payByCCAccountCredit" ||
	$method=="payByCCNewDID" || 
	$method=="payByCCRenewDID" || 
	$method=="payByCCRestoreDID" || 
	$method=="payByExistingCCAccountCredit" || 
	$method=="payByExistingCCNewDID" || 
	$method=="payByExistingCCRenewDID" || 
	$method=="payByExistingCCRestoreDID" || 
	$method=="payByPayPalAccountCredit" ||
	$method=="payByPayPalNewDID" ||
	$method=="payByPayPalRenewDID" ||
	$method=="payByPayPalRestoreDID" || 
	$method=="saveVoiceMessage" || 
	$method=="setVoiceMessageAsRead" || 
	$method=="unsaveVoiceMessage" || 
	$method=="updateCreditCard" ||
	$method=="updateVoiceMailRings"){
	$authentication_required = TRUE;
}

$user_logins_id = "";

//Required include files
require("../dashboard/includes/config.inc.php");
require("../dashboard/includes/db.inc.php");

//Required Classes
require("../dashboard/classes/Access.class.php");
require("../dashboard/classes/Assigned_DID.class.php");
require("../dashboard/classes/AsteriskSQLiteCDR.class.php");
require("../dashboard/classes/AST_RT_Extensions.class.php");
require("../dashboard/classes/CC_Details.class.php");
require("../dashboard/classes/Did_Cities.class.php");
require("../dashboard/classes/Did_Countries.class.php");
require("../dashboard/classes/Did_Plans.class.php");
require("../dashboard/classes/DidwwAPI.class.php");
require("../dashboard/classes/Country.class.php");
require("../dashboard/classes/Member_credits.class.php");
require("../dashboard/classes/Payments_received.class.php");
require("../dashboard/classes/SystemConfigOptions.class.php");
require("../dashboard/classes/Unassigned_087DID.class.php");
require("../dashboard/classes/User.class.php");
require("../dashboard/classes/VoiceMail.class.php");

//Required Libraries
require("../dashboard/libs/user_maintenance.lib.php");
require("../dashboard/libs/didww.lib.php");

if( $authentication_required ){
 
    if( empty($_POST['username']) || empty($_POST['password']) ){
        $response['code'] = 4;
        $response['status'] = "Required parameter username and/or password not posted";
        $response['data'] = NULL;
        deliver_response($response);
    }else{
    	//Validate userid and password
    	$user = new User();
    	if(is_object($user)==false){
			$response['code'] = 5;
	    	$response['status'] = "Failed to create User object";
	    	$response['data'] = NULL;
	    	deliver_response($response);
		}else{
			if($user->isValidLogin(trim($_POST['username']),$_POST['password'],'AU')===false){
		        $response['code'] = 6;
		        $response['status'] = "Invalid Login Credentials";
		        $response['data'] = NULL;
		        deliver_response($response);
			}else{
				$user_info = $user->getUserInfo(trim($_POST['username']),'AU');
				$user_logins_id = $user_info["id"];
				$user_name = $user_info["name"];
			}
		}
    }
}

switch($method){
	case "changePassword":
		$user = new User();
		if(is_object($user)==false){
			$response['code'] = 7;
	    	$response['status'] = "Failed to create User object";
	    	$response['data'] = NULL;
	    	break;
		}
		if(isset($_POST['new_password'])==false || $_POST['new_password']==""){
			$response['code'] = 8;
	    	$response['status'] = "Required parameter 'new_password' not posted";
	    	$response['data'] = NULL;
	    	break;
		}
		$status = $user->updatePassword($user_logins_id,$_POST['new_password'],'AU');
		if($status==true){
			account_change_mailer(trim($_POST['username']), $user_name, trim($_POST['new_password']), 'update');
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = "Password successfully changed.";
		}else{
			$response['code'] = 9;
	    	$response['status'] = "Failed to change password";
	    	$response['data'] = NULL;
	    	break;
		}
		break;
	case "createAccount":
		$user = new User();
		if(is_object($user)==false){
			$response['code'] = 10;
	    	$response['status'] = "Failed to create User object";
	    	$response['data'] = NULL;
	    	break;
		}
		if(isset($_POST['username'])==false || trim($_POST['username'])==""){
			$response['code'] = 11;
	    	$response['status'] = "Required parameter 'username' not posted";
	    	$response['data'] = NULL;
	    	break;
		}
		if(isset($_POST['password'])==false || $_POST['password']==""){
			$response['code'] = 12;
	    	$response['status'] = "Required parameter 'password' not posted";
	    	$response['data'] = NULL;
	    	break;
		}
		if(isset($_POST['name'])==false || trim($_POST['name'])==""){
			$response['code'] = 13;
	    	$response['status'] = "Required parameter 'name' not posted(Customer Name missing)";
	    	$response['data'] = NULL;
	    	break;
		}
		if($user->checkEmailExists(trim($_POST['username']),"AU")){
			$response['code'] = 14;
	    	$response['status'] = "This Login Email (username) already exists in the system";
	    	$response['data'] = NULL;
	    	break;
		}
		$insert_id = $user->insertUser(trim($_POST['name']), trim($_POST['username']), $_POST['password'], "AU");
		if($insert_id!=0){
			$uniquecode =   md5(trim($_POST['username']).$_POST['password']);//This is the same code used while inserting user_logins record
	        account_verify_email(trim($_POST['username']),$uniquecode,trim($_POST['name']));
			account_change_mailer(trim($_POST['username']), trim($_POST['name']), trim($_POST['password']));
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = "User ID: ".$insert_id;
		}else{
			$response['code'] = 15;
	    	$response['status'] = "Failed to create user account";
	    	$response['data'] = NULL;
	    	break;
		}
		break;
	case "deleteCreditCard":
		if(isset($_POST['cc_id'])==false || trim($_POST['cc_id'])==""){
	    	$response['code'] = 16;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID not posted.";
		    break;
	    }
		if(is_numeric(trim($_POST['cc_id']))==false){
	    	$response['code'] = 17;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID (cc_id) must be numeric.";
		    break;
	    }
		$cc_details = new CC_Details();
    	if(is_object($cc_details)==false){
    		$response['code'] = 18;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create CC_Details object.";
    	}
		$status = $cc_details->deleteCCDetails(trim($_POST['cc_id']),$user_logins_id);
		if($status==true){
	    	$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Credit Card Deleted";
			break;
		}else{
			$response['code'] = 19;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to delete credit card.";
	   		break;
		}
		break;
	case "deletePrimaryBackupSettingOnCC":
		if(isset($_POST['cc_id'])==false || trim($_POST['cc_id'])==""){
	    	$response['code'] = 20;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID (cc_id) not posted.";
		    break;
	    }
	    if(is_numeric(trim($_POST['cc_id']))==false){
	    	$response['code'] = 21;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID (cc_id) must be numeric.";
		    break;
	    }
		$cc_details = new CC_Details();
    	if(is_object($cc_details)==false){
    		$response['code'] = 22;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create CC_Details object.";
    	}
		$status = $cc_details->removePrimaryBackupCC(trim($_POST['cc_id']),$user_logins_id);
		if(substr($status,0,6)!="Error:"){
	    	$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Primary or Backup status deleted on Credit Card";
			break;
		}else{
			$response['code'] = 23;
	    	$response['status'] = "failed";
	   		$response['data'] = $status;
	   		break;
		}
		break;
	case "deleteVoiceMessage":
		if(isset($_POST["voice_message_id"])==false || trim($_POST["voice_message_id"])==""){
			$response['code'] = 24;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter voice_message_id not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 25;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMsgEntitlements(trim($_POST["voice_message_id"]),$user_logins_id);
		if($status===false){
			$response['code'] = 26;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This voicemail message does not belong to one of your mailbox.";
	   		break;
		}
		$status = $voicemail->delete_voice_message(trim($_POST["voice_message_id"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 27;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to save voice mail message.";
	   		break;
		}else{
			$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Saved voice mail message.";
			break;
		}
		break;
	case "disableVoiceMail":
		if(isset($_POST["virtual_number"])==false || trim($_POST["virtual_number"])==""){
			$response['code'] = 28;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter virtual_number not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 29;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMailboxEntitlements(trim($_POST["virtual_number"]),$user_logins_id);
		if($status===false){
			$response['code'] = 30;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This mailbox does not belong to one of your user account.";
	   		break;
		}
		$status = $voicemail->enable_disable_mailbox(trim($_POST["virtual_number"]),0);
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 31;
	    	$response['status'] = "failed";
	   		$response['data'] = $status;
	   		break;
		}else{
			$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Voice Mail disabled.";
			break;
		}
		break;
	case "enableVoiceMail":
		if(isset($_POST["virtual_number"])==false || trim($_POST["virtual_number"])==""){
			$response['code'] = 32;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter virtual_number not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 33;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMailboxEntitlements(trim($_POST["virtual_number"]),$user_logins_id);
		if($status===false){
			$response['code'] = 34;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This mailbox does not belong to one of your user account.";
	   		break;
		}
		$status = $voicemail->enable_disable_mailbox(trim($_POST["virtual_number"]),1);
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 35;
	    	$response['status'] = "failed";
	   		$response['data'] = $status;
	   		break;
		}else{
			$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Voice Mail enabled.";
			break;
		}
		break;
	case "forwardingCountries":
		$countries = new Country();
		if(is_object($countries)){
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $countries->getAllCountry();
		}else{
			$response['code'] = 36;
	    	$response['status'] = "Failed to create Country object";
	    	$response['data'] = NULL;
		}
		break;
	case "getAccountCredit":
		$member_credits = new Member_credits();
		if(is_object($member_credits)==false){
			$response['code'] = 37;
	    	$response['status'] = "Failed to create Member_credits object";
	    	$response['data'] = NULL;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $member_credits->getTotalCredits($user_logins_id);
	    break;
	case "getAccountDetails":
		$user = new User();
		if(is_object($user)==false){
			$response['code'] = 38;
	    	$response['status'] = "Failed to create User object";
	    	$response['data'] = NULL;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = array("member_id"=>"M".($user_logins_id+10000),"name"=>$user_name);
		break;
	case "getCallRecords":
		if(isset($_POST["records_per_page"])==false || trim($_POST["records_per_page"])=="" || is_numeric(trim($_POST["records_per_page"]))==false){
			$response['code'] = 39;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter records_per_page is not posted.";
	    	break;
		}
		if(isset($_POST["start_rec"])==false || trim($_POST["start_rec"])=="" || is_numeric(trim($_POST["start_rec"]))==false){
			$response['code'] = 40;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter start_rec is not posted.";
	    	break;
		}
		$cdr = new AsteriskSQLiteCDR();
		if(is_object($cdr)==false){
			$response['code'] = 41;
	    	$response['status'] = "Failed to create AsteriskSQLiteCDR object";
	    	$response['data'] = NULL;
	    	break;
		}
		$status = $cdr->for_page_range($user_logins_id, trim($_POST["records_per_page"]), trim($_POST["start_rec"]));
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $status;
		break;
	case "getCallsCount":
		$cdr = new AsteriskSQLiteCDR();
		if(is_object($cdr)==false){
			$response['code'] = 42;
	    	$response['status'] = "Failed to create AsteriskSQLiteCDR object";
	    	$response['data'] = NULL;
	    	break;
		}
		$status = $cdr->total_calls_for_user_id($user_logins_id);
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $status;
		break;
	case "getCreditCards":
		$cc_details = new CC_Details();
		if(is_object($cc_details)==false){
			$response['code'] = 43;
	    	$response['status'] = "Failed to create CC_Details object";
	    	$response['data'] = NULL;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $cc_details->getCCLastFourOfUser($user_logins_id);
	    break;
	case "getCreditCardsCount":
		$cc_details = new CC_Details();
		if(is_object($cc_details)==false){
			$response['code'] = 44;
	    	$response['status'] = "Failed to create CC_Details object";
	    	$response['data'] = NULL;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $cc_details->get_cc_count($user_logins_id);
	    break;
	case "getCreditCardsDetails":
		if(isset($_POST["records_per_page"])==false || trim($_POST["records_per_page"])=="" || is_numeric(trim($_POST["records_per_page"]))==false){
			$response['code'] = 45;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter records_per_page is not posted.";
	    	break;
		}
		if(isset($_POST["start_rec"])==false || trim($_POST["start_rec"])=="" || is_numeric(trim($_POST["start_rec"]))==false){
			$response['code'] = 46;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter start_rec is not posted.";
	    	break;
		}
		$cc_details = new CC_Details();
		if(is_object($cc_details)==false){
			$response['code'] = 47;
	    	$response['status'] = "Failed to create CC_Details object";
	    	$response['data'] = NULL;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $cc_details->for_page_range($user_logins_id, trim($_POST["records_per_page"]), trim($_POST["start_rec"]));
	    break;
	case "getDIDs":
		if(isset($_POST["records_per_page"])==false || trim($_POST["records_per_page"])=="" || is_numeric(trim($_POST["records_per_page"]))==false){
			$response['code'] = 48;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter records_per_page is not posted.";
	    	break;
		}
		if(isset($_POST["start_rec"])==false || trim($_POST["start_rec"])=="" || is_numeric(trim($_POST["start_rec"]))==false){
			$response['code'] = 49;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter start_rec is not posted.";
	    	break;
		}
		$assigned_did = new Assigned_DID();
		if(is_object($assigned_did)==false){
			$response['code'] = 50;
	    	$response['status'] = "Failed to create Assigned_DID object";
	    	$response['data'] = NULL;
	    	break;
		}
		$status = $assigned_did->getAUUserDIDsForPage($user_logins_id,trim($_POST["start_rec"]),trim($_POST["records_per_page"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 51;
	    	$response['status'] = "Failed";
	    	$response['data'] = $status;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $status;
		break;
	case "getDIDsCount":
		$assigned_did = new Assigned_DID();
		if(is_object($assigned_did)==false){
			$response['code'] = 52;
	    	$response['status'] = "Failed to create Assigned_DID object";
	    	$response['data'] = NULL;
	    	break;
		}
		$status = $assigned_did->getUserDIDsCount($user_logins_id);
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 53;
	    	$response['status'] = "Failed";
	    	$response['data'] = $status;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $status;
		break;
	case "getInvoices":
		if(isset($_POST["records_per_page"])==false || trim($_POST["records_per_page"])=="" || is_numeric(trim($_POST["records_per_page"]))==false){
			$response['code'] = 54;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter records_per_page is not posted.";
	    	break;
		}
		if(isset($_POST["start_rec"])==false || trim($_POST["start_rec"])=="" || is_numeric(trim($_POST["start_rec"]))==false){
			$response['code'] = 55;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter start_rec is not posted.";
	    	break;
		}
		$payments_received = new Payments_received();
		if(is_object($payments_received)==false){
			$response['code'] = 56;
	    	$response['status'] = "Failed to create Payments_received object";
	    	$response['data'] = NULL;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $payments_received->for_page_range($user_logins_id,trim($_POST["records_per_page"]),trim($_POST["start_rec"]));
		break;
	case "getInvoicesCount":
		$payments_received = new Payments_received();
		if(is_object($payments_received)==false){
			$response['code'] = 57;
	    	$response['status'] = "Failed to create Payments_received object";
	    	$response['data'] = NULL;
	    	break;
		}
		$response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $payments_received->get_payments_count($user_logins_id);
		break;
	case "getPlanMinutesNewDID":
		if(isset($_POST["country_iso"])==false || trim($_POST["country_iso"])==""){
	    	$response['code'] = 58;
	    	$response['status'] = "Required parameter 'country_iso' not posted";
	    	$response['data'] = NULL;
	    	break;
	    }
	    if(isset($_POST["city_prefix"])==false || trim($_POST["city_prefix"])==""){
	    	$response['code'] = 59;
	    	$response['status'] = "Required parameter 'city_prefix' not posted";
	    	$response['data'] = NULL;
	    	break;
	    }
		if(isset($_POST["fwd_number"])==false || trim($_POST["fwd_number"])==""){
	    	$response['code'] = 60;
	    	$response['status'] = "Required parameter 'fwd_number' not posted";
	    	$response['data'] = NULL;
	    	break;
	    }
	    $status = getPlanMinutesNewDID(trim($_POST["fwd_number"]), trim($_POST["country_iso"]), trim($_POST["city_prefix"]));
	    if(substr($status,0,6)=="Error:"){
	    	$response['code'] = 61;
	    	$response['status'] = $status;
	    	$response['data'] = NULL;
	    	break;
	    }
	    $response['code'] = 1;
	    $response['status'] = "success";
	    $response['data'] = $status;
	    break;
	case "getPlans":
		$did_plans = new Did_Plans();
		if(is_object($did_plans)){
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $did_plans->getDIDPlans();
		}else{
			$response['code'] = 62;
	    	$response['status'] = "Failed to create Did_Plans object";
	    	$response['data'] = NULL;
		}
		break;
	case "getNewVoiceMailsCount":
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 63;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->get_total_unread_mails_for_user($user_logins_id);
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 64;
	    	$response['status'] = $status;
	    	$response['data'] = NULL;
	    	break;
		}else{
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $status;
	    	break;
		}
		break;
	case "getNewVoiceMailsPerMailBoxCount":
		if(isset($_POST["virtual_number"])==false || trim($_POST["virtual_number"])==""){
			$response['code'] = 65;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter virtual_number not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 66;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMailboxEntitlements(trim($_POST["virtual_number"]),$user_logins_id);
		if($status===false){
			$response['code'] = 67;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This mailbox does not belong to one of your user account.";
	   		break;
		}
		$status = $voicemail->get_unread_mail(trim($_POST["virtual_number"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 68;
	    	$response['status'] = $status;
	    	$response['data'] = NULL;
	    	break;
		}else{
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $status;
	    	break;
		}
		break;
	case "getTotalVoiceMailsPerMailBox":
		if(isset($_POST["virtual_number"])==false || trim($_POST["virtual_number"])==""){
			$response['code'] = 69;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter virtual_number not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 70;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMailboxEntitlements(trim($_POST["virtual_number"]),$user_logins_id);
		if($status===false){
			$response['code'] = 71;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This mailbox does not belong to one of your user account.";
	   		break;
		}
		$status = $voicemail->get_total_mail(trim($_POST["virtual_number"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 72;
	    	$response['status'] = $status;
	    	$response['data'] = NULL;
	    	break;
		}else{
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $status;
	    	break;
		}
		break;
	case "getVoiceMailBoxesCount":
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 73;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->total_calls_for_user_id($user_logins_id);
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 74;
	    	$response['status'] = $status;
	    	$response['data'] = NULL;
	    	break;
		}else{
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $status;
	    	break;
		}
		break;
	case "getVoiceMailBoxesInfo":
		if(isset($_POST["records_per_page"])==false || trim($_POST["records_per_page"])=="" || is_numeric(trim($_POST["records_per_page"]))==false){
			$response['code'] = 75;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter records_per_page is not posted.";
	    	break;
		}
		if(isset($_POST["start_rec"])==false || trim($_POST["start_rec"])=="" || is_numeric(trim($_POST["start_rec"]))==false){
			$response['code'] = 76;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter start_rec is not posted.";
	    	break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 77;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->for_page_range($user_logins_id,trim($_POST["records_per_page"]),trim($_POST["start_rec"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 78;
	    	$response['status'] = $status;
	    	$response['data'] = NULL;
	    	break;
		}else{
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $status;
	    	break;
		}
		break;
	case "getVoiceMailsInfo":
		if(isset($_POST["virtual_number"])==false || trim($_POST["virtual_number"])==""){
			$response['code'] = 79;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter virtual_number not posted.";
	   		break;
		}
		if(isset($_POST["records_per_page"])==false || trim($_POST["records_per_page"])=="" || is_numeric(trim($_POST["records_per_page"]))==false){
			$response['code'] = 80;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter records_per_page is not posted.";
	    	break;
		}
		if(isset($_POST["start_rec"])==false || trim($_POST["start_rec"])=="" || is_numeric(trim($_POST["start_rec"]))==false){
			$response['code'] = 81;
	    	$response['status'] = "failed";
	    	$response['data'] = "Required parameter start_rec is not posted.";
	    	break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 82;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMailboxEntitlements(trim($_POST["virtual_number"]),$user_logins_id);
		if($status===false){
			$response['code'] = 83;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This mailbox does not belong to one of your user account.";
	   		break;
		}
		$status = $voicemail->total_mails(trim($_POST["virtual_number"]),trim($_POST["records_per_page"]),trim($_POST["start_rec"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 84;
	    	$response['status'] = $status;
	    	$response['data'] = NULL;
	    	break;
		}else{
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $status;
	    	break;
		}
		break;
	case "getVoiceMessage":
		if(isset($_POST["voice_message_id"])==false || trim($_POST["voice_message_id"])==""){
			$response['code'] = 85;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter voice_message_id not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 86;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMsgEntitlements($_POST["voice_message_id"],$user_logins_id);
		if($status===false){
			$response['code'] = 87;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This message does not belong to your mailbox.";
	   		break;
		}
		$status = $voicemail->get_file(trim($_POST["voice_message_id"]),"mp3");
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 88;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to save voice mail message.";
	   		break;
		}else{
			$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Saved voice mail message.";
			break;
		}
		break;
	case "makeBackupCC":
		if(isset($_POST['cc_id'])==false || trim($_POST['cc_id'])==""){
	    	$response['code'] = 89;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID (cc_id) not posted.";
		    break;
	    }
		if(is_numeric(trim($_POST['cc_id']))==false){
	    	$response['code'] = 90;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID (cc_id) must be numeric.";
		    break;
	    }
		$cc_details = new CC_Details();
    	if(is_object($cc_details)==false){
    		$response['code'] = 91;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create CC_Details object.";
    	}
		$status = $cc_details->makeBackupCC(trim($_POST['cc_id']),$user_logins_id);
		if($status==true){
	    	$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Backup status set on Credit Card";
			break;
		}else{
			$response['code'] = 92;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to set Backup status on credit card.";
	   		break;
		}
		break;
	case "makePrimaryCC":
		if(isset($_POST['cc_id'])==false || trim($_POST['cc_id'])==""){
	    	$response['code'] = 93;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID (cc_id) not posted.";
		    break;
	    }
		if(is_numeric(trim($_POST['cc_id']))==false){
	    	$response['code'] = 94;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID (cc_id) must be numeric.";
		    break;
	    }
		$cc_details = new CC_Details();
    	if(is_object($cc_details)==false){
    		$response['code'] = 95;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create CC_Details object.";
    	}
		$status = $cc_details->makePrimaryCC(trim($_POST['cc_id']),$user_logins_id);
		if($status==true){
	    	$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Primary status set on Credit Card";
			break;
		}else{
			$response['code'] = 96;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to set Primary status on credit card.";
	   		break;
		}
		break;
	case "numberCities":
		if(isset($_POST['country_iso'])==false || trim($_POST['country_iso'])==""){
			$response['code'] = 97;
	    	$response['status'] = "Required parameter 'country_iso' not posted";
	    	$response['data'] = NULL;
		}else{
			$did_cities = new Did_Cities();
			if(is_object($did_cities)){
				$response['code'] = 1;
		    	$response['status'] = "success";
		    	$response['data'] = $did_cities->getAvailableCities($_POST['country_iso']);
			}else{
				$response['code'] = 98;
		    	$response['status'] = "Failed to create Did_Cities object";
		    	$response['data'] = NULL;
			}
		}
		break;
	case "numberCountries":
		$did_countries = new Did_Countries();
		if(is_object($did_countries)){
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = $did_countries->getAvailableCountries();
		}else{
			$response['code'] = 99;
	    	$response['status'] = "Failed to create Did_Countries object";
	    	$response['data'] = NULL;
		}
		break;
	case "payByAccountCreditNewDID":
		$status = validateNewDIDInputs($user_logins_id, trim($_POST["country_iso"]), trim($_POST["city_prefix"]), trim($_POST["fwd_number"]), trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]));
		if($status!==true){
	    	$response['code'] = 100;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = chargeAccountCredit($user_logins_id,trim($_POST["choosen_plan"]));
	    if($status!==true){
	    	$response['code'] = 101;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "new_did", "AcctCredit", "", trim($_POST["country_iso"]), trim($_POST["city_prefix"]), trim($_POST["fwd_number"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 102;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByAccountCreditRenewDID":
		$status = validateRenewOrRestoreInputs($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
		if($status!==true){
	    	$response['code'] = 103;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = chargeAccountCredit($user_logins_id,trim($_POST["choosen_plan"]));
	    if($status!==true){
	    	$response['code'] = 104;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "renew_did", "AcctCredit", trim($_POST["did_id"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 105;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByAccountCreditRestoreDID":
		$status = validateRenewOrRestoreInputs($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
		if($status!==true){
	    	$response['code'] = 106;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = chargeAccountCredit($user_logins_id,trim($_POST["choosen_plan"]));
	    if($status!==true){
	    	$response['code'] = 107;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "restore_did", "AcctCredit", trim($_POST["did_id"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 108;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByCCAccountCredit":
		$member_credits = new Member_credits();
		if(is_object($member_credits)==false){
			$response['code'] = 109;
	    	$response['status'] = "Failed to create Member_credits object";
	    	$response['data'] = NULL;
	    	break;
		}
		$status = validateCreditCardInputs($user_logins_id,$_POST['street'],$_POST['city'],$_POST['country'],$_POST['postal_code'],$_POST['pay_method'],$_POST['card_type'],$_POST['card_number'],$_POST['expiry_month'],$_POST['expiry_year'],$_POST['security_cvv2']);
		if($status!==true){
	    	$response['code'] = 110;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
	    if(isset($_POST['credit_amount'])==false || trim($_POST['credit_amount'])==""){
	    	$response['code'] = 111;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Required parameter credit_amount not posted.";
		    break;
	    }
		if(is_numeric($_POST['credit_amount'])==false){
	    	$response['code'] = 112;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Value of parameter credit_amount must be numeric.";
		    break;
	    }
	    $blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 113;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the account_credit amount to credit card.";
			break;
		}
		$status = autoSaveCC($user_logins_id,trim($_POST['street']),trim($_POST['city']),trim($_POST['state']),trim($_POST['country']),trim($_POST['postal_code']),trim($_POST['card_type']),trim($_POST['pay_method']),trim($_POST['card_number']),trim($_POST['expiry_month']),trim($_POST['expiry_year']),trim($_POST['security_cvv2']));
		//Failure in autoSaveCC is NOT a critical issue
		$status = $member_credits->addMemberCredits($user_logins_id,$_POST['credit_amount'],$user_logins_id);
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 114;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}else{
			$payments_received = new Payments_received();
			if(is_object($payments_received)==false){
				$response['code'] = 115;
		    	$response['status'] = "Failed to create Payments_received object";
		    	$response['data'] = NULL;
		    	break;
			}
			//Now add a record in payments_received so that invoices are generated from that.
			$payments_received->addPayment($user_logins_id, 0, 'U', trim($_POST['credit_amount']),"CreditCard", trim($_POST['card_type']), trim($_POST['card_number']), $_SERVER['REMOTE_ADDR'], "NULL", "NULL", "NULL", "NULL", "NULL", 0, 0, "NULL");
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = "Account Credit successfully added.";
		    break;
		}
		break;
	case "payByCCNewDID":
		$status = validateCreditCardInputs($user_logins_id,$_POST['street'],$_POST['city'],$_POST['country'],$_POST['postal_code'],$_POST['pay_method'],$_POST['card_type'],$_POST['card_number'],$_POST['expiry_month'],$_POST['expiry_year'],$_POST['security_cvv2']);
		if($status!==true){
	    	$response['code'] = 116;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = validateNewDIDInputs($user_logins_id, trim($_POST["country_iso"]), trim($_POST["city_prefix"]), trim($_POST["fwd_number"]), trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]));
		if($status!==true){
	    	$response['code'] = 117;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 118;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the amount to account credit.";
			break;
		}
		$status = autoSaveCC($user_logins_id,trim($_POST['street']),trim($_POST['city']),trim($_POST['state']),trim($_POST['country']),trim($_POST['postal_code']),trim($_POST['card_type']),trim($_POST['pay_method']),trim($_POST['card_number']),trim($_POST['expiry_month']),trim($_POST['expiry_year']),trim($_POST['security_cvv2']));
		//Failure in autoSaveCC is NOT a critical issue
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "new_did", "CreditCard", "", trim($_POST["country_iso"]), trim($_POST["city_prefix"]), trim($_POST["fwd_number"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 119;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByCCRenewDID":
		$status = validateCreditCardInputs($user_logins_id,$_POST['street'],$_POST['city'],$_POST['country'],$_POST['postal_code'],$_POST['pay_method'],$_POST['card_type'],$_POST['card_number'],$_POST['expiry_month'],$_POST['expiry_year'],$_POST['security_cvv2']);
		if($status!==true){
	    	$response['code'] = 120;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = validateRenewOrRestoreInputs($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
		if($status!==true){
	    	$response['code'] = 121;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 122;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the amount to account credit.";
			break;
		}
		$status = autoSaveCC($user_logins_id,trim($_POST['street']),trim($_POST['city']),trim($_POST['state']),trim($_POST['country']),trim($_POST['postal_code']),trim($_POST['card_type']),trim($_POST['pay_method']),trim($_POST['card_number']),trim($_POST['expiry_month']),trim($_POST['expiry_year']),trim($_POST['security_cvv2']));
		//Failure in autoSaveCC is NOT a critical issue
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "renew_did", "CreditCard", trim($_POST["did_id"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 123;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByCCRestoreDID":
		$status = validateCreditCardInputs($user_logins_id,$_POST['street'],$_POST['city'],$_POST['country'],$_POST['postal_code'],$_POST['pay_method'],$_POST['card_type'],$_POST['card_number'],$_POST['expiry_month'],$_POST['expiry_year'],$_POST['security_cvv2']);
		if($status!==true){
	    	$response['code'] = 124;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = validateRenewOrRestoreInputs($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
		if($status!==true){
	    	$response['code'] = 125;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 126;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the amount to account credit.";
			break;
		}
		$status = autoSaveCC($user_logins_id,trim($_POST['street']),trim($_POST['city']),trim($_POST['state']),trim($_POST['country']),trim($_POST['postal_code']),trim($_POST['card_type']),trim($_POST['pay_method']),trim($_POST['card_number']),trim($_POST['expiry_month']),trim($_POST['expiry_year']),trim($_POST['security_cvv2']));
		//Failure in autoSaveCC is NOT a critical issue
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "restore_did", "CreditCard", trim($_POST["did_id"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 127;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByExistingCCAccountCredit":
		$member_credits = new Member_credits();
		if(is_object($member_credits)==false){
			$response['code'] = 128;
	    	$response['status'] = "Failed to create Member_credits object";
	    	$response['data'] = NULL;
	    	break;
		}
		if(isset($_POST['existing_cc_id'])==false || trim($_POST['existing_cc_id'])==""){
	    	$response['code'] = 129;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Existing CreditCard ID not posted.";
		    break;
	    }
		if(isset($_POST['credit_amount'])==false || trim($_POST['credit_amount'])==""){
	    	$response['code'] = 130;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Required parameter credit_amount not posted.";
		    break;
	    }
		if(is_numeric($_POST['credit_amount'])==false){
	    	$response['code'] = 131;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Value of parameter credit_amount must be numeric.";
		    break;
	    }
	    $blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 132;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the amount to existing credit card.";
			break;
		}
		$status = $member_credits->addMemberCredits($user_logins_id,$_POST['credit_amount'],$user_logins_id);
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 133;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}else{
			$payments_received = new Payments_received();
			if(is_object($payments_received)==false){
				$response['code'] = 134;
		    	$response['status'] = "Failed to create Payments_received object";
		    	$response['data'] = NULL;
		    	break;
			}
			//Now add a record in payments_received so that invoices are generated from that.
			$payments_received->addPayment($user_logins_id, 0, 'U', trim($_POST['credit_amount']),"CreditCard", trim($_POST['card_type']), trim($_POST['card_number']), $_SERVER['REMOTE_ADDR'], "NULL", "NULL", "NULL", "NULL", "NULL", 0, 0, "NULL");
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = "Account Credit successfully added.";
		    break;
		}
		break;
	case "payByExistingCCNewDID":
		if(isset($_POST['existing_cc_id'])==false || trim($_POST['existing_cc_id'])==""){
	    	$response['code'] = 135;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Existing CreditCard ID not posted.";
		    break;
	    }else{
	    	if(is_numeric(trim($_POST['existing_cc_id']))==false){
	    		$response['code'] = 136;
			    $response['status'] = "failed";
			    $response['data'] = "Error: Existing CreditCard ID must be numeric.";
			    break;
	    	}
	    	$cc_details = new CC_Details();
	    	if(is_object($cc_details)==false){
	    		$response['code'] = 137;
		    	$response['status'] = "failed";
		   		$response['data'] = "Error: Failed to create CC_Details object.";
	    	}
	    	$status = $cc_details->getCCDetails(trim($_POST['existing_cc_id']),$user_logins_id);
	    	if(substr($status,0,6)=="Error:"){
	    		$response['code'] = 138;
		    	$response['status'] = "failed";
		   		$response['data'] = $status;
		    	break;
	    	}else{
		    	$existing_cc_details = $status;
				$card_type = $existing_cc_details["card_logo"];
				$card_number = $existing_cc_details["card_number"];
	    	}
	    }
		$status = validateNewDIDInputs($user_logins_id, trim($_POST["country_iso"]), trim($_POST["city_prefix"]), trim($_POST["fwd_number"]), trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]));
		if($status!==true){
	    	$response['code'] = 139;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 140;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the amount to account credit.";
			break;
		}
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "new_did", "CreditCard", "", trim($_POST["country_iso"]), trim($_POST["city_prefix"]), trim($_POST["fwd_number"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 141;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByExistingCCRenewDID":
		if(isset($_POST['existing_cc_id'])==false || trim($_POST['existing_cc_id'])==""){
	    	$response['code'] = 142;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Existing CreditCard ID not posted.";
		    break;
	    }else{
	   	 if(is_numeric(trim($_POST['existing_cc_id']))==false){
	    		$response['code'] = 143;
			    $response['status'] = "failed";
			    $response['data'] = "Error: Existing CreditCard ID must be numeric.";
			    break;
	    	}
	    	$cc_details = new CC_Details();
	    	if(is_object($cc_details)==false){
	    		$response['code'] = 144;
		    	$response['status'] = "failed";
		   		$response['data'] = "Error: Failed to create CC_Details object.";
	    	}
	    	$status = $cc_details->getCCDetails(trim($_POST['existing_cc_id']),$user_logins_id);
	    	if(substr($status,0,6)=="Error:"){
	    		$response['code'] = 145;
		    	$response['status'] = "failed";
		   		$response['data'] = $status;
		    	break;
	    	}else{
		    	$existing_cc_details = $status;
				$card_type = $existing_cc_details["card_logo"];
				$card_number = $existing_cc_details["card_number"];
	    	}
	    }
		$status = validateRenewOrRestoreInputs($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
		if($status!==true){
	    	$response['code'] = 146;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 147;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the amount to account credit.";
			break;
		}
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "renew_did", "CreditCard", trim($_POST["did_id"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 148;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByExistingCCRestoreDID":
		if(isset($_POST['existing_cc_id'])==false || trim($_POST['existing_cc_id'])==""){
	    	$response['code'] = 149;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Existing CreditCard ID not posted.";
		    break;
		}else{
			if(is_numeric(trim($_POST['existing_cc_id']))==false){
	    		$response['code'] = 150;
			    $response['status'] = "failed";
			    $response['data'] = "Error: Existing CreditCard ID must be numeric.";
			    break;
	    	}
			$cc_details = new CC_Details();
	    	if(is_object($cc_details)==false){
	    		$response['code'] = 151;
		    	$response['status'] = "failed";
		   		$response['data'] = "Error: Failed to create CC_Details object.";
	    	}
	    	$status = $cc_details->getCCDetails(trim($_POST['existing_cc_id']),$user_logins_id);
	    	if(substr($status,0,6)=="Error:"){
	    		$response['code'] = 152;
		    	$response['status'] = "failed";
		   		$response['data'] = $status;
		    	break;
	    	}else{
		    	$existing_cc_details = $status;
				$card_type = $existing_cc_details["card_logo"];
				$card_number = $existing_cc_details["card_number"];
	    	}
	    }
		$status = validateRenewOrRestoreInputs($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
		if($status!==true){
	    	$response['code'] = 153;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 154;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the amount to account credit.";
			break;
		}
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "restore_did", "CreditCard", trim($_POST["did_id"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 155;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}
		break;
	case "payByPayPalAccountCredit":
		$status = "Error: PayPal payments are not yet integrated.";//Send to PayPal site here
	    if($status!==true){
	    	$response['code'] = 156;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		if(isset($_POST['credit_amount'])==false || trim($_POST['credit_amount'])==""){
	    	$response['code'] = 157;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Required parameter credit_amount not posted.";
		    break;
	    }
		if(is_numeric($_POST['credit_amount'])==false){
	    	$response['code'] = 158;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Value of parameter credit_amount must be numeric.";
		    break;
	    }
		$blnPaymentSuccess = true;//Hardcoded as true until the Payment Gateway is integrated
	    if($blnPaymentSuccess == false){
	    	$response['code'] = 159;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Failed to charge the account_credit amount to PayPal.";
			break;
		}
		$status = $member_credits->addMemberCredits($user_logins_id,$_POST['credit_amount'],$user_logins_id);
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 160;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
		}else{
			$payments_received = new Payments_received();
			if(is_object($payments_received)==false){
				$response['code'] = 161;
		    	$response['status'] = "Failed to create Payments_received object";
		    	$response['data'] = NULL;
		    	break;
			}
			//Now add a record in payments_received so that invoices are generated from that.
			$payments_received->addPayment($user_logins_id, 0, 'U', trim($_POST['credit_amount']),"PayPal", trim($_POST['card_type']), trim($_POST['card_number']), $_SERVER['REMOTE_ADDR'], "NULL", "NULL", "NULL", "NULL", "NULL", 0, 0, "NULL");
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = "Account Credit successfully added.";
		    break;
		}
		$member_credits = new Member_credits();
		if(is_object($member_credits)==false){
			$response['code'] = 162;
	    	$response['status'] = "Failed to create Member_credits object";
	    	$response['data'] = NULL;
	    	break;
		}
		break;
	case "payByPayPalNewDID":
		$status = validateNewDIDInputs($user_logins_id, trim($_POST["country_iso"]), trim($_POST["city_prefix"]), trim($_POST["fwd_number"]), trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]));
		if($status!==true){
	    	$response['code'] = 163;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = "Error: PayPal payments are not yet integrated.";//Send to PayPal site here
	    if($status!==true){
	    	$response['code'] = 164;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "new_did", "PayPal", "", trim($_POST["country_iso"]), trim($_POST["city_prefix"]), trim($_POST["fwd_number"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 165;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		}
		break;
	case "payByPayPalRenewDID":
		$status = validateRenewOrRestoreInputs($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
		if($status!==true){
	    	$response['code'] = 166;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = "Error: PayPal payments are not yet integrated.";//Send to PayPal site here
	    if($status!==true){
	    	$response['code'] = 167;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "renew_did", "PayPal", trim($_POST["did_id"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 168;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		}
		break;
	case "payByPayPalRestoreDID":
		$status = validateRenewOrRestoreInputs($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
		if($status!==true){
	    	$response['code'] = 169;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = "Error: PayPal payments are not yet integrated.";//Send to PayPal site here
	    if($status!==true){
	    	$response['code'] = 170;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$status = processDIDPurchase($user_logins_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), "restore_did", "PayPal", trim($_POST["did_id"]));
		if(substr($status,0,6)!=="Error:"){
			$response['code'] = 1;
		    $response['status'] = "success";
		    $response['data'] = $status;
		}else{
			$response['code'] = 171;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		}
		break;
	case "saveVoiceMessage":
		if(isset($_POST["voice_message_id"])==false || trim($_POST["voice_message_id"])==""){
			$response['code'] = 172;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter voice_message_id not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 173;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMsgEntitlements(trim($_POST["voice_message_id"]),$user_logins_id);
		if($status===false){
			$response['code'] = 174;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This voicemail message does not belong to one of your mailbox.";
	   		break;
		}
		$status = $voicemail->save_voice_message(trim($_POST["voice_message_id"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 175;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to save voice mail message.";
	   		break;
		}else{
			$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Saved voice mail message.";
			break;
		}
		break;
	case "setVoiceMessageAsRead":
		if(isset($_POST["voice_message_id"])==false || trim($_POST["voice_message_id"])==""){
			$response['code'] = 176;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter voice_message_id not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 177;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMsgEntitlements(trim($_POST["voice_message_id"]),$user_logins_id);
		if($status===false){
			$response['code'] = 178;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This voicemail message does not belong to one of your mailbox.";
	   		break;
		}
		$status = $voicemail->setAsRead(trim($_POST["voice_message_id"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 179;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to unsave voice mail message.";
	   		break;
		}else{
			$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Unsaved voice mail message.";
			break;
		}
		break;
	case "unsaveVoiceMessage":
		if(isset($_POST["voice_message_id"])==false || trim($_POST["voice_message_id"])==""){
			$response['code'] = 180;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter voice_message_id not posted.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 181;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->checkMsgEntitlements(trim($_POST["voice_message_id"]),$user_logins_id);
		if($status===false){
			$response['code'] = 182;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: This voicemail message does not belong to one of your mailbox.";
	   		break;
		}
		$status = $voicemail->unsave_voice_message(trim($_POST["voice_message_id"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 183;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to unsave voice mail message.";
	   		break;
		}else{
			$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Unsaved voice mail message.";
			break;
		}
		break;
	case "updateCreditCard":
		if(isset($_POST['cc_id'])==false || trim($_POST['cc_id'])==""){
	    	$response['code'] = 184;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID not posted.";
		    break;
	    }
		if(is_numeric(trim($_POST['cc_id']))==false){
	    	$response['code'] = 185;
		    $response['status'] = "failed";
		    $response['data'] = "Error: CreditCard ID (cc_id) must be numeric.";
		    break;
	    }
		if(isset($_POST['primary_back_up'])==false || trim($_POST['primary_back_up'])==""){
	    	$response['code'] = 186;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Required parameter primary_back_up not posted.";
		    break;
	    }
	    if(is_numeric(trim($_POST['primary_back_up']))==false || intval(trim($_POST['primary_back_up']))<0 ||  intval(trim($_POST['primary_back_up']))>3){
	    	$response['code'] = 187;
		    $response['status'] = "failed";
		    $response['data'] = "Error: Required parameter primary_back_up must be numeric: 0=None, 1=Primary and 2=Backup.";
		    break;
	    }
		$status = validateCreditCardInputs($user_logins_id,$_POST['street'],$_POST['city'],$_POST['country'],$_POST['postal_code'],$_POST['pay_method'],$_POST['card_type'],$_POST['card_number'],$_POST['expiry_month'],$_POST['expiry_year'],$_POST['security_cvv2']);
		if($status!==true){
	    	$response['code'] = 188;
		    $response['status'] = "failed";
		    $response['data'] = $status;
		    break;
	    }
		$cc_details = new CC_Details();
    	if(is_object($cc_details)==false){
    		$response['code'] = 189;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create CC_Details object.";
    	}
    	$status = $cc_details->updateCCDetails(trim($_POST['cc_id']),$user_logins_id,$_POST['card_type'],$_POST['pay_method'],$_POST['card_number'],$_POST['expiry_month'],$_POST['expiry_year'],$_POST['security_cvv2'],$_POST['street'],$_POST['city'],$_POST['state'],$_POST['country'],$_POST['postal_code'],trim($_POST['primary_back_up']));
		if($status==true){
	    	$response['code'] = 1;
			$response['status'] = "success";
			$response['data'] = "Credit Card Details successfully updated";
			break;
		}else{
			$response['code'] = 190;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to update CC_Details.";
	   		break;
		}
    	break;
	case "updateVoiceMailRings":
		if(isset($_POST["virtual_number"])==false || trim($_POST["virtual_number"])==""){
			$response['code'] = 191;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter virtual_number not posted.";
	   		break;
		}
		if(isset($_POST["rings_before_vm"])==false || trim($_POST["rings_before_vm"])==""){
			$response['code'] = 192;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter rings_before_vm not posted.";
	   		break;
		}
		if(is_numeric($_POST["rings_before_vm"])==false){
			$response['code'] = 193;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Required parameter rings_before_vm must be numeric.";
	   		break;
		}
		$voicemail = new VoiceMail();
		if(is_object($voicemail)==false){
			$response['code'] = 194;
	    	$response['status'] = "failed";
	   		$response['data'] = "Error: Failed to create VoiceMail object.";
	   		break;
		}
		$status = $voicemail->update_rings_before_vm(trim($_POST["virtual_number"]),trim($_POST["rings_before_vm"]));
		if(substr($status,0,6)=="Error:"){
			$response['code'] = 195;
	    	$response['status'] = $status;
	    	$response['data'] = NULL;
	    	break;
		}else{
			$response['code'] = 1;
	    	$response['status'] = "success";
	    	$response['data'] = "Rings before activating voice mail updated.";
	    	break;
		}
		break;
	default: 
		//Unknown method requested
		$authentication_required = FALSE;
		$response['code'] = 299;
    	$response['status'] = $api_response_code[ $response['code'] ]['HTTP Response'];
    	$response['data'] = 'Unknown API Method';
    	break;
}
 
// Return Response to browser
deliver_response($response);
 
?>