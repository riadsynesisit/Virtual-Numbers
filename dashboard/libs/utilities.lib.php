<?php

//This library uses the following classes - Ensure that all of these classess are available whereever this library is used
// 1. Shopping_cart.class.php
// 2. Blacklist_countries.class.php
// 3. Free_email_providers.class.php
// 4. Blacklist_ip.class.php
// 5. Assigned_DID.class.php
// 6. Block_Destination.class.php
// 7. Risk_score.class.php
// 8. User.class.php
// 9. SystemConfigOptions.php
// 10. DIDWW class
// 11. DID_Cities class

function process_risk_score($cart_id, $country_iso, $ip_address){
	
	//Returns an array containing the following
	//1. High Activity Y or N : 3 or More payments in the past 7 days is high activity - Score: 5
	//2. Black listed country Y or N : Score: 3
	//3. IP Address does not match members country : Y or N: Score: 2
	//4. Member email from free provider: Y or N: Score: 1
	//5. Black listed IP : Y or N : Score : 1
	//6. IP Location - For display purposes on the 'more info' link
	//7. Forwarding Number does not match site signed up on: Y or N : Score: 2
	//8. Multiple forwarding numbers: Y or N : Score: 1
	//9. New member - A member is new until one valid approved order: Y or N : Score: 1
	//10. Order total amount over $20: Y or N : Score: 2
	//11. Over 2 DID Numbers in past 7 days : Y or N : Score : 3
	//12. Over 1 Account Credit in past 7 days : Y or N : Score : 3
	//13. Forwarding Number in black list: Y or N : Score: 2
	
	$risk_details = array();
	$risk_score = 0;
	
	$shopping_cart = new Shopping_cart();
	if(is_object($shopping_cart)==false){
		return "Error: Failed to create Shopping_cart object in libs/utilities.";
	}
	
	$blacklist_countries = new Blacklist_countries();
	if(is_object($blacklist_countries)==false){
		return "Error: Failed to create Blacklist_countries object in libs/utilities";
	}
	
	$fep = new Free_email_providers();
	if(is_object($fep)==false){
		return "Error: Failed to create Free_email_providers object in libs/utilities";
	}
	
	$blacklist_ip = new Blacklist_ip();
	if(is_object($blacklist_ip)==false){
		return "Error: Failed to create Blacklist_ip object in libs/utilities";
	}
	
	$assigned_dids = new Assigned_DID();
	if(is_object($assigned_dids)==false){
		return "Error: Failed to create Assigned_DID object in libs/utilities";
	}
	
	$blocked_destinations = new Block_Destination();
	if(is_object($blocked_destinations)==false){
		return "Error: Failed to create Assigned_DID Block_Destination in libs/utilities";
	}
	
	//Get the shopping_cart details
	$cart_details = $shopping_cart->get_cart_details($cart_id);
	if(substr($cart_details, 0,6)=="Error:"){
		return $cart_details;
	}
	
	//Initialize the return array with all 'N' values
	$risk_details["risk_score"] = 0;
	$risk_details["high_purchase_activity"] = "N";
	$risk_details["country_blacklisted"] = "N";
	$risk_details["free_provider_email"] = "N";
	$risk_details["ip_not_matches_country"] = "N";
	$risk_details["ip_blacklisted"] = "N";
	$risk_details["ip_location"] = "XX";
	$risk_details["fwd_num_not_match_site"] = "N";
	$risk_details["multiple_fwd_numbers"] = "N";
	$risk_details["fwd_num_blacklisted"] = "N";
	$risk_details["new_member"] = "N";
	$risk_details["order_over_20_dollars"] = "N";
	$risk_details["over_2_nums_in_7_days"] = "N";
	$risk_details["over_1_acct_credit_in_7_days"] = "N";
	$risk_details["docs_required"] = "N";
	
	//1. Check for High Activity
	$orders_count = $shopping_cart->get7DaysFulfilledOdersCount($cart_id);
	if(substr($orders_count, 0,6)=="Error:"){
		return $orders_count;
	}
	
	if(intval($orders_count)>=3){//Case of High Activity
		$risk_details["high_purchase_activity"] = "Y";
		$risk_score += 5;
	}
	
	//Needs to get IP Location
	$ip_country = get_ip_location($ip_address);
	if(substr($ip_country,0,6)=="Error:"){
		return $ip_country;
	}
	if($ip_country=="--"){
		return "Error: IP Location was not returned by the IP Location webservice for IP address: $ip_address while processing risk score of Cart ID: $cart_id.";
	}elseif(substr($ip_country,0,6)=="Error:"){
		return $ip_country;
	}
	
	if($country_iso=="XX"){//PayPal does not have country and ipaddress)
		$country_iso = $ip_country;
	}
	
	//2. Check for black listed country
	$blacklist_country_count = $blacklist_countries->is_country_black_listed($country_iso);
	if(substr($blacklist_country_count, 0,6)=="Error:"){
		return $blacklist_country_count;
	}
	
	if(intval($blacklist_country_count)>=1){//Blacklisted country
		$risk_details["country_blacklisted"] = "Y";
		$risk_score += 3;
	}
	
	//3. Check if IP Address does not match members country
	if(trim($ip_country) != trim($country_iso)){
		$risk_details["ip_not_matches_country"] = "Y";
		$risk_score += 2;
	}

	//4. Member email from free provider: Y or N: Score: 1
	$is_free_email = $fep->is_free_email_provider($cart_id);
	if(substr($is_free_email,0,6)=="Error:"){
		return $is_free_email;
	}
	if($is_free_email===true){
		$risk_details["free_provider_email"] = "Y";
		$risk_score += 1;
	}
	
	//5. Black listed IP : Y or N : Score : 1
	$blacklist_ip_count = $blacklist_ip->is_ip_black_listed($ip_address);
	if(substr($blacklist_ip_count, 0,6)=="Error:"){
		return $blacklist_ip_count;
	}
	
	if(intval($blacklist_ip_count)>=1){//Blacklisted IP Address
		$risk_details["ip_blacklisted"] = "Y";
		$risk_score += 1;
	}
	
	//6. IP Location - For display purposes on the 'more info' link
	$risk_details["ip_location"] = $ip_country;
	
	if(trim($cart_details["pymt_type"])!="AccountCredit"){
		//7. Forwarding Number does not match site signed up on: Y or N : Score: 2
		$fwd_number = $cart_details["fwd_number"];
		if(substr($fwd_number,0,2)!="61"){//Not an Australian forwarding number
			$risk_details["fwd_num_not_match_site"] = "Y";
			$risk_score += 2;
		}
		
		//8. Multiple forwarding numbers: Y or N : Score: 1 (Multiple DIDs whether or not belonging to this user are forwarded to the same destination)
		$dids_count = $assigned_dids->get_did_count_forwarding_to($fwd_number);
		if(substr($dids_count, 0,6)=="Error:"){
			return $dids_count;
		}
		if(intval($dids_count)>1){
			$risk_details["multiple_fwd_numbers"] = "Y";
			$risk_score += 1;
		}
	}
	
	//9. New member - A member is new until one valid approved order: Y or N : Score: 1
	$fulfilled_orders_count = $shopping_cart->getApprovedOrdersCountOfUser($cart_details["user_id"]);
	if(substr($fulfilled_orders_count, 0,6)=="Error:"){
		return $fulfilled_orders_count;
	}
	if(intval($fulfilled_orders_count)<1){//No fulfilled order for this member yet
		$risk_details["new_member"] = "Y";
		$risk_score += 1;
	}

	//10. Order total amount over $20: Y or N : Score: 2
	if($cart_details["amount"]>=20){
		$risk_details["order_over_20_dollars"] = "Y";
		$risk_score += 2;
	}
	
	//11. Over 2 DID Numbers in past 7 days : Y or N : Score : 3
	$did_count_7days = $assigned_dids->get7DaysDIDsCount($cart_details["user_id"]);
	if(substr($did_count_7days, 0,6)=="Error:"){
		return $did_count_7days;
	}
	if(intval($did_count_7days)>=2){
		$risk_details["over_2_nums_in_7_days"] = "Y";
		$risk_score += 3;
	}
	
	//12. Over 1 Account Credit in past 7 days : Y or N : Score : 3
	$acct_credit_orders_count_7days = $shopping_cart->get7DaysAcctCreditOdersCount($cart_id);
	if(substr($acct_credit_orders_count_7days, 0,6)=="Error:"){
		return $acct_credit_orders_count_7days;
	}
	if(intval($acct_credit_orders_count_7days)>=1){//Already have one or more account credit orders in the past 7 days
		$risk_details["over_1_acct_credit_in_7_days"] = "Y";
		$risk_score += 3;
	}
	
	//13. Forwarding Number in black list: Y or N : Score: 2
	$is_blocked_fwd_number = $blocked_destinations->isDestinationBlocked($cart_details["fwd_number"]);
	if($is_blocked_fwd_number===true){
		$risk_details["fwd_num_blacklisted"] = "Y";
		$risk_score += 2;
	}
	
	// get city prefix first
	
	$did_cities = new Did_Cities();
	if(is_object($did_cities)!=true){
		$err_msg = "Error: Failed to create did_cities object.";
		return $err_msg;"Error: Failed to create did_cities object.";
		
	}
	
	//14. check for docs required
	$docs_required = false;
	
	$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
	if(is_object($client)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		//return $err_msg;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Sticky Number dev<dev@stickynumber.com>' . "\r\n";
		$headers .= 'Cc: riadsynesisit@gmail.com' . "\r\n";
	
		$mail_msg = "DIDww error,file=".dir(__FILE__)."==cartid=".$cart_id;
		$is_sent = mail("riadul.i@stickynumber.com", $mail_msg, "$errMsg", $headers);

  	}else{
	
		
		$did_city = $did_cities->getCountryAndCityNameFromCityId($cart_details["city_prefix"]);
		$city_prefix = $did_city['city_prefix'];
		//echo 'ID='.$city_prefix;
		$didwwRegions = $client->getRegions($country_iso, $city_prefix, null);
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
		}
	}
	
	if($docs_required == true){
		$risk_details["docs_required"] = "Y";
		$risk_score += 1;
	}
	//Now set the final risk_score.
	$risk_details["risk_score"] = $risk_score;
	
	//Now save the risk_score record
	$risk_score_obj = new Risk_score();
	if(is_object($risk_score_obj)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Risk_score object in wp_pay_response.";
		return $errMsg;
	}
	if($blnError==false){
		$risk_score_obj->cart_id = $cart_id;
		$risk_score_obj->ip_address = $ip_address;
		$risk_score_obj->ip_location = $risk_details["ip_location"];
		$risk_score_obj->risk_score = $risk_details["risk_score"];
		$risk_score_obj->high_purchase_activity = $risk_details["high_purchase_activity"];
		$risk_score_obj->country_blacklisted = $risk_details["country_blacklisted"];
		$risk_score_obj->free_provider_email = $risk_details["free_provider_email"];
		$risk_score_obj->ip_blacklisted = $risk_details["ip_blacklisted"];
		$risk_score_obj->ip_not_matches_country = $risk_details["ip_not_matches_country"];
		$risk_score_obj->fwd_num_not_match_site = $risk_details["fwd_num_not_match_site"];
		$risk_score_obj->multiple_fwd_numbers = $risk_details["multiple_fwd_numbers"];
		$risk_score_obj->fwd_num_blacklisted = $risk_details["fwd_num_blacklisted"];
		$risk_score_obj->new_member = $risk_details["new_member"];
		$risk_score_obj->order_over_20_dollars = $risk_details["order_over_20_dollars"];
		$risk_score_obj->over_2_nums_in_7_days = $risk_details["over_2_nums_in_7_days"];
		$risk_score_obj->over_1_acct_credit_in_7_days = $risk_details["over_1_acct_credit_in_7_days"];
		$risk_score_obj->order_status = 0;//Pending order until captured
		$risk_score_obj->docs_required = $risk_details["docs_required"] ;
		$status = $risk_score_obj->add_record();
		if(substr($status,0,6)=="Error:"){
			return $status;
		}
	}
	
	return $risk_score;
	
}

function get_ip_location($ip_address){
	
	if($ip_address == "127.0.0.1"){
		return SITE_COUNTRY;
	}
	$headers_data = array("Content-type: application/vnd.maxmind.com-country+json; charset=UTF-8; version=2.0",
							"Content-length: 0");
	$ch = curl_init();
	if($ch==FALSE){
		return "Error: Failed to create a cURL handle while getting ip location.";
	}
	
	curl_setopt($ch, CURLOPT_URL, MAXMIND_URL.$ip_address);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, MAXMIND_USERID.":".MAXMIN_LICENSE_KEY);
	curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	
	$response = urldecode(curl_exec($ch));
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);
	curl_close($ch);
	
	//Check for any error:
	if(stripos($header,"application/vnd.maxmind.com-error+json")!==false){
		$err_obj = json_decode($body);
		$err_code = $err_obj->code;
		$err_msg = $err_obj->error;
		return "Error: An error occured while getting IP location. Error Code: ".$err_code." Error Message: ".$err_msg;
	}
	
	$resp_obj = json_decode($body);
	$country_obj = $resp_obj->country;
	$country_iso = $country_obj->iso_code;
	if(trim($country_iso)==""){
		return "--";
	}else{
		return $country_iso;
	}
	
}

function create_payments_received_record($cartId,$auto_renewal=false, $card_details=array()){
	
	if(count($card_details) > 0){
		$card_number   = $card_details["card_number"];
		$payment_brand = $card_details["payment_brand"];
		$transID = isset($card_details['transID']) ? $card_details['transID'] : "";
		$ndc = isset($card_details['ndc']) ? $card_details['ndc'] : "";
	}else{
		$card_number   = "";
		$payment_brand = "";
		$transID = '';
	}
	
	$blnError = false;
	$errMsg = "";
	
	$shopping_cart = new Shopping_cart();
	if(is_object($shopping_cart)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Shopping_cart object.";
	}
	
	if($blnError==false){
		$cart_details = $shopping_cart->get_cart_details($cartId);
		if(is_array($cart_details)==false){
			$blnError = true;
			$errMsg = $cart_details;
		}
	}
	
	if($blnError==false){
		$user = new User();
		if(is_object($user)==false){
			$err_msg = "Error: Failed to create the User object.";
			return $err_msg;
		}
	}
	
	if($blnError==false){
		$user_currency = $user->get_user_site_currency($cart_details["user_id"]);
		if(substr($user_currency,0,6)=="Error:"){
			$blnError = true;
			$errMsg = $user_currency;
		}
	}
	
	$fwd_number = $cart_details["fwd_number"];
	
	if($blnError==false && ($cart_details["pymt_type"]=="DIDRenew" || $cart_details["pymt_type"]=="DIDRestore" || $auto_renewal==true)){
		$assigned_did = new Assigned_DID();
		if(is_object($assigned_did)==false){
			$blnError = true;
			$errMsg = "Error: Failed to create DID_Cities object.";
		}
		if($blnError==false){
			//Validate DID_ID is available
			if(trim($cart_details["did_id"])==""){
				$blnError = true;
				$errMsg = "Error: DID_ID not available in cart_id $cartId.";
			}else{
				$did_details = $assigned_did->getDIDDetailsFromID($cart_details["did_id"]);
			}
		}
		if($blnError==false){
			if(is_array($did_details)==false){
				$blnError = true;
				$errMsg = $did_details;
			}
		}
		if($blnError==false){
			//Overwrite the forwarding number - This should come from assigned_dids table in this case - not from shopping cart
			$fwd_number = $did_details["route_number"];
		}
	}
	
	if($blnError==false){
		if($cart_details["pymt_type"]!="AccountCredit"){
			if($blnError==false){
				$sco = new System_Config_Options();
				if(is_object($sco)==false){
					$blnError = true;
					$errMsg = "Error: Failed to create System_Config_Options object.";
				}
			}
			if($blnError==false){
				$gbp_to_aud_rate = $sco->getRateGBPToAUD();
			}
			
			if($blnError==false){
				$unassigned_087did= new Unassigned_087DID();
				if(is_object($unassigned_087did)==false){
					$blnError = true;
					$errMsg = "Error: Failed to create Unassigned_087DID object.";
				}
			}
		
			if($user_currency=="AUD"){
		    	$exchange_rate = $gbp_to_aud_rate;
		    }else{
		    	$exchange_rate = 1;
		    }
			
			//Calculate the call cost in GBP cents based on the destination number
			$callCost = 0;//Initialize
			if($blnError==false && intval($did_details["forwarding_type"])==1){//Calculate call costs only for PSTN forwarded DIDs
				$callCost = $unassigned_087did->callCost($fwd_number,"peak",true)*$exchange_rate;
			}
			
			if($blnError==false){
				$did_cities = new Did_Cities();
			  	if(is_object($did_cities)==false){
			  		$blnError = true;
					$errMsg = "Error: Failed to create DID_Cities object.";
				}
			}
		}else{//If payment type is Account Credit
			$gbp_to_aud_rate = 0;
			$callCost = 0;
		}
	}
	
	if($blnError==false){
		if($cart_details["pymt_type"]=="AccountCredit"){//They are simply buying account credit
			$didww_city_id = 0;
			$country_iso = "";
			$is_new_did = 0;
			$existing_did_number = "";
		}elseif($auto_renewal==true){
			$didww_city_id = $cart_details["city_prefix"];//Though it is called as city_prefix in shopping_cart it is really DIDWW city_id
			$country_iso = $cart_details["country_iso"];
			$is_new_did = 0;
			$existing_did_number = $assigned_did->getDIDNumberFromID($cart_details["did_id"]);
		}elseif($cart_details["pymt_type"]=="DIDRenew" || $cart_details["pymt_type"]=="DIDRestore"){//Manual renewal or restore of the DID
			$didww_city_id = $did_details["city_id"];
			$country_iso = $did_details["did_country_iso"];
			$is_new_did = 0;
			$existing_did_number = $assigned_did->getDIDNumberFromID($cart_details["did_id"]);
			if($cart_details["pymt_gateway"]!="AccountCredit"){//Except when paid by Account Credit
				$cancel_fp_result = $assigned_did->cancelFuturePay($cart_details["did_id"],"DID Manually renewed or restored");//This must be done here before a new payments_received record is created.
			}
			if(substr($cancel_fp_result,0,6)=="Error:"){
				$blnError = true;
				$errMsg = $cancel_fp_result;
			}
		}else{
			$didww_city_id = $cart_details["city_prefix"];//Though it is called as city_prefix in shopping_cart it is really DIDWW city_id
			$country_iso = $cart_details["country_iso"];
			$is_new_did = 1;
			$existing_did_number = "";
		}
	}
	
	if($blnError==false){
		if($cart_details["pymt_type"]!="AccountCredit" && $cart_details["pymt_type"]!="LeadsTracking"){
			$sticky_did_monthly_setup = $did_cities->getStickyDIDMonthlyAndSetupFromCityId($didww_city_id,$user_currency);
			$sticky_did_monthly = $sticky_did_monthly_setup["sticky_monthly"];
			if(trim($sticky_did_monthly)==""){
				$blnError = true;
				$errMsg = "Error: No sticky did_monthly found in did_cities for country_iso: $country_iso and City_id $didww_city_id.";
			}
			switch(intval($cart_details["did_plan"])){
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
		}else{//If payment type is AccountCredit
			$sticky_did_monthly = 0;
			$pay_amount = 0;
			$sticky_setup = 0;
		}
	}
	
	if($blnError==false){
		if($cart_details["pymt_type"]=="DIDRenew" || $cart_details["pymt_type"]=="DIDRestore"){
			$effective_amount = $pay_amount - ($sticky_did_monthly*$cart_details["num_of_months"]);
		}elseif($cart_details["pymt_type"]=="AccountCredit"){
			$effective_amount = 0;
		}elseif($auto_renewal==true){
			$effective_amount = $pay_amount - ($sticky_did_monthly*$cart_details["num_of_months"]);
		}else{
			$effective_amount = $pay_amount - ($sticky_did_monthly*$cart_details["num_of_months"]) - $sticky_setup;
		}
	}
	
	//Calculate the number of minutes purchased
	if($blnError==false && intval($did_details["forwarding_type"])==1 && ($cart_details["pymt_type"]=="DIDPurchase" || $cart_details["pymt_type"]=="DIDRenew" || $cart_details["pymt_type"]=="DIDRestore" || $auto_renewal==true)){
		if($callCost!=0){
			$num_of_minutes = floor($effective_amount/$callCost);
		}else{
			$num_of_minutes = 0;
		}
	}else{
		$num_of_minutes = 0;
	}
	
	$payment_reversed=0;
	if($blnError==false){
		if($auto_renewal==true){
			$is_auto_renewal=1;
		}else{
			$is_auto_renewal=0;
		}
	}
	
	//Create a payments_received record
	if($blnError==false){
		$payments_received = new Payments_received();
		if(is_object($payments_received)==false){
			$blnError = true;
			$errMsg = "Error: Failed to create Payments_received object.";
		}
	}
	$remote_address = "0.0.0.0";
	if(isset($_SERVER["REMOTE_ADDR"]) && trim($_SERVER["REMOTE_ADDR"])!=""){
		$remote_address = trim($_SERVER["REMOTE_ADDR"]);
	}
	
	// if auto renew with credit_card then need to code.
	
	if($transID != '' && $auto_renewal){
		$cart_details["transId"] = $transID;
		$cart_details["transStatus"] = 'Y';
		$cart_details["transTime"] = "";
		$cart_details["pp_sender_trans_id"]  = $ndc;
	}
	
	if($blnError==false){
		$payments_received_id = $payments_received->addPayment($cart_details["user_id"],
															$cart_details["did_plan"],
															$cart_details["plan_period"],
															$cart_details["amount"],
															$cart_details["pymt_type"],
															$cartId, 
															$payment_brand,
															$card_number,
															$remote_address,
															$callCost,
															$sticky_did_monthly,
															$sticky_setup,
															$num_of_minutes,
															$is_new_did,
															$payment_reversed,
															$is_auto_renewal,
															$gbp_to_aud_rate,
															$cart_details["pymt_gateway"], 
															$cart_details["transId"],
															$cart_details["transStatus"],
															$cart_details["transTime"],
															$cart_details["pp_sender_trans_id"],
															$cart_details["pp_sender_trans_status"],
															$cart_details["pp_receiver_trans_id"],
															$cart_details["pp_receiver_trans_status"],
															$cart_details["pp_sender_mail"],
															$auto_renewal);
	}
	
	if(is_integer($payments_received_id)==false && substr($payments_received_id,0,6)=="Error:"){
		$blnError = true;
		$errMsg = $payments_received_id;
	}
	
	if($blnError==false){
		$status = $shopping_cart->update_last_pay_id($cartId,$payments_received_id);
		if($status!="success"){
			$blnError = true;
			$errMsg = $status;
		}
	}
	
	if($blnError==false){
		return $payments_received_id;
	}else{
		return $errMsg;
	}
	
}

function send_auto_billing_failed_email($email,$name,$user_id,$description,$did_number,$site_country,$expiry_date,$failure_reason){
	
	$member_id = "M".($user_id+10000);
	
	switch($site_country){
		case "AU":
			$site_url = "https://www.stickynumber.com.au/";
			$main_logo = "main-logo.png";
			$help_desk_email = "support@stickynumber.com.au";
			break;
		case "COM":
			$site_url = "https://www.stickynumber.com/";
			$main_logo = "main-logo-r1.png";
			$help_desk_email = "support@stickynumber.com";
			break;
		case "UK":
			$site_url = "https://www.stickynumber.uk/";
			$main_logo = "main-logo-uk.png";
			$help_desk_email = "support@stickynumber.uk";
			break;
		default:
			return "Error: Unknown site_country $site_country for user with email id: $email";
			break;
	}
	
	$from_email = $help_desk_email;

	$subject_line = $did_number." | Virtual Number Auto Billing Declined";
	$email_html = '
		<html>
			<head>
			  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>DID Number Auto Billing Declined Email Notification</title>
			  	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
			  	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
			</head>
			<body>
				<table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: transparent;margin:0 auto;font: 100%/1.6 \'Open Sans\',Verdana, Arial, Helvetica, sans-serif;">
					<tr>
						<td valign="middle" style="background-color:#F5F5F5">
							<img src="'.$site_url.'images/'.$main_logo.'" style="text-align:center; margin:0 auto; display:block" />
						</td>
					</tr>
					<tr>
    					<td style="padding:15px; background-color:#F5F5F5">
    						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#cc0000; color:#fff;">
    							<tr>
    								<td style="text-align:center;">
    									<h1 style="margin-bottom:20px; font-size:40px; line-height:48px;">Auto Billing has been Declined</h1>
    									<h3 style="margin:0 0 20px 0;">You are at risk of losing this online number</h3></td>
    								</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:20px; background-color:#F5F5F5"><h3 style="margin-bottom:0">Hello '.$name.',</h3>
    						<p>If this online number is not renewed by '.$expiry_date.' it will expire and as a result any forwarding setup will no longer work.</p>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:15px 15px 50px 15px">
    						<table width="100%" border="0" cellspacing="0" cellpadding="0">
    							<tr>
    								<td width="50%" valign="top" style="color:#4B5868">
    									<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:18px">
    										<tr>
    											<td>
    												<h3 style="font-size:20px; margin:0 0 10px 0;">Customer ID# <span style="color:#090">'.$member_id.'</span></h3>
    											</td>
    										</tr>
    										<tr>
										    	<td>
										    		<p style="margin:0">Status</p><p style="margin:0; color:#ff0000"><strong>Auto billing failed</strong></p>
										    	</td>
										  	</tr>
										  	<tr>
										  		<td style="padding-top:15px">
										  			<p style="margin:0;"><strong>'.$description.' - (+'.$did_number.')</strong></p>
										  		</td>
										  	</tr>
										  	<tr>
    											<td style="padding-top:15px">
    												<p style="margin:0">Declined Reason</p><p style="margin:0;"><strong>'.$failure_reason.'</strong></p>
    											</td>
  											</tr>
    									</table>
    								</td>
    								<td width="50%" valign="top" style="background-color:#4A5865; padding:15px; color:#fff; text-align:center;">
    									<h1 style="font-size:44px; margin:0 0 20px 0;">Need help?</h1>
    									<p style="margin:0 0 20px 0; font-size:24px">Our friendly support agents are ready to help. <br /><br />Chat | Phone | Email</p>
    								</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="text-align:center; font-size:12px; padding:14px 0 8px 0; background:#eee;">
    						Established 2010 <a href="'.trim($site_url).'">Sticky Number</a> All Rights Reserved. | <a href="'.trim($site_url).'terms/">Terms of Service</a> & <a href="'.$site_url.'privacy-policy/">Privacy Policy</a>.</p><p>Phone: (+61) 0291 192 986 | Email: <a href="mailto:'.$help_desk_email.'">'.$help_desk_email.'</a></p>
    					</td>
    				</tr>
				</table>
			</body>
		</html>
	';//$email_html ends here
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
	$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
	$status = mail($email, $subject_line, $email_html, $headers, $addl_params);
	if($status==true){
		$status = create_client_notification($user_id, $from_email, $email, $subject_line, $email_html);
		if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
			echo $status;
			return false;
		}else{
			return true;
		}
	}
	
}

function get_leads_tracking_rate_in_user_currency($user_id){
	
	//Create required objects.
	$user = new User();
	if(is_object($user)==false){
		$err_msg = "Error: Failed to create the User object.";
		return $err_msg;
	}
	$sco = new System_Config_Options();
	if(is_object($sco)==false){
		$err_msg = "Error: Failed to create System_Config_Options object.";
		return $err_msg;
	}
	
	//Get users currency
	$user_currency = $user->get_user_site_currency($user_id);
	if(substr($user_currency,0,6)=="Error:"){
		$err_msg = $user_currency;
		return $err_msg;
	}
	
	//Determine the exchange rate based on user currency
	switch($user_currency){
		case "AUD":
			$exchange_rate = 1;
			break;
		case "GBP":
			$gbp_to_aud_rate = $sco->getRateGBPToAUD();
			$exchange_rate = 1/$gbp_to_aud_rate;
			break;
		default:
			$err_msg = "Error: Unknown user currency '$user_currency' returned.";
			return $err_msg;
			break;
	}
	
	$leads_plan_rate = round($sco->getConfigOption("leads_tracking_rate_aud")*$exchange_rate,2);
	
	return $leads_plan_rate;
	
}

function create_client_notification($user_id, $sender, $recipient, $subject, $contents){
	
	//Validate inputs
	if(trim($user_id)==""){
		echo "Error: Required parameter 'user_id' not passed.";exit;
	}
	if(trim($sender)==""){
		echo "Error: Required parameter 'sender' not passed.";exit;
	}
	if(trim($recipient)==""){
		echo "Error: Required parameter 'recipient' not passed.";exit;
	}
	if(trim($subject)==""){
		echo "Error: Required parameter 'subject' not passed.";exit;
	}
	if(trim($contents)==""){
		echo "Error: Required parameter 'contents' not passed.";exit;
	}
	
	$client_notifications = new Client_notifications();
	if(is_object($client_notifications)==false){
		echo "Error: Failed to create Client_notifications object.";exit;
	}
	$status = $client_notifications->save_client_notification($user_id, $sender, $recipient, $subject, $contents);
	if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
		return $status;
	}else{
		return $status;
	}
	
}

/* SIMPLE PAYMENT get status */

function get_payment_response( $checkOutID ) {
	$url = SIMPLEPAY_URL . "/".$checkOutID."/payment";
	$url .= "?authentication.userId=". SIMPLEPAY_USER ;
	$url .= "&authentication.password=" . SIMPLEPAY_PASS;
	$url .= "&authentication.entityId=" . SIMPLEPAY_ENTITYID;
	//echo $url."<br/>";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$responseData = curl_exec($ch);
	if(curl_errno($ch)) {
		return curl_error($ch);
	}
	curl_close($ch);
	return $responseData;
}

function simplepay_capture_pa($pay_id, $user_currency, $amount) {
	$url = SIMPLEPAY_PAY_URL . "/".$pay_id;
	$data = "authentication.userId=" . SIMPLEPAY_USER . 
		"&authentication.entityId=" . SIMPLEPAY_ENTITYID .
		"&authentication.password=" . SIMPLEPAY_PASS . 
		"&amount=" . $amount .
		"&currency=" . $user_currency . 
		"&paymentType=CP";

	//echo $url."<br/>";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$responseData = curl_exec($ch);
	if(curl_errno($ch)) {
		return curl_error($ch);
	}
	curl_close($ch);
	return $responseData;
}

function simplepay_reverse_pa($pay_id) {
	$url = SIMPLEPAY_PAY_URL."/". $pay_id;
	$data = "authentication.userId=" . SIMPLEPAY_USER . 
		"&authentication.password=" . SIMPLEPAY_PASS .
		"&authentication.entityId=" . SIMPLEPAY_ENTITYID .
		"&paymentType=RV";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$responseData = curl_exec($ch);
	if(curl_errno($ch)) {
		return curl_error($ch);
	}
	curl_close($ch);
	return $responseData;
}

