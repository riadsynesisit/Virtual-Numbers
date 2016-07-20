<?php

class Did_Plans{
	
	function getDIDPlans(){
		
		$query = "select * from did_plans order by id asc";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while querying for available DID Plans. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No plans found in DID Plans.");
		}
		if(mysql_num_rows($result)<3){
			die("Error: Insufficient plans found in DID Plans.");
		}
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        	$retArr[] = $row;
        }
        return $retArr;
	}
	
}