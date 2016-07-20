<?php
class Worldpay_preapprovals{
	
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
					from worldpay_preapprovals pa
					where pa.did_id is not null and
						pa.did_id != '' and
						pa.did_id=(select did_id from shopping_cart where id=$cart_id and did_id is not null and did_id!='')";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking if a WP preapproval exists.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		if(mysql_num_rows($result)>0){
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			//Need to cancel this by calling the iadmin
			$status = cancel_wp_agreement($row["agreement_id"],$cart_id,"New agreement $agreement_id takes over for cart_id $cart_id");
			if($status!==true){
				return "Error: Failed to cancel existing WP agreement - $status";
			}
			$query = "update worldpay_preapprovals set cart_id=$cart_id, agreement_id=$agreement_id where agreement_id=".$row["agreement_id"];
		}else{
			$query = "insert into worldpay_preapprovals(
											cart_id, 
											agreement_id,
											create_date
											)values(
											$cart_id, 
											$agreement_id,
											now()
											)";
		}
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while adding or updating a record to worldpay_preapprovals.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
	}
	
	function get_agreement_id($did_id){
		
		$query = "select agreement_id from worldpay_preapprovals where did_id=$did_id";

		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows in worldpay_preapprovals for DID_id $did_id";
			}
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			return $row["agreement_id"];
		}else{
			return "Error: An error occurred while getting agreement_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_agreement_id_by_cart_id($cart_id){
		
		$query = "select agreement_id from worldpay_preapprovals where cart_id=$cart_id";

		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows in worldpay_preapprovals for cart_id $cart_id";
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
		
		$query = "update worldpay_preapprovals set did_id=$did_id where cart_id=$cart_id";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while updating did_id in worldpay_preapprovals.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function cancel_preapproval($did_id,$cancel_reason=""){
		
		//First get the cart_id of this transaction id
		$query = "select id from shopping_cart where did_id=$did_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting cart_id in shopping_cart for did_id: $did_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No record for did_id: $did_id found in shopping_cart.";
		}
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$cart_id = $row["id"];
		//Now get the WP FuturePay Agreement ID of this cart_id
		$query = "select agreement_id from worldpay_preapprovals where cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting agreement_id in worldpay_preapprovals for cart_id: $cart_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return true;
		}
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$agreement_id = $row["agreement_id"];
		//Now cancel the WP Future Pay agreement
		return cancel_wp_agreement($agreement_id,$cart_id,$cancel_reason);
		
	}

	function does_preapproval_exist($did_id){
		
		$query = "select wpa.id as id 
					from worldpay_preapprovals wpa, 
					shopping_cart sc 
					where wpa.cart_id=sc.id and 
						sc.did_id=$did_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking WorldPay preapproval existence for DID ID: $did_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return false;
		}else{
			return true;
		}
	}
	
}