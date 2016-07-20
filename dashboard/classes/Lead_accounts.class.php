<?php

class Lead_accounts{
	
	function get_lead_accounts($user_id){
		
		$query = "select la.id as id, 
							la.lead_reporting_email as lead_reporting_email, 
							date_format(la.date_created,'%d %b %Y %H:%i:%s') as date_created, 
							date_format(la.next_renewal_on,'%d %b %Y %H:%i:%s') as next_renewal_on, 
							(unix_timestamp(convert_tz(la.next_renewal_on,'GMT','UTC')) - unix_timestamp()) as secs_to_expire 
						from lead_accounts la 
						where la.user_id=$user_id and 
							la.deleted='N'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting lead accounts. Error is: ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return 0;
		}else{
			$row=mysql_fetch_array($result);
			return $row;
		}
		
	}
	
	function insert_or_renew_lead_account($cart_id){
		
		//Get the required details from the Shopping Cart
		$query = "select sc.user_id as user_id, 
							sc.last_pay_id as last_pay_id 
						from shopping_cart sc 
						where id=$cart_id;";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting shopping cart details. Error is: ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No rows returned from shopping_cart for cart id: $cart_id";
		}
		$row=mysql_fetch_array($result);
		$user_id = $row["user_id"];
		$last_pay_id = $row["last_pay_id"];
		
		//If no lead account exists for this user - insert a new record
		$blnLeadAccountExists = $this->get_lead_accounts($user_id);
		if(is_array($blnLeadAccountExists)==false && $blnLeadAccountExists==0){
			//Insert a lead account
			$query = "insert into lead_accounts (
									user_id, 
									date_created, 
									next_renewal_on, 
									last_pay_id
								) values (
									$user_id, 
									now(), 
									now() + interval 1 month, 
									$last_pay_id
								);";
		}else{
			//Get the lead acount record id and then update it
			$rec_id = $blnLeadAccountExists["id"];
			$existing_renewal_date = $blnLeadAccountExists["next_renewal_on"];
			//If the renewal is done late they still get charged from the back date
			$query = "update lead_accounts set 
							next_renewal_on=$existing_renewal_date + interval 1 month, 
							last_pay_id=$last_pay_id 
						where id=$rec_id;";
			
		}
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while updating lead_accounts. Error is: ".mysql_error();
		}
		if(mysql_affected_rows()==0){
			return "Error: No rows inserted or updated in lead_accounts for user_id $user_id";
		}elseif(mysql_affected_rows()==1){
			return true;
		}else{
			return "Error: Unexpected number of rows : ".mysql_affected_rows()." were either inserted or updated in lead_accounts for user_id $user_id.";
		}
		
	}
	
	function update_lead_reporting_email($user_id, $email_id){
		
		$query = "update lead_accounts set lead_reporting_email='".mysql_real_escape_string($email_id)."' where deleted='N' and user_id=$user_id;";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while updating lead reporting email for: $user_id. Error is: ".mysql_error();
		}
		if(mysql_affected_rows()>1){
			return "Error: Unexpexted number of rows (".mysql_affected_rows().") were updated while updating lead reporting email for user_id $user_id.";
		}
		return true;
		
	}
	
	function update_lead_form_code($user_id,$form_code){
		
		$query = "update lead_accounts set lead_form_code='".mysql_real_escape_string($form_code)."' where deleted='N' and user_id=$user_id;";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while updating lead form code for: $user_id. Error is: ".mysql_error();
		}
		if(mysql_affected_rows()>1){
			return "Error: Unexpexted number of rows (".mysql_affected_rows().") were updated while updating lead reporting email for user_id $user_id.";
		}
		return true;
		
	}
	
	function get_lead_form_code($user_id){
		
		$query = "select lead_form_code from lead_accounts where user_id=".mysql_real_escape_string($user_id);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting lead form code. Error is: ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return 0;
		}else{
			$row=mysql_fetch_array($result);
			return $row["lead_form_code"];
		}
	}
	
}