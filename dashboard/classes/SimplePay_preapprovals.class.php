<?php
class SimplePay_preapprovals{
	
	function add_record($cart_id,$agreement_id){
		
		//If there is a did_id for this cart_id record - check if there is an existing preapproval
		//If so, need to cancel the old one using iadmin of WP - There can be only one preapproval per did_id
		/*$query = "select pa.agreement_id as agreement_id 
					from worldpay_preapprovals pa, 
						shopping_cart sc
					where pa.cart_id=sc.id and
						sc.did_id is not null and
						sc.did_id != '' and
						sc.id=$cart_id";*/
		$query = "select pa.agreement_id as agreement_id 
					from simplepay_preapprovals pa
					where pa.did_id is not null and
						pa.did_id != '' and
						pa.did_id=(select did_id from shopping_cart where id=$cart_id and did_id is not null and did_id!='')";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking if a WP preapproval exists.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		if(mysql_num_rows($result)>0){
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			/* Need to cancel this by calling the iadmin
			$status = cancel_sp_agreement($row["agreement_id"],$cart_id,"New agreement $agreement_id takes over for cart_id $cart_id");
			if($status!==true){
				return "Error: Failed to cancel existing WP agreement - $status";
			} */
			$query = "update simplepay_preapprovals set cart_id=$cart_id, agreement_id='$agreement_id' where agreement_id='".$row["agreement_id"]."' ";
		}else{
			$query = "insert into simplepay_preapprovals(
											cart_id, 
											agreement_id,
											create_date
											)values(
											$cart_id, 
											'$agreement_id',
											now()
											)";
		}
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while adding or updating a record to simplepay_preapprovals.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
	}
	
	function get_agreement_id($did_id){
		
		$query = "select agreement_id from simplepay_preapprovals where did_id=$did_id";

		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows in simplepay_preapprovals for DID_id $did_id";
			}
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			return $row["agreement_id"];
		}else{
			return "Error: An error occurred while getting agreement_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_agreement_id_by_cart_id($cart_id){
		
		$query = "select agreement_id from simplepay_preapprovals where cart_id=$cart_id";

		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows in simplepay_preapprovals for cart_id $cart_id";
			}
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			return $row["agreement_id"];
		}else{
			return "Error: An error occurred while getting agreement_id for cart_id $cart_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function update_did_id($cart_id, $did_id){
		
		//Validate Inputs
		if(isset($cart_id)==false || trim($cart_id)==""){
			return "Error: Required parameter cart_id not passed to function update_did_id.";
		}
		if(isset($did_id)==false || trim($did_id)==""){
			return "Error: Required parameter did_id not passed to function update_did_id.";
		}
		
		$ag_id = $this->get_agreement_id_by_cart_id($cart_id);
		if(substr($ag_id,0,6)=="Error:"){
			//echo $status;
			return true;
		}
		
		$query = "update simplepay_preapprovals set did_id=$did_id where cart_id=$cart_id";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while updating did_id in simplepay_preapprovals.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	
	function cancel_preapproval($cart_id,$cancel_reason="", $simplepay_user='', $simplepay_pass='', $simplepay_entityId='', $widgetURL=''){
		
		
		//Get the WP FuturePay Agreement ID of this cart_id :: not needed
		$query = "select agreement_id from simplepay_preapprovals sp where status = 'A' AND cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting agreement_id in worldpay_preapprovals for cart_id: $cart_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No record found for this cart id";
		}
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$agreement_id = $row["agreement_id"];
		//Now cancel the WP Future Pay agreement
		$resp = $this->cancel_sp_agreement_request($agreement_id, $simplepay_user, $simplepay_pass, $simplepay_entityId, $widgetURL);
		$data = json_decode($resp, true);
		//echo '<br/><pre>';print_r($data); echo '</pre>';
		
		if($data['result']['code'] == SIMPLEPAY_SUCCESS_CODE){
			// successfully cancelled
			$query = "UPDATE simplepay_preapprovals SET status = 'D' WHERE agreement_id = '" . $agreement_id . "'";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while updating of simplepay_preapprovals for did_id: $did_id. But the agreement cancelled successfully. Plz contact DEV. ".mysql_error();
			}
			
			return true;
		}else{
			return "Error: An error occurred while cancelling the agreement of SIMPLE_PAY. RESPONSE returned: " . $data['result']['description'];
		}	
		
	}

	function does_preapproval_exist($did_id){
		
		$query = "select spa.id as id 
					from simplepay_preapprovals spa, 
					shopping_cart sc 
					where spa.cart_id=sc.id and 
						sc.did_id=$did_id and status = 'A'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking SimplePay preapproval existence for DID ID: $did_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return false;
		}else{
			return true;
		}
	}
	
	function cancel_sp_agreement_request($regID, $simplepay_user='', $simplepay_pass='', $simplepay_entityId='', $widgetURL='') {
		$url = $widgetURL ."v1/registrations/".$regID;
		$url .= "?authentication.userId=" . $simplepay_user;
		$url .= "&authentication.password=" . $simplepay_pass;
		$url .= "&authentication.entityId=" . $simplepay_entityId;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$responseData = curl_exec($ch);
		if(curl_errno($ch)) {
			return curl_error($ch);
		}
		curl_close($ch);
		return $responseData;
	}
	
	function sp_recurring_payment_request($regID, $amount, $currency, $simplepay_user='', $simplepay_pass='', $simplepay_entityId='', $widgetURL=''){
		$url = $widgetURL ."v1/registrations/".$regID."/payments";
		$data = "authentication.userId=" . $simplepay_user .
			"&authentication.password=" . $simplepay_pass .
			"&authentication.entityId=" . $simplepay_entityId .
			"&amount=" . $amount . 
			"&currency=" . $currency . 
			"&paymentType=DB". 
			"&recurringType=REPEATED";

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
		$data = json_decode($responseData, true);
		return $data;
	}
	
	function saveAutoRenewal($user_id, $amount, $agreement_id){
		
		$query = "insert into simplepay_autorenewals(
											user_id, 
											agreement_id,
											amount,
											create_date
											)values(
											$user_id, 
											'$agreement_id',
											'$amount',
											now()
											)";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while updating of simplepay_preapprovals for did_id: $did_id. But the agreement cancelled successfully. Plz contact DEV. ".mysql_error();
		}
		
		return true;
	}
	
}