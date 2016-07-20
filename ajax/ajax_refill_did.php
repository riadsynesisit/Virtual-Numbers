<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Assigned_DID.class.php");
    
    $user_id = $_SESSION['user_logins_id'];
    
    //Input Validation
     $did_number = "";
    if(isset($_POST["did_number"])==false || trim($_POST["did_number"])==""){
    	echo "Error: Required parameter did_number not passed.";
    	exit;
    }else{
    	$did_number = trim($_POST["did_number"]);
    }
    
    $assigned_dids = new Assigned_DID();
    if(is_object($assigned_dids)==false){
    	echo "Error: Failed to create Assigned_DID object.";
    	exit;
    }
    //Do the refill
    $status = $assigned_dids->refill_did($user_id,$did_number);
    
    if(is_bool($status)==false && substr($status,0,6)=="Error:"){
    	echo $status;
    }
    if($status==true){
    	echo "Success";
    }
    exit;
    