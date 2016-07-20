<?php
class DCMOriginationConfig{
	
	public $id;
	public $within_uk;
	public $did_range;
	public $peak_limit;
	public $evening_limit;
	public $weekend_limit;
	public $flag_call_limit;
    public $flag_call_cost;
	public $last_changed;
	
	function from_within_uk($phone_number){
		
		//This was not coded in the ruby site
		return false;
		
	}
	
	function getUKOriginationLimits($did_range){
		
		$query = "select peak_limit, 
							evening_limit, 
							weekend_limit, 
							flag_call_limit,
                                                        flag_call_cost
						from dcm_origination_config 
						where within_uk=1 and did_range=".$did_range;
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row;
		
	}
	
	function getIntlOriginationLimits($did_range){
		
		$query = "select peak_limit, 
							evening_limit, 
							weekend_limit, 
							flag_call_limit,
                                                        flag_call_cost
						from dcm_origination_config 
						where within_uk=0 and did_range=".$did_range;
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row;
		
	}
	
	function saveOriginationLimits(){
		
		$query = "update dcm_origination_config set
							peak_limit=$this->peak_limit, 
							evening_limit=$this->evening_limit,
							weekend_limit=$this->weekend_limit,
							flag_call_limit=$this->flag_call_limit,
                            flag_call_cost=$this->flag_call_cost,
							last_changed=now()
						where within_uk=$this->within_uk and
								did_range=$this->did_range";
		
		mysql_query($query);
		
		if(mysql_error()==""){
			return true;
		}else{
			echo "ERROR: $query<br>".mysql_error();
			return false;
		}
		
	}
	
}