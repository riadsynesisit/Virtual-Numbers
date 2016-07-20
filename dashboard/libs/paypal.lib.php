<?php

//This library uses the following classes
// 1. Shopping_cart.class.php

	function get_pp_payment($cart_id, $currency_code, $preapprovalkey, $amount, $cancel_url, $return_url){
    	
    	$post_data = "actionType=PAY".
    					"&cancelUrl=".$cancel_url.
						"&returnUrl=".$return_url.
						"&currencyCode=".$currency_code.
    					"&preapprovalKey=".$preapprovalkey.
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
	
		$resp_status = "";
		$payKey = "";
		$paymentExecStatus = "";
		$resp_time = "";
		$sender_trans_id = "";
		$sender_trans_status = "";
		$receiver_trans_id = "";
		$receiver_trans_status = "";
		
    	$response_array = explode("&",$response);
		foreach($response_array as $response_item){
			$resp_nv = explode("=",$response_item);
			if($resp_nv[0]=="responseEnvelope.ack"){
				$resp_status = $resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.timestamp"){
				$resp_time = $resp_nv[1];
			}elseif($resp_nv[0]=="payKey"){
				$payKey = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentExecStatus"){
				$paymentExecStatus = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentInfoList.paymentInfo(0).senderTransactionId"){
				$sender_trans_id = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentInfoList.paymentInfo(0).senderTransactionStatus"){
				$sender_trans_status = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentInfoList.paymentInfo(0).transactionId"){
				$receiver_trans_id = $resp_nv[1];
			}elseif($resp_nv[0]=="paymentInfoList.paymentInfo(0).transactionStatus"){
				$receiver_trans_status = $resp_nv[1];
			}
			
		}
		
    	if($resp_status=="Success" && $paymentExecStatus=="COMPLETED"){
	    	$shopping_cart = new Shopping_cart();
		    if(is_object($shopping_cart)==false){
		    	return "Error: Failed to create Shopping_cart object.";
		    }
    		$paid_status = $shopping_cart->update_paid($cart_id, $payKey, $paymentExecStatus, $resp_time, "0.0.0.0", $sender_trans_id, $sender_trans_status, $receiver_trans_id, $receiver_trans_status);
    		if(substr($paid_status,0,6)=="Error:"){
    			return $paid_status;//Return error message from here.
    		}
			return true;
		}else{
			return "Error: Failed to get payment from PayPal for pre_approval key $preapprovalkey. PP Response status is: ". $resp_status.", PP PaymentExec status is: ".$paymentExecStatus." Full response is: ".$response;
    	}
    	
    }
    
    function refund_pp_payment($cart_id, $amount, $currency_code, $payKey){
    	
    	$post_data = "currencyCode=".$currency_code.
						"&amount=".$amount.
    					"&payKey=".$payKey.
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
		curl_setopt($ch, CURLOPT_URL, PP_AP_END_POINT."/Refund");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$response = urldecode(curl_exec($ch));
		curl_close($ch);
		
		$resp_status = "";
		$resp_time = "";
		$refundStatus = "";
		$refundTransStatus = "";
		
		$response_array = explode("&",$response);
		foreach($response_array as $response_item){
			$resp_nv = explode("=",$response_item);
			if($resp_nv[0]=="responseEnvelope.ack"){
				$resp_status = $resp_nv[1];
			}elseif($resp_nv[0]=="responseEnvelope.timestamp"){
				$resp_time = $resp_nv[1];
			}elseif($resp_nv[0]=="refundInfoList.refundInfo(0).refundStatus"){
				$refundStatus = $resp_nv[1];
			}elseif($resp_nv[0]=="refundInfoList.refundInfo(0).refundTransactionStatus"){
				$refundTransStatus = $resp_nv[1];
			}
		}//end of foreach loop 
		
		if($resp_status=="Success" && ($refundStatus=="REFUNDED" || $refundStatus=="REFUNDED_PENDING")){
			return true;
		}else{
			return "Error: Failed to make PayPal refund for cart_id $cart_id and payKey $payKey. Response status is $resp_status, Refund Status is $refundStatus and refund transaction status is $refundTransStatus.";
		}
    	
    }