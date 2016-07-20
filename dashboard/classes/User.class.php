<?php

class User{
	
	public $id;
	public $email;
	public $name;
	public $password;
	public $user_created_on;
	public $user_last_login;
	public $last_changed_state;
	public $admin_flag;
	public $status;
	public $date_status_set;
	public $did_limit;
	public $did_limit_per_ip;
	public $address1;
	public $address2;
	public $user_city;
	public $user_state;
	public $user_country;
	public $postal_code;
	public $max_30days_acct_credit;
	public $leads_tracking_menu;
	public $show_wp;
	public $show_acct_crdt;
	public $allow_over_4_credits;
	
	function checkEmailExists($email,$site_country){
		
		$query = "select id from user_logins where site_country='".mysql_real_escape_string($site_country)."' and email='".mysql_real_escape_string($email)."'";
		$result = mysql_query($query);
		if(mysql_num_rows($result)!=0){
			return true;
		}else{
			return false;
		}
		
	}
        
        
	
	function insertUser($name,$email,$password,$site_country,$user_type="P",$company="",$address1="",$address2="",$postal_code="",$user_city="",$user_state="",$user_country="", $is_free_number="N"){
		
                $uniquecode =   md5($email.$password);
                
		$query = "insert into user_logins (
							email, 
							name, 
							password,
                                                        uniquecode,
							user_created_on, 
							last_changed_state,
							ip_address,
							site_country, 
							user_type, 
							company, 
							address1, 
							address2, 
							postal_code, 
							user_city, 
							user_state, 
							user_country,
							email_verify_required	
							) values (
							'".mysql_real_escape_string($email)."',
							'".mysql_real_escape_string($name)."',
							'".md5($password)."',
                                                        '".$uniquecode."',    
							CURRENT_DATE,
							CURRENT_DATE,
							'".mysql_real_escape_string($_SERVER["REMOTE_ADDR"])."',
							'".mysql_real_escape_string($site_country)."', 
							'".mysql_real_escape_string($user_type)."', 
							'".mysql_real_escape_string($company)."', 
							'".mysql_real_escape_string($address1)."', 
							'".mysql_real_escape_string($address2)."', 
							'".mysql_real_escape_string($postal_code)."', 
							'".mysql_real_escape_string($user_city)."', 
							'".mysql_real_escape_string($user_state)."', 
							'".mysql_real_escape_string($user_country)."',
							'".$is_free_number."'
							)";
		//echo $query;
		mysql_query($query);
		if(mysql_error()==""){
			return mysql_insert_id();
		}else{
			return 0;
		}
		
	}
	
	function insertUser_frnd($name,$email,$password,$frnd_email,$site_country){
		
                $uniquecode =   md5($email.$password);
                
		$query = "insert into user_logins (
							email, 
							name, 
							password,
                                                        uniquecode,
							frnd_email,
							user_created_on, 
							last_changed_state,
							ip_address,
							site_country
							) values (
							'".mysql_real_escape_string($email)."',
							'".mysql_real_escape_string($name)."',
							'".md5($password)."',
                                                        '".$uniquecode."',     
							'".mysql_real_escape_string($frnd_email)."',
							CURRENT_DATE,
							CURRENT_DATE,
							'".mysql_real_escape_string($_SERVER["REMOTE_ADDR"])."',
							'".$site_country."'
							)";
		//echo $query;
		mysql_query($query);
		if(mysql_error()==""){
			return mysql_insert_id();
		}else{
			return 0;
		}
		
	}
	
	function isValidLogin($email,$password,$site_country, $login_attempt=null){
		
		$_SESSION['verify_required'] = 'N';
		
		$query = "select id, name, status, admin_flag, login_attempt_count, password, email_verify_required, verify_email from user_logins where site_country='".mysql_real_escape_string($site_country)."' and email='".mysql_real_escape_string($email)."' ";
		$result = mysql_query($query);
		if(mysql_num_rows($result)!=0){
			$row = mysql_fetch_assoc($result);
			
			if( $row['password'] == md5($password) ){
				if(strtolower( $row['status'] ) == 'active'){
					
					if( $row['email_verify_required'] == 'Y' && $row['verify_email'] == 'N' ){
						$_SESSION['verify_required'] = 'Y';
					}
					//admin_flag==2 for basicsupport - only member search is available for this type of login	
					if(($row['admin_flag']==1 || $row['admin_flag']==2) && ($_SERVER["HTTP_HOST"]!="system.hawkingsoftware.ae" && $_SERVER["HTTP_HOST"]!="dev.stickynumber.com" && $_SERVER["HTTP_HOST"]!="localhost" && $_SERVER["HTTP_HOST"]!="dev.stickynumber.uk")){
						return false;
					}
					
					// update login_attempt_count to zero ...
					if( $row['login_attempt_count'] > 0 && strtolower( $row['status'] ) != 'loginrestricted' ){
						$query = "update user_logins set login_attempt_count = 0 where id = " . $row['id'];
						mysql_query($query) ;
					}
				}
				return $row['status'];
			}else{
				if($login_attempt){
					return ($row['id'].'#'.$row['login_attempt_count'].'#'.$row['name'].'#'.$row['status']) ;
				}else{
					return false ;
				}
			}
			
		}else{
			return false;
		}
		
	}
	
	function getUserInfo($email,$site_country){
		
		$query = "select status,
							id, 
							name, 
							email, 
							user_last_login, 
							admin_flag,
							user_type, 
							company, 
							address1, 
							address2, 
							postal_code, 
							user_city, 
							user_state, 
							user_country
						from user_logins where site_country='".mysql_real_escape_string($site_country)."' and email='".mysql_real_escape_string($email)."'";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				$row = mysql_fetch_array($result);
				return $row;
			}else{
				return 0;
			}
		}else{
			echo "An error occurred while getting user info.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
			return false;
		}
		
	}
	function getUserInfoById($id){
		
		$query = "select status, 
							DATE_FORMAT(date_status_set,'%d/%m/%Y') as date_status_set, 
							notes, 
							did_limit,
							did_limit_per_ip,							
							id, 
							name, 
							email, 
							user_last_login, 
							admin_flag, 
							site_country, 
							leads_tracking_menu, 
							show_wp, 
							show_acct_crdt, 
							user_type, 
							company, 
							address1, 
							address2, 
							postal_code, 
							user_city, 
							user_state, 
							user_country,
							allow_over_4_credits 
						from user_logins 
						where id=".mysql_real_escape_string($id);
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				$row = mysql_fetch_assoc($result);
				return $row;
			}else{
				return 0;
			}
		}else{
			return "Error: An error occurred while getting user information from id $id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
			return false;
		}
		
	}
	
	function updateLoginAttempt($userID){
		
		$query = "update user_logins set login_attempt_count= login_attempt_count+1 WHERE id = " . $userID ;
		mysql_query($query);
		
	}
	
	function updateLastLogin($email,$site_country){
		
		$query = "update user_logins set user_last_login=CURRENT_DATE where site_country='".mysql_real_escape_string($site_country)."' and email='".mysql_real_escape_string($email)."'";
		mysql_query($query);
		
	}
	
	function updatePassword($email,$password,$site_country){
		
		$query = "update user_logins set password='".md5($password)."', last_changed_state=NOW(),password_ip_address='".mysql_real_escape_string($_SERVER["REMOTE_ADDR"])."',password_reset_date=CURRENT_DATE where site_country='".mysql_real_escape_string($site_country)."' and email='".mysql_real_escape_string($email)."'";
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return false;
		}
		
	}
	
	function saveUser($site_country){
        $query = "update user_logins set name='".mysql_real_escape_string($this->name)."', email='".mysql_real_escape_string($this->email)."', last_changed_state=NOW() where site_country='".mysql_real_escape_string($site_country)."' and email='".mysql_real_escape_string($_SESSION['user_login'])."'";
		mysql_query($query);
		if(mysql_error()==""){
                    return true;
		}else{
                    return false;
		}
		
	}
	
	function updateUserAccount($site_country){
		
		//First check if the email id is being updated
		$existing_email = "";
		$blnEmailUpdated = false;
		$query = "select email from user_logins where id=".mysql_real_escape_string($_SESSION['user_logins_id']);
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				$row = mysql_fetch_assoc($result);
				$existing_email = trim($row["email"]);
			}else{
				return "Error: No user id ".$_SESSION['user_logins_id']." record found.";
			}
		}else{
			return "Error: ".mysql_error()." Query is ".$query;
		}
		if($existing_email!=trim($this->email)){
			$blnEmailUpdated = true;
			//Check if the new email is valid and is not already taken
			$email_exists = $this->checkEmailExists($this->email,$site_country);
			if($email_exists===true){
				return "Error: Email ID entered by you already exists.";
			}
		}
		
		$query = "update user_logins set 
							name='".mysql_real_escape_string($this->name)."', 
							email='".mysql_real_escape_string($this->email)."', 
							password='".mysql_real_escape_string(md5($this->password))."',
							address1='".mysql_real_escape_string($this->address1)."',
							address2='".mysql_real_escape_string($this->address2)."',
							user_city='".mysql_real_escape_string($this->user_city)."',
							user_state='".mysql_real_escape_string($this->user_state)."',
							user_country='".mysql_real_escape_string($this->user_country)."',
							postal_code='".mysql_real_escape_string($this->postal_code)."'
						where id=".mysql_real_escape_string($_SESSION['user_logins_id']);
		mysql_query($query);
		if(mysql_error()==""){
			$_SESSION['user_name'] = $this->name;
			if($blnEmailUpdated==true){
				//Need to update the session variable here
				$_SESSION['user_login'] = trim($this->email);
			}
        	return true;
		}else{
        	return "Error: ".mysql_error(). " Query is : ".$query;
		}
		
	}
	
	function sign_ups_today($site_country){
		
		$day_start = date('Y-m-d 00:00:00');
		
		$query = "select count(*) as sign_ups_count from user_logins where site_country='$site_country' and user_created_on >='$day_start'";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['sign_ups_count'];
		
	}
	function saveUserNotes(){
		
		$query = "update user_logins set notes='".mysql_real_escape_string($this->notes)."', last_changed_state=NOW() where id=$this->id";
		
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return false;
		}
		
	}
	
	function saveUserStatus(){
		
		$query = "update user_logins 
					set status='".mysql_real_escape_string($this->status)."', 
					date_status_set=NOW(), 
					did_limit=".mysql_real_escape_string($this->did_limit).",
					did_limit_per_ip=".mysql_real_escape_string($this->did_limit_per_ip).",					
					max_30days_acct_credit=".mysql_real_escape_string($this->max_30days_acct_credit).", 
					leads_tracking_menu='".mysql_real_escape_string($this->leads_tracking_menu)."', 
					show_wp='".mysql_real_escape_string($this->show_wp)."',
					allow_over_4_credits='".mysql_real_escape_string($this->allow_over_4_credits)."',
					show_acct_crdt='".mysql_real_escape_string($this->show_acct_crdt)."' ";
					if( strtolower( $this->status ) != 'loginrestricted'){
						$query .= " , login_attempt_count = 0 " ;
					}
					
		$query .=	" where id=".mysql_real_escape_string($this->id);
		
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return false;
		}
		
	}
	
	function suspendUser($user_id, $status='Suspended'){
		
		
		$query = "update user_logins set status='".$status."', date_status_set=NOW() where id=$user_id ";
		
		
		mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return false;
		}
		
	}
	
	function getUserAddress($user_id){
		
		$query = "Select card_declined_attempts, max_30days_acct_credit, auto_refill, auto_refill_amount, show_wp, address1, address2, postal_code, user_city, user_state, user_country, card_declined_attempts from user_logins where id=$user_id";
	
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				$row = mysql_fetch_assoc($result);
				return $row;
			}else{
				return 0;
			}
		}else{
			return "Error: An error occurred while getting user address.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_show_wp($user_id){
		
		$query = "Select show_wp from user_logins where id=$user_id";
	
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				$row = mysql_fetch_assoc($result);
				return $row["show_wp"];
			}else{
				return 0;
			}
		}else{
			return "Error: An error occurred while getting user show_wp option.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_show_acct_crdt($user_id){
		
		$query = "Select show_acct_crdt from user_logins where id=$user_id";
	
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)!=0){
				$row = mysql_fetch_assoc($result);
				return $row["show_acct_crdt"];
			}else{
				return 0;
			}
		}else{
			return "Error: An error occurred while getting user show_acct_crdt option.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function increaseCardDeclinedAttempts($user_id){
		
		$query = "update user_logins set card_declined_attempts = card_declined_attempts + 1 where id=$user_id";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			//Now check number of declined attempts and send e-mail out
			$num_declined = $this->getCardDeclinedAttempts($user_id);
			if($num_declined >= 3){
				//Send email out
				$member_id = intval($user_id)+10000;
				mail(ACCOUNT_CHANGE_EMAIL,"Card of M".$member_id." Declined over 3 times","Credit Card Payment attempts of M$member_id was declined 3 or more times in a row.");
			}
			return true;
		}else{
			return "Error: An error occurred while increasing card declined attempts of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function resetCardDeclinedAttempts($user_id){
		
		$query = "update user_logins set card_declined_attempts = 0 where id=$user_id";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred while resetting card declined attempts of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function getCardDeclinedAttempts($user_id){
		
		$query = "select card_declined_attempts from user_logins where id=$user_id";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_assoc($result);
			return $row["card_declined_attempts"];
		}else{
			return "Error: An error occurred while getting card declined attempts of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_user_age_in_days($user_id){
		
		$query = "select DATEDIFF(now(), user_created_on) as days_old from user_logins where id=$user_id";
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_assoc($result);
			return $row["days_old"];
		}else{
			return "Error: An error occurred while getting age in days of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_user_site_country($user_id){
		
		$query = "select site_country from user_logins where id=$user_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_assoc($result);
			return $row["site_country"];
		}else{
			return "Error: An error occurred while getting site_country of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_user_site_currency($user_id){
		
		$query = "select site_country from user_logins where id=$user_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_assoc($result);
			switch($row["site_country"]){
				case "COM":
					return "AUD";
					break;
				case "AU":
					return "AUD";
					break;
				case "UK":
					return "GBP";
					break;
				default:
					return "Error: Unknown user site country : ".$row["site_country"];
					break;
			}
		}else{
			return "Error: An error occurred while getting user site currency of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_user_auto_refill($user_id){
		
		$query = "select auto_refill from user_logins where id=$user_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_assoc($result);
			return $row["auto_refill"];
		}else{
			return "Error: An error occurred while getting auto_refill of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_user_refill_amount($user_id){
		
		$query = "select auto_refill_amount from user_logins where id=$user_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_assoc($result);
			return $row["auto_refill_amount"];
		}else{
			return "Error: An error occurred while getting auto_refill_amount of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function update_auto_refill($user_id,$auto_refill,$refill_amount){
		
		$query = "update user_logins set auto_refill = '".mysql_real_escape_string($auto_refill)."', auto_refill_amount=".mysql_real_escape_string($refill_amount)." where id=".mysql_real_escape_string($user_id);
		
		$result = mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred while updating auto refill settings of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_user_total_credits($user_id){
		
		$query = "select total_credits from user_logins where id=$user_id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_assoc($result);
			return $row["total_credits"];
		}else{
			return "Error: An error occurred while getting total_credits of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function get_max_30days_credit($user_id, $is_array="N"){
		
		$query = "select max_30days_acct_credit, allow_over_4_credits from user_logins where id=".mysql_real_escape_string($user_id);
		$result = mysql_query($query);
		if(mysql_error()==""){
			$row = mysql_fetch_assoc($result);
			if($is_array == "Y"){
				return array($row["max_30days_acct_credit"], $row["allow_over_4_credits"]);
			}else{
				return $row["max_30days_acct_credit"];
			}
		}else{
			return "Error: An error occurred while getting max_30days_acct_credit of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function force_pwd_change($email,$site_country){
		
		$query = "select force_pwd_change from user_logins where email='".mysql_real_escape_string($email)."' and site_country='".mysql_real_escape_string($site_country)."'";
		$result = mysql_query($query);
		if(mysql_error()==""){
			if(mysql_num_rows($result)==1){
				$row = mysql_fetch_assoc($result);
				return $row["force_pwd_change"];
			}else{
				return "Error: Unexpected number of rows returned while checking for force password change indicator.";
			}
		}else{
			return "Error: An error occurred while getting force_pwd_change indicator of user email : $email.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function reset_password_updated_indicator($user_id){
		//force users to change their password
		
		$query = "update user_logins set force_pwd_change='N' where id=".mysql_real_escape_string($user_id);
		$result = mysql_query($query);
		if(mysql_error()==""){
			return true;
		}else{
			return "Error: An error occurred while updating force password reset indicator of user ID: $user_id.<br>
					Error is: ".mysql_error()."<br>
					Query is: ".$query."<br>Please try again or contact us if the problem persists.";
		}
		
	}
	
	function updateUserByUniqueCode($code, $action='verify'){
		if($action == 'verify'){
			$query = "SELECT verify_email, email_verify_required FROM user_logins WHERE uniquecode = '".$code."' ";
			$result = mysql_query($query);
			if(mysql_num_rows($result) > 0){
				//$user = mysql_fetch_array($result) ;
				$query = "UPDATE user_logins SET verify_email = 'Y' WHERE uniquecode = '".$code."' ";
				$result = mysql_query($query);
				return 1;
			}else{
				return false;
			}
			
		}else if($action == 'cancel'){
			$query = "SELECT id, status, site_country FROM user_logins WHERE uniquecode = '".$code."' ";
			$result = mysql_query($query);
			if( mysql_num_rows() > 0 ){
				$user = mysql_fetch_array($result) ;
				return array($user['id']);
			}else{
				return false;
			}
		}
		
		return false;
	}
	
}
?>