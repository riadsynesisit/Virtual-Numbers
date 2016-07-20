<?php

function cancel_wp_agreement($agreement_id,$cart_id,$cancel_reason=""){
		
	//Use curl to post data to WP iadmin servlet
	$post_data = "instId=".WP_IADMIN_INSTID.
					"&authPW=".WP_IADMIN_AUTHPW.
					"&futurePayId=".$agreement_id.
					"&op-cancelFP=true";
	$content_length = strlen($post_data);
	$headers_data = array("Content-type: application/x-www-form-urlencoded", 
							"Content-length: $content_length"
							);
	
	$ch = curl_init();
	if($ch==FALSE){
		return "Error: Failed to create a cURL handle.";
	}
	curl_setopt($ch, CURLOPT_URL, WP_IADMIN_URL);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
	$response = urldecode(curl_exec($ch));
	curl_close($ch);
	
	$response_array = explode(',',$response);
	if($response_array[0]=="Y" || trim($response)=="E,Agreement already finished"){//Agreement Cancelled
		//Now delete the record from worldpay_preapprovals table
		$query = "delete from worldpay_preapprovals where agreement_id=$agreement_id;";
		$result = mysql_query($query);
		if(mysql_error()==""){
			//insert a record into wp_agreement_cancellations table
			$query = "insert into wp_agreement_cancellations (agreement_id, cart_id, cancelled_reason, date_cancelled) values ('$agreement_id',$cart_id,'$cancel_reason',now())";
			mysql_query($query);
			if(mysql_error()!=""){
				return "Error: Failed to insert the record into wp_agreement_cancellations. Error is: ".mysql_error();
			}
			return true;
		}else{
			return "Error: Failed to delete the record from worldpay_preapprovals: ".mysql_error();
		}
	}else{
		return "Error: Failed to cancel WP Agreement $agreement_id - $response_array[1] - response from ".WP_IADMIN_URL." is $response";
	}
	
}

function capture_wp_payment($transId, $cart_id){
	
	//Validate input parameters
	if(trim($transId)==""){
		return "Error: Required parameter trandID not passed to capture_wp_payment for cart_id $cart_id.";
	}
	if(trim($cart_id)==""){
		return "Error: Required parameter cart_id not passed to capture_wp_payment for cart_id $cart_id.";
	}
	
	if(strpos(WP_ITRANS_URL,"secure-test")>0){//Test environment
		$test_string = "&testMode=100";
	}else{
		$test_string = "";
	}
	
	//Use curl to post data to WP iadmin servlet
	$post_data = "instId=".WP_IADMIN_INSTID.
					"&authPW=".WP_IADMIN_AUTHPW.
					"&authMode=O$test_string".
					"&op=postAuth-full".
					"&transId=$transId";
	$content_length = strlen($post_data);
	$headers_data = array("Content-type: application/x-www-form-urlencoded", 
							"Content-length: $content_length"
							);
	$ch = curl_init();
	if($ch==FALSE){
		return "Error: Failed to create a cURL handle.";
	}
	curl_setopt($ch, CURLOPT_URL, WP_ITRANS_URL);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
	$response = urldecode(curl_exec($ch));
	curl_close($ch);
	
	$response_array = explode(',',$response);
	
	if($response_array[0]=="A"){
		return true;
	}else{
		return "Error: Failed to capture WP Transaction: $transId. Response from WP is: ".$response;
	}
	
}

function refund_wp_payment($transId, $cart_id){
	
	if(strpos(WP_ITRANS_URL,"secure-test")>0){//Test environment
		$test_string = "&testMode=100";
	}else{
		$test_string = "";
	}
	
	//Use curl to post data to WP iadmin servlet
	$post_data = "instId=".WP_IADMIN_INSTID.
					"&authPW=".WP_IADMIN_AUTHPW.
					"&cartId=$cart_id".$test_string.
					"&op=refund-full".
					"&transId=$transId";
	$content_length = strlen($post_data);
	$headers_data = array("Content-type: application/x-www-form-urlencoded", 
							"Content-length: $content_length"
							);
	$ch = curl_init();
	if($ch==FALSE){
		return "Error: Failed to create a cURL handle.";
	}
	curl_setopt($ch, CURLOPT_URL, WP_ITRANS_URL);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
	$response = urldecode(curl_exec($ch));
	curl_close($ch);
	
	$response_array = explode(',',$response);
	
	if($response_array[0]=="A"){
		//Mark this order as refunded : Order Status = 2
		$query = "update risk_score set order_status=2 where cart_id=$cart_id";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: Failed to update order_status as refunded in risk_score. ".mysql_error();
		}
		return true;
	}else{
		return "Error: Failed to cancel WP Transaction: $transId. Response from WP is: ".$response. " Data posted to ".WP_ITRANS_URL." is ".$post_data;
	}
	
}

function cancel_wp_authorization($cart_id){
	
	//Mark this order as cancelled : Order Status = 4
	$query = "update risk_score set order_status=4 where cart_id=$cart_id";
	mysql_query($query);
	if(mysql_error()!=""){
		return "Error: Failed to update order_status as cancelled in risk_score for cart_id $cart_id. ".mysql_error();
	}
	return true;
	
}