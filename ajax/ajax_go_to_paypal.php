<?php

	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Paypal_submits.class.php");
    require("../dashboard/classes/Paypal_preapprovals.class.php");
    require("../dashboard/classes/Shopping_cart.class.php");
    require("../dashboard/classes/Member_credits.class.php");
    
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id
    
    if(isset($_POST["cartId"])==false || trim($_POST["cartId"])==""){
    	return "Error: Required parameter cartId not passed.";
    }
  
    $shopping_cart = new Shopping_cart();
    if(is_object($shopping_cart)==false){
    	return "Error: Failed to create Shopping_cart object.";
    }
    
    $cart_details = $shopping_cart->get_cart_details(trim($_POST["cartId"]));
    if(substr($cart_details,0,6)=="Error:"){
    	echo "Error: An error occurred while getting shopping_cart details. Please try again.";
    	exit;
    }
    
    $cart_amount = $cart_details["amount"];
    $return_to = trim($cart_details["return_to"]);
    $plan_period = trim($cart_details["plan_period"]);
    
    $agreement_years = 1;//Set to 1 years
    
    if($plan_period=="M" || trim($plan_period)==""){
    	$preapproval_amount = $cart_amount*12*$agreement_years;
    }elseif($plan_period=="Y"){
    	$preapproval_amount = $cart_amount*$agreement_years;
    }else{
    	echo "Error: Unknown plan_period : ".$plan_period;
    	exit;
    }
    
    $cart_id = trim($_POST["cartId"]);
    
    if($return_to =="AccountCredit"){
    	
    	$member_credits = new Member_credits();
    	if(is_object($member_credits)==false){
    		echo "Error: Failed to credate Member_credits object.";
    		exit;
    	}
    	if(isset($_POST['allow_over_4_credit']) && $_POST['allow_over_4_credit'] == 'Y'){
			// ok to proceed
		}else{
			$status = $member_credits->is_under_4_lots_in_30days($user_id);
			if($status!==true){
				echo $status;
				exit;
			}
		}
		
		$status = $member_credits->is_under_200_in_30days($user_id,$num_credits);
		if($status!==true){
			echo $status;
			exit;
		}
    	
    	$pp_return_url = PP_RETURN_URL."dashboard/pp_pay.php?cartId=".$cart_id;
    	$payKey = get_paypal_paykey($user_id, $cart_id,SITE_CURRENCY,$cart_amount,PP_CANCEL_URL,$pp_return_url,PP_IPN_URL,"shopping_cart");
	    if(substr($payKey,0,6)!="Error:"){
			if($conn){
				mysql_close($conn);
		
			}
			
			echo PP_PAYKEY_URL.$payKey;
			exit;
		}else{
			echo "Error: Failed to get PayPal payKey. Please try again. $payKey";
			exit;
	    }
    }else{
    	$pp_return_url = PP_RETURN_URL."dashboard/pp_preapproval.php?cartId=".$cart_id;
    	$preapprovalKey = get_preapproval_key($user_id, $cart_id,SITE_CURRENCY,$preapproval_amount,PP_CANCEL_URL,$pp_return_url,PP_IPN_URL,"shopping_cart", $agreement_years);
	    if(substr($preapprovalKey,0,6)!="Error:"){
			
			if($conn){
				mysql_close($conn);
		
			}
	
			echo PP_AUTHORIZATION_URL.$preapprovalKey;
			exit;
		}else{
			echo "Error: Failed to get PayPal preapprovalKey. Please try again. $preapprovalKey. Inputs are User Id: $user_id, Cart Id: $cart_id, Amount: $preapproval_amount, Return URL: $pp_return_url, Agreement Years: $agreement_years";
			exit;
	    }	
    }
	
	
    
	function get_paypal_paykey($user_id, $cart_id, $currency_code, $amount, $cancel_url, $return_url, $ipn_url, $payment_for){
		
		
		$uuid = uniqid("SN1",true);
		
		$post_data = "actionType=PAY".
						"&cancelUrl=".$cancel_url.
						"&returnUrl=".$return_url.
						"&ipnNotificationUrl=".$ipn_url."/$uuid".
						"&currencyCode=".$currency_code.
						"&receiverList.receiver.amount=".$amount.
						"&receiverList.receiver.accountId=".PP_RECEIVER_ACCOUNTID.
						"&requestEnvelope.errorLanguage=en_US".
						"&requestEnvelope.detailLevel=ReturnAll";
		$content_length = strlen($post_data);
		$headers_data = array("Content-type: text/plain", 
								"Content-length: $content_length",
								"X-PAYPAL-SECURITY-USERID: ".X_PAYPAL_SECURITY_USERID,
								"X-PAYPAL-SECURITY-PASSWORD: ".X_PAYPAL_SECURITY_PASSWORD,
								"X-PAYPAL-SECURITY-SIGNATURE: ".X_PAYPAL_SECURITY_SIGNATURE,
								"X-PAYPAL-APPLICATION-ID: ".X_PAYPAL_APPLICATION_ID,
								"X-PAYPAL-REQUEST-DATA-FORMAT: NV",
								"X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
								);
		
		$ch = curl_init();
		if($ch==FALSE){
			return "Error: Failed to create a cURL handle.";
		}
		curl_setopt($ch, CURLOPT_URL, PP_AP_END_POINT."/Pay");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$response = urldecode(curl_exec($ch));
		curl_close($ch);
		
		$resp_time = "";
		$resp_status="";
		$correlation_id="";
		$build = "";
		$payKey = "";
		$paymentExecStatus = "";

		$response_array = explode("&",$response);
		foreach($response_array as $response_item){
			$resp_nv = explode("=",$response_item);
			if($resp_nv[0]=="responseEnvelope.timestamp"){
				$resp_time = $resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.ack"){
				$resp_status = $resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.correlationId"){
				$correlation_id=$resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.build"){
				$build = $resp_nv[1];
			}elseif($resp_nv[0]=="payKey"){
				$payKey = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentExecStatus"){
				$paymentExecStatus = $resp_nv[1];
			}
		}
		
		if($resp_status=="Success" && $paymentExecStatus=="CREATED"){
			//Save a record in paypal_submits table
			$paypal_submits = new Paypal_submits();
			if(is_object($paypal_submits)==false){
				return "Error: Failed to create paypal_submits object.";
			}
			$status = $paypal_submits->add_record($user_id,$cart_id,$uuid,$payKey,$amount,$currency_code,$payment_for);
			if(substr($status,0,6)=="Error:"){
				return $status;
			}
			if($status==TRUE){
				return $payKey;
			}else{
				return "Error: Failed to add a record in paypal_submits.";
			}
		}else{
			return "Error: Failed to get PayPal payKey. Please try again.";
    	}
		
	}
	
	function get_preapproval_key($user_id, $cart_id, $currency_code, $amount, $cancel_url, $return_url, $ipn_url, $payment_for, $agreement_years){
		
		$uuid = uniqid("SN1",true);
		date_default_timezone_set("UTC");
		
		$startDate = date("Y-m-d")."T00%3A00%3A00.000Z";
		$endDate = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + ".(365*$agreement_years)." day"))."T00%3A00%3A00.000Z";
		
		$post_data = "startingDate=$startDate".
						"&endingDate=$endDate".
						"&maxTotalAmountOfAllPayments=$amount".
						"&cancelUrl=".$cancel_url.
						"&returnUrl=".$return_url.
						"&ipnNotificationUrl=".$ipn_url."/$uuid".
						"&currencyCode=".$currency_code.
						"&receiverList.receiver.amount=".$amount.
						"&receiverList.receiver.accountId=".PP_RECEIVER_ACCOUNTID.
						"&requestEnvelope.errorLanguage=en_US".
						"&requestEnvelope.detailLevel=ReturnAll";
		//$post_data = urlencode($post_data);
		$content_length = strlen($post_data);
		$headers_data = array("Content-type: text/plain", 
								"Content-length: $content_length",
								"X-PAYPAL-SECURITY-USERID: ".X_PAYPAL_SECURITY_USERID,
								"X-PAYPAL-SECURITY-PASSWORD: ".X_PAYPAL_SECURITY_PASSWORD,
								"X-PAYPAL-SECURITY-SIGNATURE: ".X_PAYPAL_SECURITY_SIGNATURE,
								"X-PAYPAL-APPLICATION-ID: ".X_PAYPAL_APPLICATION_ID,
								"X-PAYPAL-REQUEST-DATA-FORMAT: NV",
								"X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
								);
		
		$ch = curl_init();
		if($ch==FALSE){
			return "Error: Failed to create a cURL handle.";
		}
		curl_setopt($ch, CURLOPT_URL, PP_AP_END_POINT."/Preapproval");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$response = urldecode(curl_exec($ch));
		curl_close($ch);
		
		$resp_time = "";
		$resp_status="";
		$correlation_id="";
		$build = "";
		$preapprovalKey = "";
		$paymentExecStatus = "";

		$response_array = explode("&",$response);
		foreach($response_array as $response_item){
			$resp_nv = explode("=",$response_item);
			if($resp_nv[0]=="responseEnvelope.timestamp"){
				$resp_time = $resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.ack"){
				$resp_status = $resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.correlationId"){
				$correlation_id=$resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.build"){
				$build = $resp_nv[1];
			}elseif($resp_nv[0]=="preapprovalKey"){
				$preapprovalKey = $resp_nv[1];
			}
		}
		
		if($resp_status=="Success"){
			//Save a record in paypal_preapprovals table
			$paypal_preapprovals = new Paypal_preapprovals();
			if(is_object($paypal_preapprovals)==false){
				return "Error: Failed to create paypal_preapprovals object.";
			}
			$startDate = date("Y-m-d");
			$endDate = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 365 day"));
			$status = $paypal_preapprovals->add_record($user_id, $cart_id,$uuid,$preapprovalKey,$amount,$currency_code,$startDate,$endDate);
			if(substr($status,0,6)=="Error:"){
				return $status;
			}
			if($status==TRUE){
				//If this is a manual renewal of the DID, the existing paypal pre-approvals of this DID, if any, are to be disabled.
				$status = $paypal_preapprovals->disable_existing_preapprovals_of_same_did($cart_id);
				return $preapprovalKey;
			}else{
				return "Error: Failed to add a record in paypal_preapprovals.";
			}
		}else{
			$fh = fopen("./a.txt", "w+");
			fwrite($fh, $post_data."=="."Content-type: text/plain, 
								Content-length: ".$content_length.",
								X-PAYPAL-SECURITY-USERID: ".X_PAYPAL_SECURITY_USERID.",
								X-PAYPAL-SECURITY-PASSWORD: ".X_PAYPAL_SECURITY_PASSWORD.",
								X-PAYPAL-SECURITY-SIGNATURE: ".X_PAYPAL_SECURITY_SIGNATURE.",
								X-PAYPAL-APPLICATION-ID: ".X_PAYPAL_APPLICATION_ID.",
								X-PAYPAL-REQUEST-DATA-FORMAT: NV,
								X-PAYPAL-RESPONSE-DATA-FORMAT: NV==URL=".PP_AP_END_POINT."==resp=".$response);
			fclose($fh);
			return "Error: $resp_status, PayPal Response is: $response and post_data= $post_data and URL=".PP_AP_END_POINT ;
    	}
		
	}