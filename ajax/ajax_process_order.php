<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Shopping_cart.class.php");
    require("../dashboard/classes/SystemConfigOptions.class.php");
    require("../dashboard/classes/Unassigned_087DID.class.php");
    require("../dashboard/classes/Did_Cities.class.php");
    require("../dashboard/classes/Member_credits.class.php");
    require("../dashboard/classes/Assigned_DID.class.php");
    require("../dashboard/classes/Payments_received.class.php");
    require("../dashboard/classes/DidwwAPI.class.php");
    require("../dashboard/classes/Did_Countries.class.php");
    require("../dashboard/classes/VoiceMail.class.php");
    require("../dashboard/classes/Access.class.php");
    require("../dashboard/classes/AST_RT_Extensions.class.php");
    require("../dashboard/classes/Risk_score.class.php");
    require("../dashboard/classes/Paypal_preapprovals.class.php");
    require("../dashboard/classes/Worldpay_preapprovals.class.php");
	require("../dashboard/classes/SimplePay_preapprovals.class.php");
    require("../dashboard/classes/User.class.php");
    require("../dashboard/classes/Blacklist_ip.class.php");
    require("../dashboard/classes/Block_Destination.class.php");
    require("../dashboard/classes/Worldpay_refunds.class.php");
    require("../dashboard/classes/Client_notifications.class.php");
	require("../dashboard/classes/Auto_Renewal_Failures.class.php");
    require("../dashboard/libs/didww.lib.php");
    require("../dashboard/libs/worldpay.lib.php");
    require("../dashboard/libs/paypal.lib.php");
    require("../dashboard/libs/utilities.lib.php");
    
    
    //Allow only admin users to run this page
    if($_SESSION['admin_flag']!=1){
    	echo "Error: You are not authorized to run this page.";
    	exit;
    }
    
    //Validate input params
    $cart_id = "";
	if(isset($_POST['cart_id']) && trim($_POST['cart_id'])!=""){
    	$cart_id = trim($_POST['cart_id']);
	}elseif(isset($_GET['cart_id']) && trim($_GET['cart_id'])!=""){
    	$cart_id = trim($_GET['cart_id']);
    }else{
    	echo "Error: Required parameter cart_id not posted.";
    	exit;
    }
    
    $action = "";
    if(isset($_POST["action"]) && trim($_POST["action"])!=""){
    	$action = trim($_POST["action"]);
    }elseif(isset($_GET["action"]) && trim($_GET["action"])!=""){
    	$action = trim($_GET["action"]);
    }else{
    	echo "Error: Required parameter action not passed.";
    	exit;
    }
    
    $shopping_cart = new Shopping_cart();
	if(is_object($shopping_cart)==false){
		echo "Error: Failed to create Shopping_cart object.";
		exit;
	}
	
	//Get order details
	$cart_details = $shopping_cart->get_cart_details($cart_id);
	if(is_array($cart_details)==false && substr($cart_details,0,6)=="Error:"){
		echo $cart_details;
		exit;
	}
	
	if($cart_details["purchase_type"]=="renew_did" || $cart_details["purchase_type"]=="restore_did"){
		//Validate availability of DID_ID in shopping_cart record
		if($cart_details["did_id"]==""){
			echo "Error: No DID_ID found in cart_id $cart_id.";
			exit;
		}
	}
	
	if($cart_details["fulfilled"]=="Y" && ($action!="refund" || $action!="delete")){//This check is applicable only for cancel order
		echo "Error: This order with cart_id $cart_id was already fulfilled.";
		exit;
	}
	
	$user_id = $cart_details["user_id"];
	
	$status = "";

	switch($action){
		case "approve":
			$status = approve_order($cart_details);
			break;
		case "cancel":
			$status = cancel_order($cart_details);
			break;
		case "delete":
			$status = delete_order($cart_details);
			break;
		case "refund":
			$status = refund_order($cart_details);
			break;
		default:
			echo "Error: Unknown action: '$action' passed.";
			break;
	}
	
	if(is_bool($status)==false && substr($status,0,6)=="Error:"){
		$script_name = __FILE__ ;
		$auto_renewal_failures = new Auto_Renewal_Failures();
		$af_status = $auto_renewal_failures->insert_auto_renewal_failure($cart_id, $status, $action." order", $script_name, "Pending_Order_approve");
		
		$this_access = new Access();
		$this_access->updateAccessLogs($action."_Order :: ".$status, false, false, $cart_details["did_number"], $user_id);
				
		echo $status;//Return error status
	}else{
		echo "Success";
	}
	
	if($conn){
		mysql_close($conn);
	}
	exit;
	
	function approve_order($cart_details){
		//Capture the payment here
		$shopping_cart = new Shopping_cart();
		if(is_object($shopping_cart)==false){
			return "Error: Could not reate Shopping_cart object.";
		}
		$user = new User();
		if(is_object($user)==false){
			return "Error: Could not create User object.";
		}
		
		$user_details = $user->getUserInfoById($cart_details["user_id"]);
		$is_allow_over_4_credits = $user_details['allow_over_4_credits'];
		
		//$user_site_country = $user->get_user_site_country($cart_details["user_id"]);
		$user_site_country = $user_details['site_country'];
		if($user_site_country=="UK"){
			$user_currency="GBP";
		}else{
			$user_currency="AUD";
		}
		
		//Case 1: Payment is by WorldPay
		if($cart_details["pymt_gateway"]=="WorldPay"){
			//Validate transaction id
			if(trim($cart_details["transId"])==""){
				return "Error: No WP transId found for cart id: ".$cart_details["id"];
			}
			if(trim($cart_details["paid"])=="Y"){
				$status = true;//This order is already paid for - No need to capture again.
			}else{
				$status = capture_wp_payment($cart_details["transId"],$cart_details["id"]);
			}
			if($status===true){
				//Update the shopping_cart record - Must be done before creating the payments received record.
				if($blnError==false){
					$status = $shopping_cart->update_paid($cart_details["id"], $cart_details["transId"], $cart_details["transStatus"], $cart_details["transTime"], $_SERVER['REMOTE_ADDR']);
				}	
				//Now create a payment received record
			    $status = create_payments_received_record($cart_details["id"]);
			    if(substr($status,0,6)=="Error:"){
			    	return $status;
			    }
			    
			}else{
				return $status;//In case any error or unexpected return value
			}
		}elseif($cart_details["pymt_gateway"]=="PayPal"){
			//Retrieve Data about the Preapproval
		    $paypal_preapprovals = new Paypal_preapprovals();
		    if(is_object($paypal_preapprovals)==false){
		    	return "Error: Failed to create paypal_preapprovals object.";
		    }
			$preapprovalkey = $paypal_preapprovals->get_preapproval_key($cart_details["id"]);
		    if(substr($preapprovalkey,0,6)=="Error:" || trim($preapprovalkey)==""){
		    	return "Error: Failed to get preapproval key. ".$preapprovalkey;
		    }
			
			if( $cart_details["pp_sender_trans_status"] == "COMPLETED" && $cart_details["pp_receiver_trans_status"] == "COMPLETED" ){
				// no need to capture payment again...
			}
			else{
				//Capture the first payment
				$status = get_pp_payment($cart_details["id"],$user_currency,$preapprovalkey,$cart_details["amount"], PP_CANCEL_URL,PP_RETURN_URL);
				if($status!==true){
					return $status;
				}
				
				//Update the shopping_cart record - Must be done before creating the payments received record.
				//This is done in case of PayPal in the get_pp_payment function (called above).
				//$status = $shopping_cart->update_paid($cart_id, "", "", "", $_SERVER['REMOTE_ADDR'],);
				
				//Now create a payment received record
				$status = create_payments_received_record($cart_details["id"]);
				if(substr($status,0,6)=="Error:"){
					return $status;
				}
			}
		}elseif($cart_details["pymt_gateway"]=="Credit_Card"){
			
			$payments_received = new Payments_received();
			if(is_object($payments_received)==false){
				return "Error: Faile to create Payments_received object.";
			}
				
			$isCaptured = $payments_received->isCaptured($cart_details["last_pay_id"]);
			if($isCaptured == false){
				$status = simplepay_capture_pa($cart_details["transId"],$user_currency,$cart_details["amount"]);
				$data = json_decode($status, true);
				//echo $cart_details["pymt_gateway"]." ::= desc=".$data['result']['description'];
				if($data['result']['code'] == SIMPLEPAY_SUCCESS_CODE){
					$payID = $data['id'];
					// update payment received done.
					
					$status = $payments_received->updateSPcaptured($cart_details["id"], $payID, $cart_details["user_id"]);
					if($status!=true){
						return $status;
					}
					
				}else{
					return "Error:". $cart_details["pymt_gateway"] . " desc=".$data['result']['description'];
				}
			}else{
				
				// already taken the payment...so no need to capture again.
			}
			
		}else{
			return "Error: Unknown payment gateway '".$cart_details["pymt_gateway"]."' in shopping cart for cart ID: ".$cart_details["id"];
		}
		
		if($cart_details["pymt_type"]=="AccountCredit"){
			$member_credits = new Member_credits();
			if(is_object($member_credits)==false){
				return "Error: Failed to create Member_credits object.";
			}
			$status = $member_credits->addMemberCredits($cart_details["user_id"],$cart_details["amount"],$_SESSION['user_logins_id'], $is_allow_over_4_credits);
			if($status!==true){
				return $status;
			}
			$status = $shopping_cart->update_fulfilled_acct_credit($cart_details["id"]);
			if($status != "success"){
				return $status;//Return error message from here
			}
		}else{
			//Place the order with DIDWW
			$did_details = processDIDPurchase($cart_details["user_id"], $cart_details["did_plan"], $cart_details["plan_period"], $cart_details["num_of_months"], $cart_details["purchase_type"], $cart_details["pymt_type"], $cart_details["did_id"], $cart_details["id"], $cart_details["country_iso"], $cart_details["city_prefix"], $cart_details["fwd_number"], $cart_details["forwarding_type"]);
		
			if(is_array($did_details)==false && substr($did_details,0,6)=="Error:"){
				return $did_details;//Return the error message from here
			}
		}
		
		if($cart_details["fulfilled"]!="Y" && $cart_details["pymt_type"]!="AccountCredit"){//This code need not run for "auto" renewals
			if($cart_details["purchase_type"]=="renew_did" || $cart_details["purchase_type"]=="restore_did"){
				$did_id = $cart_details["did_id"];
			}else{
				$did_id = $did_details["assigned_did_id"];
				if($cart_details["purchase_type"]=="new_did"){
					//Send Number activated E-mail from here
					$status = send_number_activated_email(trim($user_details["email"]),trim($user_details["name"]),$cart_details["user_id"],trim($cart_details["description"]),$did_details["did_number"],$user_site_country);
					if(is_bool($status) && $status === true){
						//Email successfully sent
					}
				}
			}
			//Validate the availability of did_id
			if(trim($did_id)==""){
				return "Error: No did_id found while processing the order of cart ID: ".$cart_details["id"];
			}
			$status = $shopping_cart->update_fulfilled($cart_details["id"], $did_id);
			if($status == "success" && $cart_details["pymt_gateway"]=="WorldPay"){
				if($cart_details["pymt_type"]!="AccountCredit"){
					$wp_preapprovals = new Worldpay_preapprovals();
					if(is_object($wp_preapprovals)==false){
						return "Error: Could not create Worlppay_preapprovals object.";
					}
					$ret_val = $wp_preapprovals->update_did_id($cart_details["id"], $did_id);
					if($ret_val!==TRUE){
						return $ret_val;
					}
				}
			}elseif($status == "success" && $cart_details["pymt_gateway"]=="Credit_Card"){
				if($cart_details["pymt_type"]!="AccountCredit"){
					$sp_preapprovals = new SimplePay_preapprovals();
					if(is_object($sp_preapprovals)==false){
						return "Error: Could not create Simplepay_preapprovals object.";
					}
					$ret_val = $sp_preapprovals->update_did_id($cart_details["id"], $did_id);
					if($ret_val!==TRUE){
						return $ret_val;
					}
				}
			}elseif($status == "success" && $cart_details["pymt_gateway"]=="PayPal"){
				//Implementation for PayPal is yet to be done.
			}elseif($cart_details["pymt_gateway"]!="WorldPay" && $cart_details["pymt_gateway"]!="PayPal" && $cart_details["pymt_gateway"]!="Credit_Card"){
				return "Error: Unknown payment gateway ".$cart_details["pymt_gateway"]." for cart_id: ".$cart_details["id"];
			}else{
				return $status;//Return the error message if any from here. edited by RIAD.. 11th Nov 2015 not returned.
			}
		}

		if($cart_details["pymt_type"]!="AccountCredit"){
			//Update the preapprovalKey in assigned_dids table - used during auto renewals
			$assigned_dids = new Assigned_DID();
			if(is_object($assigned_dids)==false){
				return "Error: Could not create Assigned_DID object.";
			}
			$status = $assigned_dids->updatePreapprovalKey($did_id,$cart_details["id"]);
			if(is_bool($status)==false && substr($status,0,6)=="Error:"){
				return $status;
			}
		}
		
		//Now update the order_status in risk_score table as completed.
	    $risk_score = new Risk_score();
	    if(is_object($risk_score)==false){
	    	return "Error: Failed to create risk_score object.";
	    }
	    $status = $risk_score->update_order_status($cart_details["id"],1);//Mark this order as completed : Order Status = 1
	    if($status !== true){
	    	return $status;//Return error message from here.
	    }
		
		//Send an email to the client from here
		return true;

	}
	
	function cancel_order($cart_details){
		//Cancel the order here
		//Case 1: Payment is by WorldPay
		if($cart_details["pymt_gateway"]=="WorldPay"){
			$status = cancel_wp_authorization($cart_details["id"]);
			if($status===true){
				if($cart_details["pymt_type"]!="AccountCredit"){
					//cancel the future pay agreement here
					$wp_preapprovals = new Worldpay_preapprovals();
					if(is_object($wp_preapprovals)==false){
						return "Error: Could not create Worlppay_preapprovals object.";
					}
					$agreement_id = $wp_preapprovals->get_agreement_id_by_cart_id($cart_details["id"]);
					if(is_int($agreement_id)==false && substr($agreement_id,0,6)=="Error:"){
						return $agreement_id;//This is an error message
					}
					$status = cancel_wp_agreement($agreement_id,$cart_details["id"],"Order cancelled for high risk by admin.");
					if($status===true){
						
					}else{
						return $status;//In case any error or unexpected return value
					}
				}
			}else{
				return $status;//In case any error or unexpected return value
			}
		}elseif($cart_details["pymt_gateway"]=="PayPal"){
			//No code is required in this part.
		}elseif($cart_details["pymt_gateway"]=="Credit_Card"){
			//return "Error:Need to code for Credit Card Payment Cancellation....";
			$status = simplepay_reverse_pa($cart_details["transId"]);
			$data = json_decode($status, true);
			if($data['result']['code'] == SIMPLEPAY_SUCCESS_CODE){
				//delete the payment
				$payments_received = new Payments_received();
				if(is_object($payments_received)==false){
					return "Error: Failed to create Payments_received object.";
				}
				$status = $payments_received->deletePayment($cart_details["last_pay_id"]);
				if($status!=true){
					return $status;
				}
				
				if($cart_details["pymt_type"]!="AccountCredit"){
					//cancel the future pay agreement here
					$sp_preapprovals = new SimplePay_preapprovals();
					if(is_object($sp_preapprovals)==false){
						return "Error: Could not create Simplepay_preapprovals object.";
					}
					$status = $sp_preapprovals->cancel_preapproval($cart_details["id"],"Admin Cancelled Order", SIMPLEPAY_USER, SIMPLEPAY_PASS, SIMPLEPAY_ENTITYID, SIMPLEPAY_WIDGET_URL);
					if($status!=true){
						return $status;//This is an error message
					}
				}
			}else{
				return "Error: Simplepay response=".$data['result']['description'].' for trx id='.$cart_details["transId"];//In case any error or unexpected return value
			}
		}else{
			return "Error: Unknown payment gateway '".$cart_details["pymt_gateway"]."' in shopping cart for cart ID: ".$cart_details["id"];
		}
		//Update order status as cancelled - Order Status=4 for cancelled
		$risk_score = new Risk_score();
		if(is_object($risk_score)==false){
			return "Error: Could not create Risk_score object.";
		}
		$status = $risk_score->update_order_status($cart_details["id"],4);//Cancelled order status is 4.
		if($status!==true){
			return $status;//Return the error message from here
		}
		//Now email client as order is refunded
		return true;
		
	}
	
	function delete_order($cart_details){
		//Delete order must first be preceeded by cancel order
		$status = cancel_order($cart_details);
		if($status!==true){
			return $status;//Return the error message from here
		}
		//Now set the member account as suspended
		$user = new User();
		if(is_object($user)==false){
			return "Error: Could not create User object.";
		}
		$status = $user->suspendUser($cart_details["user_id"]);
		if($status===false){
			return "Error: Failed to suspend member account.";
		}
		//IP Address is saved in risk_score table
		$risk_score = new Risk_score();
		if(is_object($risk_score)==false){
			return "Error: Could not create Risk_score object.";
		}
		$ip_address = $risk_score->getIPAddress($cart_details["id"]);
		
		if(trim($ip_address)!=""){
			//Now blacklist user ip address
			$blacklist_ip = new Blacklist_ip();
			if(is_object($blacklist_ip)==false){
				return "Error: Could not create Blacklist_ip object.";
			}
			$status = $blacklist_ip->black_list_ip($ip_address,$cart_details["user_id"]);
			if($status!==true){
				return $status;
			}
		}
		if($cart_details["pymt_type"]!="AccountCredit"){
			//Add the forwarding number to blocked destinations
			$blocked_destinations = new Block_Destination();
			if(is_object($blocked_destinations)==false){
				return "Error: Could not create Block_Destination object.";
			}
			$status = $blocked_destinations->add_blocked_destination($cart_details["fwd_number"]);
			if($status!==true){
				return $status;
			}
		}
		
		$payments_received = new Payments_received();
		if(is_object($payments_received)==false){
			return "Error: Failed to create Payments_received object.";
		}
		$status = $payments_received->deletePayment($cart_details["last_pay_id"]);
		if($status!=true){
			return $status;
		}
				
		//Update order status as fraud deleted - Order Status=3 for fraud deleted
		$status = $risk_score->update_order_status($cart_details["id"],3);//Fraud deleted order status is 3.
		if($status!==true){
			return $status;//Return the error message from here
		}
		return true;
		
	}
	
	function refund_order($cart_details){
		
		$user = new User();
		if(is_object($user)==false){
			return "Error: Could not create User object.";
		}
		$user_site_country = $user->get_user_site_country($cart_details["user_id"]);
		if($user_site_country=="UK"){
			$user_currency="GBP";
		}else{
			$user_currency="AUD";
		}
		
		//Case 1: Payment is by WorldPay
		if($cart_details["pymt_gateway"]=="WorldPay"){
			if(trim($cart_details["transId"])==""){
				return "Error: WP Transaction ID is empty in shopping_cart for cart ID: ".$cart_details["id"];
			}
			$status = refund_wp_payment($cart_details["transId"],$cart_details["id"]);
			if($status===true){
				if($cart_details["pymt_type"]!="AccountCredit"){
					//cancel the future pay agreement here
					$wp_preapprovals = new Worldpay_preapprovals();
					if(is_object($wp_preapprovals)==false){
						return "Error: Could not create Worlppay_preapprovals object.";
					}
					$agreement_id = $wp_preapprovals->get_agreement_id_by_cart_id($cart_details["id"]);
					if(is_int($agreement_id)==false && substr($agreement_id,0,6)=="Error:"){
						return $agreement_id;//This is an error message
					}
					$status = cancel_wp_agreement($agreement_id,$cart_details["id"],"Order refunded by admin.");
					if($status===true){
						
					}else{
						return $status;//In case any error or unexpected return value
					}
				}
			}else{
				return $status;//In case any error or unexpected return value
			}
		}elseif($cart_details["pymt_gateway"]=="PayPal"){
			$payments_received = new Payments_received();
			if(is_object($payments_received)==false){
				return "Error: Faile to create Payments_received object.";
			}
			$payKey = $payments_received->getPayPalPayKey($cart_details["last_pay_id"]);
			if(substr($payKey,0,6)=="Error:"){
				return $payKey;//Return error message from here
			}
			$status = refund_pp_payment($cart_details["id"], $cart_details["amount"],$user_currency,$payKey);
			if($status!==true){
				return $status;//Return error message from here
			}
		}else{
			return "Error: Unknown payment gateway '".$cart_details["pymt_gateway"]."' in shopping cart for cart ID: ".$cart_details["id"];
		}
		if($cart_details["pymt_type"]!="AccountCredit"){
			//Need to delete the DID
			$assigned_did = new Assigned_DID();
			if(is_object($assigned_did)==false){
				return "Error: Failed to create Assigned_DID object.";
			}
			if(trim($cart_details["did_id"])==""){
				return "Error: No DID_ID in shopping_cart for cart ID: ".$cart_details["id"];
			}
			$did_number = $assigned_did->getDIDNumberFromID($cart_details["did_id"]);
			if($did_number===false){
				return "Error: Failed to get did_number from DID_ID:".$cart_details["did_id"];
			}
			$status = $assigned_did->deleteAUDIDData($cart_details["user_id"],$did_number,"Deleted from ajax process Order Refunded");
			if($status!==true){
				return "Error: Failed to delete AU DID Data.";
			}
		}
		$risk_score = new Risk_score();
		if(is_object($risk_score)==false){
			return "Error: Could not create Risk_score object.";
		}
		$status = $risk_score->update_order_status($cart_details["id"],2);//Refunded order status is 2.
		if($status!==true){
			return $status;//Return the error message from here
		}
		
		return true;
	}
	
function send_number_activated_email($email,$name,$user_id,$description,$did_number,$site_country){
	
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

	$subject_line = $did_number." | Virtual Number Activated";
	$email_html = '
		<html>
			<head>
			  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>DID Number Activated Email Notification</title>
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
    						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#0C6; color:#fff;">
    							<tr>
    								<td style="text-align:center;">
    									<h1 style="margin-bottom:20px; font-size:40px; line-height:44px;">Congratulations! Online Number Activated</h1>
    									<h3 style="margin:0 0 20px 0;">Your online number is deactivated</h3>
    								</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:20px; background-color:#F5F5F5"><h3 style="margin-bottom:0">Hello '.$name.',</h3>
    						<h4 style="margin:0 0 25px 0">Your Online Number has been <span style="color:#090">Activated.</span></h4>
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
										    		<p style="margin:0">Status</p><p style="margin:0; color:#090"><strong>Activated</strong></p>
										    	</td>
										  	</tr>
										  	<tr>
										  		<td style="padding-top:15px">
										  			<p style="margin:0;"><strong>'.$description.' - (+'.$did_number.')</strong></p>
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