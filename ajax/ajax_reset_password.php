<?php

	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
	require("../dashboard/classes/User.class.php");
	require("../dashboard/classes/Client_notifications.class.php");
	require("../dashboard/libs/user_maintenance.lib.php");
	require("../dashboard/libs/jkl5_common.php");
	require("../dashboard/libs/utilities.lib.php");
	
	if(isset($_POST["email"])==false || trim($_POST["email"])==""){
		echo "Error: Required parameter emailid not passed. Please pass email id.";
		exit;
	}
	
	$emailid = trim($_POST["email"]);

	if($emailid==""){
		//No e-mail id provided
		echo "Error: Required parameter emailid not entered. Please enter email id.";
		exit;
	}
	
	//Check if the email is valid email id
	if(filter_var($emailid,FILTER_VALIDATE_EMAIL)==false){
		echo "Error: Invalid email id passed.";
		exit;
	}

	//Check if this e-mail exists in the database
	$user = new User();
	if(is_object($user)==false){
		echo "Error: Failed to created User object.";
		exit;
	}
	
	$status=$user->getUserInfo($emailid ,SITE_COUNTRY);
	
	if(is_array($status)==false && $status==0){
		echo "Error: No user found with the email id you entered.";
		exit;
	}else if(is_array($status)==true && $status[0]=='Terminated') {
		echo "Error: This account is terminated. Please contact us for support.";
		exit;
	}else if(is_array($status)==true && $status[0]=='Suspended'){
		echo "Error: This account is suspended. Please contact us for help.";
		exit;
	}else if($user->checkEmailExists($emailid, SITE_COUNTRY)==true){
		//Generate a random password
		$pass = generatePassword();
		//Update the users password
		if($user->updatePassword($emailid, $pass, SITE_COUNTRY)){
			//Get users name
			$user_info = $user->getUserInfo($emailid, SITE_COUNTRY);
			//Send e-mail to the user
			$status = account_change_mailer($user_info["id"], $emailid, $user_info["name"], $pass, $reason_code="update");
			if($status===true){
				echo "Success: Your password was reset and an email sent to you with the new password.";
				//Updte the force password reset indicator
				$status = $user->reset_password_updated_indicator($user_info["id"]);
				exit;
			}else{
				echo $status;
				exit;	
			}
		}else{
			//Password update in database failed
			echo "Error: A database error occurred while updating your password. Please try again or contact us for help.";
			exit;
		}
	}else{
		//Email id not found
		echo "Error: Email id entered by you was not found.";
		exit;
	}

?>