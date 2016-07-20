<?php
class Assigned_DID{
	
	public $id;
	public $user_id;
	public $did_number;
	public $did_country_code;
	public $did_country_iso;
	public $did_area_code;
	public $city_id;
	public $forwarding_type;
	public $route_number;
	public $callerid_on;
	public $callerid_option;
	public $callerid_custom;
	public $ringtone;
	public $total_minutes;
	public $did_assigend_on;
	public $did_expires_on;
	public $route_last_used;
	public $route_last_changed;
	public $payments_received_id;
	public $did_plan_id;
	public $plan_period;
	public $balance;
	public $user_timezone;
	
	function update_route($did_id, $new_destination){
		
		$query = "update assigned_dids set 
                route_number='".mysql_real_escape_string($new_destination)."',
                route_last_changed  =   NOW()
                where id=".mysql_real_escape_string($did_id);
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_affected_rows()==0){
				return true;// "Error: No rows affected while updating forwarding number of DID ID: ".$did_id;
			}elseif(mysql_affected_rows()>1){
				return "Error: Multiple rows affected while updating forwarding number of DID ID: ".$did_id;
			}elseif(mysql_affected_rows()==1){
				return true;
			}else{
				return "Error: Unexpected results while updating forwarding number of DID ID: ".$did_id;
			}
		}else{
			return "Error: An error occurred while updating DID Forwarding number for did id: $did_id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function getUserDIDs(){
		
		$query = "select * from assigned_dids where user_id='$this->user_id'";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
	}
	
	function getUserDIDsByUserId($user_id){
		
		$query = "select * from assigned_dids where user_id=$user_id";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
        
        function getBlockDIDs($user_id,$start_rec){
		
		$query = "select id,
                                                user_id,
						did_number, 
						did_block_reason
                                                from assigned_dids 
                                                where did_block_status LIKE 'Y'
					 	order by did_number
					 	limit $start_rec, ".RECORDS_PER_PAGE;
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
        
        function getBlockArchieveDIDs($user_id,$start_rec){
		
		$query = "select id,
                                                user_id,
						did_number, 
						did_block_reason
                                                from assigned_dids_archieve 
                                                order by did_number
					 	limit $start_rec, ".RECORDS_PER_PAGE;
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getUserDIDsCount($user_id,$free_dids_only=false){
		
		//Check input parameter
		if(isset($user_id)==false || trim($user_id)==""){
			return "Error: Required parameter user_id not passed.";
		}
		//Check if the user_id passed is valid or not
		$query = "select count(*) as count from user_logins where id=".mysql_real_escape_string($user_id);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking existance of user id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		$row = mysql_fetch_array($result);
		if($row["count"]==0){
			return "Error: Invalid or non-existing user_id $user_id passed.";
		}
		if($free_dids_only==true){
			$query = "select count(id) as users_dids from assigned_dids where (SUBSTRING(did_number,1,3)='070' OR SUBSTRING(did_number,1,3)='087') and user_id='".mysql_real_escape_string($user_id)."'";
		}else{
			$query = "select count(id) as users_dids from assigned_dids where user_id='".mysql_real_escape_string($user_id)."'";
		}
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting User DIDs count.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		$row = mysql_fetch_array($result);
		return $row['users_dids'];
		
	}
	function getUserDIDsLimit($user_id, $per_ip = false){
		
		//Check input parameter
		if(isset($user_id)==false || trim($user_id)==""){
			return "Error: Required parameter user_id not passed.";
		}
		//Check if the user_id passed is valid or not
		/*$query = "select count(*) as count from user_logins where id=".mysql_real_escape_string($user_id);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking existance of user id.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		$row = mysql_fetch_array($result);
		if($row["count"]==0){
			return "Error: Invalid or non-existing user_id $user_id passed.";
		}
		*/
		
		if($per_ip){
			$query = "select did_limit_per_ip as total from user_logins where id='".mysql_real_escape_string($user_id)."'";
		}else{
			$query = "select did_limit as total from user_logins where id='".mysql_real_escape_string($user_id)."'";
		}
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			echo "Error: An error occurred while getting User DIDs count.<br><br>".mysql_error()."<br><br>Query is: $query";
			die();
		}
		
		if(mysql_num_rows($result) == 0){
			return 0 ;
		}
		
		$row = mysql_fetch_array($result);
		return $row['total'];
		
	}
	
	function getUserDIDsForPage($user_id,$start_rec){
		
		$query = "select id, 
						did_number, 
						route_number, 
						callerid_on, 
						did_assigned_on, 
						route_last_used,
						route_last_changed
					 from assigned_dids 
					 where user_id='$user_id'
					 	order by did_number
					 	limit $start_rec, ".RECORDS_PER_PAGE;
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getAUUserDIDsForPage($user_id,$start_rec,$per_page=RECORDS_PER_PAGE){
		
		$query = "select * from (select ad.id as id, 
						ad.did_number as did_number, 
						ad.did_country_iso as did_country_iso, 
						ad.did_country_code as did_country_code, 
						ad.did_area_code as did_area_code, 
						ad.city_id as city_id, 
						ad.route_number as route_number, 
						ad.balance as balance, 
						ad.callerid_on as callerid_on, 
						ad.total_minutes as total_minutes, 
						ad.did_assigned_on as did_assigned_on, 
						ad.route_last_used as route_last_used, 
						ad.route_last_changed as route_last_changed, 
						date_format(if(ad.plan_period='Y',date_add(pr.date_paid,INTERVAL 1 YEAR),ad.did_expires_on),'%d %b %Y %H:%i:%s') as did_expires_on, 
						ifnull(ad.did_plan_id,0) as chosen_plan, 
						(unix_timestamp(convert_tz(if(ad.plan_period='Y',date_add(pr.date_paid,INTERVAL 1 YEAR),ad.did_expires_on),'GMT','UTC')) - unix_timestamp()) as secs_to_expire, 
						ifnull(date_format(ad.renew_declined,'%d %b %Y %H:%i:%s'),0) as renew_declined, 
						ad.renew_declined_count as renew_declined_count, 
						pr.amount as amount, 
						ifnull(pr.pymt_gateway,0) as pymt_gateway, 
						ifnull(pr.wp_transId,0) as transId, 
						ifnull(pr.pp_receiver_trans_id,0) as pp_receiver_trans_id, 
						1 as myorder, 
						ad.did_expires_on as expiry_order,
						did_cities.city_name, did_countries.country_name 
					 from assigned_dids ad left join payments_received pr on ad.last_pay_id=pr.id 
					   left join did_countries on did_countries.country_iso = ad.did_country_iso left join did_cities on did_cities.city_id = ad.city_id 
					 where ad.user_id='$user_id' 
					 union 
					 select dd.id as id, 
						dd.did_number as did_number, 
						'N.A.' as did_country_iso, 
						dd.did_country_code as did_country_code, 
						dd.did_area_code as did_area_code, 
						'N.A.' as city_id, 
						dd.route_number as route_number, 
						0 as balance, 
						'N.A.' as callerid_on, 
						0 as total_minutes, 
						'N.A.' as did_assigned_on, 
						'N.A.' as route_last_used, 
						'N.A.' as route_last_changed, 
						date_format(dd.did_deleted_on,'%d %b %Y %H:%i:%s') as did_expires_on, 
						0 as chosen_plan, 
						0 as secs_to_expire, 
						0 as renew_declined, 
						0 as renew_declined_count, 
						0 as amount, 
						0 as pymt_gateway, 
						0 as transId, 
						0 as pp_receiver_trans_id, 
						2 as myorder, 
						dd.did_deleted_on as expiry_order,
						'' as city_name,
					    '' as country_name  
					 from deleted_dids dd   
					 where dd.user_id='$user_id' ) as t  group by t.did_number 
					 	order by t.myorder asc, t.expiry_order asc 
					 	limit ".mysql_real_escape_string($start_rec).", ".mysql_real_escape_string($per_page);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting AU DIDs for page. ".mysql_error();
		}
		$ret_arr = array();
		while($row=mysql_fetch_assoc($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getLeadDIDsForPage($user_id,$start_rec,$per_page=RECORDS_PER_PAGE){
		
		$query = "
			select ad.id as id, 
						ad.did_number as did_number, 
						ad.route_number as route_number, 
						ad.balance as balance, 
						ad.lead_did_in_use as lead_did_in_use, 
						date_format(if(ad.plan_period='Y',date_add(pr.date_paid,INTERVAL 1 YEAR),ad.did_expires_on),'%d %b %Y %H:%i:%s') as did_expires_on, 
						(unix_timestamp(convert_tz(if(ad.plan_period='Y',date_add(pr.date_paid,INTERVAL 1 YEAR),ad.did_expires_on),'GMT','UTC')) - unix_timestamp()) as secs_to_expire 
					 from assigned_dids ad left outer join payments_received pr on ad.last_pay_id=pr.id 
					 where ad.user_id='$user_id' and
					 	ad.is_lead_did='Y'  
					 	limit $start_rec, $per_page
		";
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting Lead DIDs for page. ".mysql_error();
		}
		$ret_arr = array();
		while($row=mysql_fetch_assoc($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getDIDData($user_id,$did_number){
		
		$query = "select * from assigned_dids where user_id='$user_id' and did_number='$did_number'";
		$result = mysql_query($query);
		if(mysql_num_rows($result)>0){
			$row = mysql_fetch_array($result);
			return $row;
		}else{
			return 0;
		}
		
	}
	
	function updateDIDData(){
		
		$query = "update assigned_dids set 
                route_number='".mysql_real_escape_string($this->route_number)."',
                callerid_on=$this->callerid_on,
                callerid_option=$this->callerid_option,
                callerid_custom='".$this->callerid_custom."',
                total_minutes='".$this->total_minutes."',
				user_timezone='".$this->user_timezone."',
                route_last_changed  =   NOW()
                where id=".$this->id;
		$result = mysql_query($query);
		if(mysql_error()==""){
			return $result;
		}else{
			die("Error: An error occurred while updating DID Data.<br><br>".mysql_error()."<br><br>Query is: $query");
			return false;
		}
	}
	
	function renewDID($did_id,$added_minutes,$last_pay_id,$did_expires_on,$did_plan_id,$balance=0, $plan_period=''){
		
		//Renew DID does NOT rollover the existing balance - only the new balance is added.
		//Likewise - All old minutes are lost - only the added minutes take effect.
		
		$query = "update assigned_dids set 
                	total_minutes=$added_minutes,
                	last_pay_id=$last_pay_id,
                	did_plan_id=$did_plan_id, 
                	did_expires_on='$did_expires_on', 
                	balance=$balance ";
		if($plan_period != ''){
				$query = $query . ", plan_period = '$plan_period' ";  
		}
        
		$query = $query . " where id=".$did_id;
		$result = mysql_query($query);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Sticky Number dev<dev@stickynumber.com>' . "\r\n";
		$headers .= 'Cc: riadsynesisit@gmail.com' . "\r\n";
	
		mail("riadul.i@stickynumber.com", "DID_RENEW", "renew query=".$query, $headers);
		
		if(mysql_error()==""){
			if(mysql_affected_rows()==0){
				die("Error: No rows updated in assigned_dids.");
			}
			return true;
		}else{
			die("Error: An error occurred while renewing DID.<br><br>".mysql_error()."<br><br>Query is: $query");
			return false;
		}
		
	}
	
	function insertDIDData(){
		
		//Check validity of the user_id and also the DID Number limits
		$users_dids = $this->getUserDIDsCount($this->user_id,true);
		$admin_limit= $this->getUserDIDsLimit($this->user_id);
		$admin_limit_ip = $this->getUserDIDsLimit($this->user_id, true);
		
		if($users_dids>=$admin_limit){
			return "Error: Number of free DIDs in this account $users_dids exceeding the number of allowed free DIDs $admin_limit.";
		}
		
		//Get the number of DIDs added from this IP address in the past 30 days regardless of which user account the DIDs are in
		$ip_address = $_SERVER["REMOTE_ADDR"];
		if(isset($ip_address)==false || trim($ip_address)==""){
			return "Error: No client IP address. ".$ip_address;
		}
		//$ip_query = "select count(*) as count from assigned_dids where (SUBSTRING(did_number,1,3)='070' OR SUBSTRING(did_number,1,3)='087') and (unix_timestamp() - unix_timestamp(did_assigned_on)) < 30*24*60*60 and ip_address='".mysql_real_escape_string($ip_address)."';";
		
		$ip_query = "select count(*) as count from assigned_dids where (SUBSTRING(did_number,1,3)='070' OR SUBSTRING(did_number,1,3)='087') and ip_address='".mysql_real_escape_string($ip_address)."';";
		
		$ip_result = mysql_query($ip_query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of DIDs from the ip. ".mysql_error()." Query is : ".$ip_query;
		}
		$ip_row = mysql_fetch_array($ip_result);
		$ip_count = $ip_row["count"];
		//Now check against the allowed limit
		if($ip_count >= $admin_limit_ip){
			return "Error: You have reached your allocated limit for ip address, contact support to increase.";
		}
		
		if(isset($this->forwarding_type) == false || trim($this->forwarding_type)==""){
			$fwd_type = 1;
		}else{
			$fwd_type = $this->forwarding_type;
		}
		
		if(isset($this->payments_received_id) == false || trim($this->payments_received_id)==""){
			$pay_id = "NULL";
		}else{
			$pay_id = $this->payments_received_id;
		}
		if(isset($this->did_country_code) == false || trim($this->did_country_code)==""){
			$did_country_code = "NULL";
		}else{
			$did_country_code = $this->did_country_code;
		}
		if(isset($this->did_area_code) == false || trim($this->did_area_code)==""){
			$did_area_code = "NULL";
		}else{
			$did_area_code = $this->did_area_code;
		}
		if(isset($this->city_id) == false || trim($this->city_id)==""){
			$did_city_id = "NULL";
		}else{
			$did_city_id = $this->city_id;
		}
		if(isset($this->did_expires_on) == false || trim($this->did_expires_on)==""){
			$did_expires_on = "NULL";
		}else{
			$did_expires_on = "'".$this->did_expires_on."'";
		}
		if(isset($this->did_plan_id) == false || trim($this->did_plan_id)==""){
			$did_plan_id = "NULL";
		}else{
			$did_plan_id = $this->did_plan_id;
		}
		if(isset($this->plan_period) == false || trim($this->plan_period)==""){
			$plan_period = "NULL";
		}else{
			$plan_period = "'".$this->plan_period."'";
		}
		if(isset($this->did_country_iso) == false || trim($this->did_country_iso)==""){
			$did_country_iso = "NULL";
		}else{
			$did_country_iso = "'".$this->did_country_iso."'";
		}
		if(isset($this->balance) == false){
			$this->balance = 0;
		}
		
		 $query = "insert into assigned_dids (
						user_id,
						did_number, 
						did_country_code, 
						did_country_iso, 
						did_area_code, 
						city_id, 
						forwarding_type, 
						route_number, 
						callerid_on,
						callerid_option,
						callerid_custom, 
						ringtone,
						total_minutes,
						last_pay_id,
						did_plan_id,
						plan_period,
						did_assigned_on,
						did_expires_on, 
						balance, 
						ip_address 
						) values(
						'$this->user_id',
						'$this->did_number',
						$did_country_code, 
						$did_country_iso, 
						$did_area_code, 
						$did_city_id, 
						$fwd_type, 
						'$this->route_number',
						$this->callerid_on,
						$this->callerid_option,
						'$this->callerid_custom',
						$this->ringtone,
						'$this->total_minutes',
						$pay_id,
						$did_plan_id,
						$plan_period,
						NOW(),
						$did_expires_on,
						$this->balance, 
						'".$ip_address."' 
						)";
		$result = mysql_query($query);
		if(mysql_error()==""){
			return mysql_insert_id();
		}else{
			return "Error: An error occurred while inserting DID Data.<br><br>".mysql_error()."<br><br>Query is: $query";
		}
		
	}
	
	function getUnconfigured_DIDs(){
		
		$query = "select * from assigned_dids where user_id='$this->user_id' and did_number not in (select exten from ast_rt_extensions)";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getCountry_Codes(){
		
                $query = "select printable_name,country_code from country order by printable_name";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getAllUsersDIDs(){
		
		$query = "select a.id as id,
							a.user_id as user_id,
							u.name as name,
							a.did_number as did_number,
							a.total_minutes as total_minutes,
							a.route_last_used as route_last_used
				 from assigned_dids a, user_logins u 
				 where a.user_id=u.id 
				 order by a.user_id, a.did_number, a.total_minutes";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row = mysql_fetch_array($result)){
			$ret_arr[] = $row;
		}
		return $ret_arr;
	}
	
	function get070DIDsCount(){
		
        $query = "select count(*) as dids_count from assigned_dids where SUBSTRING(did_number,1,3 )='070'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['dids_count'];
		
	}
	
	function get087DIDsCount(){
		
        $query = "select count(*) as dids_count from assigned_dids where SUBSTRING(did_number,1,3 )='087'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['dids_count'];
		
	}
	
	function terminate_dids($user_id,$reason){
		//Call DEAC with MagTel or cancelOrder on DIDWW, as the case may be, on each of the numbers to be deleted
		$query = "select ad.id as id, 
							ad.did_number as did_number, 
							ad.route_number as route_number
							ul.site_country as site_country
						from assigned_dids ad, user_logins ul
						where ad.user_id=ul.id and 
								ad.user_id=$user_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while retrieving the user DIDs.");
		}
		while($row = mysql_fetch_array($result)){
			$did_id = $row["id"];
			if(substr($row["did_number"],0,3)=="070" || substr($row["did_number"],0,3)=="087" || substr($row["did_number"],0,4)=="4470" || substr($row["did_number"],0,4)=="4487"){//DEAC with MagTel
				$deac_status = $this->DEAC_did_number($row['did_number']);
				if($deac_status!=true){
					die("Error: An error occurred while DEACtivating the DID.");
				}
			}else{//All other DIDs cancelOrder with DIDWW
				$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
				if(is_object($client)==false){
				  	die("Failed to create DidwwApi object.");
				}
				$did_order_cancel = $client->cancelOrder($user_id,$row['did_number']);
				if(is_object($did_order_cancel) && $did_order_cancel->result==0){//success
					//All is good - continue
				}else{
					if(is_object($did_order_cancel)==true){
						die("Error: ".$client->getErrorString().", Error code: ".$client->getErrorCode()." Result: ".$did_order_cancel->result);
					}else{
						die("Error: did_order_cancel returned by cancelOrder is NOT an object.");
					}
				}
			}
			$extquery = "delete from ast_rt_extensions where exten='".$row['did_number']."'";
			mysql_query($extquery);
			if(mysql_error()!=""){
				die("Error: An error occurred while deleting the record from ast_rt_extensions. ".mysql_error());
			}
			//Now add a record in deleted DIDs table
			$ins_query = "insert into deleted_dids(
						user_id, 
						did_id, 
						did_number,  
						route_number, 
						did_deleted_on,
						delete_reason
					) values(
						'$user_id',
						$did_id, 
						'".$row['did_number']."', 
						'".$row['route_number']."', 
						NOW(),
						'".mysql_real_escape_string($reason)."'
					)";
			$result = mysql_query($ins_query);
			if(mysql_error()!=""){
				die("Error: An error occurred while inserting deleted DID Data.<br><br>".mysql_error());
				return false;
			}
		}//End of while loop
		//Now delete the numbers
		$query = "delete from assigned_dids where user_id=$user_id";
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while deleting the record from assigned_dids. ".mysql_error());
		}
		return true;
	}
	
	function deleteDIDData($user_id,$did_number){
		//Get the DID data before deleting the record
		$did_data = $this->getDIDData($user_id,$did_number);
        //Call DEAC on the number to be deleted
        $this->DEAC_did_number($did_number);
		$query = "delete from assigned_dids where user_id=$user_id and did_number='$did_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while deleting the record from assigned_dids. ".mysql_error());
		}
		$asquery = "delete from ast_rt_extensions where  exten='$did_number'";
		$asresult = mysql_query($asquery);
		if(mysql_error()!=""){
			die("Error: An error occurred while deleting the record from ast_rt_extensions. ".mysql_error());
		}
		if(mysql_error()==""){
			//Insert record in deleted_dids table
			$deleted_dids_ins_query = "insert into deleted_dids(
						user_id, 
						did_id, 
						did_number,  
						route_number, 
						did_deleted_on,
						delete_reason
					) values(
						'$user_id', 
						".$did_data["id"].", 
						'$did_number',
						'".$did_data["route_number"]."',
						NOW(),
						'Deleted by user'
					)";
			$deleted_dids_result = mysql_query($deleted_dids_ins_query);
			//Insert record in access_logs
			$this_access = new Access();
			$this_access->updateAccessLogs('Deleted DID : ',$did_number);
			//Send email to the user
			$user_query = "select uniquecode, email, name, unsubscribe from user_logins where id=".$user_id;
			$user_result = mysql_query($user_query);
			$user_row = mysql_fetch_array($user_result);
			if($user_row['unsubscribe']=='N'){//Send emails only to those who did NOT unsubscribe
				$virt_num = "(+44) ".substr($did_number,1);
				$this->send_did_delete_email($user_id,$user_row['email'],$user_row['name'],$virt_num,$user_row['uniquecode']);
			}
			return true;			
		}else{
			die("Error: An error occurred while deleting the record from voicemessages. ".mysql_error());
		}
		
	}
	
	function deleteAUDIDData($user_id,$did_number,$reason=""){
		
		//Get the DID data before deleting the record
		$did_data = $this->getDIDData($user_id,$did_number);
		
		//Check if there is a WP Preapproval for this DID
		$query = "select wpa.agreement_id as agreement_id, wpa.cart_id as cart_id from  worldpay_preapprovals wpa,  assigned_dids ad where wpa.did_id=ad.id and ad.user_id=$user_id and ad.did_number=$did_number";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while checking if a WP Preapproval exists for user_id $user_id and did $did_number. ".mysql_error());
		}
		if(mysql_num_rows($result)>0){
			//Need to cancel the WP Preapproval agreement here
			$row = mysql_fetch_assoc($result);
			$status = cancel_wp_agreement($row["agreement_id"],$row["cart_id"],"DID $did_number is deleted.");//This function exists in worldpay.lib.php
			if($status!==true){
				die("Error: Failed to cancel existing WP agreement ".$row["agreement_id"]." while deleting AU DID - $status");
			}
		}
		
		$query = "delete from assigned_dids where user_id=$user_id and did_number='$did_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while deleting the record from assigned_dids. ".mysql_error());
		}
		$asquery = "delete from ast_rt_extensions where  exten='$did_number'";
		$asresult = mysql_query($asquery);
		if(mysql_error()!=""){
			die("Error: An error occurred while deleting the record from ast_rt_extensions. ".mysql_error());
		}
		//Insert record in deleted_dids table
		$deleted_dids_ins_query = "insert into deleted_dids(
					user_id, 
					did_id, 
					did_number,  
					route_number, 
					did_deleted_on,
					delete_reason
				) values(
					'$user_id',
					".$did_data["id"].", 
					'$did_number',
					'".$did_data["route_number"]."',
					NOW(),
					'".mysql_real_escape_string($reason)."'
				)";
		$deleted_dids_result = mysql_query($deleted_dids_ins_query);
		//Insert record in access_logs
		$this_access = new Access();
		$this_access->updateAccessLogs('Deleted DID : ',$did_number);
		return true;
		
	}
	
	function send_did_delete_email($user_id, $to_email,$cust_name,$did_number,$uniquecode){
		
		$from_email = ACCOUNT_CHANGE_EMAIL;
		if(trim($from_email)==""){
			echo "\nERROR: NO FROM EMAIL";
			return false;
		}
		$subject_line = $did_number." | Virtual Number Deleted";
		$email_hmtl_start = '<html> 
	  	<body style="padding: 10px 15px; background: #FDFFC0; border: 1px dotted #999; color: #666; margin-bottom: 15px;">'; 
		$email_top = "
		Hello $cust_name,<br />
		<br />
		Your virtual number $did_number has been <font color='red'>DELETED</font>. 
		Your virtual number will no longer show within your account and calls can no longer be received. 
		<br /><br />
		-------------------------------------------------------------------------------------------------------
		<br /><br />
		<b>$did_number</b>
		<br /><br />
		<br />Status:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deleted
		<br /><br />
		-------------------------------------------------------------------------------------------------------
		<br /><br />";
		$email_body = '<br />
		If you have any questions or need assistance with your account management, please contact us.<br />
	 	<br />
		Kind Regards,<br />
	 	<br />
		Sticky Number <br />
		Customer Support<br />
	
		e: <a href="mailto:info@stickynumber.com">info@stickynumber.com</a><br />
		w: <a href="'.SERVER_WEB_NAME.'">'.SERVER_WEB_NAME.'</a><br />';
		$email_hmtl_end = '
		<br />You are receiving this email because you signed up on StickyNumber.com. Please click here to <a href="https://www.stickynumber.com/unsubscribe.php?id='.$uniquecode.'">unsubscribe</a>.
	  	</body> 
		</html>';
	
		$email_message  = $email_hmtl_start . $email_top . $email_body . $email_hmtl_end;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
		$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
		$status = mail($to_email, $subject_line, $email_message, $headers, $addl_params);
		if($status==true){
			$status = create_client_notification($user_id, $from_email, $to_email, $subject_line, $email_message);
			if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
				return $status;
			}else{
				return true;
			}
		}
		
	}
	
	function DEAC_did_number($did_number){
		
		$fp = fsockopen (MAGRATHEA_API_HOST, MAGRATHEA_PORT, $errno, $errstr, 30);
		fputs($fp, "AUTH ".MAGRATHEA_AUTH_NAME." ".MAGRATHEA_AUTH_WORD);
		fputs($fp, "\n");
		fputs($fp, "DEAC ".$did_number);
		fputs($fp, "\n");
		fputs($fp, "QUIT");
		fputs($fp, "\n");
		return true;
		
	}
	
	function updateDIDExpiry($did_number,$period){
		
		if($this->isDIDExpired($did_number)=="YES"){
			$query = "update assigned_dids set did_expires_on=DATE_ADD(now(), INTERVAL $period MONTH) where did_number='$did_number';";
		}else{
			$did_expiry = $this->getDIDExpiry($did_number);
			if($did_expiry==0 || $did_expiry=="0"){
				$did_expiry = " now() ";
			}
			$query = "update assigned_dids set did_expires_on=DATE_ADD($did_expiry, INTERVAL $period MONTH) where did_number='$did_number';";
		}
		mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while updating DID Expiry date.<br><br>".mysql_error()."<br><br>Query is: $query");
		}
		return true;
	}
	
	function getDIDExpiry($did_number){
		
		$query = "select IFNULL(did_expires_on,0) as did_expires_on from assigned_dids where did_number = '$did_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting DID Expiry date.<br><br>".mysql_error()."<br><br>Query is: $query");
		}
		if(mysql_num_rows($result)!=0){
			$row = mysql_fetch_array($result);
			return $row['did_expires_on'];
		}else{
			die("No DID record found in assigned_dids for DID: ".$did_number);
		}
		
	}
	
	function getDIDExpiryFromID($did_id){
		
		$query = "select IFNULL(did_expires_on,0) as did_expires_on from assigned_dids where id=$did_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting DID Expiry date.<br><br>".mysql_error()."<br><br>Query is: $query");
		}
		if(mysql_num_rows($result)!=0){
			$row = mysql_fetch_array($result);
			return $row['did_expires_on'];
		}else{
			die("No DID record found in assigned_dids for DID ID: ".$did_id);
		}
		
	}
	
	function isDIDExpired($did_number){
		
		$query = "select IF(did_expires_on,IF(convert_tz(did_expires_on,'GMT','UTC') > now(),'NO','YES'),'YES') as isDIDExpired from assigned_dids where did_number = '$did_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while checking if DID Expired.<br><br>".mysql_error()."<br><br>Query is: $query");
		}
		$row = mysql_fetch_array($result);
		return $row['isDIDExpired'];
		
	}
	
	function getSimilarFwdNumCount($user_id,$fwd_num){
		
		$similar_numbers = 0;
		
		if(substr(trim($fwd_num),0,1)=="+"){
			$fwd_number = substr(trim($fwd_num),1);
		}else{
			$fwd_number = trim($fwd_num);
		}
		if(substr($fwd_number,0,2)=="00"){
			$fwd_number = substr($fwd_number,2);
		}
		
		$query = "select distinct(route_number) as route_number from assigned_dids where user_id=$user_id and date_add(did_assigned_on, INTERVAL 7 DAY) >= CURDATE() and SUBSTRING(did_number,1,3 )='070'";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)<=0){
				return $similar_numbers;
			}
			while($row = mysql_fetch_array($result)){
				if(substr(trim($row['route_number']),0,1)=="+"){
					$route_number = substr(trim($row['route_number']),1);
				}else{
					$route_number = trim($row['route_number']);
				}
				if(substr($route_number,0,2)=="00"){
					$route_number = substr($route_number,2);
				}
				$fwd_num_len = strlen($fwd_number);
				$route_number_len = strlen($route_number);
				$len_except_last_two = intval($fwd_num_len - 2);
				if($fwd_num_len==$route_number_len && substr($fwd_number,0,$len_except_last_two)==substr($route_number,0,$len_except_last_two)){
					$similar_numbers = $similar_numbers + 1;
				}
			}//end of while loop
		}else{
			die("Error: An error occurred while getting similar forwarding numbers count. ".mysql_error());
		}
		
		return $similar_numbers;
	}
	
	function getSimilarNumbersInfo($user_id,$fwd_num){
		
		$numbers_info = "";
		
		if(substr(trim($fwd_num),0,1)=="+"){
			$fwd_number = substr(trim($fwd_num),1);
		}else{
			$fwd_number = trim($fwd_num);
		}
		if(substr($fwd_number,0,2)=="00"){
			$fwd_number = substr($fwd_number,2);
		}
		
		$query = "select did_number, route_number, did_assigned_on from assigned_dids where user_id=$user_id and date_add(did_assigned_on, INTERVAL 7 DAY) >= CURDATE()";
		$result = mysql_query($query);
		if(mysql_error()==""){
			while($row = mysql_fetch_array($result)){
				$numbers_info .= "DID : ".$row["did_number"]." assigned on : ".$row["did_assigned_on"]." forwarding to : ".$row["route_number"]."<br>";
			}
		}else{
			die("Error: An error occurred while getting similar forwarding numbers info. ".mysql_error());
		}
		return $numbers_info;
	}
	
	function getDIDWWOrderId($user_id, $did_id){
		
		$query = "select ifnull(pr.didww_order_id,0) as didww_order_id
					from assigned_dids ad, payments_received pr
					where ad.id = '$did_id' and 
						ad.user_id = '$user_id' and
						ad.last_pay_id = pr.id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			if(mysql_num_rows($result)>0){
				return $row["didww_order_id"];
			}else{
				return 0;
			}
		}else{
			die("Error: An error occurred while getting DIDWWOrderID. ".mysql_error());
		}
		
	}
	
	function getDIDWWOrderIdFromDIDNumber($user_id, $did_number){
		
		$query = "select ifnull(pr.didww_order_id,0) as didww_order_id
					from assigned_dids ad, payments_received pr
					where ad.did_number = $did_number and 
						ad.user_id = '$user_id' and
						ad.last_pay_id = pr.id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_array($result);
			if(mysql_num_rows($result)>0){
				return $row["didww_order_id"];
			}else{
				return 0;
			}
		}else{
			die("Error: An error occurred while getting DIDWWOrderID from DID Number for user ID $user_id and DID Number $did_number. ".mysql_error());
		}
		
	}
	
	function updateRenewDeclined($did_id){
		
		$query = "update assigned_dids set renew_declined=now(), renew_declined_count = renew_declined_count+1 where id=$did_id";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			echo "Error: An error occurred while updating renew declined: ".mysql_error();
			return false;
		}
		
	}
	
	function resetRenewDeclined($did_id){
		
		$query = "update assigned_dids set renew_declined=NULL, renew_declined_count = 0 where id=$did_id";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			echo "Error: An error occurred while resetting renew declined: ".mysql_error();
			return false;
		}
		
	}
	
	function showDIDToMember($did_id){
		
		$query = "update assigned_dids set renew_declined_count = 1 where id=$did_id";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			echo "Error: An error occurred while resetting renew declined to 1 to show to the member: ".mysql_error();
			return false;
		}
		
	}
	
	function getDIDNumberFromID($did_id){
		
		$query = "select did_number from assigned_dids where id=$did_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)>0){
				$row = mysql_fetch_assoc($result);
				return $row["did_number"];
			}else{
				echo "Error: No rows found for DID id: ".$did_id;
				return false;
			}
		}else{
			echo "Error: An error occurred while getting did_number from did_id: ".mysql_error();
			return false;
		}
		
	}
	
	function getDIDDetailsFromID($did_id){
		
		$query = "select * from assigned_dids where id=$did_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows found for DID id: ".$did_id;
			}
			return mysql_fetch_assoc($result);
		}else{
			return "Error: An error occurred while getting DID Details from did_id $did_id: ".mysql_error()." Query is $query";
		}
		
	}
	
	function updatePreapprovalKey($did_id, $cart_id){
		
		//Get the Payment Gateway used for this shopping cart purchase
		$pymt_gateway = "";$preapprovalkey = "";
		$query = "select pymt_gateway from shopping_cart where id=$cart_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return "Error: No rows found in shopping_cart for cart_id: ".$cart_id;
			}
			$row = mysql_fetch_assoc($result);
			$pymt_gateway = $row["pymt_gateway"];
		}else{
			return "Error: An error occurred while getting pymt_gateway : ".mysql_error();
		}
		
		if($pymt_gateway=="PayPal"){
			//First get the preapprovalkey from the paypal_preapprovals table based on the cart_id
			
			$query = "select preapprovalkey from paypal_preapprovals where cart_id=$cart_id";
			$result = mysql_query($query);
			if(mysql_error()==""){
				if(mysql_num_rows($result)==0){
					return "Error: No rows found in paypal_preapprovals for cart_id: ".$cart_id;
				}
				$row = mysql_fetch_assoc($result);
				$preapprovalkey = $row["preapprovalkey"];
			}else{
				return "Error: An error occurred while getting preapprovalkey : ".mysql_error();
			}
		}elseif($pymt_gateway=="WorldPay" || $pymt_gateway == "Credit_Card"){
			//Need to cancel the existing future pay agreement using iadmin servlet
		}elseif($pymt_gateway=="AccountCredit"){
			//Preapproval is not applicable for payments by AccountCredit
			return true;
		}else{
			return "Error: Unknown pymt_gateway $pymt_gateway in shopping_cart for cart_id: $cart_id";
		}
		
		//Now update the assigned_dids table
		$query = "update assigned_dids set preapprovalkey='$preapprovalkey' where id=$did_id";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred while updating preapprovalkey : ".mysql_error();
		}
		
	}
	
	function cancelFuturePay($did_id,$cancel_reason=""){
		
		$query = "select pr.cart_id as cart_id, 
						pr.pymt_gateway as pymt_gateway 
					from payments_received pr, 
						assigned_dids ad 
					where ad.last_pay_id=pr.id and
						ad.id=$did_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==0){
				return true;//No existing pre-approvals found for cancellation
			}
			while($row = mysql_fetch_assoc($result)){
				$cart_id = $row["cart_id"];
				//$pymt_gateway = $row["pymt_gateway"];
				//Regardless of the current payment gateway we need to check if there exists a pre-approval in both PayPal and WP - If previous paid using a different payment gateway
				$pp_query = "select id from paypal_preapprovals where cart_id=$cart_id";
				$pp_result = mysql_query($pp_query);
				if(mysql_error()!=""){
					return "Error: An error occurred while checking if a PP pre-approval exists for cart_id $cart_id - ".mysql_error();
				}
				if(mysql_num_rows($pp_result)>0){
					$update_query = "update paypal_preapprovals set status='C' where cart_id=$cart_id";
					$update_result = mysql_query($update_query);
					if(mysql_error()!=""){
						return "Error: An error occurred while cancelling paypal preapproval for cart_id $cart_id - ".mysql_error();
					}
				}
				//Now check if a WP Pre approval exists and cancel it
				//Get the WP Agreement ID for this WP transaction
				$wp_query = "select agreement_id from  worldpay_preapprovals where cart_id=$cart_id";
				$wp_result = mysql_query($wp_query);
				if(mysql_error()!=""){
					return "Error: An error occurred while getting WP Agreement ID for cart_id $cart_id - ".mysql_error();
				}
				if(mysql_num_rows($wp_result)>0){
					$wp_row = mysql_fetch_assoc($wp_result);
					$cancel_fp_status = cancel_wp_agreement($wp_row["agreement_id"],$cart_id,$cancel_reason);
					if($cancel_fp_status!==true){
						return $cancel_fp_status.", Cart_ID passed is $cart_id and agreement_id passed is :".$wp_row["agreement_id"];
					}
				}
				
			}//End of While loop
		}else{
			return "Error: An error occurred while getting existing pre-approvals of DID_ID: $did_id. ".mysql_error();
		}
		
	}
	
	function get_did_count_forwarding_to($fwd_number){
		
		$query = "select count(*) as rec_count from assigned_dids where route_number='$fwd_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of DIDs forwarding to $fwd_number  : ".mysql_error();
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row["rec_count"];
		
	}
	
	function get7DaysDIDsCount($user_id){
			
		$query = "select count(*) as dids_count from assigned_dids where user_id=$user_id and date_add(did_assigned_on, INTERVAL 7 DAY) >= NOW()";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting 7 days dids count from assigned_dids : ".mysql_error();
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row["dids_count"];
		
	}
	
	function reset_balance($did_id, $is_reset = false, $balance = ""){
		
		if($is_reset){
			$query = "UPDATE assigned_dids SET balance=$balance where id=$did_id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return false;
			}else{
				return true;
			}
		}else{
			$query = "select (pr.amount - pr.didww_did_monthly) as balance  
				from assigned_dids ad, 
					payments_received pr 
				where ad.last_pay_id=pr.id and 
					ad.id=$did_id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting reset balance of assigned_dids id $did_id. Error is: ".mysql_error();
			}
			if(mysql_num_rows($result)==0){
				return "Error: No rows found while geeting balance for DID ID: $did_id. Query is $query";
			}
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$balance = $row["balance"];
			$query = "update assigned_dids set balance=$balance where id=$did_id";
			if(mysql_error()!=""){
				return "Error: An error occurred while updating balance of assigned_dids id $did_id. Query is $query. Error is: ".mysql_error();
			}
			if(mysql_affected_rows()==0){
				return "Error: No rows affected while updating balance of did id: $did_id";
			}
			return true;
		}
	}
	
	function refill_did($user_id, $did_number){
		
		//Ensure that this did number belongs to this user id
		$query = "select id, last_pay_id from assigned_dids where user_id='$user_id' and did_number='$did_number'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting assigned_dids id of DID: $did_number. Error is: ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No rows found for DID Number: $did_number in User: $user_id.";
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$did_id = $row["id"];
		$last_pay_id=$row["last_pay_id"];
		
		//Now get the Account Credit of this user
		$query = "select IFNULL(auto_refill_amount,10) as refill_amount, total_credits from user_logins where id=$user_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting refill amount of User: $user_id. Error is: ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No rows found for User: $user_id.";
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$refill_amount = $row["refill_amount"];
		$total_credits = $row["total_credits"];
		
		if(doubleval($refill_amount)>doubleval($total_credits)){
			$refill_amount = $total_credits;
		}
		//First deduct the money
		$query = "update user_logins set total_credits=total_credits-$refill_amount where id=$user_id and total_credits>=$refill_amount;";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while deducting refill amount of User: $user_id. Error is: ".mysql_error();
		}
		if(mysql_affected_rows()==0){
			return "Error: No rows were updated while deducting refill amount from user account credit for user $user_id";
		}
		//Now do the balance refill
		$query = "update assigned_dids set balance=balance+$refill_amount where id=$did_id";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while adding refill amount to did_id: $did_id. Error is: ".mysql_error();
		}
		if(mysql_affected_rows()==0){
			return "Error: No rows were updated while upating refill amount to did_id $did_id";
		}
		//Now generate an invoice
		$query = "insert into payments_received (
						user_id, 
						chosen_plan, 
						plan_period,
						amount,
						pymt_type, 
						cart_id,  
						ip_address, 
						is_new_did,
						payment_reversed,
						is_auto_renewal, 
						pymt_gateway,  
						transTime
						)
						select $user_id,
							chosen_plan, 
							plan_period,
							$refill_amount,
							'UserRefill', 
							cart_id, 
							'0.0.0.0', 
							0,
							0,
							0, 
							'AccountCredit', 
							NOW() 
						from payments_received 
						where id=$last_pay_id
						";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while generating invoice for Refill Amount for: $did_id. Error is: ".mysql_error();
		}
		$ins_id = mysql_insert_id();
		//Now update the last_pay_id in assigned_dids with the id of the payments_received record
		$query = "update assigned_dids set last_pay_id=$ins_id  where id=$did_id";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while updating last pay id for: $did_id. Error is: ".mysql_error();
		}
		return true;
	}
	
	function remove_lead_tracking($did_id){
		
		$query = "update assigned_dids set lead_did_in_use='N' where id=$did_id;";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while removing lead tracking for: $did_id. Error is: ".mysql_error();
		}
		if(mysql_affected_rows()>1){
			return "Error: Unexpexted number of rows (".mysql_affected_rows().") were updated while updating lead reporting email for did_id $did_id.";
		}
		return true;
		
	}
	
	function get_lead_dids_count($user_id){
		
		$query = "select count(*) as count from assigned_dids where is_lead_did='Y' and user_id=$user_id;";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of lead DIDs NOT in use of user_id: $user_id. Error is: ".mysql_error();
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row["count"];
		
	}
	
	function getLeadDIDsByUserId($user_id){
		
		$query = "select * from assigned_dids where is_lead_did='Y' and user_id=$user_id";
		$result = mysql_query($query);
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function mark_lead_did($did_id){
		
		//mark this DID as a lead DID and blank out the forwarding number
		$query = "update assigned_dids set is_lead_did='Y', route_number=NULL where id=$did_id;";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while marking did as lead did for DID ID: $did_id. Error is: ".mysql_error();
		}
		if(mysql_affected_rows()>1){
			return "Error: Unexpexted number of rows (".mysql_affected_rows().") were updated while marking did as lead did for did_id $did_id.";
		}
		return true;
		
	}
	
	function assign_lead_did($user_id){
		
		$query = "select ad.id as id, 
							ad.did_number as did_number 
						from assigned_dids ad 
						where ad.is_lead_did='Y' and 
							ad.lead_did_in_use='N' and 
							(substring(ad.did_number,1,3)='070' or 
								substring(ad.did_number,1,3)='087' or 
								substring(ad.did_number,1,4)='4470' or 
								substring(ad.did_number,1,4)='4487' or 
								(unix_timestamp(convert_tz(ad.did_expires_on,'GMT','UTC')) - unix_timestamp()) > 0) and  
							ad.user_id=".mysql_real_escape_string($user_id)." 
							order by id 
							limit 1";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while assigning a lead DID. ".mysql_error()." Query is: ".$query;
		}
		if(mysql_num_fields($result)==0){
			return 0;//No DIDs available
		}
		$row = mysql_fetch_array($result);
		return $row;
	}
	
	function mark_lead_did_in_use($did_id){
		
		$query = "update assigned_dids set lead_did_in_use='Y' where id=$did_id and lead_did_in_use='N'";
		mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while marking lead did in use: $did_id. Error is: ".mysql_error();
		}
		if(mysql_affected_rows()==0){
			return false;//Could not be marked as in use.
		}elseif(mysql_affected_rows()!=1){
			return "Error: Unexpexted number of rows (".mysql_affected_rows().") were updated while marking did as lead did for did_id $did_id.";
		}else{
			return true;
		}
		
	}
	
	function get_active_paid_dids_count(){
		
		$query = "select count(*) as count 
						from assigned_dids ad, 
							payments_received pr 
						where ad.last_pay_id=pr.id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count of active paid DIDs. ".mysql_error()." Query is: ".$query;
		}
		$row = mysql_fetch_array($result);
		return $row["count"];
		
	}
	
	function get_active_paid_dids($start_rec,$per_page=RECORDS_PER_PAGE){
		
		$query = "select ad.id as id, 
							ad.did_number as did_number, 
							concat('M',ad.user_id+10000) as member_id, 
							date_format(if(ad.plan_period='Y',date_add(pr.date_paid,INTERVAL 1 YEAR),ad.did_expires_on),'%d %b %Y %H:%i:%s') as did_expires_on, 
							(unix_timestamp(convert_tz(if(ad.plan_period='Y',date_add(pr.date_paid,INTERVAL 1 YEAR),ad.did_expires_on),'GMT','UTC')) - unix_timestamp()) as secs_to_expire, 
							pr.pymt_gateway as pymt_gateway, 
							pr.pymt_type as pymt_type, 
							if(pr.pymt_gateway='PayPal',pr.pp_receiver_trans_id,ifnull(wp_transId,'N/A')) as transaction_id, 
							(case ul.site_country when 'COM' then '$' when 'AU' then '$' when 'UK' then '&pound;' else '' end) as currency_symbol, 
							round(pr.amount,2) as amount, 
							date_format(pr.date_paid,'%d %b %Y %H:%i:%s') as last_bill_date 
						from assigned_dids ad, 
							payments_received pr, 
							user_logins ul 
						where ad.last_pay_id=pr.id and
								ad.user_id=ul.id 
						order by secs_to_expire asc
						limit ".mysql_real_escape_string($start_rec).", ".mysql_real_escape_string($per_page);
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting active paid DIDs. ".mysql_error()." Query is: ".$query;
		}
		if(mysql_num_rows($result)==0){
			return 0;
		}
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function getIPCount($ip_address){
		$query = "SELECT count(id) as ip_count FROM assigned_dids WHERE ip_address LIKE '" . $ip_address . "' AND did_assigned_on = date(now()) AND ( did_number like '070%' OR did_number like '087%') ";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "error";
		}
		$ipRow = mysql_fetch_array($result) ;
		
		$ipCount = $ipRow['ip_count'];
		return $ipCount;
			
	}
	
	function updateRenewFailed($did_id, $is_failed){
		$query = "UPDATE assigned_dids SET is_renew_failed = '".$is_failed."' WHERE id = " . $did_id;
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return false;
		}else{
			return true;
		}
	}
	
	
}