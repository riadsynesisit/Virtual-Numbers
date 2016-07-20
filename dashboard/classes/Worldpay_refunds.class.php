<?php
class Worldpay_refunds{
	
	function add_wp_refund_record($cart_id,$trans_id){
		
		$query = "insert into worldpay_refunds (cart_id, trans_id, date_added) values ($cart_id,'$trans_id', now())";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while adding a record to worldpay_refunds for cart_id $cart_id and trans_id $trans_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function update_wp_refund_record($trans_id,$wp_response){
		
		$query = "update worldpay_refunds set date_refunded=now(), wp_response='$wp_response';";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while updating worldpay_refunds for trans_id $trans_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
}