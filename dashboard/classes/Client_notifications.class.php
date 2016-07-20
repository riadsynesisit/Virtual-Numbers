<?php
class Client_notifications{
	
	function get_client_notifications_count($user_id){
		
		$query = "select count(*) as count from client_notifications where user_id=".mysql_real_escape_string($user_id);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurrred while inserting a row into client notifications. Error is: ".mysql_error();
		}
		$row = mysql_fetch_array($result);
		return $row['count'];
	}
	
	function get_client_notifications_for_page($user_id, $start_rec, $per_page=RECORDS_PER_PAGE){
		
		$query = "select cn.id as id, 
							date_format(cn.date_sent,'%d %b %Y %H:%i:%s') as date_sent,
							cn.sender as sender,
							cn.recipient as recipient, 
							cn.subject as subject, 
							cn.contents as contents 
						from client_notifications cn 
						where user_id=".mysql_real_escape_string($user_id)." 
						order by cn.date_sent desc
						limit ".mysql_real_escape_string($start_rec).", ".mysql_real_escape_string($per_page);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurrred while getting client notifications of user id: $user_id. Error is: ".mysql_error();
		}
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function save_client_notification($user_id, $sender, $recipient, $subject, $contents){
		
		$query = "insert into client_notifications (
										user_id, 
										date_sent, 
										sender, 
										recipient,
										subject,
										contents
										)values(
										".mysql_real_escape_string($user_id).", 
										now(), 
										'".mysql_real_escape_string($sender)."',
										'".mysql_real_escape_string($recipient)."', 
										'".mysql_real_escape_string($subject)."',
										'".mysql_real_escape_string($contents)."'
										);";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurrred while inserting a row into client notifications.query: $query .. Error is: ".mysql_errno();
		}
		return mysql_insert_id();
		
	}
	
	function get_client_notification($id, $user_id){
		
		//Ensure that the notification really belongs to the login user
		$query = "select date_format(cn.date_sent,'%d %b %Y %H:%i:%s') as date_sent,
							cn.sender as sender,
							cn.recipient as recipient, 
							cn.subject as subject, 
							cn.contents as contents 
						from client_notifications cn 
						where id=".mysql_real_escape_string($id)." and 
								user_id=".mysql_real_escape_string($user_id);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurrred while getting client notification id $id. Error is: ".mysql_errno();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No client notification with id $id found.";
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row;
		
	}
	
}