<?php
class Worldpay_autorenewals{
	
	function add_record($cart_id, $future_pay_id, $description, $amount, $trans_id, $trans_status, $trans_time, $trans_amount, $did_old_expiry_date, $did_new_expiry_date){
		
		$query = "insert into worldpay_autorenewals(
								cart_id, 
								future_pay_id, 
								description, 
								amount, 
								trans_id, 
								trans_status, 
								trans_time, 
								trans_amount, 
								did_old_expiry_date, 
								did_new_expiry_date 
								)values(
								$cart_id, 
								'$future_pay_id', 
								'$description', 
								$amount, 
								'$trans_id', 
								'$trans_status', 
								'$trans_time', 
								$trans_amount,
								'$did_old_expiry_date',
								'$did_new_expiry_date')";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return TRUE;
		}else{
			return "Error: An error occurred while adding a record to worldpay_autorenevals.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
	}
	
}