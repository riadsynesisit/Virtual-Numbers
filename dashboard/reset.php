<?php

require("./includes/config.inc.php");
require("./includes/db.inc.php");

require("./classes/User.class.php");

require("./libs/jkl5_common.php");
require("./libs/user_maintenance.lib.php");
require("./libs/utilities.lib.php");
session_start();//Use sessions
$valid=false;
$emailid = trim($_GET["email_address"]);
 if($_SESSION["recaptcha_validate_forgot"]=="true")
 {

  require_once "./includes/recaptchalib.php";
  $privatekey = "6Lffs8kSAAAAAHVlDL6JDqLpL8oCMUpcHhNKGUls";
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_GET["recaptcha_challenge_field"],
                                $_GET["recaptcha_response_field"]);

  if (!$resp->is_valid) {
      echo -6;
  }
  else
  {
  
    $valid=true;
  
  
  }
  }
  else
  {
  
	$valid=true;
  
  }
  
  

if($valid==true)
  {
if($emailid==""){
	//No e-mail id provided
	echo -1;
	exit;
}

//Check if this e-mail exists in the database
$user = new User();
$status=$user->getUserInfo($emailid ,SITE_COUNTRY);
if($status==0)
{
echo -2;

}
else if($status[0]=='Terminated') {

echo -4;

}
else if($status[0]=='Suspended')
{
echo -5;

}
else if($user->checkEmailExists($emailid, SITE_COUNTRY)==true){
	//Generate a random password
	$pass = generatePassword();
	//Update the users password
	if($user->updatePassword($emailid, $pass, SITE_COUNTRY)){
		//Get users name
		$user_info = $user->getUserInfo($emailid, SITE_COUNTRY);
		//Send e-mail to the user
		account_change_mailer($user_info["id"], $emailid, $user_info["name"], $pass, $reason_code="update");
		echo 1;//Success
	}else{
		//Password update in database failed
		echo -3;
	}
}else{
	//Email id not found
	echo -2;
}
}
?>