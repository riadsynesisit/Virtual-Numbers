<?php
class Unassigned_AUDID{
	
	public $id;
	public $did_number;
	public $reserved_until;
	
	function retUnassigned_DIDs(){
		
		$retArr = array();
		
		$query = "select id, did_number, prefix, city_id from unassigned_audids where reserved_until is null order by did_number asc";
		
		$result = mysql_query($query);
		if(mysql_num_rows($result)!=0){
			while($row = mysql_fetch_array($result)){
				$retArr[] = array($row[0],$row[1],$row[2],$row[3]);
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
		
		$query = "select * from unassigned_audids where id=$this->id";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		return $row;
		
	}
	
	function setReserved(){
		
		//Check if this DID is still unreserved and then reserve it
		$query = "update unassigned_audids 
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
		$query = "update unassigned_audids 
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

		$query = "update unassigned_audids set reserved_until=null where id=".$id;
		mysql_query($query);
		
	}
	
	function destroyByDID_Number(){
		
		$query = "delete from unassigned_audids where did_number='$this->did_number'";
		mysql_query($query);
		
	}
	
	function getUnassignedDIDsCount($city_id){
		
		$query = "select count(*) as unassigned_count from unassigned_audids where city_id=$city_id and reserved_until is null";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['unassigned_count'];
		
	}
	
	function getDateAddedByDID_Number(){
		
		$query = "select date_added from unassigned_audids where did_number='$this->did_number'";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['date_added'];
		
	}
	
	function janitor(){
		
		$query = "SELECT * FROM `unassigned_audids` WHERE reserved_until is not null and reserved_until < now()";
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result)){
			$this->clear_reservation($row['id']);
			//Also remove these records from the assigned_dids table because as soon as a DID is reserved it is all inserted into assigned_dids table
			$query = "delete from assigned_dids where did_number='".$row['did_number']."'";
			$result = mysql_query($query);
			$query = "delete from ast_rt_extensions where exten='".$row['did_number']."'";
			$result = mysql_query($query);
		}
		
	}
    
    public function getDIDByCityID($city_id) {
        $query = "SELECT unassigned_audids.id, unassigned_audids.did_number FROM unassigned_audids,  country_city_prefixes
                    WHERE unassigned_audids.city_id = country_city_prefixes.city_id AND country_city_prefixes.id = '" . $city_id . "' 
                    ORDER BY unassigned_audids.did_number ASC";
        $result = mysql_query($query);
        
        $data = array();
        if(mysql_num_rows($result) != 0 ) {
            
            while($row = mysql_fetch_assoc($result)) {
                $data[] = $row;
            }
            
        } else {
            return false;
        }
        return $data;                         
    }
	
	function insertDID($did_number, $date_expires, $prefix, $city_id, $order_id, $uniq_id){
		
		$query = "insert into unassigned_audids (
								did_number, 
								date_expires, 
								prefix, 
								city_id, 
								order_id, 
								uniq_id
								) values (
								'$did_number',
								'$date_expires',
								$prefix,
								$city_id,
								'$order_id',
								'$uniq_id'
								);";
		mysql_query($query);
		if(mysql_error()=="" && mysql_affected_rows()>0){
			return true;
		}else{
			return false;
		}
		
	}
	
}
?>