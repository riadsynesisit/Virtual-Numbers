<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Member_credits.class.php");
    
    if(isset($_SESSION['user_logins_id'])==false || trim($_SESSION['user_logins_id'])==""){
    	echo "Error: No user session found. Please login.";
    	exit;
    }
    
    $member_credits = new Member_credits();
    if(is_object($member_credits)==false){
    	echo "Error: Failed to create member_credits object.";
    	exit;
    }
    $account_credit = $member_credits->getTotalCredits($_SESSION['user_logins_id']);
    
    echo "Success,".$account_credit;