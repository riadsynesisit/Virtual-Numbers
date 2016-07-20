<?php
class Unassigned_087DID{
	
	public $id;
	public $did_number;
	public $reserved_until;
	
	function retunassigned_dids(){
		
		$retArr = array();
		
		$query = "select id, did_number from unassigned_087dids where reserved_until is null order by did_number asc";
		
		$result = mysql_query($query);
		if(mysql_num_rows($result)!=0){
			while($row = mysql_fetch_array($result)){
				$retArr[] = array($row[0],$row[1]);
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
		
		$query = "select * from unassigned_087dids where id=$this->id";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		return $row;
		
	}
	
	function setReserved(){
		
		//Check if this DID is still unreserved and then reserve it
		$query = "update unassigned_087dids 
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
		$query = "update unassigned_087dids 
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

		$query = "update unassigned_087dids set reserved_until=null where id=".$id;
		mysql_query($query);
		
	}
	
	function destroyByDID_Number(){
		
		$query = "delete from unassigned_087dids where did_number='$this->did_number'";
		mysql_query($query);
		
	}
	
	function getUnassignedDIDsCount(){
		
		$query = "select count(*) as unassigned_count from unassigned_087dids where reserved_until is null";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['unassigned_count'];
		
	}
	
	function checkDIDUnassigned($did_number){
		
		$query = "select count(*) as unassigned_count from unassigned_087dids where did_number=$did_number and reserved_until is null";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		if(intval($row['unassigned_count'])>0){
			return true;
		}else{
			return false;
		}
		
	}
	
	function getDateAddedByDID_Number(){
		
		$query = "select date_added from unassigned_087dids where did_number='$this->did_number'";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['date_added'];
		
	}
	
	function janitor(){
		
		$query = "SELECT * FROM `unassigned_087dids` WHERE reserved_until is not null and reserved_until < now()";
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
	
	//Returns call cost based on the forwarding number
	function callCost($fwd_number,$which="peak",$markup=false){
		
		$sco = new System_Config_Options();
		if(is_object($sco)==false){
			die("Failed: Cannot create System_Config_Options object in Unassigned_087DID.callCost.");
		}
		$markup_pct = $sco->getSIPMarkUpCost();
		
		if(substr($fwd_number,0,1)=="+"){//strip the leading + sign if any
			$fwd_number = substr($fwd_number,1);
		}
		if(substr($fwd_number,0,2)=="00"){//strip leading 00 - international access code if any
			$fwd_number = substr($fwd_number,2);	
		}
		$num_len = strlen($fwd_number);
		//Run the MySQL queries - Most Specific number to the least specific - until the cost is found
		for($i=0;$i<$num_len;$i++){
			$fwd_number = substr($fwd_number,0,$num_len - $i);//chop one digit from the right side for each iteration of the loop
			$query = "select round(peak_per_min_gbp,4) as peak_per_min_gbp,
								round(offpeak_per_min_gbp,4) as offpeak_per_min_gbp 
								from dcm_destination_costs where prefix='$fwd_number'";
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0){//prefix not found
				//continue with a less specific prefix
				continue;
			}else{//Found the prefix in the costs table
				$row = mysql_fetch_array( $result );//Fetch the row
				//Check the costs and return the max one
				if($which=="peak"){
					if($markup){
						return $row['peak_per_min_gbp']*(1+($markup_pct/100));
					}else{
						return $row['peak_per_min_gbp'];
					}
				}else{
					if($markup){
						return $row['offpeak_per_min_gbp']*(1+($markup_pct/100));
					}else{
						return $row['offpeak_per_min_gbp'];
					}
				}
				break;//No need to continue any further - break out of the for loop
			}//end of if(mysql_num_rows($result) == 0){//prefix not found
		}//End of for loop
		//If you get this far it means we do not have this prefix in our dcm_destination_costs table
		return false;//Means the call cost was not found for this prefix
	}
	
	function callCostTollFree($did_city_id,$markup=false){
		
		$sco = new System_Config_Options();
		if(is_object($sco)==false){
			return "Error: Cannot create System_Config_Options object in Unassigned_087DID.callCost.";
		}
		$markup_pct = $sco->getTollFreeMarkUpCost();
		
		$query = "select pt.rate as rate from pricing_tollfree pt, did_cities dc, did_countries dco where dc.did_country_id=dco.id and dc.city_id=$did_city_id and pt.destination=CAST(CONCAT(CAST(dco.country_prefix as CHAR),CAST(dc.city_prefix as CHAR)) as UNSIGNED)";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: ".mysql_error()." Query is :".$query;
		}
		if(mysql_num_rows($result)==0){
			return 0;//No rows found implies not a Toll Free Number and therefore toll free costs are zero
		}
		if(mysql_num_rows($result)>0){
			$row = mysql_fetch_array( $result );
			if($markup){
				return $row["rate"]*(1+($markup_pct/100));
			}else{
				return $row["rate"];
			}
		}
		
	}
	
	function isCallCostOK($fwd_number){
		
		$query = "select round(min(peak_limit),4) as min_peak_limit, 
							round(max(peak_limit),4) as max_peak_limit,
							round(min(evening_limit),4) as min_eve_lmt, 
							round(min(weekend_limit),4) as min_we_lmt from dcm_origination_config;";
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0){
			$row = mysql_fetch_array( $result );
			$peakcallcost = $this->callCost($fwd_number,"peak",true);//With markup
			if((double)$peakcallcost <= (double)$row["max_peak_limit"]){
				//$offpeakcallcost = $this->callCost($fwd_number,"offpeak");
				//if((double)$offpeakcallcost <= (double)$row["min_eve_limit"] && (double)$offpeakcallcost <= (double)$row["min_we_lmt"]){
					return true;
				//}
			}
			return false;
		}else{
			die("No rows found in dcm_origination_config in isCallCostOK function.");
		}
		
	}
	
	function peakLimit087(){
		
		$query = "select max(peak_limit) as peak_limit from dcm_origination_config where did_range=87;";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting max peak limit from dcm_origination_config. Error is: ".mysql_error());
		}
		if(mysql_num_rows($result) > 0){
			$row = mysql_fetch_array( $result );
			return $row["peak_limit"];
		}else{
			die("Error: No rows found in dcm_origination_config in peakLimit087 function.");
		}
		
	}
	
}
?>