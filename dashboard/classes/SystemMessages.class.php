<?php
class SystemMessages{
	
	public $id;
	public $message_title;
	public $message_body;
	public $message_creation;
	public $valid_until;
	public $msg_severity;
	public $msg_type;
	public $user_id;
	
	public $severity = array(
						array('Blue','Info'),
						array('Green','Non-Service Impacting'),
						array('Yellow','Service Impacting'),
						array('Red','Severely Service Impacting')
						);
							
	public $message_type = array('Planned','Current','Recent');
	
	function get_old_issues(){
		
		$query = "select * from system_messages where valid_until < NOW() AND message_creation < NOW() order by valid_until, message_creation, severity limit 5";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function get_current_issues(){
		
		$query = "select * from system_messages where valid_until >= NOW() AND message_creation < NOW() order by valid_until, message_creation, severity";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function get_future_issues(){
		
		$query = "select * from system_messages where valid_until > NOW() AND message_creation >= NOW() order by valid_until, message_creation, severity limit 5";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function saveMessage(){
		
		$query = "insert into system_messages (
							message_title, 
							message_body, 
							message_creation, 
							valid_until, 
							severity, 
							message_type, 
							user_id
							) values (
							'".mysql_real_escape_string($this->message_title)."', 
							'".mysql_real_escape_string($this->message_body)."', 
							'".$this->message_creation."', 
							'".$this->valid_until."', 
							$this->msg_severity, 
							$this->msg_type, 
							$this->user_id
							)";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return false;
		}
		
	}
	
	function deleteMessage(){
		
		$query = "delete from system_messages where id=".$this->id;
		
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return false;
		}
		
	}
	
}
?>