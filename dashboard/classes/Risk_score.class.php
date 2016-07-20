<?php
class Risk_score{
	
	public $id;
	public $order_status;
	public $cart_id;
	public $ip_address;
	public $ip_location;
	public $risk_score;
	public $high_purchase_activity;
	public $country_blacklisted;
	public $free_provider_email;
	public $ip_blacklisted;
	public $ip_not_matches_country;
	public $fwd_num_not_match_site;
	public $multiple_fwd_numbers;
	public $fwd_num_blacklisted;
	public $new_member;
	public $order_over_20_dollars;
	public $over_2_nums_in_7_days;
	public $over_1_acct_credit_in_7_days;
	public $docs_required;
	
	function add_record(){
		
		//First check if a recrod already exists for the cart_id and if it does return the id of the record
		//This is done because of the "duplicate key" issues due to multiple hits of PayPal return URL by PayPal
		$query = "select id from risk_score where cart_id=$this->cart_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking if a record exists in risk_score for cart_id $cart_id. Error is: ".mysql_error();
		}
		if(mysql_num_rows($result)!=0){
			$row = mysql_fetch_assoc($result);
			$this->id = $row["id"];
			return $this->id;
		}
		
		$query = "insert into risk_score(
						order_status, 
						cart_id, 
						ip_address, 
						ip_location, 
						risk_score, 
						high_purchase_activity, 
						country_blacklisted, 
						free_provider_email, 
						ip_blacklisted, 
						ip_not_matches_country, 
						fwd_num_not_match_site, 
						multiple_fwd_numbers, 
						fwd_num_blacklisted, 
						new_member, 
						order_over_20_dollars, 
						over_2_nums_in_7_days, 
						over_1_acct_credit_in_7_days,
						docs_required
					)values(
						".$this->order_status.", 
						".$this->cart_id.", 
						'".$this->ip_address."', 
						'".$this->ip_location."', 
						".$this->risk_score.", 
						'".$this->high_purchase_activity."', 
						'".$this->country_blacklisted."', 
						'".$this->free_provider_email."', 
						'".$this->ip_blacklisted."', 
						'".$this->ip_not_matches_country."', 
						'".$this->fwd_num_not_match_site."', 
						'".$this->multiple_fwd_numbers."', 
						'".$this->fwd_num_blacklisted."', 
						'".$this->new_member."', 
						'".$this->order_over_20_dollars."', 
						'".$this->over_2_nums_in_7_days."', 
						'".$this->over_1_acct_credit_in_7_days."',
						'".$this->docs_required."'
					)";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$this->id = mysql_insert_id();
			return $this->id;
		}else{
			return "Error: An error occurred while inserting Risk Score record.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_pending_orders_count(){
		
		$query = "select count(*) as count from risk_score where order_status=0";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			return $row["count"];
		}else{
			return "Error: An error occurred while getting count of pending orders.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_orders_count_of_status($status){
		
		$query = "select count(*) as count from risk_score where order_status=$status";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			return $row["count"];
		}else{
			return "Error: An error occurred while getting count of count of orders of status $status.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_cancelled_orders_count(){
		
		$query = "select count(*) as count from risk_score where order_status=4";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			return $row["count"];
		}else{
			return "Error: An error occurred while getting count of cancelled orders.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_deleted_orders_count(){
		
		$query = "select count(*) as count from risk_score where order_status=3";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			return $row["count"];
		}else{
			return "Error: An error occurred while getting count of deleted orders.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_refunded_orders_count(){
		
		$query = "select count(*) as count from risk_score where order_status=2";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			return $row["count"];
		}else{
			return "Error: An error occurred while getting count of refunded orders.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_approved_orders_count(){
		
		$query = "select count(*) as count from risk_score where order_status=1";//Completed or approved order
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			return $row["count"];
		}else{
			return "Error: An error occurred while getting count of approved orders.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_risk_orders($status,$limit,$offset){
		
		//For the $status value refer to order_statuses table
		//0=Pending
		//1=Completed
		//2=Refunded
		//3=FraudDeleted
		
		$query = "select sc.id as cart_id, 
						sc.user_id as user_id, 
						sc.amount as amount, 
						sc.description as description, 
						sc.did_number as did_number, 
						sc.transTime as transTime, 
						sc.pymt_type as pymt_type, 
						sc.pymt_gateway as pymt_gateway, 
						rs.risk_score as risk_score, 
						rs.order_status as order_status, 
						rs.ip_address as ip_address,
						rs.ip_location as ip_location, 
						rs.high_purchase_activity as high_purchase_activity, 
						rs.country_blacklisted as country_blacklisted,
						rs.free_provider_email as free_provider_email, 
						rs.ip_blacklisted as ip_blacklisted, 
						rs.ip_not_matches_country as ip_not_matches_country, 
						rs.fwd_num_not_match_site as fwd_num_not_match_site, 
						rs.multiple_fwd_numbers as multiple_fwd_numbers, 
						rs.fwd_num_blacklisted as fwd_num_blacklisted, 
						rs.new_member as new_member,
						rs.docs_required as docs_required,
						rs.is_mail_sent as is_mail_sent,						
						rs.order_over_20_dollars as order_over_20_dollars, 
						rs.over_2_nums_in_7_days as over_2_nums_in_7_days, 
						rs.over_1_acct_credit_in_7_days as over_1_acct_credit_in_7_days, 
						ul.status as member_status, 
						ul.site_country as site_country, 
						ul.id as member_id 
					from shopping_cart sc, 
						risk_score rs, 
						user_logins ul 
					where rs.cart_id=sc.id and 
						sc.user_id=ul.id and
						rs.order_status=$status LIMIT $limit OFFSET $offset";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "";
			}else{
				$ret_arr = array();
				while($row=mysql_fetch_assoc($result)){
					$ret_arr[]=$row;
				}
				return $ret_arr;
			}
		}else{
			return "Error: An error occurred while getting risk orders of status $status.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}

	function is_payment_completed($cart_id){//if a record exists in risk_score table and if the order_status field of risk_score is 1 (completed / captured)
		
		$query = "select order_status from risk_score where cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return true;//No record in risk score exists - risk score record is being created only for WP payments to start with
			}
			$row=mysql_fetch_assoc($result);
			if(intval($row["order_status"])==1){
				return true;//Order completed or captured
			}else{
				return false;//Order is not completed or not captured
			}
		}else{
			return "Error: An error occurred while getting risk_orders.order_status of cart_id $cart_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function update_order_status($cart_id,$status){
		
		//First check if a record for this cart_id exists
		$query = "select count(*) as count from risk_score where cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row=mysql_fetch_assoc($result);
			if(intval($row["count"])==0){
				return "Error: No records found in risk_score for cart_id=$cart_id.";
			}elseif(intval($row["count"])>1){
				return "Error: More than one record ".$row["count"]." in risk_score found for cart_id=$cart_id.";
			}
		}else{
			return "Error: An error while getting count of orders in risk_score for cart_id $cart_id.";
		}
		
		$query = "update risk_score set order_status=$status where cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			//if(mysql_affected_rows()!=1){
			//	return "Error: Unexpected number of affected rows: ".mysql_affected_rows()." while updating order status. Expected value is 1. Query is $query";
			//}
			return true;
		}else{
			return "Error: An error occurred while updating risk_orders.order_status of cart_id $cart_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_order_status($cart_id){
		
		$query = "select order_status from risk_score where cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows found in risk_score for cart_id $cart_id";
			}
			$row=mysql_fetch_assoc($result);
			return $row["order_status"];
		}else{
			return "Error: An error occurred while getting risk_orders.order_status of cart_id $cart_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function get_risk_info($cart_id){
		
		$query = "select rs.id as id, 
						rs.order_status as order_status,
						rs.cart_id as cart_id,
						rs.ip_address as ip_address,
						rs.ip_location as ip_location,
						rs.risk_score as risk_score,
						rs.high_purchase_activity as high_purchase_activity,
						rs.country_blacklisted as country_blacklisted,
						rs.free_provider_email as free_provider_email,
						rs.ip_blacklisted as ip_blacklisted,
						rs.ip_not_matches_country as ip_not_matches_country,
						rs.fwd_num_not_match_site as fwd_num_not_match_site,
						rs.multiple_fwd_numbers as multiple_fwd_numbers,
						rs.fwd_num_blacklisted as fwd_num_blacklisted,
						rs.new_member as new_member,
						rs.order_over_20_dollars as order_over_20_dollars,
						rs.over_2_nums_in_7_days as over_2_nums_in_7_days,
						rs.over_1_acct_credit_in_7_days as over_1_acct_credit_in_7_days, 
						ul.name as name, 
						ul.email as email, 
						ul.address1 as address1, 
						ul.address2 as address2, 
						ul.user_city as user_city, 
						ul.user_state as user_state, 
						ul.user_country as user_country, 
						ul.postal_code as postal_code, 
						concat('M',ul.id+10000) as member_id, 
						sc.pymt_gateway as pymt_gateway, 
						sc.pp_sender_mail as pp_sender_mail, 
						sc.forwarding_type as forwarding_type, 
						IF(sc.forwarding_type=1,sc.fwd_number,sc.sip_address) as fwd_number 
					from risk_score rs, 
						shopping_cart sc, 
						user_logins ul
					where rs.cart_id=sc.id and 
						sc.user_id=ul.id and
						cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting risk information for cart_id $cart_id. Error is: ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No rows found in risk_score for cart_id $cart_id";
		}
		$row=mysql_fetch_assoc($result);
		return $row;
		
	}
	
	function get_search_orders_count($input_type, $input_value){
		
		//Note: Any query must be joined by risk_score score table so that only orders that were placed after the fraud check is completed are used.
		//In other words no orders before the fraud check is implemented are part of this search.
		switch($input_type){
			case "cart_id":
				$query = "select count(*) as count from risk_score where cart_id=$input_value";
				break;
			case "did_number":
				$query = "select count(*) as count from shopping_cart sc, risk_score rs, assigned_dids ad where sc.id=rs.cart_id and sc.did_id=ad.id and ad.did_number='$input_value'";
				break;
			case "dest_number":
				$query = "select count(*) as count from shopping_cart sc, risk_score rs where sc.id=rs.cart_id and sc.fwd_number=$input_value";
				break;
			case "email":
				$query = "select count(*) as count from shopping_cart sc, risk_score rs, user_logins ul where sc.id=rs.cart_id and sc.user_id=ul.id and ul.email='$input_value'";
				break;
			case "member_id":
				//first convert input value to user id value
				$user_id = intval(substr($input_value,1)) - 10000;
				$query = "select count(*) as count from shopping_cart sc, risk_score rs where sc.id=rs.cart_id and sc.user_id=$user_id";
				break;
			case "ip_address":
				$query = "select count(*) as count from risk_score where ip_address='$input_value'";
				break;
		}
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of records on search field $input_type for value $input_value. Error is: ".mysql_error();
		}
		$row=mysql_fetch_assoc($result);
		return $row["count"];
		
	}
	
	function get_search_orders($input_type, $input_value, $limit, $offset){
		
		$resultset_fields = "sc.id as cart_id, 
						sc.user_id as user_id, 
						sc.amount as amount, 
						sc.description as description, 
						sc.did_number as did_number, 
						sc.transTime as transTime, 
						sc.pymt_type as pymt_type, 
						sc.pymt_gateway as pymt_gateway, 
						rs.risk_score as risk_score, 
						rs.order_status as order_status, 
						rs.ip_address as ip_address,
						rs.ip_location as ip_location, 
						rs.high_purchase_activity as high_purchase_activity, 
						rs.country_blacklisted as country_blacklisted,
						rs.free_provider_email as free_provider_email, 
						rs.ip_blacklisted as ip_blacklisted, 
						rs.is_mail_sent as is_mail_sent,
						rs.docs_required as docs_required,
						rs.ip_not_matches_country as ip_not_matches_country, 
						rs.fwd_num_not_match_site as fwd_num_not_match_site, 
						rs.multiple_fwd_numbers as multiple_fwd_numbers, 
						rs.fwd_num_blacklisted as fwd_num_blacklisted, 
						rs.new_member as new_member, 
						rs.order_over_20_dollars as order_over_20_dollars, 
						rs.over_2_nums_in_7_days as over_2_nums_in_7_days, 
						rs.over_1_acct_credit_in_7_days as over_1_acct_credit_in_7_days,
						ul.status as member_status, 
						ul.site_country as site_country, 
						ul.id as member_id";
		
		//Note: Any query must be joined by risk_score score table so that only orders that were placed after the fraud check is completed are used.
		//In other words no orders before the fraud check is implemented are part of this search.
		switch($input_type){
			case "cart_id":
				$query = "select $resultset_fields from shopping_cart sc, risk_score rs, user_logins ul where sc.id=rs.cart_id and sc.user_id=ul.id and rs.cart_id=$input_value";
				break;
			case "did_number":
				$query = "select $resultset_fields from shopping_cart sc, risk_score rs, user_logins ul, assigned_dids ad where sc.id=rs.cart_id and sc.user_id=ul.id and sc.did_id=ad.id and ad.did_number='$input_value'";
				break;
			case "dest_number":
				$query = "select $resultset_fields from shopping_cart sc, risk_score rs, user_logins ul where sc.id=rs.cart_id and sc.user_id=ul.id and sc.fwd_number=$input_value";
				break;
			case "email":
				$query = "select $resultset_fields from shopping_cart sc, risk_score rs, user_logins ul where sc.id=rs.cart_id and sc.user_id=ul.id and ul.email='$input_value'";
				break;
			case "member_id":
				//first convert input value to user id value
				$user_id = intval(substr($input_value,1)) - 10000;
				$query = "select $resultset_fields from shopping_cart sc, risk_score rs, user_logins ul where sc.id=rs.cart_id and sc.user_id=ul.id and sc.user_id=$user_id";
				break;
			case "ip_address":
				$query = "select $resultset_fields from shopping_cart sc, risk_score rs, user_logins ul where sc.id=rs.cart_id and sc.user_id=ul.id and rs.ip_address='$input_value'";
				break;
		}
		$query = $query." LIMIT $limit OFFSET $offset;";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting records of search field $input_type for value $input_value. Error is: ".mysql_error();
		}
		$ret_arr = array();
		while($row=mysql_fetch_assoc($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
	}
	
	function getIPAddress($cart_id){
		
		$query = "select ip_address from risk_score where cart_id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting IP Address from risk_score for cart_id $cart_id. Error is: ".mysql_error();
		}
		$row=mysql_fetch_assoc($result);
		return $row["ip_address"];
		
	}
	
	function updateRenewFailed($cartId, $fail_status = 'Y'){
		$sql = "UPDATE risk_score SET is_renew_failed = '" . $fail_status . "' WHERE cart_id = " . $cartId;
		$result = mysql_query($sql);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting IP Address from risk_score for cart_id $cartId. Error is: ".mysql_error();
		}else{
			return true;
		}
		
	}
	
	function updateMailStatus($cartId, $mail_sent='Y'){
		$sql = "UPDATE risk_score SET is_mail_sent = '" . $mail_sent . "' WHERE cart_id = " . $cartId;
		$result = mysql_query($sql);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting IP Address from risk_score for cart_id $cartId	. Error is: ".mysql_error();
		}else{
			return true;
		}
	}
}