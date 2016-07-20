<?php

	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Paypal_preapprovals.class.php");
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
	require("../dashboard/classes/DidwwAPI.class.php");
    require("../dashboard/libs/utilities.lib.php");
    require("../dashboard/libs/paypal.lib.php");
    
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
    
    //Retrieve Data about the Preapproval
    $paypal_preapprovals = new Paypal_preapprovals();
    if(is_object($paypal_preapprovals)==false){
    	echo "Error: Failed to create paypal_preapprovals object.";
    	exit;
    }
    $preapprovalkey = $paypal_preapprovals->get_preapproval_key($cart_id);
    if(substr($preapprovalkey,0,6)=="Error:" || trim($preapprovalkey)==""){
    	echo "Error: Failed to get preapproval key. ".$preapprovalkey;
    	exit;
    }
    
    //Check if the pre-approval was successful
    if(is_preapproved($cart_id,$preapprovalkey)===false){
    	echo "Error: Preapproval was not successful.";
    	exit;
    }
    
    $risk_score_obj = new Risk_score();
    if(is_object($risk_score_obj)==false){
    	echo "Error: Failed to create risk_score object.";
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
    
    //Processing to avoid multiple charges - on multiple hits of paypal return URL
    //First check if this is already marked as paid
    $is_paid = $shopping_cart->check_is_paid($cart_id);
    if(substr($is_paid,0,6)=="Error:"){
    	echo $is_paid;
    	exit;
    }
    //Now check if the payment is in process
    $is_in_process = $shopping_cart->check_in_process_lock($cart_id);
    if(substr($is_in_process,0,6)=="Error:"){
    	echo $is_in_process;
    	exit;
    }
    //IF not marked as paid and also not in_process
    if($is_paid!='Y' && $is_in_process!='Y'){
    	//Now mark as in_process
    	$status = $shopping_cart->place_in_process_lock($cart_id);
    	if($status!==true){
    		echo $status;
    		exit;
    	}
    	
    	//Calculate the risk score - and create a record in risk_score table
		$risk_score = process_risk_score($cart_id,"XX",$_SERVER['REMOTE_ADDR']);
		if(substr($risk_score,0,6)=="Error:"){
			echo $risk_score;
			release_in_process_lock($cart_id);//Release in_process lock before exiting
			exit;
		}	
		
	    //Now update the order status as Payment Gateway /PayPal AUTHORISED (order status=5 for authorised)
		$status = $risk_score_obj->update_order_status(trim($cart_id),5);
		if(is_bool($status)===false || $status!=true || substr($status,0,6)=="Error:"){
			echo $status;
			exit;
		}
    	
		if($risk_score <= 0){//Only process orders automatically for the risk score below the threshold
		    //Capture the first payment - This function is in paypal.lib.php library
		    $status = get_pp_payment($cart_id,"AUD",$preapprovalkey,$cart_amount, PP_CANCEL_URL,$pp_return_url);
			if($status!==true){
		    	echo $status;
		    	release_in_process_lock($cart_id);//Release in_process lock before exiting
		    	exit;
		    }
		    
			//Update the shopping_cart record - Must be done before creating the payments received record.
			//This is done in case of PayPal in the get_pp_payment function (called above).
			//$status = $shopping_cart->update_paid($cart_id, "", "", "", $_SERVER['REMOTE_ADDR'],);
		    
			//Now create a payment received record
		    $status = create_payments_received_record($cart_id);
		    if(substr($status,0,6)=="Error:"){
		    	echo $status;
		    	release_in_process_lock($cart_id);//Release in_process lock before exiting
		    	exit;
		    }
		    
			//Now update the order_status in risk_score table as completed.
		    $status = $risk_score_obj->update_order_status($cart_id, 1);//Mark this order as completed : Order Status = 1
		    if($status !== true){
		    	echo $status;//Return error message from here.
		    	release_in_process_lock($cart_id);//Release in_process lock before exiting
		    	exit;
		    }
		    
		}else{//end of IF risk_score<7
			//Now update the order status as Payment Gateway AUTHORISED (order status=5 for authorised)
			$status = $risk_score_obj->update_order_status($cart_id,5);
			if(is_bool($status)===false || $status!=true){
				echo "Error: Failed to update the order status as authorised.";
				release_in_process_lock($cart_id);//Release in_process lock before exiting
				exit;
			}
		}
	    
	    //Now release the in_process lock
	    release_in_process_lock($cart_id);
	    
	    header("Location: $pp_return_url");
	    
    }//End of IF not marked as paid and also not in_process
    
    function release_in_process_lock($cart_id){
    	
	    $shopping_cart = new Shopping_cart();
	    if(is_object($shopping_cart)==false){
	    	echo "Error: Failed to create Shopping_cart object in release_in_process_lock function.";
	    	return false;
	    }
    	
    	$status = $shopping_cart->release_in_process_lock($cart_id);
	    if($status!==true){
	    	echo $status;
	    	return $status;
	    }
    	return true;
    }
    
    function is_preapproved($cart_id,$preapprovalkey){
    	
    	$post_data = "preapprovalKey=".$preapprovalkey.
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
		curl_setopt($ch, CURLOPT_URL, PP_AP_END_POINT."/PreapprovalDetails");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$response = urldecode(curl_exec($ch));
		curl_close($ch);
		
		$resp_status = "";
		$approved = "";
		$status = "";
		$senderEmail = "";
		
		$response_array = explode("&",$response);
    	foreach($response_array as $response_item){
			$resp_nv = explode("=",$response_item);
			if($resp_nv[0]=="responseEnvelope.ack"){
				$resp_status = $resp_nv[1];
			}elseif($resp_nv[0]=="approved"){
				$approved=$resp_nv[1];
			}elseif($resp_nv[0]=="status"){
				$status = $resp_nv[1];
			}elseif($resp_nv[0]=="senderEmail"){
				$senderEmail = $resp_nv[1];
			}
		}
		
		if($resp_status=="Success" && $approved=="true" && $status=="ACTIVE"){
			//Need to update the pp_sender_email is shopping_cart
			$shopping_cart = new Shopping_cart();
			if(is_object($shopping_cart)==false){
				return "Error: Failed to create Shopping_cart object in is_preapproved check function of pp_preapproval.";
			}
			$status = $shopping_cart->update_pp_sender_email($cart_id,$senderEmail);
			if(substr($status,0,6)=="Error:"){
				return $status;
			}
			return true;
		}else{
			return false;
		}
    	
    }