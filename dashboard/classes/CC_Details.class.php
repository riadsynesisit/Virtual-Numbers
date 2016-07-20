<?php
class CC_Details{
	
	function get_cc_count($user_id){
		
		$query = "select count(*) as cc_count from cc_details where user_id=$user_id";
		
		$result = mysql_query($query);
        
        if(mysql_error()==""){
	        if(mysql_num_rows($result)!=0){
	        	$row = mysql_fetch_array($result);
	        	return $row["cc_count"];
	        }else{
	        	return 0;
	        }
        }else{
        	die("An error occured while getting number of credit cards from pbx_system_db.cc_details table: ".mysql_error());
        }
		
	}
	
	function for_page_range($user_id, $limit, $offset){
		
			$retArr =   array();
            $query  =   "SELECT * FROM cc_details WHERE user_id = '$user_id' ORDER BY date_added DESC LIMIT $limit OFFSET $offset;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
                while($row  =   mysql_fetch_assoc($result))
                {
                    $retArr[]   =   $row;
                }
                return $retArr;
            }else{
                return 0;
            }
		
	}
	
	function addCCDetails($user_id,
							$card_logo,
							$card_type,
							$card_number,
							$exp_month,
							$exp_year,
							$security_code,
							$street,
							$city,
							$state,
							$country,
							$postal_code,
							$is_primary){
		
		//If making this as primary and other credit cards exist
		if($is_primary == 1 && $this->get_cc_count($user_id)!=0){//No existing credit cards
			//Must make the existing Credit Cards as NON-primary since there can be ONLY ONE PRIMARY per user
			$query = "update cc_details set is_primary=0 where user_id=$user_id";
			mysql_query($query);
			if(mysql_error()!=""){
	        	die("An error occured while making existing credit cards as NON-PRIMARY: ".mysql_error());
	        }
		}
								
		$query = "insert into cc_details(
											user_id,
											card_logo,
											card_type,
											card_number,
											exp_month,
											exp_year,
											security_code,
											street,
											city,
											state,
											country,
											postal_code,
											is_primary,
											date_updated
										)values(
											$user_id,
											'$card_logo',
											'$card_type',
											$card_number,
											$exp_month,
											$exp_year,
											$security_code,
											'$street',
											'$city',
											'$state',
											'$country',
											'$postal_code',
											$is_primary,
											now()
										)";
		mysql_query($query);
		if(mysql_error()==""){
	        return true;
        }else{
        	die("An error occured while adding credit card details: ".mysql_error());
        }
	}
	
	function updateCCDetails($id,
							$user_id,
							$card_logo,
							$card_type,
							$card_number,
							$exp_month,
							$exp_year,
							$security_code,
							$street,
							$city,
							$state,
							$country,
							$postal_code,
							$is_primary){
								
		//There can be only one primary Credit Card per user
		if($is_primary==1){//Before setting this as primary make all existing as NON-primary first except the back-up (for back-up is_primary=2)
			//If they are PROMOTING an existing back-up to a primary then the existing primary should become the back-up
			$primary_or_backup = $this->getPrimaryBackupStatus($id);
			if($primary_or_backup==2){
				$query = "update cc_details set is_primary=2 where user_id=$user_id and is_primary=1";
				mysql_query($query);
				if(mysql_error()!=""){
					die("An error occurred while setting the existing primary as back-up. ".mysql_error());
				}
			}
			$query = "update cc_details set is_primary=0 where user_id=$user_id and is_primary!=2";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occurred while setting all existing credit cards as NON-primary. ".mysql_error());
			}
		}elseif($is_primary==2){//there can be only one back-up - before making this as back-up ensure that all others, except the primary, are zero
			$query = "update cc_details set is_primary=0 where user_id=$user_id and is_primary!=1";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occurred while setting all existing credit cards as NON-back-up. ".mysql_error());
			}
		}
								
		$query = "update cc_details set 
							card_logo='$card_logo', 
							card_type='$card_type',
							card_number=$card_number,
							exp_month=$exp_month,
							exp_year=$exp_year,
							security_code=$security_code,
							street='$street',
							city='$city',
							state='$state',
							country='$country',
							postal_code='$postal_code',
							is_primary=$is_primary,
							date_updated=now()
							where user_id=$user_id and id=$id";
							mysql_query($query);
		if(mysql_error()==""){
	        return true;
        }else{
        	die("An error occured while updating credit card detailss in pbx_system_db.cc_details table: ".mysql_error());
        }
		
	}
	
	function deleteCCDetails($id,$user_id){
		
		$query = "delete from cc_details where id=$id and user_id=$user_id";
		mysql_query($query);
		if(mysql_error()==""){
	        return true;
        }else{
        	die("An error occured while deleting credit card detailss in pbx_system_db.cc_details table: ".mysql_error());
        }
		
	}
	
	function getCCDetails($id,$user_id){
		
		//This function MUST have the user_id parameter so that they get the CC Details IF and ONLY IF it belongs to them
		//as a measure of leakage of data - fraud prevention.
		$query = "select * from cc_details where id=$id and user_id=$user_id";

		$result = mysql_query($query);
        
        if(mysql_error()==""){
	        if(mysql_num_rows($result)!=0){
	        	$row = mysql_fetch_array($result);
	        	return $row;
	        }else{
	        	return "Error: No cc_details record exists for id $id and user combination.";
	        }
        }else{
        	return "Error: An error occured while getting number of credit cards from pbx_system_db.cc_details table: ".mysql_error();
        }
		
	}
	
	function isExistingCC($cc_num,$user_id){
		
		$query = "select * from cc_details where card_number=$cc_num and user_id=$user_id";
		
		$result = mysql_query($query);
        
        if(mysql_error()==""){
	        if(mysql_num_rows($result)!=0){
	        	return true;
	        }else{
	        	return false;
	        }
        }else{
        	die("An error occured while checking if card number exists: ".mysql_error());
        }
		
	}
	
	function makePrimaryCC($cc_details_id,$user_id){
		
		//If they are PROMOTING an existing back-up to a primary then the existing primary should become the back-up
		$primary_or_backup = $this->getPrimaryBackupStatus($cc_details_id);
		if($primary_or_backup==2){
			$query = "update cc_details set is_primary=2 where user_id=$user_id and is_primary=1";
			mysql_query($query);
			if(mysql_error()!=""){
				die("An error occurred while setting the existing primary as back-up. ".mysql_error());
			}
		}
		
		//There can be only one primary credit card per user
		//So make existings CCs as NON-primary first except the back up card (For back-up is_primary=2
		$query = "update cc_details set is_primary=0 where user_id=$user_id and is_primary!=2";
		mysql_query($query);
		if(mysql_error()==""){
	        //Now set this specific Credit Card as Primary
	        $query = "update cc_details set is_primary=1 where user_id=$user_id and id=$cc_details_id";
	        mysql_query($query);
	        if(mysql_error()==""){
	        	return true;
	        }else{
	        	die("An error occurred while setting the credit card as primary.");
	        }
        }else{
        	die("An error occured while making all credits cards of this user, except the back-up, as NON-primary. ".mysql_error());
        }
		
	}
	
	function makeBackupCC($cc_details_id,$user_id){
		
		//There can be only one back-up credit card per user
		//So make existings CCs, all except the primary, as NON-backup first
		$query = "update cc_details set is_primary=0 where user_id=$user_id and is_primary!=1";
		mysql_query($query);
		if(mysql_error()==""){
	        //Now set this specific Credit Card as Back-up
	        $query = "update cc_details set is_primary=2 where user_id=$user_id and id=$cc_details_id";
	        mysql_query($query);
	        if(mysql_error()==""){
	        	return true;
	        }else{
	        	die("An error occurred while setting the credit card as back-up.");
	        }
        }else{
        	die("An error occured while making all credits cards, except the primary, of this user as NON-back-up. ".mysql_error());
        }
		
	}
	
	function removePrimaryBackupCC($cc_details_id,$user_id){
		
		$query = "update cc_details set is_primary=0 where id=$cc_details_id and user_id=$user_id";
		mysql_query($query);
		if(mysql_error()==""){
	        return true;
        }else{
        	die("An error occured while making all credits cards, except the primary, of this user as NON-back-up. ".mysql_error());
        }
		
	}
	
	function getCCDetailsOfUser($user_id){//Returns the list of all CCs of the user which haven't expired
		
		$query = "select * from cc_details 
					where user_id=$user_id and 
						exp_year >= year(curdate()) and
						if(exp_year = year(curdate()),if(exp_month >= month(curdate()),1,0),1) = 1";
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
	
	function getCCLastFourOfUser($user_id){//Returns the list of all CCs of the user which haven't expired
		
		$query = "select id, concat('xxxx xxxx ', SUBSTRING(card_number,-4),' ',card_logo) as card_number from cc_details 
					where user_id=$user_id and 
						exp_year >= year(curdate()) and
						if(exp_year = year(curdate()),if(exp_month >= month(curdate()),1,0),1) = 1";
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
	
	function getPrimaryBackupStatus($id){
		
		$query = "select is_primary from cc_details where id=$id";
		$result = mysql_query($query);
		if(mysql_error()==""){
			//is_primary : 0=None, 1=Primary and 2=Back up
			if(mysql_num_rows($result) != 0 ) {
				$row = mysql_fetch_array($result);
	        	return $row["is_primary"];
			}else{
				die("No rows found while getting Primary or Backup status of cc for id: ".$id);
			}
		}else{
			die("Error: An error occurred while getting the primary or backup status of CC. Error: ".mysql_error());
		}
		
	}
	
}