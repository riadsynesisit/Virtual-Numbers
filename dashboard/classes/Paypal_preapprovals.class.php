<?php
class Paypal_preapprovals{
	
	function add_record($user_id, $cart_id,$uuid,$preapprovalKey,$amount,$currency_code,$start_date,$end_date){
		
		$query = "insert into paypal_preapprovals(user_id, 
											cart_id, 
											uuid, 
											preapprovalkey,
											amount,
											avbl_amount,
											status,
											start_date,
											end_date,
											currency
											)values(
											$user_id, 
											$cart_id, 
											'$uuid', 
											'$preapprovalKey',
											$amount,
											$amount,
											'A',
											'$start_date',
											'$end_date', 
											'$currency_code'
											)";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while adding a record to paypal_preapprovals.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
	}
	
	function get_preapproval_key($cart_id){
		
		$query = "select preapprovalkey from paypal_preapprovals where cart_id=$cart_id";

		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows in paypal_preapprovals for cart_id $cart_id";
			}
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			return $row["preapprovalkey"];
		}else{
			return "Error: An error occurred while getting pre-approval key.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function update_avbl_amount($used_amount,$preapproval_key){
		
		$query = "update paypal_preapprovals set avbl_amount=avbl_amount-$used_amount where preapprovalkey='$preapproval_key'";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred while updating avbl_amount in paypal_preapprovals :".mysql_error();
		}
		
	}
	
	function cancel_preapproval($did_id){
		
		//First get the cart_id of this PP received transaction id
		$query = "select id from shopping_cart where did_id=$did_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting cart_id in paypal_preapprovals for DID ID: $did_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No record for DID ID: $did_id found in shopping_cart.";
		}
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$cart_id = $row["id"];
		//Now cancel the Paypal preapproval of this cart_id
		$query = "update paypal_preapprovals set status='C' where cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred in paypal_preapprovals while cancelling the future pay agreement for PP Reciever trans ID: $pp_receiver_trans_id. ".mysql_error();
		}
		
	}
	
	function disable_preapproval($preapproval_key){
		
		$query = "update paypal_preapprovals set status='D' where preapprovalkey='$preapproval_key'";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred in paypal_preapprovals while disabling the future pay agreement for PP Preapproval key: $preapproval_key. ".mysql_error();
		}
		
	}
	
	function does_preapproval_exist($did_id){
		
		$query = "select ppa.id as id 
					from paypal_preapprovals ppa, 
					shopping_cart sc 
					where ppa.cart_id=sc.id and 
						ppa.status='A' and 
						sc.did_id=$did_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting id in paypal_preapprovals for DID ID: $did_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return false;
		}else{
			return true;
		}
		
	}
	
	function disable_existing_preapprovals_of_same_did($cart_id){
		
		$query = "update paypal_preapprovals ppa, 
							shopping_cart sc1, 
							shopping_cart sc2 
						set ppa.status='D' 
						where ppa.cart_id=sc2.id and 
							sc1.did_id=sc2.did_id and 
							sc1.id=$cart_id and 
							ppa.cart_id!=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred in paypal_preapprovals while disabling the existing future pay agreements of the DID in shopping cart ID: $cart_id. ".mysql_error();
		}
		
	}

}