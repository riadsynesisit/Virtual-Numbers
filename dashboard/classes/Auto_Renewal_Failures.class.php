<?php

class Auto_Renewal_Failures{
	
	function insert_auto_renewal_failure($cart_id,$error_message,$notes, $script_name="", $failure_reason=""){
		
		$query = "insert into auto_renewal_failures(cart_id, 
													date_failed, 
													error_message, 
													notes, 
													status,
													failure_reason,
													php_script_name
													
												)values(
													$cart_id, 
													NOW(), 
													'".mysql_real_escape_string($error_message)."', 
													'".mysql_real_escape_string($notes)."', 
													'new',
													'".mysql_real_escape_string($failure_reason)."',
													'".mysql_real_escape_string($script_name)."'
												)";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: Failed to insert record in auto renewal failures. Error is: ".mysql_error()." Query is: ".$query;
		}
		return true;
	}
	
	function getMonthlyFailures($month_num, $year){
		
		$query = "select concat('M',sc.user_id+10000) as member_id, 
							ad.did_number as did_number,
							date_format(arf.date_failed,'%d %b %Y %H:%i:%s') as date_failed, 
							sc.pymt_gateway as pymt_gateway, 
							arf.error_message as error_message
						from auto_renewal_failures arf, 
							shopping_cart sc, 
							assigned_dids ad
						where arf.cart_id=sc.id and 
							sc.did_id=ad.id and
							SUBSTRING(ad.did_number,1,3)!='087' and 
							SUBSTRING(ad.did_number,1,3)!='070' and 
							month(arf.date_failed)=".mysql_real_escape_string($month_num)." and  
							year(arf.date_failed)=".mysql_real_escape_string($year)." 
							order by arf.date_failed desc";
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
	
	function getRenewalFailuresByYear($count = false, $year = "", $failures = "DIDWW", $start_rec=0,$per_page=RECORDS_PER_PAGE){
		
		if(!$year){
			$year = date("Y");
		}
		$fail_conditions = "" ;
		if($failures != ""){
			$fail_conditions = " arf.failure_reason LIKE '%". $failures . "%' AND ";
		}
		if($count){
			$sql = "select count(id) as rows from auto_renewal_failures arf where `failure_reason` LIKE '%".$failures . "%' AND  year(arf.date_failed)='".$year."' ";
		}else{
			$sql = "select arf.id as id, concat('M',sc.user_id+10000) as member_id, sc.did_number as did_number,date_format(arf.date_failed,'%d %b %Y %H:%i:%s') as date_failed, sc.pymt_gateway as pymt_gateway, arf.error_message as error_message, arf.notes, arf.failure_reason from auto_renewal_failures arf left join shopping_cart sc ON arf.cart_id=sc.id where " . $fail_conditions ." year(arf.date_failed)='".$year."' AND is_visible = 'Y' order by arf.date_failed desc limit $start_rec, $per_page " ;
		}
		$result = mysql_query($sql);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting errors DIds of month $month_num/$year. Error is: ".mysql_error();
			}
			if($count){
				$row = mysql_fetch_assoc($result);
				return $row["rows"];
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
	
	function updateFailureVisible($id , $is_visible="N"){
		if($id){
			$sql = "update auto_renewal_failures set is_visible='".$is_visible."' where id = " . $id;
			$result = mysql_query($sql);
			if(mysql_error()!=""){
				return "Error: An error occurred while updating record. Error is: ".mysql_error();
			}else{
				return 'success';
			}
		}else{
			return "Error: id not passed";
		}
	}
	
	
}