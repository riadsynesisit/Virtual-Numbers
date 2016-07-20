<?php
class Unassigned_DID{
	
	public $id;
	public $did_number;
	public $reserved_until;
	
	function retUnassigned_DID(){//Retunrs only one DID Number for use in .au website
		
		$retArr = array();
		
		$query = "select id, did_number from unassigned_dids where reserved_until is null limit 1";
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error ocurred while retrieving data from unassinged_did - ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No unassinged 070 DID found.";
		}
		$row = mysql_fetch_array($result);
		return $row;
		
	}
	
	function retUnassigned_DIDs(){
		
		$retArr = array();
		
		$query = "select id, did_number from unassigned_dids where reserved_until is null order by did_number asc";
		
		$result = mysql_query($query);
		if(mysql_num_rows($result)!=0){
			while($row = mysql_fetch_array($result)){
				$retArr[] = $row;//array($row[0],$row[1]);
			}
			return $retArr;
		}else{
			return 0;
		}
		
	}
	
	function setRequested_DID($rec_id){
		
		 $this->id = $rec_id;
		
	}
	
	function getUnassigned_DID(){
		
		$query = "select * from unassigned_dids where id=$this->id";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		return $row;
		
	}
	
	function getUnassigned_DIDbyID($did_id){
		
		$query = "select did_number from unassigned_dids where id=$did_id";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		return $row["did_number"];
		
	}
	
	function setReserved(){
		
		//Check if this DID is still unreserved and then reserve it
		$query = "update unassigned_dids 
					set reserved_until = ADDDATE(NOW(), INTERVAL ".DID_RESERVATION_EXPIRES_MINUTES." MINUTE)
					where id=$this->id and reserved_until is null";
		$result = mysql_query($query);
		if(mysql_error()=="" && mysql_affected_rows()>0){
			return true;
		}else{
			return false;
		}
	}
	
	function setReservedByDID($did){
		
		//Check if this DID is still unreserved and then reserve it
		$query = "update unassigned_dids 
					set reserved_until = ADDDATE(NOW(), INTERVAL ".DID_RESERVATION_EXPIRES_MINUTES." MINUTE)
					where did_number='$did' and reserved_until is null";
		$result = mysql_query($query);
		if(mysql_error()=="" && mysql_affected_rows()>0){
			return true;
		}else{
			return false;
		}
	}
	
	function clear_reservation($id){

		$query = "update unassigned_dids set reserved_until=null where id=".$id;
		mysql_query($query);
		
	}
	
	function destroyByDID_Number(){
		
		$query = "delete from unassigned_dids where did_number='$this->did_number'";
		mysql_query($query);
		
	}
	
	function getUnassignedDIDsCount(){
		
		$query = "select count(*) as unassigned_count from unassigned_dids where reserved_until is null";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['unassigned_count'];
		
	}
	
	function checkDIDUnassigned($did_number){
		
		$query = "select count(*) as unassigned_count from unassigned_dids where did_number=$did_number and reserved_until is null";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		if(intval($row['unassigned_count'])>0){
			return true;
		}else{
			return false;
		}
		
	}
	
	function getDateAddedByDID_Number(){
		
		$query = "select date_added from unassigned_dids where did_number='$this->did_number'";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['date_added'];
		
	}
	
	function janitor(){
		
		$query = "SELECT * FROM `unassigned_dids` WHERE reserved_until is not null and reserved_until < now()";
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0){
			while($row = mysql_fetch_array($result)){
				$this->clear_reservation($row['id']);
				//Also remove these records from the assigned_dids table because as soon as a DID is reserved it is all inserted into assigned_dids table
				$query = "delete from assigned_dids where did_number='".$row['did_number']."'";
				$result = mysql_query($query);
				$query = "delete from ast_rt_extensions where exten='".$row['did_number']."'";
				$result = mysql_query($query);
			}
		}
		
	}
	
}
?>