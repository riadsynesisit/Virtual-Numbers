<?php

	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Paypal_submits.class.php");
    require("../dashboard/classes/Shopping_cart.class.php");
    require("../dashboard/classes/SystemConfigOptions.class.php");
    require("../dashboard/classes/Unassigned_087DID.class.php");
    require("../dashboard/classes/Did_Cities.class.php");
    require("../dashboard/classes/Assigned_DID.class.php");
    require("../dashboard/classes/Payments_received.class.php");
    require("../dashboard/classes/Blacklist_countries.class.php");
    require("../dashboard/classes/Blacklist_ip.class.php");
    require("../dashboard/classes/Free_email_providers.class.php");
    require("../dashboard/classes/Block_Destination.class.php");
    require("../dashboard/classes/Risk_score.class.php");
    require("../dashboard/classes/User.class.php");
    require("../dashboard/libs/utilities.lib.php");
    require("../dashboard/classes/DidwwAPI.class.php");
	
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id
    
    if(isset($_GET["cartId"])==false || trim($_GET["cartId"])==""){
    	echo "Error: Required parameter cartId not passed.";
    	exit;
    }
    
    $cart_id = trim($_GET["cartId"]);
  
    $shopping_cart = new Shopping_cart();
    if(is_object($shopping_cart)==false){
    	echo "Error: Failed to create Shopping_cart object.";
    	exit;
    }
    
    //Retrieve Data about the Paypal pay request
    $paypal_submits = new Paypal_submits();
    if(is_object($paypal_submits)==false){
    	echo "Error: Failed to create paypal_submits object.";
    	exit;
    }
    $paykey = $paypal_submits->get_paykey($cart_id);
    if(substr($paykey,0,6)=="Error:" || trim($paykey)==""){
    	echo "Error: Failed to get paykey. ".$paykey;
    	exit;
    }
    
    //Check if the PayPal pay was successful
    if(is_pay_successful($cart_id, $paykey)===false){
    	echo "Error: PayPal payment was not successful.";
    	exit;
    }
    
    $risk_score_obj = new Risk_score();
    if(is_object($risk_score_obj)==false){
    	echo "Error: Failed to create risk_score object.";
    	exit;
    }
    
    //Calculate the risk score - and create a record in risk_score table
	$risk_score = process_risk_score($cart_id,"XX",$_SERVER['REMOTE_ADDR']);
	if(substr($risk_score,0,6)=="Error:"){
		echo $risk_score;
		exit;
	}
	
	//Now update the order_status in risk_score table as completed.
    $status = $risk_score_obj->update_order_status($cart_id, 1);//Mark this order as completed : Order Status = 1
    if($status !== true){
    	echo $status;
    	exit;
    }
    
    //Now create a payment received record
    $status = create_payments_received_record($cart_id);
    if(substr($status,0,6)=="Error:"){
    	echo $status;
    	exit;
    }
    
    $cart_details = $shopping_cart->get_cart_details($cart_id);
    if(substr($cart_details,0,6)=="Error:"){
    	echo "Error: Failed to get cart_details. ".$cart_details;
    	exit;
    }
    
    $cart_amount = $cart_details["amount"];
    $return_to = trim($cart_details["return_to"]);

    $pp_return_url = PP_RETURN_URL;
	switch($return_to){
    	case "AccountCredit":
    		$pp_return_url .= "dashboard/add_account_credit.php";
    		break;
    	case "MemberArea":
    		$pp_return_url .= "dashboard/new_did.php";
    		break;
    	case "NumberRoutes":
    		$pp_return_url .= "dashboard/edit_routes.php";
    		break;
    	case "Slider":
    		$pp_return_url .= "";//Keep it blank for slider
    		break;
    	default:
    		echo "Unknown return_to paramter: $return_to";
    		exit;
    }
    $pp_return_url .= "?cartId=".$cart_id;
    
    
    header("Location: $pp_return_url");
    
    function is_pay_successful($cart_id, $paykey){
    	
    	$post_data = "payKey=".$paykey.
    					"&requestEnvelope.errorLanguage=en_US";
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
		curl_setopt($ch, CURLOPT_URL, PP_AP_END_POINT."/PaymentDetails");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$response = urldecode(curl_exec($ch));
		curl_close($ch);
		
		$resp_status = "";
		$status = "";
		$resp_time = "";
		$sender_trans_id = "";
		$sender_trans_status = "";
		$receiver_trans_id = "";
		$receiver_trans_status = "";
		$senderEmail = "";
		
		$response_array = explode("&",$response);
    	foreach($response_array as $response_item){
			$resp_nv = explode("=",$response_item);
			if($resp_nv[0]=="responseEnvelope.ack"){
				$resp_status = $resp_nv[1];
			}elseif($resp_nv[0]=="status"){
				$status = $resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.timestamp"){
				$resp_time = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentInfoList.paymentInfo(0).senderTransactionId"){
				$sender_trans_id = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentInfoList.paymentInfo(0).senderTransactionStatus"){
				$sender_trans_status = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentInfoList.paymentInfo(0).transactionId"){
				$receiver_trans_id = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentInfoList.paymentInfo(0).transactionStatus"){
				$receiver_trans_status = $resp_nv[1];
			}elseif($resp_nv[0]=="senderEmail"){
				$senderEmail = $resp_nv[1];
			}
		}
		
		if($resp_status=="Success" && $status=="COMPLETED"){
			$shopping_cart = new Shopping_cart();
		    if(is_object($shopping_cart)==false){
		    	echo "Error: Failed to create Shopping_cart object.";
		    	exit;
		    }
		    
		    //Now update the shopping cart record as paid
    		$status = $shopping_cart->update_paid($cart_id, $payKey, $status, $resp_time, $ipAddress, $sender_trans_id, $sender_trans_status, $receiver_trans_id, $receiver_trans_status);
			if(substr($status,0,6)=="Error:"){
				return $status;
			}
    		
    		//Now update the sender email in shopping cart record
    		$status = $shopping_cart->update_pp_sender_email($cart_id,$senderEmail);
			if(substr($status,0,6)=="Error:"){
				return $status;
			}
			return true;
		}else{
			return false;
		}
    	
    }