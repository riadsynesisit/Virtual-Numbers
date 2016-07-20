<?php

function updateDIDmapping($user_id, $did_number){
	
	//Inputs validation
	if(trim($user_id)==""){
		return "Error: Required parameter user_id not passed.";
	}
	
	if(trim($did_number)==""){
		return "Error: Required parameter did_number not passed.";
	}
	
	$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
	if(is_object($client)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		return $err_msg;
  	}
  	
	//Now update the mapping here
	$update_map_data = array(
		"map_type" => "URI", 
		"map_proto" => "SIP", 
		"map_detail" => SERVER_SIP_NAME."/".$did_number, //MUST BE UPDATED since we know this DID now - It was initially mapped to a random number
		"map_pref_server" => 0, // map_pref_server can be: 0 Ð Local Server (automatic detection), 1 Ð USA Server, 3 Ð Europe Server
		"map_itsp_id" => null,
		"cli_format"=>"e164", //RAW - Do not  alter CLI (default); E164 - Attempt to convert CLI to E.164 format;  Local - Attempt to convert CLI to Localized format
		"cli_prefix"=>"00" //CLI can be prefixed with optional '+' sign followed by up to 6 characters including digits and '#'
	);
	$update_map = $client->updateMapping($user_id, $did_number, $update_map_data);
	if(is_object($update_map)==false){
		$err_msg = "Error: update_map returned by updateMapping is not an object.";
		$err_msg = "\n<br />".print_r($update_map);
		return $err_msg;
	}
	if($update_map->result!=0){
		$err_msg = "Error: Failed to do update_map. Result is: $update_map->result, Error Code is: $client->getErrorCode(), Error is: $client->getErrorString()";
		return $err_msg;
	}
	
	return "success";
	
}

//Obtain a new DID
function orderNewDID($user_id, $country_iso, $city_prefix, $num_of_months, $city_id, $payments_received_id, $dest_number, $did_plan_id, $num_of_minutes, $plan_period, $forwarding_type, $balance){
	
	//Inputs validation
	if(trim($dest_number)==""){
		$err_msg = "Error: No destination number passed to orderNewDID function. User ID is $user_id.";
		return $err_msg;
	}
	
	$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
	if(is_object($client)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		return $err_msg;
  	}
	$did_countries = new Did_Countries();
	if(is_object($did_countries)!=true){
		$err_msg = "Error: Failed to create Did_Countries object.";
		return $err_msg;
	}
	$assigned_did = new Assigned_DID();
	if(is_object($assigned_did)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		return $err_msg;
  	}
	$payments_received = new Payments_received();
	if(is_object($payments_received)==false){
		$err_msg = "Error: Failed to create the Payments_received object.";
		return $err_msg;
	}
	$voice_mail = new VoiceMail();
	if(is_object($voice_mail)==false){
		$err_msg = "Error: Failed to create the VoiceMail object.";
		return $err_msg;
	}
	$this_access = new Access();
	if(is_object($this_access)==false){
		$err_msg = "Error: Failed to create the Access object.";
		return $err_msg;
	}
	$ast_rt_extensions = new ASTRTExtensions();
	if(is_object($ast_rt_extensions)==false){
		$err_msg = "Error: Failed to create ASTRTExtensions object.";
		return $err_msg;
	}
	
	$uniq_id = uniqid();
	
  	$map_data = array(
		"map_type" => "URI", 
		"map_proto" => "SIP", 
		"map_detail" => SERVER_SIP_NAME."/3763126666123", //Mapped to a random number initially - MUST BE UPDATED as soon as we know this DID.
		"map_pref_server" => 0, // map_pref_server can be: 0 Ð Local Server (automatic detection), 1 Ð USA Server, 3 Ð Europe Server
		"map_itsp_id" => null,
		"cli_format"=>"e164", //RAW - Do not  alter CLI (default); E164 - Attempt to convert CLI to E.164 format;  Local - Attempt to convert CLI to Localized format
		"cli_prefix"=>"00" //CLI can be prefixed with optional '+' sign followed by up to 6 characters including digits and '#'
	);
	$did_order = $client->createOrder($user_id, $country_iso, $city_prefix, $num_of_months, $map_data, '0', $uniq_id, $city_id);
	if(is_object($did_order)==false){
		$err_msg = $client->getError();
		return $err_msg;
	}
	if($did_order->result!=0){//Failed
		$err_msg = "Error: Failed to createOrder with DIDWW. Result is: $did_order->result, Error Code is: $client->getErrorCode(), Error is: $client->getErrorString()";
		return $err_msg;
	}
	//Now update the mapping here
	$update_map_data = array(
		"map_type" => "URI", 
		"map_proto" => "SIP", 
		"map_detail" => SERVER_SIP_NAME."/".$did_order->did_number, //MUST BE UPDATED since we know this DID now - It was initially mapped to a random number
		"map_pref_server" => 0, // map_pref_server can be: 0 Ð Local Server (automatic detection), 1 Ð USA Server, 3 Ð Europe Server
		"map_itsp_id" => null,
		"cli_format"=>"e164", //RAW - Do not  alter CLI (default); E164 - Attempt to convert CLI to E.164 format;  Local - Attempt to convert CLI to Localized format
		"cli_prefix"=>"00" //CLI can be prefixed with optional '+' sign followed by up to 6 characters including digits and '#'
	);
	$update_map = $client->updateMapping($user_id, $did_order->did_number, $update_map_data);
	if(is_object($update_map)==false){
		$err_msg = "Error: update_map returned by updateMapping is not an object.";
		return $err_msg;
	}
	if($update_map->result!=0){
		$err_msg = "Error: Failed to do update_map. Result is: $update_map->result, Error Code is: $client->getErrorCode(), Error is: $client->getErrorString()";
		return $err_msg;
	}
	
	/* Check IF number requires Docs :: Coded on 18th October 2015 */
	
	$docs_required = false;
	/*$didwwRegions = $client->getRegions($country_iso, $city_prefix, null);
	if(is_array($didwwRegions)){
		$region_country_obj = $didwwRegions[0];
		$city_array = $region_country_obj->cities;
		if(is_array($city_array)){
			foreach($city_array as $city_obj){
				if(is_object($city_obj)){
					$islnrrequired = $city_obj->islnrrequired ;
					if($islnrrequired == 1){
						$docs_required = true;
						break;
					}
				}
			}
		}
	}*/
	
	/* Check IF number requires Docs */
	
	$did_number = $did_order->did_number;
	$did_expiry = $did_order->did_expire_date_gmt;
	
	$country_prefix = $did_countries->getCountryPrefixFromISO($country_iso);
	//Now add this DID to assigned_dids table and also to ast_rt_extensions table
	$assigned_did->user_id 		= $user_id;
	$assigned_did->did_number	= $did_number;
	$assigned_did->did_country_code = $country_prefix;
	$assigned_did->did_country_iso = $country_iso;
	$assigned_did->did_area_code = $city_prefix;
	$assigned_did->city_id = $city_id;
	$assigned_did->callerid_on	= 1;
	$assigned_did->ringtone		= -9;
	$assigned_did->forwarding_type = $forwarding_type;
	$assigned_did->route_number	= $dest_number;
	$assigned_did->total_minutes	= $num_of_minutes;
	$assigned_did->payments_received_id = $payments_received_id;
	$assigned_did->did_plan_id = $did_plan_id;
	$assigned_did->plan_period = $plan_period;
	$assigned_did->callerid_option = 1;
	$assigned_did->callerid_custom = "";
	$assigned_did->did_expires_on = $did_expiry;
	$assigned_did->balance = $balance;
	$assigned_did_id = $assigned_did->insertDIDData();
	if(is_int($assigned_did_id)==false && substr($assigned_did_id,0,6)=="Error:"){
		return $assigned_did_id;//Return error message from here
	}
    if($assigned_did_id<=0){
		return $err_msg;
	}
	$didww_order_id = $did_order->order_id;
	$didww_order_status = $did_order->order_status;
	$didww_did_status = $did_order->did_status;
	$didww_did_setup = $did_order->did_setup;
	$didww_did_monthly = $did_order->did_monthly;
	//Update payments_received.assigned_dids_id table with the id of the assigned_dids table
	$status = $payments_received->updateAssigned_DIDs_id($payments_received_id, $assigned_did_id, $didww_order_id, $didww_order_status, $didww_did_status, $didww_did_setup, $didww_did_monthly, $did_expiry);
	if($status!=true){
		//Must delete the record from assigened_dids
		$status = $assigned_did->deleteAUDIDData($user_id, $did_number,"Deleted from didww.lib.php Assigned DID_ID cannot be updated in paymens received");
		$err_msg = "Error: Failed to update payment_received.assigned_dids_id.";
		return $err_msg;
	}
	
	//Now add a record to voicemail_users table
	$status = $voice_mail->add_voicemail_user($did_number,$user_id);
	if($status==false){
		//Must delete the record from assigened_dids
		$status = $assigned_did->deleteAUDIDData($user_id, $did_number,"Deleted from didww.lib.php Voie Mail User record could not be added");
		$err_msg = "Error: Failed to create a record in voicemail_users";
		return $err_msg;
	}
	
	//Now update access logs
	$this_access->updateAccessLogs('Added DID:',false,$did_number);
 	
    //set the properties to build the real time extension in Asterisk Dialplan
	$ast_rt_extensions->context = "vssp";
	$ast_rt_extensions->exten = $did_number;
	$ast_rt_extensions->priority = 1;
	$ast_rt_extensions->app="macro";
	$ast_rt_extensions->appdata="did_route,$dest_number,$user_id,$did_number";
	$record_id = $ast_rt_extensions->createRoute();
	if($record_id == 0){//Failed to create the extension
		//Must delete the record from assigened_dids
		$status = $assigned_did->deleteAUDIDData($user_id, $did_number,"Deleted from didww.lib.php Failed to create extension in ast_rt_extensions");
		$err_msg = "Error: Failed to create a record in ast_rt_extensions.";
		return $err_msg;
	}
	$did_expiry = date("d M Y H:i:s",strtotime($did_expiry));
	
	Return "Success,$did_number,$did_expiry,$assigned_did_id,$docs_required";
	
}

function renewDID($user_id,$did_number,$num_of_months,$payments_received_id,$did_id,$num_of_minutes,$did_plan_id,$balance=0, $plan_period=""){
	
	$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
	if(is_object($client)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		return $err_msg;
  	}
	$payments_received = new Payments_received();
	if(is_object($payments_received)==false){
		$err_msg = "Error: Failed to create the Payments_received object.";
		return $err_msg;
	}
	$assigned_did = new Assigned_DID();
	if(is_object($assigned_did)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		return $err_msg;
  	}
	$this_access = new Access();
	if(is_object($this_access)==false){
		$err_msg = "Error: Failed to create the Access object.";
		return $err_msg;
	}
  	
  	$uniq_id = uniqid();
  	
  	if($user_id==234896 && $did_number=='61291889362'){$user_id=235374;}//This DID was moved
  	$did_order = $client->orderautorenew($user_id,$did_number,$num_of_months,$uniq_id);
	if(is_object($did_order)==false){
		$err_msg = $client->getError();
		return $err_msg. " User: $user_id, DID Number: $did_number, Number of Months: $num_of_months and Unique ID Is : $uniq_id";
	}
	if($did_order->result!=0){//Failed
		$err_msg = "Error: Failed to orderautorenew. Result is: $did_order->result, Error Code is: $client->getErrorCode(), Error is: $client->getErrorString()";
		return $err_msg. " User: $user_id, DID Number: $did_number, Number of Months: $num_of_months and Unique ID Is : $uniq_id";
	}
	$did_expiry = $did_order->did_expire_date_gmt;
	$didww_order_id = $did_order->order_id;
	$didww_order_status = $did_order->order_status;
	$didww_did_status = $did_order->did_status;
	$didww_did_setup = $did_order->did_setup;
	$didww_did_monthly = $did_order->did_monthly;
	$status = $payments_received->updateAssigned_DIDs_id($payments_received_id, $did_id, $didww_order_id, $didww_order_status, $didww_did_status, $didww_did_setup, $didww_did_monthly, $did_expiry);
	if($status!=true){
		$err_msg = "Error: Failed to update payment_received.assigned_dids_id for Payments Received ID: $payments_received_id.";
		return $err_msg;
	}
  	//Update the assigned_dids table
	$status = $assigned_did->renewDID($did_id,$num_of_minutes,$payments_received_id,$did_expiry,$did_plan_id,$balance, $plan_period);
	if($status==false){
		$err_msg = "Error: Failed to update assigned_dids with upgrade/renewal information.";
		return $err_msg;
	}
	// update access logs
	$this_access->updateAccessLogs("Changed or extended to Plan ".$did_plan_id." of User: $user_id with plan:$plan_period for DID:",false,$did_number);
	
	$did_expiry_str = date("d M Y H:i:s",strtotime($did_expiry));
	Return "Success,$did_expiry_str,$did_expiry";
	
}

//Renew an Expired DID if it is within the aging period of DIDWW
function restoreDID($user_id,$did_number,$num_of_months,$payments_received_id,$did_id,$num_of_minutes,$did_plan_id,$balance=0, $plan_period=""){
	
	$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
	if(is_object($client)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		return $err_msg;
  	}
	$payments_received = new Payments_received();
	if(is_object($payments_received)==false){
		$err_msg = "Error: Failed to create the Payments_received object.";
		return $err_msg;
	}
	$assigned_did = new Assigned_DID();
	if(is_object($assigned_did)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		return $err_msg;
  	}
	$this_access = new Access();
	if(is_object($this_access)==false){
		$err_msg = "Error: Failed to create the Access object.";
		return $err_msg;
	}
  	
  	$uniq_id = uniqid();
  	
  	$did_order = $client->didrestore($user_id,$did_number,$num_of_months,0,$uniq_id);
	if(is_object($did_order)==false){
		$err_msg = $client->getError();
		return $err_msg;
	}
	if($did_order->result!=0){//Failed
		$err_msg = "Error: Failed to didrestore. Result is: $did_order->result, Error Code is: $client->getErrorCode(), Error is: $client->getErrorString()";
		return $err_msg;
	}
	//Get the DIDWW Order ID of this DID
	$didww_order_id = $assigned_did->getDIDWWOrderId($user_id,$did_id);
	if($didww_order_id == 0){
		$err_msg = "Error: No record for DIDWWOrder found.";
		return $err_msg;
	}
	
	//Now call getservicedetails to get the expire date in GMT
	$did_order = $client->getservicedetails($user_id,$didww_order_id);
	if(is_object($did_order)==false){
		$err_msg = $client->getError();
		return $err_msg;
	}
	if($did_order->result!=0){//Failed
		$err_msg = "Error: Failed to getservicedetails. Result is: $did_order->result, Error Code is: $client->getErrorCode(), Error is: $client->getErrorString()";
		return $err_msg;
	}
	
	$did_expiry = $did_order->did_expire_date_gmt;
	$didww_order_id = $did_order->order_id;
	$didww_order_status = $did_order->order_status;
	$didww_did_status = $did_order->did_status;
	$didww_did_setup = $did_order->did_setup;
	$didww_did_monthly = $did_order->did_monthly;
	$status = $payments_received->updateAssigned_DIDs_id($payments_received_id, $did_id, $didww_order_id, $didww_order_status, $didww_did_status, $didww_did_setup, $didww_did_monthly, $did_expiry);
	if($status!=true){
		$err_msg = "Error: Failed to update payment_received.assigned_dids_id.";
		return $err_msg;
	}
  	//Update the assigned_dids table
	$status = $assigned_did->renewDID($did_id,$num_of_minutes,$payments_received_id,$did_expiry,$did_plan_id,$balance, $plan_period);
	if($status==false){
		$err_msg = "Error: Failed to update assigned_dids with upgrade/renewal information. Please contact technical support.";
		return $err_msg;
	}
	// update access logs
	$this_access->updateAccessLogs("Reactivated expired DID to Plan ".$did_plan_id. ":",false,$did_number);
	
	$did_expiry = date("d M Y H:i:s",strtotime($did_expiry));
	Return "Success,$did_expiry";
	
}

function getPlanMinutesNewDID($fwd_number, $country_iso, $city_id){
	
	$sco = new System_Config_Options();
	if(is_object($sco)!=true){
		$err_msg = "Error: Failed to create System_Config_Options object.";
		return $err_msg;
	}
	$unassigned_087did = new Unassigned_087DID();
	if(is_object($unassigned_087did)!=true){
		$err_msg = "Error: Failed to create Unassigned_087DID object.";
		return $err_msg;
	}
	$did_cities = new Did_Cities();
  	if(is_object($did_cities)!=true){
		$err_msg = "Error: Failed to create DID_Cities object.";
		return $err_msg;
	}
	
	$num_of_months = 1;//Hard coded until the DID Annual payment option comes into force.
    
    $gbp_to_aud_rate = $sco->getRateGBPToAUD();
    
    //Calculate the call cost in GBP cents based on the destination number
    if(SITE_CURRENCY=="AUD"){
    	$exchange_rate = $gbp_to_aud_rate;
    }else{
    	$exchange_rate = 1;
    }
	$callCost = $unassigned_087did->callCost($fwd_number,"peak",true)*$exchange_rate;
	
	$sticky_did_monthly_setup = $did_cities->getStickyDIDMonthlyAndSetupFromCityId($city_id);
	if(is_array($sticky_did_monthly_setup)==false && substr($sticky_did_monthly_setup,0,6)=="Error:"){
		$err_msg = $sticky_did_monthly_setup;
		return $err_msg;
	}
	$sticky_did_monthly = $sticky_did_monthly_setup["sticky_monthly"];
	$plan1_amount = $sticky_did_monthly_setup["sticky_monthly_plan1"];
	$plan2_amount = $sticky_did_monthly_setup["sticky_monthly_plan2"];
	$plan3_amount = $sticky_did_monthly_setup["sticky_monthly_plan3"];
	$sticky_did_yearly = $sticky_did_monthly_setup["sticky_yearly"];
	$plan1_amount_yearly = $sticky_did_monthly_setup["sticky_yearly_plan1"];
	$plan2_amount_yearly = $sticky_did_monthly_setup["sticky_yearly_plan2"];
	$plan3_amount_yearly = $sticky_did_monthly_setup["sticky_yearly_plan3"];
	$sticky_setup = $sticky_did_monthly_setup["sticky_setup"];
	if(trim($sticky_did_monthly)==""){
		$err_msg = "Error: No sticky did_monthly found in did_cities for country_iso: $country_iso and City prefix $city_id.";
		return $err_msg;
	}
	//Amount available for SIP Cost after paying for the DID Monthly fees and Set Up costs if any
	$effective_amount_plan1 = $plan1_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	$effective_amount_plan2 = $plan2_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	$effective_amount_plan3 = $plan3_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	$effective_amount_plan1_yearly = $plan1_amount_yearly - $sticky_did_yearly - $sticky_setup;
	$effective_amount_plan2_yearly = $plan2_amount_yearly - $sticky_did_yearly - $sticky_setup;
	$effective_amount_plan3_yearly = $plan3_amount_yearly - $sticky_did_yearly - $sticky_setup;
	
	$plan1_minutes = floor($effective_amount_plan1/$callCost);
	$plan2_minutes = floor($effective_amount_plan2/$callCost);
	$plan3_minutes = floor($effective_amount_plan3/$callCost);
	$plan1_minutes_yearly = floor($effective_amount_plan1_yearly/($callCost*12));
	$plan2_minutes_yearly = floor($effective_amount_plan2_yearly/($callCost*12));
	$plan3_minutes_yearly = floor($effective_amount_plan3_yearly/($callCost*12));
	
	$plan_minutes = array("plan1_minutes" => $plan1_minutes, "plan2_minutes" => $plan2_minutes, "plan3_minutes" => $plan3_minutes, "plan1_minutes_yearly" => $plan1_minutes_yearly, "plan2_minutes_yearly" => $plan2_minutes_yearly, "plan3_minutes_yearly" => $plan3_minutes_yearly);
	
	return $plan_minutes;
	
}

function processDIDPurchase($user_id, $choosen_plan, $plan_period, $num_of_months, $purchase_type, $pymt_type, $did_id="", $cart_id, $did_country_iso="", $did_city_id="", $did_fwd_number="", $forwarding_type=1){
	
	$shopping_cart = new Shopping_cart();
	if(is_object($shopping_cart)==false){
		$err_msg = "Error: Failed to create Shopping_cart object.";
		return $err_msg;
	}
	
	$sco = new System_Config_Options();
	if(is_object($sco)==false){
		$err_msg = "Error: Failed to create System_Config_Options object.";
		return $err_msg;
	}
	$unassigned_087did = new Unassigned_087DID();
	if(is_object($unassigned_087did)==false){
		$err_msg = "Error: Failed to create Unassigned_087DID object.";
		return $err_msg;
	}
	$did_cities = new Did_Cities();
  	if(is_object($did_cities)==false){
		$err_msg = "Error: Failed to create DID_Cities object.";
		return $err_msg;
	}
	$member_credits = new Member_credits();
	if(is_object($member_credits)==false){
		$err_msg = "Error: Failed to create Member_credits object.";
		return $err_msg;
	}
  	$assigned_did = new Assigned_DID();
	if(is_object($assigned_did)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		return $err_msg;
  	}
	$payments_received = new Payments_received();
	if(is_object($payments_received)==false){
		$err_msg = "Error: Failed to create the Payments_received object.";
		return $err_msg;
	}
	$user = new User();
	if(is_object($user)==false){
		$err_msg = "Error: Failed to create the User object.";
		return $err_msg;
	}
	
	if(trim($cart_id)==""){
		$err_msg = "Error: Required parameter cart_id not passed to processDIDPurchase.";
		return $err_msg;
	}
	
	$cart_details = $shopping_cart->get_cart_details($cart_id);
	if(is_array($cart_details)==false && substr($cart_details,0,6)=="Error:"){
		$err_msg = $cart_details;
		return $err_msg;
	}
	
	if($did_id!=""){
		$did_details = $assigned_did->getDIDDetailsFromID($did_id);
		if(is_array($did_details)==false){
			$err_msg = $did_details;
			return $err_msg;
		}else{
			$fwd_number = $did_details["route_number"];
			$country_iso = $did_details["did_country_iso"];
			$city_prefix = $did_details["did_area_code"];
			$didww_city_id = $did_details["city_id"];
			if($purchase_type=="new_did" && trim($fwd_number)==""){
				$err_msg = "Error: Forwarding number for DID ID: $did_id is empty in the database.";
				return $err_msg;
			}
			if($purchase_type=="new_did" && intval($did_details["forwarding_type"])!=2 && intval(trim($fwd_number))==0){
				$err_msg = "Error: Forwarding number for DID ID: $did_id is zero in the database.";
				return $err_msg;
			}
			if(trim($country_iso)==""){
				$err_msg = "Error: country_iso for DID ID: $did_id is empty in the database.";
				return $err_msg;
			}
			if(trim($city_prefix)==""){
				$err_msg = "Error: city_prefix for DID ID: $did_id is empty in the database.";
				return $err_msg;
			}
		}
	}else{
		if(trim($did_fwd_number)==""){
			$err_msg = "Error: DID Forwarding number passed is empty.";
			return $err_msg;
		}
		$fwd_number = $did_fwd_number;
		$country_iso = $did_country_iso;
		$didww_city_id = $did_city_id;
		$city_prefix = $did_cities->getCityPrefixFromCityId($did_city_id);
	}
	
	//Need to clean the forwarding number
	$dest_number = trim($fwd_number);
	if($forwarding_type==1){//Only for PSTN Forwarding
		$dest_number = str_replace('+','',$dest_number);
		$dest_number = str_replace('-','',$dest_number);
		$dest_number = str_replace('.','',$dest_number);
		$dest_number = str_replace(' ','',$dest_number);
		$dest_number = str_replace('(','',$dest_number);
		$dest_number = str_replace(')','',$dest_number);
	}
	
	if($purchase_type=="new_did" && trim($dest_number)==""){
		$err_msg = "Error: Destination Number after removing unwanted characters is empty. Forwarding number passed is: $fwd_number.";
		return $err_msg;
	}
	
	$payment_reversed=0;
	$is_auto_renewal=0;

	$gbp_to_aud_rate = $sco->getRateGBPToAUD();
	
	$user_currency = $user->get_user_site_currency($user_id);
	if(substr($user_currency,0,6)=="Error:"){
		$err_msg = $user_currency;
		return $err_msg;
	}
	
	if($user_currency=="AUD"){
    	$exchange_rate = $gbp_to_aud_rate;
    }else{
    	$exchange_rate = 1;
    }
	
	//Calculate the call cost in GBP cents based on the destination number
	/*if($forwarding_type==1){//Only for PSTN Forwarding
		$callCost = $unassigned_087did->callCost($dest_number,"peak",true)*$exchange_rate;
		if(is_numeric($callCost)==false || $callCost==false){
			$err_msg = "Error: Cannot get per minute call cost for destination $dest_number. Call cost returned is $callCost";
			//return $err_msg;
			$callCost = 1;//Fix to the incorrect forwarding numbers entered by the users. - Call cost is no longer important here after we changed over to dollar balance based system
		}
	}else{
		$callCost = 0;
	}*/

	$sticky_did_monthly_setup = $did_cities->getStickyDIDMonthlyAndSetupFromCityId($didww_city_id,$user_currency);
	if(is_array($sticky_did_monthly_setup)==false && substr($sticky_did_monthly_setup,0,6)=="Error:"){
		$err_msg = $sticky_did_monthly_setup;
		return $err_msg;
	}
	$sticky_did_monthly = $sticky_did_monthly_setup["sticky_monthly"];
	switch(intval($choosen_plan)){
		case 0:
			$pay_amount = $sticky_did_monthly_setup["sticky_monthly"];
			break;
		case 1:
			$pay_amount = $sticky_did_monthly_setup["sticky_monthly_plan1"];
			break;
		case 2:
			$pay_amount = $sticky_did_monthly_setup["sticky_monthly_plan2"];
			break;
		case 3:
			$pay_amount = $sticky_did_monthly_setup["sticky_monthly_plan3"];
			break;
	}
	$sticky_setup = $sticky_did_monthly_setup["sticky_setup"];
	$city_id = $sticky_did_monthly_setup["city_id"];
	if(trim($sticky_did_monthly)==""){
		$err_msg = "Error: No sticky did_monthly found in did_cities for country_iso: $country_iso and City_id $didww_city_id.";
		return $err_msg;
	}
	//Amount available for SIP Cost after paying for the DID Monthly fees and Set Up costs if any
	if($purchase_type=="new_did"){
		$effective_amount = $pay_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
		$is_new_did = 1;
		$existing_did_number = "";
	}else{
		$effective_amount = $pay_amount - ($sticky_did_monthly*$num_of_months);
		$is_new_did = 0;
		//Get Existing DID Number from the passed in did_id
		$existing_did_number = $assigned_did->getDIDNumberFromID($did_id);
		if($existing_did_number===false){
			$err_msg = "Error: Failed to get existing DID Number from id: $did_id.";
			return $err_msg;
		}
	}
	
	//Calculate the number of minutes purchased
	if($forwarding_type==1){//Only for PSTN Forwarding
		if($plan_period=='Y'){
			$balance = round($effective_amount,4);
		}else{
			$balance = round($effective_amount,4);
		}
		$num_of_minutes = 0;
	}else{
		$balance = 0;
		$num_of_minutes = 0;//For SIP forwarding - minutes are not required
	}

	$payments_received_id = $cart_details["last_pay_id"];
	if(trim($payments_received_id)==""){
		$err_msg = "Error: Payment Received Id is empty in shopping_cart id: $cart_id.";
		return $err_msg;
	}
	
	if($purchase_type=="new_did"){
		$new_did_data = orderNewDID($user_id, $country_iso, $city_prefix, $num_of_months, $city_id, $payments_received_id, $dest_number, $choosen_plan, $num_of_minutes, $plan_period, $forwarding_type, $balance);
		if(substr($new_did_data,0,7)!="Success"){
			$status = issueRefund($payments_received_id, $new_did_data);
			$err_msg = $new_did_data;
			if($status !== true){
				$err_msg = $err_msg." ".$status;
			}
			return $err_msg;
		}
		$new_did_data_array = split(",",$new_did_data);
		$did_number = $new_did_data_array[1];
		$did_expiry = $new_did_data_array[2];
		$assigned_did_id = $new_did_data_array[3];
		if(isset($new_did_data_array[4])){
			$docs_required = $new_did_data_array[4];
		}else{
			$docs_required = false;
		}
		$did_details = array("did_number"=>$did_number, "num_of_minutes"=>$num_of_minutes, "did_expiry"=>$did_expiry, "assigned_did_id"=>$assigned_did_id, "docs_required" => $docs_required);
		return $did_details;
		
	}elseif($purchase_type=="renew_did"){
		$renew_did_data = renewDID($user_id,$existing_did_number,$num_of_months,$payments_received_id,$did_id,$num_of_minutes,$choosen_plan,$balance, $plan_period);
		if(substr($renew_did_data,0,7)!="Success"){
			$status = issueRefund($payments_received_id, $renew_did_data);
			$err_msg = $renew_did_data;
			if($status !== true){
				$err_msg = $err_msg." ".$status;
			}
			return $err_msg;
		}
		$renew_did_data_array = split(",",$renew_did_data);
		$did_expiry = $renew_did_data_array[1];
		$did_expiry_raw = $renew_did_data_array[2];
		
		$did_details = array("did_expiry"=>$did_expiry,"did_expiry_raw"=>$did_expiry_raw);
		return $did_details;
		
	}elseif($purchase_type=="restore_did"){
		//$restore_did_data = restoreDID($user_id, $existing_did_number, $num_of_months, $payments_received_id, $did_id, $num_of_minutes,$choosen_plan,$balance);
		$restore_did_data = renewDID($user_id, $existing_did_number, $num_of_months, $payments_received_id, $did_id, $num_of_minutes,$choosen_plan,$balance, $plan_period);//Even for restore use the renew function
		if(substr($restore_did_data,0,7)!="Success"){
			$status = issueRefund($payments_received_id, $restore_did_data);
			$err_msg = $restore_did_data;
			if($status !== true){
				$err_msg = $err_msg." ".$status;
			}
			return $err_msg;
		}
		$restore_did_data_array = split(",",$restore_did_data);
		$did_expiry = $restore_did_data_array[1];
		
		$did_details = array("did_expiry"=>$did_expiry);
		return $did_details;
		
	}else{
		$err_msg = "Error: Unknown purchase type";
		return $err_msg;
	}

}

function issueRefund($payments_received_id, $reversal_reason){
	
	$payments_received = new Payments_received();
	if(is_object($payments_received)==false){
		$err_msg = "Error: Failed to create the Payments_received object in issueRefund.";
		return $err_msg;
	}
	$member_credits = new Member_credits();
	if(is_object($member_credits)==false){
		$err_msg = "Error: Failed to create Member_credits object in issueRefund.";
		return $err_msg;
	}
	//Get Payments Received details by ID
	$pay_recd_det = $payments_received->getPaymentDetailsById($payments_received_id);
	switch($pay_recd_det["pymt_type"]){
		case "CreditCard":
			//Call the payment gateway for payment reversal
			
			break;
		case "AcctCredit":
			//Put the money back in account credit
			$member_credits->refundMemberCredits($pay_recd_det["user_id"],$pay_recd_det["amount"]);
			break;
		default:
			$err_msg = "Error: Unknown payment type in issueRefund.";
			return $err_msg;
	}
	
	//Reverse the payment in the Sticky database by updating the payments_received record
	$status = $payments_received->reversePayment($payments_received_id, $reversal_reason);
	if($status==true){
		return true;
	}else{
		$err_msg = "Error: Faild to reversePayment in payments_received.";
		return $err_msg;
	}
	
}

function validateNewDIDInputs($user_id, $country_iso, $city_prefix, $forwarding_type, $fwd_number, $choosen_plan, $plan_period, $num_of_months){
	
	if(trim($user_id)==""){
		$err_msg = "Error: Invalid 'user_id'.";
		return $err_msg;
	}

	if(trim($country_iso)==""){
		$err_msg = "Error: Invalid 'country_iso' parameter passed.";
		return $err_msg;
	}
	
	if(trim($city_prefix)==""){
		$err_msg = "Error: Invalid 'city_prefix' parameter passed.";
		return $err_msg;
	}
	
	if(trim($fwd_number)==""){
		$err_msg = "Error: Invalid 'fwd_number' parameter passed.";
		return $err_msg;
	}
	
	if(trim($choosen_plan)=="" || is_numeric($choosen_plan)==false || intval($choosen_plan)>3 || intval($choosen_plan)<0){
		$err_msg = "Error: Invalid 'choosen_plan' parameter passed. Accepted values are 0, 1, 2 and 3.";
		return $err_msg;
	}
	
	if(trim($plan_period)=="" || (trim($plan_period)!="M" && trim($plan_period)!="Y")){
		$err_msg = "Error: Invalid 'plan_period' parameter passed. Accepted values are 'M' for monthly and 'Y' for annual or yearly.";
		return $err_msg;
	}
	
	if(trim($num_of_months)=="" || is_numeric($num_of_months)==false){
		$err_msg = "Error: Invalid 'num_of_months' parameter passed. Only numeric values are accepted.";
		return $err_msg;
	}
	
	//Need to clean the forwarding number
	$dest_number = trim($fwd_number);
	if(intval(trim($forwarding_type))==1){//Only for PSTN Forwarding
		$dest_number = str_replace('+','',$dest_number);
		$dest_number = str_replace('-','',$dest_number);
		$dest_number = str_replace('.','',$dest_number);
		$dest_number = str_replace(' ','',$dest_number);
		$dest_number = str_replace('(','',$dest_number);
		$dest_number = str_replace(')','',$dest_number);
	}
	if($dest_number==""){
    	$err_msg = "Error: Passed parameter fwd_number '".trim($fwd_number)."' does not contain a valid destination number";
    	return $err_msg;
    }
    
    return true;
	
}

function validateRenewOrRestoreInputs($user_id, $choosen_plan, $plan_period, $num_of_months, $did_id){
	
	if(trim($user_id)==""){
		$err_msg = "Error: Invalid 'user_id'.";
		return $err_msg;
	}
	
	if(trim($choosen_plan)=="" || is_numeric($choosen_plan)==false || intval($choosen_plan)>3 || intval($choosen_plan)<0){
		$err_msg = "Error: Invalid 'choosen_plan' parameter passed. Accepted values are 0, 1, 2 and 3.";
		return $err_msg;
	}
	
	if(trim($plan_period)=="" || (trim($plan_period)!="M" && trim($plan_period)!="Y")){
		$err_msg = "Error: Invalid 'plan_period' parameter passed. Accepted values are 'M' for monthly and 'Y' for annual or yearly.";
		return $err_msg;
	}
	
	if(trim($num_of_months)=="" || is_numeric($num_of_months)==false){
		$err_msg = "Error: Mising or invalid 'num_of_months' parameter passed. Only numeric values are accepted.";
		return $err_msg;
	}
	
	if(trim($did_id)=="" || is_numeric($did_id)==false){
		$err_msg = "Error: Missing or invalid 'did_id' parameter passed. Only numeric values are accepted.";
		return $err_msg;
	}
	
	return true;
	
}

function validateCreditCardInputs($user_id,$street,$city,$country,$postal_code,$pay_method,$card_type,$card_number,$expiry_month,$expiry_year,$security_cvv2){
	
		//Validate the inputs (Server side validation)
	    if(isset($street)==false || trim($street)==""){
	    	$err_msg = "Error: Required parameter 'street' not posted.";
	    	return $err_msg;
	    }
	    
	    if(isset($city)==false || trim($city)==""){
	    	$err_msg = "Error: Required parameter 'city' not posted.";
	    	return $err_msg;
	    }
	    
	    if(isset($country)==false || trim($country)==""){
	    	$err_msg = "Error: Required parameter 'country' not posted.";
	    	return $err_msg;
	    }
	    
	    if(strlen(trim($country))!=2){
	    	$err_msg = "Error: Invalid parameter value for 'country' posted. Accetable values are TWO CHARACTER Country ISO codes.";
	    	return $err_msg;
	    }
	    
	    if(isset($postal_code)==false || trim($postal_code)==""){
	    	$err_msg = "Error: Required parameter 'postal_code' not posted.";
	    	return $err_msg;
	    }
	    
	    if(isset($pay_method)==false || trim($pay_method)==""){
	    	$err_msg = "Error: Required parameter 'pay_method' not posted.";
	    	return $err_msg;
	    }
	    
	    if(trim($pay_method)!="CC" && trim($pay_method)!="DC" && trim($pay_method)!="PC"){
	    	$err_msg = "Error: Invalid value of pay_method parameter. Acceptable values are CC (for  Credit Card), DC (for Debit Card) or PC (for Payment Card)";
	    	return $err_msg;
	    }
	    
	    if(isset($card_type)==false || trim($card_type)==""){
	    	$err_msg = "Error: Required parameter 'card_type' not posted.";
	    	return $err_msg;
	    }
	    
	    if(trim($card_type)!="Visa" && trim($card_type)!="Mastercard" && trim($card_type)!="Amex"){
	    	$err_msg = "Error: Invalid value of card_type parameter. Acceptable values are Visa, Mastercard or Amex";
	    	return $err_msg;
	    }
	    
	    if(isset($card_number)==false || trim($card_number)==""){
	    	$err_msg = "Error: Required parameter 'card_number' not posted.";
	    	return $err_msg;
	    }
	    
	    if(strlen($card_number)<12){
	    	$err_msg = "Error: Card number must be a minimum 12 digits";
	    	return $err_msg;	
	    }
	    if(is_numeric($card_number)==false){
	    	$err_msg = "Error: Card Number must be NUMERIC";
	    	return $err_msg;	
	    }
	    
	    if(isset($expiry_month)==false || trim($expiry_month)==""){
	    	$err_msg = "Error: Required parameter 'expiry_month' not posted.";
	    	return $err_msg;
	    }
	    
		if(is_numeric($expiry_month)==false){
	    	$err_msg = "Error: expiry_month must be NUMERIC. Accepted values are 1 to 12";
	    	return $err_msg;	
	    }
	    
	    if($expiry_month < 1 && $expiry_month > 12){
	    	$err_msg = "Error: expiry_month must be 1 to 12";
	    	return $err_msg;
	    }
	    
	    if(isset($expiry_year)==false || trim($expiry_year)==""){
	    	$err_msg = "Error: Required parameter 'expiry_year' not posted.";
	    	return $err_msg;
	    }
	    
	    if(isset($security_cvv2)==false || trim($security_cvv2)==""){
	    	$err_msg = "Error: Required parameter 'security_cvv2' not posted.";
	    	return $err_msg;
	    }
	    
		return true;
	    
}

function chargeAccountCredit($user_id, $choosen_plan, $country_iso, $city_id ){
	
	$member_credits = new Member_credits();
	if(is_object($member_credits)==false){
		$err_msg = "Error: Failed to create Member_credits object.";
		return $err_msg;
	}
	$did_cities = new Did_Cities();
	if(is_object($did_cities)==false){
		$err_msg = "Error: Failed to create did_cities object.";
		return $err_msg;
	}
	$user = new User();
	if(is_object($user)==false){
		$err_msg = "Error: Failed to create the User object.";
		return $err_msg;
	}
	
	$user_currency = $user->get_user_site_currency($user_id);
	if(substr($user_currency,0,6)=="Error:"){
		$err_msg = $user_currency;
		return $err_msg;
	}
	
	if($city_id!=0){
		$sticky_did_monthly_setup = $did_cities->getStickyDIDMonthlyAndSetupFromCityId($city_id,$user_currency);
		if(is_array($sticky_did_monthly_setup)==false && substr($sticky_did_monthly_setup,0,6)=="Error:"){
			$err_msg = $sticky_did_monthly_setup;
			return $err_msg;
		}
		switch(intval($choosen_plan)){
			case 0:
				$pay_amount = $sticky_did_monthly_setup["sticky_monthly"];
				break;
			case 1:
				$pay_amount = $sticky_did_monthly_setup["sticky_monthly_plan1"];
				break;
			case 2:
				$pay_amount = $sticky_did_monthly_setup["sticky_monthly_plan2"];
				break;
			case 3:
				$pay_amount = $sticky_did_monthly_setup["sticky_monthly_plan3"];
				break;
		}
	}else{
		//This is a payment for leads tracking
		$pay_amount = get_leads_tracking_rate_in_user_currency($user_id);
	}
	
	$blnPaymentSuccess = $member_credits->deductMemberCredits($user_id,$pay_amount);
	if($blnPaymentSuccess == false){
		$err_msg = "Error: Failed to charge the amount of $pay_amount for plan $choosen_plan of DID City Code: $city_id to account credit.";
		return $err_msg;
	}
	
	return true;
	
}

function autoSaveCC($user_id,$street,$city,$state,$country,$postal_code,$card_type,$pay_method,$card_number,$expiry_month,$expiry_year,$security_cvv2){
	
	$cc_details = new CC_Details();
	if(is_object($cc_details)!=true){
		$err_msg = "Error: Failed to create CC_Details object.";
		return $err_msg;
	}
	
	if($cc_details->isExistingCC($card_number,$user_id)==false){
		//Get the number of existing credit cards of this user
		$cc_count = $cc_details->get_cc_count($user_id);
		$is_primary = 0;
		if($cc_count == 0){//No existing Credit Cards so make this as primary
			$is_primary = 1;
		}elseif($cc_count == 1){//One credit card already exists so make this as back-up
			$is_primary = 2;
		}
		//Save the Credit Card info for future use
		$status = $cc_details->addCCDetails($user_id,
											$card_type,
											$pay_method,
											$card_number,
											$expiry_month,
											$expiry_year,
											$security_cvv2,
											$street,
											$city,
											$state,
											$country,
											$postal_code,
											$is_primary
										);
	}
	
	return true;
	
}