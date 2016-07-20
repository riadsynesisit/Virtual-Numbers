<?php
class FraudDetection{
	
	public $fraud_detection_id;
	public $a_num_times;
	public $a_seconds;
	public $b_1_last_from_sec;
	public $b_1_period;
	public $b_1_seconds;
	public $b_2_last_from_sec;
	public $b_2_last_to_sec;
	public $b_2_period;
	public $b_2_seconds;
	public $b_3_last_from_sec;
	public $b_3_last_to_sec;
	public $b_3_period;
	public $b_3_seconds;
	public $b_4_last_from_min;
	public $b_4_last_from_sec;
	public $b_4_period;
	public $b_4_seconds;
	public $c_total_debit;
	public $c_value;
	public $d_total_debit;
	public $d_debit_percentage;
	public $d_value;
	public $max_forward;
	public $last_changed;
	
	
	
	function getFraudDetection(){
		
		$query = "select 	fraud_detection_id, 
	a_num_times, 
	a_seconds, 
	b_1_last_from_sec, 
	b_1_period, 
	b_1_seconds, 
	b_2_last_from_sec, 
	b_2_last_to_sec, 
	b_2_period, 
	b_2_seconds, 
	b_3_last_from_sec, 
	b_3_last_to_sec, 
	b_3_period, 
	b_3_seconds, 
	b_4_last_from_min, 
	b_4_last_from_sec, 
	b_4_period, 
	b_4_seconds, 
	c_total_debit, 
	c_value, 
	d_total_debit, 
	d_debit_percentage, 
	d_value, 
	max_forward,
	last_changed
	 
	from 
	fraud_detection limit 1;
";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error occurred while getting fraud detection data. Error is: ".mysql_error());
		}
		$row = mysql_fetch_array($result);
		if(mysql_num_rows($result)==0){
			die("Error: No rows found in fraud_detection table.");
		}
		return $row;
		
	}
	
		function saveFraudDetection(){
		
		 $query = "update fraud_detection 
	set
	a_num_times = $this->a_num_times , 
	a_seconds = $this->a_seconds , 
	b_1_last_from_sec = $this->b_1_last_from_sec , 
	b_1_period = $this->b_1_period , 
	b_1_seconds = $this->b_1_seconds , 
	b_2_last_from_sec = $this->b_2_last_from_sec , 
	b_2_last_to_sec = $this->b_2_last_to_sec , 
	b_2_period = $this->b_2_period , 
	b_2_seconds = $this->b_2_seconds , 
	b_3_last_from_sec = $this->b_3_last_from_sec , 
	b_3_last_to_sec = $this->b_3_last_to_sec , 
	b_3_period = $this->b_3_period , 
	b_3_seconds = $this->b_3_seconds , 
	b_4_last_from_min = $this->b_4_last_from_min , 
	b_4_period = $this->b_4_period , 
	b_4_seconds = $this->b_4_seconds , 
	c_total_debit = $this->c_total_debit , 
	c_value = $this->c_value , 
	d_total_debit = $this->d_total_debit , 
	d_debit_percentage = $this->d_debit_percentage , 
	d_value = $this->d_value,
	max_forward = $this->max_forward,
	last_changed=now() ;
";

		
		mysql_query($query);
		
		if(mysql_error()==""){
			return true;
		}else{
			//echo "ERROR: $query<br>".mysql_error();
			return false;
		}
		
	}
	
}