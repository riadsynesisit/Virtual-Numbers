<?php
class Worldpay_pending_captures{
	
	function add_wp_pending_captures_record($transId, $cartId){
		
		$query = "insert into wp_pending_captures(
								trans_id,
								cart_id,
								date_created
								) values (
								'".mysql_real_escape_string($transId)."', 
								".mysql_real_escape_string($cartId).", 
								now()
								);";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: Failed to insert record in wp_pending_captures. Error is: ".mysql_error().", Query is: ".$query;
		}
		return mysql_insert_id();
		
	}
	
	function update_wp_pending_captures($id,$status){
		
		$query = "update wp_pending_captures set status='".mysql_real_escape_string($status)."', date_captured=now() where id=".mysql_real_escape_string($id);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: Failed to update record in wp_pending_captures. Error is: ".mysql_error().", Query is: ".$query;
		}
		return true;
	}
	
}