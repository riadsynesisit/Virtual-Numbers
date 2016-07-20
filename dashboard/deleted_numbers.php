<?php

require "./includes/DRY_setup.inc.php";
require "./classes/Deleted_DID.class.php";
require("./libs/Magrathea_Telecom_NTS_API.lib.php");
require "./pages/deleted_numbers.inc.php";

function get_page_content($this_page){

	global $action_message;
	
	if(isset($_SESSION['user_logins_id'])==false || trim($_SESSION['user_logins_id'])==""){
		echo "Error: No login session found. Please login again.";
		exit;
	}
	
	$deleted_dids = new Deleted_DID();
	$deleted_dids_count = $deleted_dids->getDeletedDIDsCount($_SESSION['user_logins_id']);
	$assigned_dids = new Assigned_DID();
	
	//check and process if there is a DID re-activate request
	if(isset($_POST['submit']) && $_POST['submit']=='activate'){
		//Limit of number of reactivate attempts to only one per day
		$num_days_from_last_reactivate_attempt=$deleted_dids->getLastReactivateAttemptDays($_SESSION['user_logins_id'],$_POST['did_number']);
		if(intval($num_days_from_last_reactivate_attempt) < 7){
			$action_message = "<font color='red'>The DID reactivation attempts are limited to one per week. Please try again later.</font>";
		}else{
			//Check for maximum number of DIDs allowed in an account
			$users_dids = $assigned_dids->getUserDIDsCount($_SESSION['user_logins_id'],true);
			$admin_limit=$assigned_dids->getUserDIDsLimit($_SESSION['user_logins_id']);
			if($users_dids>=$admin_limit){
				$this_page->content_area .= getNewLimitPage();
				$page_vars['menu_item'] = 'deleted_numbers';
				$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
				common_header($this_page,"Limited");
			}else{	
				$api = NTS_API_Connect(false);
				if($api!=false){
					$reactivate_status = NTS_API_request_specific_did($api, $_POST['did_number'],false);
				}else{
					$reactivate_status = "FAILED";
				}
				if($reactivate_status=="SUCCESS"){
					//Add a new record to the assigned_dids table
					$new_did = new Assigned_DID();
					$new_did->user_id 		= $_SESSION['user_logins_id'];
					$new_did->did_number	= $_POST['did_number'];
					$new_did->callerid_on	= 1;
					$new_did->callerid_option = 1;
					$new_did->ringtone		= -9;
					$new_did->route_number	= $_POST['route_number'];
					$new_did->total_minutes	= 0;
					$new_did->balance = 0;
					//Now insert this Assigned DID record in the table
					$status = $new_did->insertDIDData();
					if(is_int($status)==false && substr($status,0,6)=="Error:"){
						echo $status;
						exit;
					}
					if($status > 0 ){//Now add a record to voicemail_users table
						$voice_mail   =   new VoiceMail();
						$status = $voice_mail->add_voicemail_user($_POST['did_number'],$_SESSION['user_logins_id']);	
					}
					//Now delete the record from the deleted_dids table
					$deleted_dids->deleteDeletedDID($_SESSION['user_logins_id'],$_POST['did_number']);
					$action_message = "<font color='green'>The DID:".$_POST['did_number']." is successfully re-activated.</font>";
				}else{
					//Update the last reactivation attempt date
					$deleted_dids->updateLastReactivateAttemptDate($_SESSION['user_logins_id'],$_POST['did_number']);
					//die($reactivate_status);
					$action_message = "<font color='red'>This DID:".$_POST['did_number']." is not available for re-activation at this time. Please try again later.</font>";
				}
			}
		}//End of elese part of number of days from last reactivation attempt
	}
	
	$page_vars = array();
	
	// sort out pagination
	// first, figure out the page selector data
	$records_per_page = RECORDS_PER_PAGE;
	$page_count = ceil($deleted_dids_count / $records_per_page);

	$page_deck = array();
	for($page=1;$page<=$page_count;$page++){
		$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
		$last_record_in_range = $page * $records_per_page;
		if($last_record_in_range > $deleted_dids_count){
			$last_record_in_range = $deleted_dids_count;
		}
		$page_deck[$page] = $first_record_in_range ." - ".$last_record_in_range;
	}
	
	//now figure out what page we are on
	$page_current = 1;
	$safe_page_select = $_POST['paginate'];
	if($safe_page_select!=""){
		$safe_page_select = intval($safe_page_select);
	}
	
	if($safe_page_select > 0 && $safe_page_select != $page_current){
		$page_current  = $safe_page_select;
	}

	$page_current_range_start = $page_current * $records_per_page - ($records_per_page -1 );
	$page_current_range_end = $page_current * $records_per_page;
	if ($page_current_range_end > $deleted_dids_count){
		$page_current_range_end = $deleted_dids_count;
	}
	
	//now update the variable stack
	$page_vars['page_count'] = $page_count;
	$page_vars['page_deck'] = $page_deck;
	$page_vars['menu_item'] = 'deleted_numbers';
	
	$page_vars['page_current'] = $page_current;
	$page_vars['page_current_range_start'] = $page_current_range_start;
	$page_vars['page_current_range_end'] = $page_current_range_end;
	
	$page_vars['deleted_DIDs_count'] = $deleted_dids_count;
	
	$page_vars['deleted_DIDs'] = $deleted_dids->getDeletedDIDsForPage($_SESSION['user_logins_id'],$page_current_range_start-1);
	
	$page_vars['action_message'] = $action_message;
	
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	//build the page
	$this_page->content_area .= getDeletedDIDsPage();
	
	common_header($this_page,"'Deleted or Expired Numbers'");
}

require("includes/DRY_authentication.inc.php");

?>