<?php
class Blacklist_ip{
	
	function is_ip_black_listed($ip_address){
		
		$query = "select count(*) as rec_count from blacklist_ip where ip_address='$ip_address'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count from blacklist_ip : ".mysql_error();
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row["rec_count"];
		
	}
	
	function black_list_ip($ip_address,$user_id){
		
		//First check if the IP address is already black listed - Avoid duplicate key error
		$query = "select id from blacklist_ip where ip_address='$ip_address'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking if IP Address $ip_address is already in blacklist_ip : ".mysql_error();
		}
		if(mysql_num_rows($result)>0){//IP Address already in black list
			return true;
		}
		
		$query = "insert into  blacklist_ip (ip_address, update_user, update_date) values ('$ip_address',$user_id,now());";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred blacklisting ip $ip_address of user_id $user_id. ".mysql_error();
		}
		return true;
		
	}
	
	function white_list_ip($ip_address){
		
		$query = "delete from  blacklist_ip where ip_address='$ip_address';";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred white listing ip $ip_address. ".mysql_error();
		}
		return true;
		
	}
	
	function get_black_listed_ips_count(){
		
		$query = "select count(*) as count from blacklist_ip";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of black listed IP addresses from blacklist_ip : ".mysql_error();
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row["count"];
		
	}
	
	function get_black_listed_ips($limit,$offset){
		
		$query = "select * from blacklist_ip order by update_date desc LIMIT $limit OFFSET $offset;";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of black listed IP addresses from blacklist_ip : ".mysql_error();
		}
		$ret_arr = array();
		while($row=mysql_fetch_assoc($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
}