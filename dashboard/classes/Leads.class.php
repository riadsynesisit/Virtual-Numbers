<?php
class Leads{
	
	function insert_lead($lead_did_id, $name, $email, $phone1, $phone2, $ipaddress){
		
		$query = "insert into leads (
							lead_did_id, 
							name, 
							email, 
							phone1, 
							phone2, 
							received_on, 
							ipaddress
							) values (
							".mysql_real_escape_string($lead_did_id).",
							'".mysql_real_escape_string($name)."', 
							'".mysql_real_escape_string($email)."', 
							".mysql_real_escape_string($phone1).", 
							".mysql_real_escape_string($phone2).", 
							now(), 
							'".mysql_real_escape_string($ipaddress)."'
							);";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while inserting lead. ".mysql_error().", Query is: ".$query;
		}else{
			return mysql_insert_id();
		}
		
	}
	
	function get_leads_count($lead_did_id){
		
		$query = "select count(*) as count from leads where completed='N' and lead_did_id=$lead_did_id;";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of leads for did id $lead_did_id. ".mysql_error().", Query is: ".$query;
		}
		$row=mysql_fetch_array($result);
		return $row["count"];
		
	}
	
	function get_completed_leads_count($user_id){
		
		$query = "select count(*) as count 
						from leads l, 
						assigned_dids ad 
					where l.lead_did_id=ad.id and 
						l.completed='Y' and 
						ad.user_id=".mysql_real_escape_string($user_id);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of completed leads for user id $user_id. ".mysql_error().", Query is: ".$query;
		}
		$row=mysql_fetch_array($result);
		return $row["count"];
		
	}
	
	function getCompletedLeadsForPage($user_id,$start_rec,$per_page=RECORDS_PER_PAGE){
		
		$query = "select l.name as name, 
							l.email as email, 
							l.phone1 as phone1, 
							l.phone2 as phone2, 
							date_format(l.received_on,'%d %b %Y %H:%i:%s') as received_on, 
							date_format(l.completed_on,'%d %b %Y %H:%i:%s') as completed_on, 
							ad.did_number as did_number, 
							l.ipaddress as ipaddress 
						from leads l, 
						assigned_dids ad 
					where l.lead_did_id=ad.id and 
						l.completed='Y' and 
						ad.user_id=".mysql_real_escape_string($user_id)."
					order by l.received_on desc  
					limit ".mysql_real_escape_string($start_rec).", ".mysql_real_escape_string($per_page);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting completed leads for page. ".mysql_error();
		}
		$ret_arr = array();
		while($row=mysql_fetch_assoc($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
	}
	
	function getLeadsForPage($lead_did_id,$start_rec,$per_page=RECORDS_PER_PAGE){
		
		$query = "select l.name as name, 
							l.email as email, 
							l.phone1 as phone1, 
							l.phone2 as phone2, 
							date_format(l.received_on,'%d %b %Y %H:%i:%s') as received_on, 
							l.ipaddress as ipaddress 
						from leads l 
						where l.completed='N' and 
								l.lead_did_id=$lead_did_id 
							order by l.received_on desc  
							limit $start_rec, $per_page";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting leads for page. ".mysql_error();
		}
		$ret_arr = array();
		while($row=mysql_fetch_assoc($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
	}
	
	function get_lead_details($lead_did_id){
		
		$query = "select l.name as name, 
							l.email as email, 
							l.phone1 as phone1, 
							l.phone2 as phone2, 
							l.received_on as received_on, 
							l.ipaddress as ipaddress 
						from leads l 
						where l.lead_did_id=$lead_did_id ";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting lead details for lead did id: $lead_did_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return 0;
		}
		$row=mysql_fetch_assoc($result);
		return $row;
		
	}
	
	function delete_lead($lead_id){//Actually mark it as completed - Do NOT deleted leads received
		
		$query = "update leads set completed='Y', completed_on=now() where completed='N' and id=".mysql_real_escape_string($lead_id);
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while setting lead id $lead_id as completed. ".mysql_error()." Query is: ".$query;
		}else{
			return true;
		}
		
	}
	
	function delete_lead_by_lead_did_id($lead_did_id){//Actually mark it as completed - Do NOT deleted leads received
		
		$query = "update leads set completed='Y', completed_on=now() where completed='N' and lead_did_id=".mysql_real_escape_string($lead_did_id);
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while setting lead by lead_did_id $lead_did_id as completed. ".mysql_error()." Query is: ".$query;
		}else{
			return true;
		}
		
	}
}