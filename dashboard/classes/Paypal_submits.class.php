<?php
class Paypal_submits{
	
	function add_record($user_id,$cart_id,$uuid,$payKey,$amount,$currency_code,$payment_for){
		
		$query = "insert into paypal_submits(user_id, 
											cart_id, 
											uuid, 
											paykey,
											amount,
											currency, 
											status,
											submit_time, 
											payment_for
											)values(
											$user_id, 
											$cart_id, 
											'$uuid', 
											'$payKey',
											$amount,
											'$currency_code', 
											'CREATED',
											now(),
											'$payment_for'
											)";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while adding a record to paypal_submits.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function is_already_processed($payKey){
		
		$query = "select status from paypal_submits where paykey='$payKey'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: ".mysql_error();
		}
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		if($row["status"]=="COMPLETED"){
			return TRUE;
		}else{
			return FALSE;
		}
		
	}
	
	function get_paykey($cart_id){
		
		$query = "select paykey from paypal_submits where cart_id=$cart_id";

		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows in paypal_submits for cart_id $cart_id";
			}
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			return $row["paykey"];
		}else{
			return "Error: An error occurred while getting paykey.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_paykey_from_cartid_and_transactionid($cart_id,$txn_id){
		
		$query = "select paykey from paypal_submits where cart_id=$cart_id and txn_id='$txn_id'";

		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows in paypal_submits for cart_id $cart_id and txn_id $txn_id";
			}
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			return $row["paykey"];
		}else{
			return "Error: An error occurred while getting paykey from cart id and transaction id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
}