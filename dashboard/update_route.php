<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
	global $action_message;
	//echo '<pre>';print_r($_POST);echo '</pre>';
	$action_message = "";
	$this_page->content_area = "";
	
	if(isset($_POST['configuring_did'])){
		process_update_did($this_page);
	}elseif(isset($_POST['did_number'])){
		show_edit_page($this_page);
	}else{
		show_oops_page($this_page);
	}
	
}

function show_edit_page($this_page){
	
	global $action_message;
	$page_vars = array();
	$did_data = array();
	
	$assigned_did = new Assigned_DID();
	
	$did_data = $assigned_did->getDIDData($_SESSION['user_logins_id'],$_POST['did_number']);
        
    if($did_data[0]==0){
		//didn't find a record, throw an error, and link to the index
		$action_message .= "Sorry, no phone number '".$_POST['did_number']."' for your account has been found.";
		$action_message .= "<p>Perhaps try going back to the <a href='index.php'>main page</a> and starting over again.</p>\n";
	}else{
		$page_vars['did_data'] = array();
		$page_vars['did_data'] = $did_data;
		//if variables are set, honor them, otherwise, give defaults	
		if(intval($did_data["callerid_on"])==1){
			$page_vars["callerid_on"] = "CHECKED";
			$page_vars["callerid_off"] = "";
		}else{
			$page_vars["callerid_on"] = "";
			$page_vars["callerid_off"] = "CHECKED";
		}
		
		$page_vars["cid_option1"] = "";
		$page_vars["cid_option2"] = "";
		$page_vars["cid_option3"] = "";
		$page_vars["cid_option4"] = "";
		
		if(trim($did_data['callerid_option'])==""){
			$page_vars["cid_option1"] = "SELECTED";
		}else{
			switch(intval($did_data['callerid_option'])){
				case 1:
					$page_vars["cid_option1"] = "SELECTED";
					break;
				case 2:
					$page_vars["cid_option2"] = "SELECTED";
					break;
				case 3:
					$page_vars["cid_option3"] = "SELECTED";
					break;
				case 4:
					$page_vars["cid_option4"] = "SELECTED";
					break;
				default:
					$page_vars["cid_option1"] = "SELECTED";
					break;
			}//end of switch case
		}//end if callerid_option is empty
		
		$page_vars["cid_custom"] = trim($did_data['callerid_custom']);
		
		$page_vars["ringtone_1"] = "";
		$page_vars["ringtone_2"] = "";
		$page_vars["ringtone_3"] = "";
		
		if(trim($did_data['ringtone'])==""){
			$page_vars["ringtone_1"] = "SELECTED";
		}else{
			switch(intval($did_data['ringtone'])){
				case 1:
					$page_vars["ringtone_1"] = "SELECTED";
					break;
				case 2:
					$page_vars["ringtone_2"] = "SELECTED";
					break;
				case 3:
					$page_vars["ringtone_3"] = "SELECTED";
					break;
				default:
					$page_vars["ringtone_1"] = "SELECTED";
					break;
			}//end of switch case
		}//end if ringtone is empty
	}//end of id did_data is zero
	$page_vars['menu_item'] = 'edit_routes';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	$this_page->content_area .= getUpdateRoutePage();
	
	common_header($this_page, "Edit Route - ".$_POST['did_number']);
	
}

function process_update_did($this_page){
	
	global $action_message;
	$page_vars = array();
	$did_data = array();
	
	if(substr($_POST['configuring_did'],0,3)!="070" && substr($_POST['configuring_did'],0,4)!="4470"){
		$status = checkSimilarDestinations($this_page,trim($_POST['destinationnumber']));
		
		if($status!==true){
			return;
		}
	}
	
	$sco = new System_Config_Options();
	if(is_object($sco)!=true){
		die("Error: Failed to create System_Config_Options object.");
	}
	$gbp_to_aud_rate = $sco->getRateGBPToAUD();
	if(SITE_CURRENCY=="AUD"){
		$exchange_rate = $gbp_to_aud_rate;
	}else{
		$exchange_rate = 1;
	}
	
	$assigned_did = new Assigned_DID();
	
	$did_data = $assigned_did->getDIDData($_SESSION['user_logins_id'],$_POST['configuring_did']);
	
	if($did_data[0]==0){
		//didn't find a record, throw an error, and link to the index
		$action_message .= "Sorry, no phone number '".$_POST['configuring_did']."' for your account has been found.";
		$action_message .= "<p>Perhaps try going back to the <a href='index.php'>main page</a> and starting over again.</p>\n";
		show_edit_page($this_page);
		return;
	}else{
		//if(SITE_COUNTRY=="AU"){
			//Need to recalculate the number of minutes each time the DID Forwarding Number is changed.
			/*$old_fwd_number = $did_data["route_number"];
			$old_total_minutes = $did_data["total_minutes"];
			$old_total_minutes = str_replace(",","",$old_total_minutes);//Removes commas if any
			if(trim($old_fwd_number)==""){//If the forwarding number is blank for any reason raise an error
				die("Error: No existing forwarding number or blank forwarding number for DID ".$_POST['configuring_did'].". Forwarding number cannot be updated while the existing number is blank. Please contact technical support.");	
			}
			if(substr($_POST['configuring_did'],0,3)!="070" && intval($old_total_minutes) <= 0 && $_POST['configuring_did']!="17062508338" && $_POST['configuring_did']!="17143955776"){
				mail("steve.s@stickynumber.com","Zero Minutes before number update alert","DID ".$_POST['configuring_did']." is having zero minutes BEFORE the forwarding number is updated.");
			}
			if(intval($old_total_minutes)>0){
				//Calculate the credit to be given for the remaining total minutes of the old forwarding number
				$unassigned_087did = new Unassigned_087DID();
				if(is_object($unassigned_087did)!=true){
					die("Failed to create Unassigned_087DID object.");
				}
				$oldCallCost = $unassigned_087did->callCost($old_fwd_number,"peak",true)*$exchange_rate;
				if($oldCallCost===false || doubleval($oldCallCost)<=0){
					die("Error: Call cost of $old_fwd_number was either not found or is zero.");
				}
				$unusedMinutesCredit = $oldCallCost*$old_total_minutes;
				$newCallCost = $unassigned_087did->callCost(trim($_POST['destinationnumber']),"peak",true)*$exchange_rate;
				if($newCallCost===false || doubleval($oldCallCost)<=0){
					die("Error: Call cost to the destination number: ".trim($_POST['destinationnumber'])." not found");
				}
				$newMinutes = floor($unusedMinutesCredit / $newCallCost );
				if(intval($newMinutes)<=0 && $_POST['configuring_did']!="17062508338" && $_POST['configuring_did']!="17143955776"){
					mail("steve.s@stickynumber.com","Zero Minutes AFTER number update alert","DID ".$_POST['configuring_did']." is having zero minutes AFTER the forwarding number is updated from $old_fwd_number to ".$_POST['destinationnumber'].". Minutes before update were $old_total_minutes. Old call cost is: $oldCallCost, New call cost is: $newCallCost, Unused Minutes credit is $unusedMinutesCredit.");
				}
			}else{//Remaining number of minutes is zero
				$newMinutes = 0;
			}*/
		//}
		//Set object properties
		$assigned_did->id = $did_data['id'];
		// set the configuration based on user inputs
		$assigned_did->callerid_on = 1;
		/*if($_POST['callerid']=="callerid_on"){
			$assigned_did->callerid_on = 1;
		}else{
			$assigned_did->callerid_on = 0;
		}*/
		
                //$assigned_did->ringtone     =   trim($_POST['ringtone']);   
		$assigned_did->route_number =   trim($_POST['destinationnumber']);
		$assigned_did->callerid_option = trim($_POST['caller_id']);
		$assigned_did->callerid_custom = trim($_POST['owncallerid']);
		$assigned_did->user_timezone = trim($_POST['DropDownTimezone']);
		//if(SITE_COUNTRY=="AU"){
			//Switched over to balance based system - minutes are not important any more. Can be set to zero.
			$assigned_did->total_minutes = 0;//str_replace(",","",$newMinutes);//Remove commans if any
		//}
		
		# => save the changes
		$status = $assigned_did->updateDIDData();
		
		if($status==0){
			$action_message .= "<h2>Problems...</h2>\n";
			$action_message .= "<p>We encountered some errors in your information when saving your changes to your Virtual Number '".$_POST['configuring_did']."'.</p>\n";
			$action_message .= "<p>Please try again or contact us if the problem persists.</p>\n";
			show_edit_page($this_page);
		}else{
			// update access logs
			if($did_data['route_number']!=trim($_POST['destinationnumber']))
			{
			$this_access = new Access();
			$this_access->updateAccessLogs('Updated Route For DID:',$did_data['route_number'],trim($_POST['destinationnumber']),$_POST['configuring_did']);
			}
			$action_message .= "<h2>Success! </h2>\n";
			$action_message .= "<p>We successfully saved your changes to your Virtual Number '".$_POST['configuring_did']."'.</p>\n";
			
			//make sure the dialplan is up to date with any changes they might just have made
			//that way if they dial before going back to the index page, their number works
			dialplan_rebuild_user_all();
			
			# => build the page
			$page_vars['menu_item'] = 'edit_routes';
			$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
			//show_success_page($this_page,$_POST['configuring_did']);
			header("Location: edit_routes.php");
		}
	}
	
}

function checkSimilarDestinations($this_page,$fwd_num){
	
	$assigned_dids = new Assigned_DID();
	$similar_numbers = $assigned_dids->getSimilarFwdNumCount($_SESSION['user_logins_id'],$fwd_num);
	
	$fraud_det = new FraudDetection();
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
		show_edit_page($this_page);
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

function show_oops_page($this_page){
	
	global $action_message;
	
	$this_page->content_area .= "<p>We experienced an error while trying to make Virtual Number Route changes for you.</p>\n "; 
	$this_page->content_area .=  "<p> Please try again or contact us if the problem persists.</p>\n";
	$page_vars['menu_item'] = 'edit_routes';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	common_header($this_page,"Making Route Changes - Something's Wrong");
	
}

function show_success_page($this_page,$virtual_number){
	
	global $action_message;
	
	$this_page->content_area .='<div class="title-bg"><h1>Saved Updated Route</h1></div><div class="mid-section">
	               <div class="date-bar">
					<div class="left-info"></div>
										</div>';
	$this_page->content_area .= "<div class='tip_box'><h3 'class' = 'page_header'>$this_page->title</h3>\n";
	$this_page->content_area .= "<p 'class' = 'tip_box'>$action_message</p></div>\n";
	$this_page->content_area .= "</div>";
	$page_vars['menu_item'] = 'edit_routes';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	common_header($this_page,"Saved Updated Route - $virtual_number");
	
}

require("includes/DRY_authentication.inc.php");

?>