<?php

require "./includes/DRY_setup.inc.php";
require "./libs/Magrathea_Telecom_NTS_API.lib.php";

function get_page_content($this_page){

	global $action_message;
	global $users_dids;
	
	$page_vars = array();
	
	if(isset($_SESSION['user_logins_id'])==false || trim($_SESSION['user_logins_id'])==""){
		echo "Error: No login session found. Please login again.";
		exit;
	}
	
	if(isset($_GET["cartId"]) && trim($_GET["cartId"])!=""){
		$status = process_purchase(trim($_GET["cartId"]));
		if(is_bool($status)==false && $status=="Pending"){
			$page_vars['order_status'] = "Pending";
		}else{
			$page_vars['order_status'] = "Completed";
		}
	}elseif(isset($_POST["free_did_number"]) && trim($_POST["free_did_number"])!=""){
		$action_message = process_free_did_restore();
	}
	
	$user = new User();
	$assigned_dids = new Assigned_DID();
	$deleted_dids = new Deleted_DID();
	
	$user_total_credits = $user->get_user_total_credits($_SESSION['user_logins_id']);
	$deleted_dids_count = $deleted_dids->getDeletedDIDsCount($_SESSION['user_logins_id']);
	
	//check and process if there is DID delete request
	if(isset($_POST['submit']) && $_POST['submit']=='Delete'){
		$del_status = $assigned_dids->deleteDIDData($_SESSION['user_logins_id'],$_POST['did_number']);
	}
	
	$users_dids = $assigned_dids->getUserDIDsCount($_SESSION['user_logins_id']);
	
	// sort out pagination
	// first, figure out the page selector data
	$records_per_page = RECORDS_PER_PAGE;
	$total_dids = $users_dids+$deleted_dids_count;
	$page_count = ceil(($total_dids) / $records_per_page);

	$page_deck = array();
	for($page=1;$page<=$page_count;$page++){
		$first_record_in_range = $page * $records_per_page - ($records_per_page -1 );
		$last_record_in_range = $page * $records_per_page;
		if($last_record_in_range > $total_dids){
			$last_record_in_range = $total_dids;
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
	if ($page_current_range_end > $total_dids){
		$page_current_range_end = $total_dids;
	}
	
	//now update the variable stack
	$page_vars['page_count'] = $page_count;
	$page_vars['page_deck'] = $page_deck;
	$page_vars['menu_item'] = 'edit_routes';
	
	$page_vars['page_current'] = $page_current;
	$page_vars['page_current_range_start'] = $page_current_range_start;
	$page_vars['page_current_range_end'] = $page_current_range_end;
	
	$page_vars['my_DIDs_count'] = $total_dids;

	$page_vars['my_DIDs'] = $assigned_dids->getAUUserDIDsForPage($_SESSION['user_logins_id'],$page_current_range_start-1);
	
	$page_vars["user_total_credits"] = $user_total_credits;
	
	$page_vars['action_message'] = $action_message;
	
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	$this_page->content_area .= getAURoutesPage();
	
	common_header($this_page,"'My Numbers'");
}

function process_purchase($cartId){
	
	$blnError=false;
	
	$assigned_dids = new Assigned_DID();
	if(is_object($assigned_dids)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Assigned_DID object";
	}
	
	//Get shopping_cart details
	if($blnError==false){
		$shopping_cart = new Shopping_cart();
		if(is_object($shopping_cart)==false){
			$blnError = true;
			$errMsg = "Error: Failed to create Shopping_cart object";
		}
	}
	
	$cart_details = "";
	if($blnError==false){
		$cart_details = $shopping_cart->get_cart_details($cartId);
	}
	
	if($blnError==false && is_array($cart_details)==false){
		$blnError = true;
		$errMsg = $cart_details;
	}
	
	//Process the DID Purchase - Get a new DID from DIDWW and provision it in StickyNumber
	$did_details = "";
	if($blnError==false){
		if($cart_details["fulfilled"]=="Y"){//Get the did details from the join of shopping_cart and assigned_dids table
			$did_details = $shopping_cart->getDIDDetails($cartId);
		}else{
			if($cart_details["paid"]=="Y"){
				$did_details = processDIDPurchase($cart_details["user_id"], $cart_details["did_plan"], $cart_details["plan_period"], $cart_details["num_of_months"], $cart_details["purchase_type"], $cart_details["pymt_type"], $cart_details["did_id"], $cart_details["id"], $cart_details["country_iso"], $cart_details["city_prefix"], $cart_details["fwd_number"], $cart_details["forwarding_type"]);
			}
		}
	}
	
	if($blnError==false && is_array($did_details)==false){
		$blnError = true;
		$errMsg = $did_details;
	}
	
	if(substr($did_details["assigned_did_id"],0,7)=="Pending"){
		$did_id = "";
	}else{
		$did_id = $did_details["assigned_did_id"];
	}
	
	if($blnError==false){
		//Update the fulfilled status
		$status = $shopping_cart->update_fulfilled($cartId, $did_id);
	}
	
	if($blnError==false && $status != "success"){
		$blnError = true;
		$errMsg = $status;
	}
	
	if($blnError==false){
		//Update the preapprovalKey in assigned_dids table - used during auto renewals
		$status = $assigned_dids->updatePreapprovalKey($did_id,$cartId);
	}
	
	if($blnError==false && $status !==true){
		$blnError = true;
		$errMsg = $status;
	}
	
	if($blnError==false && substr($did_details["assigned_did_id"],0,7)=="Pending"){
		return "Pending";
	}
	
	//If manual renewal and payment is through WP - a new Agreements gets created - so need to added the did_id in the wp_preapprovals record
	if($blnError==false && $cart_details["pymt_gateway"]=="WorldPay" && ($cart_details["purchase_type"]=="renew_did" || $cart_details["purchase_type"]=="restore_did")){
		$wp_preapprovals = new Worldpay_preapprovals();
		if(is_object($wp_preapprovals)==false){
			$blnError = true;
			$errMsg = "Error: Could not create Worlppay_preapprovals object.";
		}
		if($blnError==false){
			$ret_val = $wp_preapprovals->update_did_id($cartId,$did_details["assigned_did_id"]);
			if($ret_val!==TRUE){
				$blnError = true;
				$errMsg = $ret_val;
			}
		}
	}
	
	if($blnError==false && isset($_GET["add_lead_did"]) && trim($_GET["add_lead_did"])=="yes"){
		//Mark this DID as a lead did
		$status = $assigned_dids->mark_lead_did($did_details["assigned_did_id"]);
		if($status!==true){
			$blnError = true;
			$errMsg = $status;
		}else{
			header('Location:leads_tracking.php');
			return true;
		}
	}
	
	if($blnError==true && trim($errMsg)!=""){
		die($errMsg);
	}
	
	return true;
}

function process_free_did_restore(){
	
	if(isset($_POST["free_did_number"])==false || trim($_POST["free_did_number"])==""){
		return "Error: Required parameter free_did_number NOT posted.";
	}
	if(isset($_POST["free_did_route_number"])==false || trim($_POST["free_did_route_number"])==""){
		return "Error: Required parameter free_did_route_number NOT posted.";
	}
	
	$did_with_44 = "+44".substr(trim($_POST["free_did_number"]),1);
	
	$deleted_dids = new Deleted_DID();
	if(is_object($deleted_dids)==false){
		return "Error: Failed to create Deleted_DID object while attempting to restore free DID : $did_with_44";
	}
	$assigned_dids = new Assigned_DID();
	if(is_object($assigned_dids)==false){
		return "Error: Failed to create Assigned_DID object while attempting to restore free DID : $did_with_44";
	}
	$unassigned_dids = new Unassigned_DID();
	if(is_object($unassigned_dids)==false){
		return "Error: Failed to create Unassigned_DID object while attempting to restore free DID : $did_with_44";
	}
	$unassigned_087dids = new Unassigned_087DID();
	if(is_object($unassigned_087dids)==false){
		return "Error: Failed to create Unassigned_087DID object while attempting to restore free DID : $did_with_44";
	}
	$block_destination = new Block_Destination();
	if(is_object($block_destination)==false){
		return "Error: Failed to create Block_Destination object while attempting to restore free DID : $did_with_44";
	}
	
	//Check if the destination number is blocked
	if($block_destination->isDestinationBlocked($_POST['free_did_route_number'])==true){
		return "Error: The forwarding number selected by you: ".$_POST['free_did_route_number']." is currently blocked.";
	}
	//Now check if the call costs are within limit
	$fwd_number = str_replace('+','',$_POST['free_did_route_number']);
	if($unassigned_087dids->isCallCostOK($fwd_number)!=true){
		return "Error: The call cost of the forwarding number selected by you : ".$_POST['free_did_route_number']." exceeds the limit.";
	}
	
	//Check for maximum number of DIDs allowed in an account
	$users_dids = $assigned_dids->getUserDIDsCount($_SESSION['user_logins_id']);
	$admin_limit=$assigned_dids->getUserDIDsLimit($_SESSION['user_logins_id']);
	if($users_dids>=$admin_limit){
		return "Error: The Number of DIDs in your account is $users_dids and it exceeds/meets your allowed limit of $admin_limit.";
	}
	
	//Limit of number of reactivate attempts to only one per day
	$num_days_from_last_reactivate_attempt=$deleted_dids->getLastReactivateAttemptDays($_SESSION['user_logins_id'],$_POST['free_did_number']);
	if(intval($num_days_from_last_reactivate_attempt) < 2){
		return "Error: The DID reactivation attempts are limited to one per day. Please try again later.";
	}
	
	//Find out the type of Free DID
	if(substr($_POST["free_did_number"],0,3)=="070" || substr($_POST["free_did_number"],0,4)=="4470"){
		$type_of_free_did = "070";
	}elseif(substr($_POST["free_did_number"],0,3)=="087" || substr($_POST["free_did_number"],0,4)=="4487"){
		$type_of_free_did = "087";
	}else{
		return "Error: Unknown type of Free DID Number: $did_with_44";
	}
		
	//Now check if this DID is already available with us in our unassigned_dids table
	if($type_of_free_did=="070"){
		$is_did_in_unassinged = $unassigned_dids->checkDIDUnassigned(trim($_POST["free_did_number"]));
	}elseif($type_of_free_did=="087"){
		$is_did_in_unassinged = $unassinged_087dids->checkDIDUnassigned(trim($_POST["free_did_number"]));
	}
	if($is_did_in_unassinged==true){
		//Now reserve this did_number - if still available
		$status = $unassigned_did->setReservedByDID($did);
		if($status==false){//If reservation is NOT successful
			return "Error: Failed to reserve this Free DID Number: $did_with_44";
		}elseif($status==true){
			$reactivate_status="SUCCESS";
		}else{
			return "Error: Unknown status while reserving this Free DID Number: $did_with_44";
		}
	}else{
		$api = NTS_API_Connect(false);
		if($api!=false){
			$reactivate_status = NTS_API_request_specific_did($api, $_POST['free_did_number'],false);
		}else{
			$reactivate_status = "FAILED";
		}
	}
	if($reactivate_status=="SUCCESS"){
		//Add a new record to the assigned_dids table
		$new_did = new Assigned_DID();
		$new_did->user_id 		= $_SESSION['user_logins_id'];
		$new_did->did_number	= $_POST['free_did_number'];
		$new_did->callerid_on	= 1;
		$new_did->callerid_option = 1;
		$new_did->ringtone		= -9;
		$new_did->route_number	= $_POST['free_did_route_number'];
		$new_did->total_minutes	= 0;
		$new_did->balance = 0;
		//Now insert this Assigned DID record in the table
		$status = $new_did->insertDIDData();
		if(is_int($status)==false && substr($status,0,6)=="Error:"){
			return $status;//Return the error message from here
		}
		if($status > 0 ){//Now add a record to voicemail_users table
			$voice_mail   =   new VoiceMail();
			$status = $voice_mail->add_voicemail_user($_POST['free_did_number'],$_SESSION['user_logins_id']);	
		}
		//Now delete the record from the deleted_dids table
		$deleted_dids->deleteDeletedDID($_SESSION['user_logins_id'],$_POST['free_did_number']);
		//Now take the DID out of the unassigned list permanently
		if($is_did_in_unassinged == true){
			$unassigned_dids->did_number = $_POST['free_did_number'];
			$unassigned_dids->destroyByDID_Number();
		}
		$status = dialplan_did_route("vssp", $_POST['free_did_number'], $_POST['free_did_route_number'], $_SESSION['user_logins_id']);
		if($status!=true){
			return "Error: Failed to create route for Free DID Number: $did_with_44";
		}
		return "Success: The DID: $did_with_44 is successfully re-activated";
	}else{
		//Update the last reactivation attempt date
		$deleted_dids->updateLastReactivateAttemptDate($_SESSION['user_logins_id'],$_POST['free_did_number']);
		//die($reactivate_status);
		return "Error: This DID: $did_with_44 is not available for re-activation at this time. Please try again later.";
	}
	
}

function getAURoutesPage(){
	
	$get_CID    =   $_GET['id'];
     if(!empty($get_CID)){
         $get_cidURL =   "?id=".$get_CID;
     }
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '';
        
        $page_html .= '
        	<script>
			
			function edit_did(did){
			
				$("#did_number").val(did);
				$("#frmRoutes").attr("action","update_route.php");
				$("#frmRoutes").submit();
			
			}
			
			function refill_did(did_number){
				//Refill the DID through AJAX
				var url="../ajax/ajax_refill_did.php";
				var postdata = {"did_number" : did_number};
				jQuery.ajaxSetup({async:false});
				$.post(url, postdata, function(response){
					if(response.substr(0,6)!="Error:"){
						alert("Your DID Refill is successful.");
						window.location.reload();
						return;
					}else{
						alert(response);
						return;
					}
				});//End of $.post
			}
			
			function buy_credit(){
				window.location.href = "add_account_credit.php";
			}
			
			function renew_did(did_id,route_number,current_plan,secs_to_expire,country_iso,did_country_code,did_area_code,did_full_number,city_id){
			
			
			
			}
			
			function restore_free_did(route_number,did_full_number){
				$("#free_did_route_number").val(route_number);
				$("#free_did_number").val(did_full_number);
				$("#frmRestoreFreeDID").submit();
			}
			
			function upgrade_did(did_id,route_number,current_plan,secs_to_expire,country_iso,did_country_code,did_area_code,did_full_number,city_id){
			
				if(parseInt(secs_to_expire)<-2592000){//Expired over 30 days ago : 30*24*60*60=2592000 - Restore
					$("#activate_did_id").val(did_id);
					$("#activate_country_iso").val(country_iso);
					$("#activate_did_country_code").val(did_country_code);
					$("#activate_did_area_code").val(city_id);
					$("#activate_did_full_number").val(did_full_number);
					$("#activate_route_number").val(route_number);
					$("#frmActivate").attr("action","add-number-1.php");
					$("#frmActivate").submit();
				}else{
					$("#did_id").val(did_id);
					$("#country_iso").val(country_iso);
					$("#did_country_code").val(did_country_code);
					$("#did_area_code").val(city_id);
					$("#did_full_number").val(did_full_number);
					$("#route_number").val(route_number);
					$("#current_plan").val(current_plan);
					$("#frmUpgrade").attr("action","add-number-1.php");
					$("#frmUpgrade").submit();
				}
			
			}
			
			
			</script>';
        
        if(isset($_GET["errMsg"]) && trim($_GET["errMsg"])!=""){
        	 $page_html .= '<script>alert("'.trim($_GET["errMsg"]).'")</script>';
        }
        
        $page_html .='
        	<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">My Online Numbers</h1>
                    	<div class="table-responsive">
							<table class="table table-hover">
								<thead class="blue-bg">
									<tr>
										<th>Action</th>
										<th align="center">Virtual Number(Avbl. Balance)</th>
										<th>Plan</th>
										<th>Forwards to<br>/Additional Minutes</th>
										<th>Manage Plan</th>
										<th>Expiry(GMT)</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>';
        						if(intval($page_vars['my_DIDs_count'])>0){
									
        							$unassigned_087did = new Unassigned_087DID();
									if(is_object($unassigned_087did)!=true){
										die("Failed to create Unassigned_087DID object.");
									}
									$sco = new System_Config_Options();
									if(is_object($sco)!=true){
										die("Failed to create System_Config_Options object.");
									}
									foreach($page_vars['my_DIDs'] as $my_DID){
										
										// added country and city names
										
										$countryName = $my_DID["country_name"];
										$cityName = $my_DID["city_name"];
											
										// on 31st Jan 2016
											
											
										if(substr($my_DID['did_number'],0,3)=="070" || substr($my_DID['did_number'],0,3)=="087" || substr($my_DID['did_number'],0,4)=="4470" || substr($my_DID['did_number'],0,4)=="4487"){
											$paid_did = false;
										}else{
											$paid_did = true;
										}
										
										if($my_DID['did_country_iso']=="N.A." && $my_DID['city_id']=="N.A."){
											//This is a deleted DID
											if($paid_did==false){
												$restore_free_did_button_html='<button name="restore_free_did" class="btn btn-success addpads" onclick="restore_free_did(\''.$my_DID['route_number'].'\',\''.$my_DID['did_number'].'\')">Reactivate</button>';
											}else{
												$restore_free_did_button_html='N.A.';
											}
											$did_with_44 = "+44".substr(trim($my_DID['did_number']),1);
											
											$page_html .= '
		        								<tr>
		        									<td>
		        										&nbsp;
		        									</td>
		        									<td>'.$did_with_44.'</td>
		        									<td>N.A.</td>
		        									<td>'.$my_DID["route_number"].'</td>
		        									<td align="center">'.$restore_free_did_button_html;
		        									$page_html.='</td>
		        									<td>'.$my_DID['did_expires_on'].'<span class="text-success"></span></td>
		        									<td><p class="text-danger"><strong>Deleted</strong></p></td>
		        								</tr>
		        							';
										}else{
											
											if($my_DID["secs_to_expire"] > 0){
												if($my_DID['chosen_plan'] == 1){
													$upgrade_btn_val = "Upgrade";
												}elseif($my_DID['chosen_plan'] == 2){
													$upgrade_btn_val = "Up / Down";
												}elseif($my_DID['chosen_plan'] == 3){
													$upgrade_btn_val = "Downgrade";
												}else{
													$upgrade_btn_val = "";
												}
											}elseif($my_DID["secs_to_expire"] > -2592000 && $my_DID["secs_to_expire"] <= 0){//Expired less than 30 days ago 30*24*60*60=2592000 - Renew
												$upgrade_btn_val = "Renew";
											}else{
												$upgrade_btn_val = "Activate";
											}
											
											$renew_declined = "";
											$status = "";
											if(intval($my_DID["secs_to_expire"]) > 0 || $paid_did==false){//Active
												$status = 'Active';
											}else{
												$renew_button_html = '<input type="button" name="renew_did" value="Renew" class="logged-button" style="font-size:12px;" onclick="renew_did('.$my_DID['id'].','.$my_DID['route_number'].','.$my_DID['chosen_plan'].','.$my_DID["secs_to_expire"].',\''.$my_DID['did_country_iso'].'\','.$my_DID['did_country_code'].','.$my_DID['did_area_code'].','.$my_DID['did_number'].','.$my_DID['city_id'].')">';
												if($my_DID["renew_declined"]!=0){
													$renew_declined = $my_DID["renew_declined"];
													if(intval($my_DID["renew_declined_count"]) < 3){
														$status = 'Declined '.$renew_button_html;
													}else{
														$status = '<a href="mailto:'.ACCOUNT_CHANGE_EMAIL.'">Contact Support to Reactivate</a>';
													}
												}else{
													$status = '<font color="red">Expired</font>';
												}
											}
											if($my_DID['chosen_plan']==0){
												$plan_message = "Tech Plan";
												$available_minutes = "Unlimited";
												$available_balance = "Unlimited";
												$callCost = "0 ";
												if(substr($my_DID['did_number'],0,4)=="4470" || substr($my_DID['did_number'],0,4)=="4487"){
													$did_expires_on = "N/A";
												}else{
													$did_expires_on = $my_DID['did_expires_on'];
												}
											}else{
												$plan_message = 'Plan '.$my_DID['chosen_plan'].'<br>';
												if($my_DID["chosen_plan"]==2){
													$plan_message .= "Save 10% Call Cost";
												}elseif($my_DID["chosen_plan"]==2){
													$plan_message .= "Save 15% Call Cost";
												}
												$available_minutes = number_format($my_DID['total_minutes']);
												if(trim($available_minutes)==""){
													$available_minutes = 0;
												}
												$available_balance = number_format($my_DID['balance'],2);
												if(trim($available_balance)==""){
													$available_balance = 0;
												}
												//Get Call cost based on destination number
												$gbp_to_aud_rate = $sco->getRateGBPToAUD();
												if(SITE_CURRENCY=="AUD"){
													$exchange_rate = $gbp_to_aud_rate;
												}else{
													$exchange_rate = 1;
												}
												$callCost = number_format($unassigned_087did->callCost($my_DID["route_number"],"peak",true)*$exchange_rate,2); // remove the 100 multiplier
												$did_expires_on = $my_DID['did_expires_on'];
												
												// EDITED 26th march
												/*if($callCostdata > 1){
													$callCost = number_format($callCostdata) ;
												}else{
													$callCost = $callCostdata;
												}*/
												
											}
											
											if(substr($my_DID['did_number'],0,3)=="070" || substr($my_DID['did_number'],0,3)=="087"){
												$did_display_num = "+44".substr($my_DID['did_number'],1,10);
											}else{
												$did_display_num = "+".$my_DID['did_number'];
											}
											
											$refill_button= "";//Initialize
											if($paid_did==true && intval($available_balance) <= 1 && $status=="Active"){
												if(intval($page_vars["user_total_credits"])>=1){
													$refill_button = "<a><button name='btn_refill_did' class='btn btn-success addpads' onclick=\"javascript: refill_did('".$my_DID['did_number']."');\">Refill</button></a>";
												}else{
													$refill_button = "<a><button name='btn_buy_credit' class='btn btn-success addpads' onclick=\"javascript: buy_credit();\">Buy Credit</button></a>";
												}
											}
											
		        							$page_html .= '
		        								<tr>
		        									<td>
		        										<a><button name="btn_edit_did" class="btn btn-success addpads" onclick="javascript: edit_did(\''.$my_DID['did_number'].'\');">Edit</button></a>
		        									</td>
		        									<td>'.$did_display_num.'<span class="text-success">('.CURRENCY_SYMBOL.$available_balance.')</span>'.$refill_button.'<br/><b>'.$countryName . ' ' .$cityName. '</b></td>
		        									<td>'.$plan_message.'</td>
		        									<td>'.$my_DID["route_number"].'<br />Additional Mins <strong class="text-success">'.CURRENCY_SYMBOL.$callCost.'</strong>/min</td>
		        									<td align="center">';
													if($my_DID['chosen_plan']==0){
														$page_html .= 'N/A';
													}else{
														//$page_html .= '<input type="button" name="upgrade_did" value="'.$upgrade_btn_val.'" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:65px; margin-top:7px;" onclick="upgrade_did('.$my_DID['id'].','.$my_DID['route_number'].','.$my_DID['chosen_plan'].','.$my_DID["secs_to_expire"].',\''.$my_DID['did_country_iso'].'\','.$my_DID['did_country_code'].','.$my_DID['did_area_code'].','.$my_DID['did_number'].','.$my_DID['city_id'].')">';
														$page_html.='<button name="upgrade_did" class="btn btn-success addpads" onclick="upgrade_did('.$my_DID['id'].','.$my_DID['route_number'].','.$my_DID['chosen_plan'].','.$my_DID["secs_to_expire"].',\''.$my_DID['did_country_iso'].'\','.$my_DID['did_country_code'].','.$my_DID['did_area_code'].','.$my_DID['did_number'].','.$my_DID['city_id'].')">'.$upgrade_btn_val.'</button>';
													}
		        									$page_html.='</td>
		        									<td>'.$did_expires_on.'<span class="text-success"></span></td>
		        									<td><p class="text-danger"><strong>'.$status.'</strong></p></td>
		        								</tr>
		        							';
										}
									}
        						}else{
        							$page_html .= '<tr><td colspan="7"><font color="red">You have no virtual numbers currently active in the system.</font></td></tr>';
        						}
								$page_html .='</tbody>
							</table>
						</div>';
					if($page_vars['page_count'] > 1){	
						$page_html .= '<form name="frmPaginate" id="frmPaginate" action="edit_routes.php" method="post">';
						$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['my_DIDs_count']."\n"; 
						$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();">'."\n";
						$i=0;
						foreach($page_vars['page_deck'] as $page_range){
							$i++;
							$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
						}			 
						$page_html .= '</select>
					</p></form>';
					}//end of if page count > 1
		$page_html .='</div>
				</div>
			</div>
        ';
								
		$page_html.='
			<form name="frmRoutes" id="frmRoutes" method="post" action="">
        			<input type="hidden" name="did_number" id="did_number" value="">
        		</form>
        		<form name="frmUpgrade" id="frmUpgrade" method="post" action="">
        			<input type="hidden" name="number" id="route_number" value="">
        			<input type="hidden" name="did_id" id="did_id" value="">
        			<input type="hidden" name="country_iso" id="country_iso" value="">
        			<input type="hidden" name="did_country_code" id="did_country_code" value="">
        			<input type="hidden" name="au_city_code" id="did_area_code" value="">
        			<input type="hidden" name="did_full_number" id="did_full_number" value="">
        			<input type="hidden" name="current_plan" id="current_plan" value="">
        		</form>
        		<form name="frmActivate" id="frmActivate" method="post" action="">
        			<input type="hidden" name="number" id="activate_route_number" value="">
        			<input type="hidden" name="activate_did_id" id="activate_did_id" value="yes">
        			<input type="hidden" name="country_iso" id="activate_country_iso" value="">
        			<input type="hidden" name="did_country_code" id="activate_did_country_code" value="">
        			<input type="hidden" name="au_city_code" id="activate_did_area_code" value="">
        			<input type="hidden" name="did_full_number" id="activate_did_full_number" value="">
        		</form>
        		<form name="frmRestoreFreeDID" id="frmRestoreFreeDID" method="post" action="">
        			<input type="hidden" name="free_did_number" id="free_did_number" value="">
        			<input type="hidden" name="free_did_route_number" id="free_did_route_number" value="">
        		</form>
		';
                                     
 		if($page_vars['order_status']=="Pending"){
        	$page_html .= '<script>alert("Your order is received and is under process. It will be completed soon.");</script>';
        }elseif($page_vars['order_status']=="Completed"){
        	$page_html .= '<script>alert("Your order is successfully completed.");</script>';
        }
        
		if($page_vars['action_message'] != ""){
			$page_html .= '<script>alert("'.$page_vars['action_message'].'")</script>';
		}
        
		return $page_html;
	
}

require("includes/DRY_authentication.inc.php");

?>