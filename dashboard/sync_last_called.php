<?php
require("./includes/config.inc.php");
require("./includes/db.inc.php");

ob_implicit_flush(true);

echo "<br><br>".date(DATE_RSS)."\n\n";
set_time_limit(0);

$query = "select id, user_id, did_number from assigned_dids;";
$result = mysql_query($query);

if(mysql_error()!=""){
        echo "<br>An error occurred. \n". mysql_error();
        exit;
}

$loop_count=0;

while($row=mysql_fetch_array($result)){
	$loop_count = $loop_count + 1;
	echo "<br>$loop_count. Processing ".$row['did_number']." of user :".$row['user_id'];
	$cdr_query = "select ifnull(max(calldate),0) as maxcalldate from cdr where accountcode='".$row['did_number']."' and userfield='".$row['user_id']."';";
	$cdr_result = mysql_query($cdr_query);
	$cdr_row = mysql_fetch_array($cdr_result);
	if($cdr_row['maxcalldate']!=0){	
		$update_query = "update assigned_dids set route_last_used='".$cdr_row['maxcalldate']."' where did_number='".$row['did_number']."' and user_id='".$row['user_id']."';";
		mysql_query($update_query);
		if(mysql_error()!=""){
	        echo "<br>An error occurred while updating assinged_dids.route_last_used.<br>". mysql_error();
	        exit;
		}
		echo " Last Called on: ".$cdr_row['maxcalldate'];
	}else{
		echo " No record found in CDR table";
	}
}

echo "<br><br>".date(DATE_RSS)."\n\n";

echo "<br><br>Process completed!";