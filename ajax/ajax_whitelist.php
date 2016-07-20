<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Block_Destination.class.php");
    require("../dashboard/classes/Blacklist_ip.class.php");
    
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id
    
    if($_SESSION['admin_flag']!=1 && $_SESSION['admin_flag']!=2){
    	echo "Error: Unauthorised to access white listing page.";
    	exit;
    }
    
    $input_type = "";
    if(isset($_POST["input_type"])==false || trim($_POST["input_type"])==""){
    	echo "Error: Required parameter input_type not passed.";
    	exit;
    }else{
    	$input_type = trim($_POST["input_type"]);
    }
    
    $input_value = "";
    if(isset($_POST["input_value"])==false || trim($_POST["input_value"])==""){
    	echo "Error: Required parameter input_value not passed.";
    	exit;
    }else{
    	$input_value = trim($_POST["input_value"]);
    }
    
    switch($input_type){
    	case "dest_number":
    		$blocked_destination = new Block_Destination();
    		if(is_object($blocked_destination)==false){
    			echo "Error: Unable to create Block_Destination object.";
    			exit;
    		}
    		$status = $blocked_destination->remove_blocked_destination($input_value);
    		if($status!==true && substr($status,0,6)=="Error:"){
    			Echo $status;
    			exit;
    		}
    		break;
    	case "ip_address":
    		$blacklist_ip = new Blacklist_ip();
    		if(is_object($blacklist_ip)==false){
    			echo "Error: Unable to create Blacklist_ip object.";
    			exit;
    		}
    		$status = $blacklist_ip->white_list_ip($input_value,$user_id);
    		if($status!==true && substr($status,0,6)=="Error:"){
    			Echo $status;
    			exit;
    		}
    		break;
    	default :
    		echo "Error: Unknown input_type $input_type passed.";
    		exit;
    }
    
    echo "Success";
    exit;