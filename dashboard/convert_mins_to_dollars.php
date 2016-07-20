<?php

require("/var/www/html/stickynumber/dashboard/includes/config.inc.php");
require("/var/www/html/stickynumber/dashboard/includes/db.inc.php");
require("/var/www/html/stickynumber/dashboard/classes/Unassigned_087DID.class.php");
require("/var/www/html/stickynumber/dashboard/classes/SystemConfigOptions.class.php");

$query = "select ad.id as id, 
				ad.did_number as did_number, 
				ad.route_number as route_number, 
				ad.total_minutes as total_minutes 
			from assigned_dids ad 
			where ad.forwarding_type=1 and 
				ad.total_minutes>0 and 
				ad.did_expires_on>now() and 
				ad.balance=0 and 
				ad.did_number not like '070%' and 
				ad.did_number not like '087%' and 
				ad.did_number not like '4470%' and 
				ad.did_number not like '4487%'";
$result = mysql_query($query);
if(mysql_error()!=""){
	echo "<br />Error: An error occurred while getting balane update records. ".mysql_error(). "<br />Query is: ".$query;
	exit;
}
if(mysql_num_rows($result)==0){
	echo "<br />No records found to be balance updated.";
	exit;
}
echo "<br />Number of records to be updated is: ".mysql_num_rows($result);

$unassigned_dids = new Unassigned_087DID();
if(is_object($unassigned_dids)==false){
	echo "<br />Error: Failed to created Unassigned_087DID object.";
	exit;
}

$counter = 0;
$success_counter = 0;
while($row = mysql_fetch_assoc($result)){
	$counter = $counter + 1;
	$fwd_number = $row["route_number"];
	if(substr($fwd_number,0,1)=="+"){//strip the leading + sign if any
		$fwd_number = substr($fwd_number,1);
	}
	
	echo "<br />Processing Record No: ".$counter. " of forwarding number: ".$fwd_number;
	$call_cost = $unassigned_dids->callCost($fwd_number,"peak",true);
	if($call_cost===false){
		echo "<br />Error: Failed to get marked up call cost of ".$fwd_number;
		continue;//Try the next record
	}
	if(doubleval($call_cost)==0){
		echo "<br />Notice: Call cost is zero for ".$fwd_number;
		continue;//Try the next record
	}
	$balance = $call_cost*doubleval($row["total_minutes"]);
	echo "<br />Notice: Balance of ".$row["did_number"]. " (DID_ID:".$row["id"].") is :".$balance." Call Cost=".$call_cost.", Total Minutes=".$row["total_minutes"];
	if($balance==0){
		echo "<br />Notice: Balance is zero for ".$row["did_number"];
		continue;//Try the next record
	}
	$update_query = "update assigned_dids set balance=$balance where balance=0 and id=".$row["id"];
	mysql_query($update_query);
	if(mysql_error()!=""){
		echo "<br />Error: An error occurred while updating balance. ".mysql_error(). "<br />Query is: ".$update_query;
		continue;//Try the next record
	}
	if(mysql_affected_rows()==0){
		echo "<br />Error: No rows affected while updating balance. ".mysql_error(). "<br />Query is: ".$update_query;
		continue;//Try the next record
	}
	echo "<br />SUCCESS: Balance successfully updated.";
	$success_counter = $success_counter + 1;
}//End of while loop

echo "<br />Process completed. Total number of records successfully updated is : ".$success_counter;