<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/User.class.php");
    
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id
    
    $auto_refill = "";
    if(isset($_POST["auto_refill"])==false || trim($_POST["auto_refill"])==""){
    	echo "Error: Required parameter auto_refill not passed.";
    	exit;
    }else{
    	$auto_refill = trim($_POST["auto_refill"]);
    }
    
    $refill_amount = "";
    if(isset($_POST["auto_refill_amount"])==false || trim($_POST["auto_refill_amount"])==""){
    	echo "Error: Required parameter auto_refill_amount not passed.";
    	exit;
    }else{
    	$refill_amount = trim($_POST["auto_refill_amount"]);
    }
    
    $user = new User();
    $status = $user->update_auto_refill($user_id,$auto_refill,$refill_amount);
    if(is_bool($status) && $status === true){
    	echo "Success";
    }else{
    	echo $status;
    }
    exit;
    