<?php
	class Shopping_cart{
		
		function add_to_shopping_cart($user_id, $amount, $purchase_type, $pymt_type, $pymt_gateway, $did_plan='NULL', $plan_period="", $num_of_months, $description='NULL', $country_iso="", $city_prefix='NULL', $fwd_number, $return_to, $did_id='NULL', $did_number='NULL', $forwarding_type=1, $sip_address='NULL'){
			if($plan_period!='NULL'){
				$plan_period_val = "'".$plan_period."'";
			}else{
				$plan_period_val = 'NULL';
			}
			if($num_of_months!='NULL'){
				$num_of_months_val = $num_of_months;
			}else{
				$num_of_months_val = 'NULL';
			}
			if($country_iso!='NULL'){
				$country_iso_val = "'".$country_iso."'";
			}else{
				$country_iso_val = 'NULL';
			}
			if($did_id!='NULL' && trim($did_id)!=""){
				$did_id_val = $did_id;
			}else{
				$did_id_val = 'NULL';
			}
			if($purchase_type=='renew_did' || $purchase_type=='restore_did'){
				if($did_id_val=='NULL'){
					return "Error: Missing did_id in the parameters of add_to_shopping_cart.";
				}
			}
			if($did_number!='NULL' && trim($did_number)!=""){
				$did_number_val = $did_number;
			}else{
				$did_number_val = 'NULL';
			}
			if($description!='NULL' && trim($description)!=""){
				$description_val = "'".$description."'";
			}else{
				$description_val = 'NULL';
			}
			//Insert IP address from where shopping_cart page is accessed
			if(isset($_SERVER["REMOTE_ADDR"]) && trim($_SERVER["REMOTE_ADDR"])!=""){
				$ipAddress = "'".$_REQUEST["REMOTE_ADDR"]."'";
			}else{
				$ipAddress = 'NULL';
			}
			
			$query = "insert into shopping_cart (user_id, amount, purchase_type, pymt_type, pymt_gateway, did_plan, plan_period, num_of_months, description, did_number, did_id, country_iso, city_prefix, forwarding_type, fwd_number, sip_address, return_to, ipAddress, order_create_time) values ($user_id, $amount, '$purchase_type', '$pymt_type', '$pymt_gateway', $did_plan, $plan_period_val, $num_of_months_val, $description_val, $did_number_val, $did_id_val, $country_iso_val, $city_prefix, $forwarding_type, '$fwd_number', '$sip_address', '$return_to', $ipAddress, now())";
			
			mysql_query($query);
			if(mysql_error() == ""){
				return mysql_insert_id();
			}else{
				return "Error: An error occurred while adding shopping_cart record. Error is: ".mysql_error(). "Query is : $query";
			}
		}
		
		function update_paid($id, $transId, $transStatus, $transTime, $ipAddress, $sender_trans_id, $sender_trans_status, $receiver_trans_id, $receiver_trans_status){
			$query = "update shopping_cart set paid='Y', transId='$transId', transStatus='$transStatus', transTime='$transTime', ipAddress='$ipAddress', pp_sender_trans_id='$sender_trans_id', pp_sender_trans_status='$sender_trans_status', pp_receiver_trans_id='$receiver_trans_id', pp_receiver_trans_status='$receiver_trans_status' where id=$id";
			mysql_query($query);
			if(mysql_error() == ""){
				return "success";
			}else{
				return "Error: An error occurred while updating shopping_cart record. Error is: ".mysql_error()."<br>".$query;
			}
		}
		
		function update_trans_details($id, $transId, $transStatus, $transTime, $ipAddress, $sender_trans_id, $sender_trans_status, $receiver_trans_id, $receiver_trans_status){
			$query = "update shopping_cart set transId='$transId', transStatus='$transStatus', transTime='$transTime', ipAddress='$ipAddress', pp_sender_trans_id='$sender_trans_id', pp_sender_trans_status='$sender_trans_status', pp_receiver_trans_id='$receiver_trans_id', pp_receiver_trans_status='$receiver_trans_status' where id=$id";
			mysql_query($query);
			if(mysql_error() == ""){
				return "success";
			}else{
				return "Error: An error occurred while updating shopping_cart record with transaction details. Error is: ".mysql_error();
			}
		}
		
		function update_paid_renewals($id, $transTime, $ipAddress){
			$query = "update shopping_cart set paid='Y', transTime='$transTime', ipAddress='$ipAddress' where id=$id";
			mysql_query($query);
			if(mysql_error() == ""){
				return "success";
			}else{
				return "Error: An error occurred while updating shopping_cart record in update_paid_renewal. Error is: ".mysql_error()." Query is: $query";
			}
		}
		
		function update_pp_sender_email($id, $senderEmail){
			$query = "update shopping_cart set pp_sender_mail='$senderEmail' where id=$id";
			mysql_query($query);
			if(mysql_error() == ""){
				return "success";
			}else{
				return "Error: An error occurred while updating shopping_cart record in update_pp_sender_email. Error is: ".mysql_error()." Query is: $query";
			}
		}
		
		function get_cart_details($id){
			$query = "select * from shopping_cart where id=$id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting shopping cart details: ".mysql_error(). " Query is: ".$query;
			}
			
			return mysql_fetch_array($result,MYSQL_ASSOC);
		}
		
		function update_fulfilled($id,$did_id=NULL){
			if(is_null($did_id) || trim($did_id)==""){
				$did_id_val = 'NULL';
			}else{
				$did_id_val = $did_id;
			}
			$query = "update shopping_cart set fulfilled='Y', fulfilled_time=now(), did_id=$did_id_val where id=$id";
			mysql_query($query);
			if(mysql_error() == ""){
				return "success";
			}else{
				return "Error: An error occurred while updating shopping_cart record for fulfilled status. Error is: ".mysql_error()." Query is: $query";
			}
		}
		
		function update_fulfilled_acct_credit($id){
			$query = "update shopping_cart set fulfilled='Y', fulfilled_time=now() where id=$id";
			mysql_query($query);
			if(mysql_error() == ""){
				return "success";
			}else{
				return "Error: An error occurred while updating shopping_cart record for fulfilled status. Error is: ".mysql_error()." Query is: $query";
			}
		}
		
		function update_last_pay_id($id, $last_pay_id){
			
			$query = "update shopping_cart set last_pay_id=$last_pay_id where id=$id";
			mysql_query($query);
			if(mysql_error() == ""){
				return "success";
			}else{
				return "Error: An error occurred while updating shopping_cart record $id for last_pay_id $last_pay_id. Error is: ".mysql_error();
			}
			
		}
		
		function get_return_to($id){
			$query = "select return_to from shopping_cart where id=$id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting return_to location from shopping_cart : ".mysql_error();
			}
			if(mysql_num_rows($result)==0){
				return "Error: No rows returned from shopping_cart for id: ".$id;
			}
			
			$row = mysql_fetch_assoc($result);
			
			return $row["return_to"];
		}
		
		function getDIDDetails($cartId){
			
			$query = "select ad.id as id,
								ad.did_number as did_number, 
								ad.did_expires_on as did_expires_on, 
								pr.minutes_credited as minutes_credited
							from  assigned_dids ad, 
								 payments_received pr, 
								 shopping_cart sc 
							where ad.id = sc.did_id and
								pr.assigned_dids_id = sc.did_id and
								sc.id=$cartId";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting return_to location from shopping_cart : ".mysql_error();
			}
			
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			return array("did_number"=>$row["did_number"], "num_of_minutes"=>$row["minutes_credited"], "did_expiry"=>$row["did_expires_on"], "assigned_did_id"=>$row["id"]);
			
		}
		
		function get7DaysFulfilledOdersCount($cart_id){
			
			$query = "select count(*) as orders_count from shopping_cart where user_id=(select user_id from shopping_cart where id=$cart_id) and fulfilled='Y' and purchase_type='new_did' and date_add(fulfilled_time, INTERVAL 7 DAY) >= NOW()";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting 7 days new did fulfilled orders count from shopping_cart : ".mysql_error();
			}
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			return $row["orders_count"];
			
		}
		
		function getApprovedOrdersCountOfUser($user_id){
			
			$query = "select count(*) as orders_count from shopping_cart where user_id=$user_id and paid='Y' and fulfilled='Y' and purchase_type='new_did'";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while approved orders count of user_id $user_id from shopping_cart : ".mysql_error();
			}
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			return $row["orders_count"];
		}
		
		function get7DaysAcctCreditOdersCount($cart_id){
			
			$query = "select count(*) as orders_count from shopping_cart where user_id=(select user_id from shopping_cart where id=$cart_id) and fulfilled='Y' and purchase_type='account_credit' and date_add(fulfilled_time, INTERVAL 7 DAY) >= NOW()";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting 7 days account credit fulfilled orders count from shopping_cart : ".mysql_error();
			}
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			return $row["orders_count"];
			
		}
		
		function place_in_process_lock($cart_id){
			
			$query = "update shopping_cart set in_process_lock='Y' where id=$cart_id and in_process_lock!='Y'";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while placing a in-process_lock to shopping_cart id $cart_id : ".mysql_error();
			}
			if(mysql_affected_rows()==1){
				return true;
			}else{
				return "Error: Unable to place in-process lock on shoppinc_cart record id $cart_id";
			}
			
		}
		
		function release_in_process_lock($cart_id){
			
			$query = "update shopping_cart set in_process_lock='N' where id=$cart_id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while releasing the in-process_lock to shopping_cart id $cart_id : ".mysql_error();
			}
			return true;
			
		}
		
		function check_in_process_lock($cart_id){
			
			$query = "select in_process_lock from shopping_cart where id=$cart_id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting in-process-lock from shopping_cart id $cart_id : ".mysql_error();
			}
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			return $row["in_process_lock"];
			
		}
		
		function check_is_paid($cart_id){
			
			$query = "select paid from shopping_cart where id=$cart_id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting paid status from shopping_cart id $cart_id : ".mysql_error();
			}
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			return $row["paid"];
			
		}
		
		function reset_paid_fulfilled($cart_id){
			
			$query = "update shopping_cart set paid='N', fulfilled='N' where id=$cart_id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while resetting paid and fulfilled i shopping_cart id $cart_id : ".mysql_error();
			}
			return 'success';
			
		}
		
		function getUserDetailsByCartID($cart_id){
			$query = "select `name`, `email`, site_country, user_id, amount from shopping_cart sc left join user_logins ul on sc.user_id = ul.id where sc.id=$cart_id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting info shopping_cart id $cart_id : ".mysql_error();
			}else if(mysql_num_rows($result) > 0){
				return mysql_fetch_array($result);
			}else{
				return "Error: no rows found!";
			}
			
		}
		
	}// end of class