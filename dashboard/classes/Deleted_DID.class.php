<?php
class Deleted_DID{
	
	function getDeletedDIDs($user_id){
		
		$query = "select * from deleted_dids where user_id='$user_id'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			echo "Error: An error occurred while getting deleted DIDs.<br><br>".mysql_error();
			die();
		}
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getDeletedDIDsCount($user_id){
		
		$query = "select count(id) as deleted_dids_count from deleted_dids where user_id='$user_id'";
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			echo "Error: An error occurred while getting deleted DIDs count.<br><br>".mysql_error();
			die();
		}
		$row = mysql_fetch_array($result);
		return $row['deleted_dids_count'];
		
	}
	
	function getDeletedDIDsForPage($user_id,$start_rec){
		
		$query = "select id, 
						did_number, 
						route_number,  
						date_format(did_deleted_on,'%d %b %Y %H:%i:%s') as did_deleted_on, 
						delete_reason 
					 from deleted_dids 
					 where user_id='$user_id'
					 	order by did_deleted_on desc
					 	limit $start_rec, ".RECORDS_PER_PAGE;
		$result = mysql_query($query);
		if(mysql_error()!=""){
			echo "Error: An error occurred while getting deleted DIDs for page.<br><br>".mysql_error();
			die();
		}
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getDeletedDIDData($user_id,$did_number){
		
		$query = "select * from deleted_dids where user_id='$user_id' and did_number='$did_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			echo "Error: An error occurred while getting deleted DID data.<br><br>".mysql_error();
			die();
		}
		if(mysql_num_rows($result)>0){
			$row = mysql_fetch_array($result);
			return $row;
		}else{
			return 0;
		}
		
	}
	
	function getDeletedDIDDataById($id){
		
		$query = "select * from deleted_dids where id=$id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			echo "Error: An error occurred while getting deleted DID data by id.<br><br>".mysql_error();
			die();
		}
		if(mysql_num_rows($result)>0){
			$row = mysql_fetch_array($result);
			return $row;
		}else{
			return 0;
		}
		
	}
	
	function getDeletedDIDDataByDID($did_number){
		
		if(substr($did_number,0,4)=="4470" || substr($did_number,0,4)=="4487"){
			$did_num = "0".substr($did_number,1);
		}else{
			$did_num = $did_number;
		}
		
		$query = "select * from deleted_dids where did_number=$did_num";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			echo "Error: An error occurred while getting deleted DID data by did number for $did_num.<br><br>".mysql_error();
			die();
		}
		
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function insertDeletedDID($user_id,$did_number,$route_number,$delete_reason){
		
		//First get the DID_ID of the DID being deleted
		$query = "select id from assigned_dids where did_number=$did_number and user_id=$user_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting Assigned DID record for user_id $user_id and did_number $did_number. Error is: ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: An error occurred in insertDeletedDID. No Assigned DID record found for user_id $user_id and did_number $did_number.");
		}
		$row = mysql_fetch_array($result);
		$did_id = $row["id"];
		
		$query = "insert into deleted_dids(
						user_id, 
						did_id, 
						did_number,  
						route_number, 
						did_deleted_on,
						delete_reason
					) values(
						'$user_id',
						$did_id, 
						'$did_number',
						'$route_number',
						NOW(),
						'$delete_reason'
					)";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return mysql_insert_id();
		}else{
			die("Error: An error occurred while inserting deleted DID Data.<br><br>".mysql_error());
			return false;
		}
	}
	
	function deleteDeletedDID($user_id,$did_number){
		
		$query = "delete from deleted_dids where user_id='$user_id' and did_number='$did_number'";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			die("Error: An error occurred while inserting deleted DID Data.<br><br>".mysql_error());
			return false;
		}
		
	}
	
	function getLastReactivateAttemptDays($user_id,$did_number){
		
		$query = "select IF(last_reactivate_attempt, DATEDIFF(now(),last_reactivate_attempt), 7) last_reactivate_attempt_days from deleted_dids where user_id='$user_id' and did_number='$did_number'";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)>0){
				$row = mysql_fetch_array($result);
				return $row["last_reactivate_attempt_days"];
			}else{
				die("Error: No rows found for the did_number: $did_number and user ID: $user_id combination.");
				return false;
			}
		}else{
			die("Error: An error occurred while inserting deleted DID Data.<br><br>".mysql_error());
			return false;
		}
		
	}
	
	function updateLastReactivateAttemptDate($user_id,$did_number){
		
		$query = "update deleted_dids set last_reactivate_attempt=now() where user_id='$user_id' and did_number='$did_number'";
		mysql_query($query);
		
	}
	
	function getDIDDeletionsCount($did_number){
		
		//Gets the number of time the SAME DID Number was ever deleted in our system - A SAME DID number could possibly with different members at different times
		
		//Ensure that the Free DID Numbers work both ways - with or without country code
		
		if(substr($did_number,0,4)=="4470" || substr($did_number,0,4)=="4487"){
			$did_num = "0".substr($did_number,1);
		}else{
			$did_num = $did_number;
		}
		
		$query = "select count(id) as did_deletions_count from deleted_dids where did_number='$did_num'";
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting deleted DIDs count.<br><br>".mysql_error();
		}
		$row = mysql_fetch_array($result);
		return $row['did_deletions_count'];
		
	}
	
	function getMonthlyDeletedDIDs($month_num, $year){
		
		$query = "select concat('M',dd.user_id+10000) as member_id, 
							dd.did_number as did_number,
							date_format(dd.did_deleted_on,'%d %b %Y %H:%i:%s') as did_deleted_on, 
							dd.delete_reason as delete_reason
						from deleted_dids dd
						where SUBSTRING(did_number,1,3)!='087' and 
							SUBSTRING(did_number,1,3)!='070' and 
							month(did_deleted_on)=".mysql_real_escape_string($month_num)." and  
							year(did_deleted_on)=".mysql_real_escape_string($year)." 
							order by dd.did_deleted_on desc";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting monthly deleted DIds of month $month_num/$year. Error is: ".mysql_error();
			}
			if(mysql_num_rows($result)==0){
				return 0;
			}
			$retArr =   array();
			while($row = mysql_fetch_assoc($result)){
                $retArr[]   =   $row;
            }
            return $retArr;
							
		
	}
	
}