<?php

class UserMessages{
	
	public $id;
	public $message_title;
	public $message_body;
	public $message_creation;
	public $valid_until;
	public $catagory;
	public $user_id;
	public $viewed;
	public $user_can_delete;
	
	const DID_USAGE_WARN=1;
	
	function getUserMessagesCount($user_id){
		
		$query = "select count(id) as user_messages_count 
					from user_messages 
					where user_id=$user_id and
							valid_until>=now()
					order by viewed, catagory desc, message_creation";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			return $row['user_messages_count'];
		}else{
			echo "An error occurred while getting count of user messages.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
			return false;
		}
		
	}
	
	function getUserMessages($user_id){
		
		$ret_arr = array();
		
		$query = "select *
					from user_messages 
					where user_id=$user_id and
							valid_until>=now()
					order by viewed, catagory desc, message_creation";
		$result = mysql_query($query);
		if(mysql_error()==""){
			while($row = mysql_fetch_array($result)){
				$ret_arr[]=$row;
			}
			return $ret_arr;
		}else{
			echo "An error occurred while getting user messages.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
			//return false;
			exit;
		}
		
	}
	
	function saveUserMessage(){
		
		$query = "insert into user_messages (
								message_title, 
								message_body, 
								message_creation,
								user_id, 
								viewed, 
								user_can_delete, 
								valid_until,
								catagory
							) values (
								'".mysql_real_escape_string($this->message_title)."', 
								'".mysql_real_escape_string($this->message_body)."', 
								now(),
								$this->user_id, 
								$this->viewed,
								$this->user_can_delete, 
								'".$this->valid_until."',
								".self::DID_USAGE_WARN."
							)";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			echo "An error occurred while inserting a user message.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
			return false;
		}
		
	}
	
	function deleteSelectedUserMessage(){
		
		$query = "delete from user_messages where id=$this->id";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			echo "An error occurred while inserting a user message.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
			return false;
		}
		
	}
	
	function getDIDUsageWarnMessages($user_id){
		
		$ret_arr = array();
		
		$query = "select * from user_messages 
					where user_id=$user_id and 
					catagory=".self::DID_USAGE_WARN." order by message_creation;";
		$result = mysql_query($query);
		if(mysql_error()==""){
			while($row = mysql_fetch_array($result)){
				$ret_arr[]=$row;
			}
			return $ret_arr;
		}else{
			echo "An error occurred while inserting a user message.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
			return false;
		}
		
	}
	
	function updateUserMessagesAsViewed($user_id){
		
		$query = "update user_messages set viewed=1 
					where user_id=$user_id and
							valid_until>=now()";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			echo "An error occurred while updating user messages as viewed.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
			return false;
		}
		
	}
	
}
