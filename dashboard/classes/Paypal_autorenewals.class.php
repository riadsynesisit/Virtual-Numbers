<?php
class Paypal_autorenewals{
	
	function add_record($cart_id, $preapprovalkey, $amount, $pp_sender_trans_id, $pp_sender_trans_status, $pp_receiver_trans_id, $pp_receiver_trans_status){
		$query = "insert into paypal_autorenewals (
							cart_id, 
							preapprovalkey, 
							amount, 
							pp_sender_trans_id, 
							pp_sender_trans_status, 
							pp_receiver_trans_id, 
							pp_receiver_trans_status, 
							created_on
							)values(
							$cart_id, 
							'$preapprovalkey', 
							$amount,
							'$pp_sender_trans_id', 
							'$pp_sender_trans_status', 
							'$pp_receiver_trans_id', 
							'$pp_receiver_trans_status',
							now()
							)";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while adding a record to paypal_autorenewals.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
	}
	
}