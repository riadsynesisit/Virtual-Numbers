<?php
class daily_stats{
	
	function getRecordIdForToday($today){
		
		$query = "select id from daily_system_stats where stats_date='$today';";
		$result = mysql_query($query);
		
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				$row = mysql_fetch_array($result);
				return $row["id"];
			}else{
				return 0;//No record exists for today - returns zero
			}
		}else{
			die("An error occured while checking if record exists for today in daily_system_stats table: ".mysql_error());
		}
		
	}
	
	function getPeakVolume($recId,$type,$did_type){
		
		$field_suffix = "";
		if($did_type=="070"){
			$field_suffix = "";
		}elseif($did_type=="070"){
			$field_suffix = "_087";
		}
		
		$query = "";//Initialize to empty string
		switch($type){
			case "in":
				$query = "select peak_volume_in$field_suffix as peak from daily_system_stats where id=$recId";
				break;
			case "out":
				$query = "select peak_volume_out$field_suffix as peak from daily_system_stats where id=$recId";
				break;
			case "concurrent":
				$query = "select peak_volume_concurrent$field_suffix as peak from daily_system_stats where id=$recId";
				break;
			default:
				die("Unknown type of Peak Volume '$type' in Daily_Stats.class.php function getPeakVolume");
				break;
		}
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				$row = mysql_fetch_array($result);
				return $row["peak"];
			}else{
				die("No records found in daily_system_stats table for id: ".$recId);
			}
		}else{
			die("An error occurred while getting peak volume in Daily_Stats.class.php : ".mysql_error());
		}
	}
	
	function updatePeakVolume($inPeak, $outPeak, $concurrentPeak, $inPeak_087, $outPeak_087, $concurrentPeak_087){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		
		//Get existing peak volumes
		$existingPeakIn = $this->getPeakVolume($recId,"in","070");
		$existingPeakOut = $this->getPeakVolume($recId, "out","070");
		$existingPeakConcurrent = $this->getPeakVolume($recId, "concurrent","070");
		$existingPeakIn_087 = $this->getPeakVolume($recId,"in","087");
		$existingPeakOut_087 = $this->getPeakVolume($recId, "out","087");
		$existingPeakConcurrent_087 = $this->getPeakVolume($recId, "concurrent","087");
		
		//Update if the existins peaks are less than the current peaks
		if(intval($existingPeakIn) < intval($inPeak)){
			$query = "update daily_system_stats set peak_volume_in=$inPeak, peak_in_time=CURTIME() where id=$recId;";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while updating peak_volume_in in daily_system_stats table: ".mysql_error()."\nQuery is: ".$query);
			}
		}
		if(intval($existingPeakOut) < intval($outPeak)){
			$query = "update daily_system_stats set peak_volume_out=$outPeak, peak_out_time=CURTIME() where id=$recId;";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while updating peak_volume_out in daily_system_stats table: ".mysql_error()."\nQuery is: ".$query);
			}
		}
		if(intval($existingPeakConcurrent) < intval($concurrentPeak)){
			$query = "update daily_system_stats set peak_volume_concurrent=$concurrentPeak, peak_concurrent_time=CURTIME() where id=$recId;";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while updating peak_volume_concurrent in daily_system_stats table: ".mysql_error()."\nQuery is: ".$query);
			}
		}
		if(intval($existingPeakIn_087) < intval($inPeak_087)){
			$query = "update daily_system_stats set peak_volume_in_087=$inPeak_087, peak_in_time_087=CURTIME() where id=$recId;";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while updating peak_volume_in_087 in daily_system_stats table: ".mysql_error()."\nQuery is: ".$query);
			}
		}
		if(intval($existingPeakOut_087) < intval($outPeak_087)){
			$query = "update daily_system_stats set peak_volume_out_087=$outPeak_087, peak_out_time_087=CURTIME() where id=$recId;";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while updating peak_volume_out_087 in daily_system_stats table: ".mysql_error()."\nQuery is: ".$query);
			}
		}
		if(intval($existingPeakConcurrent_087) < intval($concurrentPeak_087)){
			$query = "update daily_system_stats set peak_volume_concurrent_087=$concurrentPeak_087, peak_concurrent_time_087=CURTIME() where id=$recId;";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while updating peak_volume_concurrent_087 in daily_system_stats table: ".mysql_error()."\nQuery is: ".$query);
			}
		}
		
	}
	
	function updateServerDownTime($which){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		
		switch($which){
			case "httpd";
				$table_field = "apache_downtime";
				break;
			case "asterisk";
				$table_field = "ast_downtime";
				break;
			case "mysqld";
				$table_field = "mysql_downtime";
				break;
			case "ntpd";
				$table_field = "ntpd_downtime";
				break;
			case "master";
				$table_field = "smtp_downtime";
				break;
			default :
				die("Unknown server parameter '$which' in Daily_Stats.class.php function updateServerDownTime");
				break;
		}
		$query = "update daily_system_stats set $which=$which+1 where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats table: ".mysql_error()." Query is $query");
		}
	}
	
	function updatePendingEmails($num_emails){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set pending_emails=$num_emails where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.pending_emails table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function updateActivationEmails($num_emails){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set activation_emails=activation_emails+$num_emails where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.activation_emails table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function updateSignupEmails($num_emails=1){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set signup_emails=signup_emails+$num_emails where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.signup_emails table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function updateConfirmEmails($num_emails=1){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set confirm_emails=confirm_emails+$num_emails where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.confirm_emails table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function updatePendingDIDRemovals($num_dids){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set 7day_dids_removed=$num_dids where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.7day_dids_removed table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function updateActiveDIDRemoval($num_dids){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set 90day_dids_removed=$num_dids where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.90day_dids_removed table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function updateFriendEmails($num_emails=1){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set friend_emails=friend_emails+$num_emails where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.friend_emails table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function updateExpire30DaysEmails($num_emails){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set expire30days_emails=$num_emails where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.expire30days_emails table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function updateVoiceMailRemoval($num_vms){
		
		$today = date('Y-m-d');
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set 7day_vm_removed=$num_vms where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats.7day_vm_removed table: ".mysql_error()." Query is $query");
		}
		
	}
	
	function insertDailyData($day, $inbound_secs, $outbound_secs, $tot_cost, $num_calls, $num_vm, $inbound_secs_087, $outbound_secs_087, $tot_cost_087, $num_calls_087, $num_vm_087, $sign_ups_count, $inbound_secs_paid_did, $outbound_secs_paid_did, $tot_cost_paid_dids, $num_calls_paid_dids, $num_vm_paid_dids){
		
		$today = $day;
		
		$recId = $this->getRecordIdForToday($today);
		if($recId==0){//create a new record
			$query = "insert into daily_system_stats(stats_date) values ('".$today."');";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occured while inserting a new record in daily_system_stats table: ".mysql_error());
			}else{
				//Get the current record id
				$recId = mysql_insert_id();
			}
		}
		$query = "update daily_system_stats set inbound_secs=$inbound_secs, 
												outbound_secs=$outbound_secs, 
												sip_credit_used=$tot_cost, 
												num_of_calls=$num_calls, 
												voice_mails=$num_vm, 
												inbound_secs_087=$inbound_secs_087, 
												outbound_secs_087=$outbound_secs_087, 
												sip_credit_used_087=$tot_cost_087, 
												num_of_calls_087=$num_calls_087, 
												voice_mails_087=$num_vm_087,
												signups=$sign_ups_count, 
												inbound_secs_paid_dids=$inbound_secs_paid_did, 
												outbound_secs_paid_dids=$outbound_secs_paid_did, 
												sip_credit_used_paid_dids=$tot_cost_paid_dids, 
												num_of_calls_paid_dids=$num_calls_paid_dids, 
												voice_mails_paid_dids=$num_vm_paid_dids 
											where id=$recId";
		mysql_query($query);
		if(mysql_error()!=""){
			die("An error occured while updating a record in daily_system_stats table with daily date: ".mysql_error()." Query is $query");
		}
		
	}
	
}

?>