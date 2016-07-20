<?php
class Free_email_providers{
	
	function is_free_email_provider($cart_id){
		
		//First find the email domain of the user of this shopping cart id
		$query = "select u.email as email 
					from user_logins u, 
						shopping_cart s 
					where u.id=s.user_id and 
						s.id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting email of shopping_cart id $cart_id user. : ".mysql_error();
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$email = $row["email"];
		$email_arr = split("@",$email);
		$email_domain = $email_arr[1];
		
		$query = "select count(*) as rec_count
					from free_email_providers 
					where domain='$email_domain'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting email domain of shopping_cart id $cart_id user. : ".mysql_error();
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if(intval($row["rec_count"])>0){
			return true;
		}else{
			return false;
		}
		
	}
	
}