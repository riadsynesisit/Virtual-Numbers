<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/CC_Details.class.php");

    if(isset($_SESSION['user_logins_id']) == false || trim($_SESSION['user_logins_id'])==""){
    	echo "Error: No user login session found.";
    	exit;
    }

    $cc_details = new CC_Details();

    $cc_details_array = $cc_details->getCCLastFourOfUser(trim($_SESSION['user_logins_id']));

    if($cc_details_array!=false){
    	echo json_encode($cc_details_array);
    }else{
    	echo "";
    }