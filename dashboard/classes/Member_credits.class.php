<?php
	class Member_credits{
		
		function getTotalCredits($user_id, $is_array=false){
			
			$query = "select round(total_credits,2) as total_credits, allow_over_4_credits from user_logins where id=$user_id";
			$result =   mysql_query($query);
			if(mysql_error()==""){
				$row = mysql_fetch_array($result);
				if($is_array){
					return array($row["total_credits"], $row["allow_over_4_credits"]);
				}else{
					return $row["total_credits"];
				}
			}else{
				die("An error occurred while getting total_credits from user_logins. Error is: ".mysql_error());
			}
		}
		
		function addMemberCredits($user_id,$num_credits,$added_by, $allow_over_4_credits = 'N'){
			
			if($allow_over_4_credits == "N"){
				$status = $this->is_under_4_lots_in_30days($user_id);
				if($status!==true){
					return $status;
				}
			}
			$status = $this->is_under_200_in_30days($user_id,$num_credits);
			if($status!==true){
				return $status;
			}
			
			//Get existing credits
			$old_balance = $this->getTotalCredits($user_id);
			
			$query = "insert into member_credits (user_id, credits, added_by, old_balance) values ($user_id, $num_credits, $added_by, $old_balance)";
			$ins_id = mysql_insert_id();
			mysql_query($query);
			if(mysql_error() == ""){
				//Update the total_credits in the user_logins table
				//Scope for implementing MySQL transaction here in this place
				$query = "update user_logins set total_credits=total_credits+$num_credits where id=$user_id";
				mysql_query($query);
				if(mysql_error() == ""){
					$new_balance = $old_balance+$num_credits;
					//Now update the member_credits record with new balance
					$query = "update member_credits set new_balance=$new_balance where id=$ins_id";
					mysql_query($query);
					return true;
				}else{
					return "Error: An error occurred while adding total_credits in user_logins. Error is: ".mysql_error();
				}
			}else{
				return "Error: An error occurred while adding member credits. Error is: ".mysql_error();
			}
			
		}
		
		function is_under_4_lots_in_30days($user_id){
			
			//Check Number of credit lots added in the last 30 days
			$query = "select count(*) as lots from member_credits where user_id=$user_id and datediff(now(),DATE(date_added)) <= 30";
			$result =   mysql_query($query);
			if(mysql_error()==""){
				$row = mysql_fetch_array($result);
				if(intval($row["lots"])>=4){
					return "Error: Maximum allowed lots of account credit were already added in the last 30 days.";
				}else{
					return true;
				}
			}else{
				return "Error: An error occurred while getting lots of account credits added for user_id $user_id. Error is: ".mysql_error();
			}
			
		}
		
		function is_under_200_in_30days($user_id,$num_credits){
			
			//Get the max allowed credit in 30 days
			$query = "select max_30days_acct_credit from user_logins where id=$user_id";
			$result =   mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting Max 30 days credit of user id $user_id. Error is: ".mysql_error();
			}
			$row = mysql_fetch_array($result);
			$max_credits = $row["max_30days_acct_credit"];
			
			//Now check the amount of credit added in the last 30 days
			$query = "select IFNULL(sum(credits),0) as credits from member_credits where user_id=$user_id and datediff(now(),DATE(date_added)) <= 30";
			$result =   mysql_query($query);
			if(mysql_error()==""){
				$row = mysql_fetch_array($result);
				if(($row["credits"] + $num_credits)>$max_credits){
					$more_can_be_added = number_format($max_credits - $row["credits"],2);
					return "Error: Maximum allowed account credit in 30 days is exceeded. Allowed $max_credits, Already Added ".$row["credits"].", More that can be added is ".$more_can_be_added;
				}else{
					return true;
				}
			}else{
				return "Error: An error occurred while getting sum of account credits added for user_id $user_id. Error is: ".mysql_error();
			}
			
		}
		
		function get_lots_added_in_30days($user_id){
			
			$query = "select count(*) as lots from member_credits where user_id=$user_id and datediff(now(),DATE(date_added)) <= 30";
			$result =   mysql_query($query);
			if(mysql_error()==""){
				$row = mysql_fetch_array($result);
				return intval($row["lots"]);
			}else{
				return "Error: An error occurred while getting lots of account credits added in last 30 days for user_id $user_id. Error is: ".mysql_error();
			}
			
		}
		
		function get_credit_added_in_30days($user_id){
			
			//Get credit amount added in the last 30 days
			$query = "select IFNULL(sum(credits),0) as credits from member_credits where user_id=$user_id and datediff(now(),DATE(date_added)) <= 30";
			$result =   mysql_query($query);
			if(mysql_error()==""){
				$row = mysql_fetch_array($result);
				return $row["credits"];
			}else{
				return "Error: An error occurred while getting credits added in 30 days for user_id $user_id. Error is: ".mysql_error();
			}
			
		}
		
		function deductMemberCredits($user_id,$num_credits){
			
			$query = "update user_logins set total_credits=total_credits-$num_credits where id=$user_id";
			mysql_query($query);
			if(mysql_error() == ""){
				return true;
			}else{
				die("An error occurred while deducting total_credits in user_logins. Error is: ".mysql_error());
			}
			
		}
		
		function refundMemberCredits($user_id,$num_credits){
			
			$query = "update user_logins set total_credits=total_credits-$num_credits where id=$user_id";
			mysql_query($query);
			if(mysql_error() == ""){
				return true;
			}else{
				die("An error occurred while refunding total_credits in user_logins. Error is: ".mysql_error());
			}
			
		}
		
	}