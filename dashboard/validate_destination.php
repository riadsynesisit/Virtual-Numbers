<?php
require("./includes/DRY_setup.inc.php");
if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
	//continue;
}else{
	die("Error: No valid login found.");
}

$conn = mysql_connect(SQLHOST, SQLUSER, SQLPASS) or die("Database Connection Error!! Cannot connect to ".SQLHOST);
mysql_select_db(SQLDB, $conn) or die("Database ".SQLDB." not found!!");

$destination = $_GET["dest"];

$block_destination = new Block_Destination();
if(!is_object($block_destination)){
	die("Error: Failed to create Block_Destinaion object.");
}
$dest_blocked = $block_destination->isDestinationBlocked($destination);

if($dest_blocked==true){
	echo "Your destination is detected as fraud, <a href='mailto:support@stickynumber.com'>click here</a> to ask for an exception.";
}else{
	$unassigned_087did = new Unassigned_087DID();
	if(!is_object($unassigned_087did)){
		die("Error: Failed to create Unassigned_087DID object.");
	}
	$call_cost_ok = $unassigned_087did->isCallCostOK($destination);
	if($call_cost_ok==true){
		echo "valid";
	}else{
		echo "Your destination is too expensive and not currently allowed.";
	}
}