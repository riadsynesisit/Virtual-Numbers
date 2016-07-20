
<?php
require "./includes/DRY_setup.inc.php";
  //session_name("admin");
  //session_start();//Use sessions
  $valid=false;
 if($_SESSION["recaptcha_validate"]=="true")
 {

  require_once "./includes/recaptchalib.php";
  $privatekey = "6Lffs8kSAAAAAHVlDL6JDqLpL8oCMUpcHhNKGUls";
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
	$realname = $_POST['realname'];
	$email = $_POST['email'];
	$recmnd_frnd_option = $_POST['recmnd_frnd_option'];
	
	if($recmnd_frnd_option == 'yes')
	{
		$recmnd_frnd_email = $_POST['recmnd_frnd_email'];
		header('Location:'.SERVER_WEB_NAME.'/index.php?message=Security code is not correct!&realname='.$realname.'&email='.$email.'&recmnd_frnd_email='.$recmnd_frnd_email.'&recmnd_frnd_option='.$recmnd_frnd_option);
	}
	else
	{
	    header('Location:'.SERVER_WEB_NAME.'/index.php?message=Security code is not correct!&realname='.$realname.'&email='.$email.'&recmnd_frnd_option='.$recmnd_frnd_option);
	}
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
    // Your code here to handle a successful verification
 




//require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	


	
	if(isset($_POST['form_sign_up_ok'])){
		process_setup_attempt($this_page);
	}else{
		show_setup_page($this_page);
	}
	
}

function build_page_content($this_page){
	


	
	$this_page->content_area = getSetupPage($this_page);
	common_header($this_page, "Setup Page - Creating your account");
	
}

function show_setup_page($this_page){
	


	
	build_page_content($this_page);
	
}

function process_setup_attempt($this_page){
	


	
	$errors = false;
	
	$front_page_setup = false;
	$secured_validate = false;
	
	if(isset($_POST['form_sign_up_ok']) && $_POST['form_sign_up_ok'] == '74285hda28'){
		$front_page_setup = true;
	}
	
	//Here we may need captcha validation to prevent scripts signing up!
	
	if($front_page_setup){
		$safe_name = trim($_POST['realname']);
	}else{
		$safe_name = trim($_POST['name']);
	}
	
	$action_message = "Setting up your account for you... please wait a moment, $safe_name<br>\n";
	
	
	
	
	
	if($safe_name=="" || trim($_POST['email'])=="" || trim($_POST['password'])==""){
		$action_message .= "<h1>Error!</h1>\n";
		$action_message .= "<p>All fields are required. Please enter all of these: Name, E-mail and Password.\n";
		$errors = true;
		$custom_error_message='All fields are required. Please enter all of these: Name, E-mail and Password.';
	}else{
		$new_user = new User();
		
		//Check if the email id already registered
		if($new_user->checkEmailExists(trim($_POST['email']),$_POST['site_country'])==true){
			$custom_error_message='Email Id Already Exists Please Enter Another Email.';
			$errors = true;
		}else{

			//Step 1 - Save the information in database
			
			if($_POST['recmnd_frnd_email'] != '')
			{
				if($_POST['recmnd_frnd_option'] == 'yes')
				{
					$userid = $new_user->insertUser_frnd($safe_name,trim($_POST['email']),trim($_POST['password']),$_POST['recmnd_frnd_email'],$_POST['site_country']);
				}
			}
			else
			{
				
				if($_POST['recmnd_frnd_option'] == 'no')
				{
					$userid = $new_user->insertUser($safe_name,trim($_POST['email']),trim($_POST['password']),$_POST['site_country']);
				}
			}
			if($userid==0){
				$custom_error_message='Insert Failed Please Try Again.';
				$errors = true;
			}else{
				// all is well, mail the user and then keep going to the wizard
				//Setp 2 - Send an E-mail to the registrant
				//if($_POST['site_country']!="AU"){
	                $uniquecode =   md5($_POST['email'].$_POST['password']);//This is the same code used while inserting user_logins record
	                account_verify_email(trim($_POST['email']),$uniquecode,$safe_name);
					account_change_mailer($userid, trim($_POST['email']), $safe_name, trim($_POST['password']));
					account_change_mailer_frnd($userid, trim($_POST['recmnd_frnd_email']), $safe_name, trim($_POST['password']));
					
					//update daily_system_stats.signup_emails table
					$daily_stats = new daily_stats();
					$daily_stats->updateSignupEmails(1);
					$daily_stats->updateConfirmEmails(1);
					if(isset($_POST['recmnd_frnd_email']) && trim($_POST['recmnd_frnd_email'])!=""){
						$daily_stats->updateFriendEmails(1);
					}
				//}
				
				//Setup sesssion for this user
				$_SESSION['user_logins_id'] = $userid;
				$_SESSION['user_name'] = $safe_name;
				$_SESSION['user_login'] = trim($_POST['email']);
				$_SESSION['recmnd_frnd_email'] = trim($_POST['recmnd_frnd_email']);
				$_SESSION['is_authenticated'] = true;
				$_SESSION['admin_flag'] = 0;
				// give the user some info about the fact that they are registered, and then take them to the login page
				/*$action_message .= "<h1>Success!</h1>\n"; 
				$action_message .= "<p>Your account set up with the ".APP_TITLE_LONG." service was successful.  We will now take you to the set-up wizard so you can get started!</p>\n"; 
				$action_message .= "<p>If for some reason you aren't taken to the set-up wizard page, just click <a href='add-number-step1.php'>this link</a> and we'll take you there.</P>\n"; 
				$this_page->content_area = $action_message;
				//Take them to the Wizard - page 1
				$meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS + 6;
				$meta_refresh_page = "add-number-step1.php";
				
				common_header($this_page, "Setup Page - Creating your account", false,$meta_refresh_sec,$meta_refresh_page);*/
				
				header("Location: add-number-1.php");
			}
		}//end of if email exists
	}
	
	if($errors){
		//Give the message and send them back to the sign up page
		if($front_page_setup){
	$realname = $_POST['realname'];
	$email = $_POST['email'];
	$recmnd_frnd_option = $_POST['recmnd_frnd_option'];
	
		if($recmnd_frnd_option == 'yes')
	{
		$recmnd_frnd_email = $_POST['recmnd_frnd_email'];
		header('Location:'.SERVER_WEB_NAME.'/index.php?message='.$custom_error_message.'&realname='.$realname.'&email='.$email.'&recmnd_frnd_email='.$recmnd_frnd_email.'&recmnd_frnd_option='.$recmnd_frnd_option);
	}
	else
	{
	    header('Location:'.SERVER_WEB_NAME.'/index.php?message='.$custom_error_message.'&realname='.$realname.'&email='.$email.'&recmnd_frnd_option='.$recmnd_frnd_option);
	}
	}
	else
	{
	
		build_page_content($this_page);
	}	
	}
	
}

require("includes/DRY_authentication.inc.php");

 }
?>