<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	if(isset($_POST['front_page_login']) || isset($_POST['submit'])){
		process_login_attempt($this_page);
	}else{
		header("Location: ../");
		exit;
		//show_login_page($this_page);
	}
	
}

function show_login_page($this_page){
	
	build_form_page($this_page);
	common_header($this_page, "Login Page - Login Required");
	
}

function process_login_attempt($this_page){
		
	$this_user = new User();
	$this_access = new Access();
	
	//Validate email
	if(isset($_POST["email"])==false || trim($_POST["email"])==""){
		echo "Error: Required parameter emailid not passed. Please pass email id.";
		exit;
	}
	//Check if the email is valid email id
	if(filter_var(trim($_POST["email"]),FILTER_VALIDATE_EMAIL)==false){
		echo "Error: Invalid email id passed. Please correct the email id.";
		exit;
	}
	//Validate and limit the Site country inputs
	if(isset($_POST["site_country"])==false || trim($_POST["site_country"])==""){
		echo "Error: Required parameter emailid not passed. Please pass email id.";
		exit;
	}
	if(trim($_POST["site_country"])!="AU" && trim($_POST["site_country"])!="UK" && trim($_POST["site_country"])!="COM"){
		echo "Error: Invalid site country.";
		exit;
	}
	
	//If not yet changed force them to change the password - whether or not the entered login is valid
	//Check force password change indicator
	$force_pwd_change = $this_user->force_pwd_change(trim($_POST['email']),trim($_POST['site_country']));
	if(trim($force_pwd_change)=="Y"){
		header("Location:../index.php?page=force_pwd_change");
		exit;
	}
	
	$status=$this_user->isValidLogin(trim($_POST['email']),$_POST['password'],trim($_POST['site_country']), true);
	if($_SESSION['verify_required'] == 'Y'){
		header('location:https://'.$_SERVER["HTTP_HOST"].'/index.php?error=failedv');
	}
	else if($status=='Active'){
		unset( $_SESSION['wrong_attempt'] ) ;
		unset( $_SESSION['verify_required'] ) ;
		//This is a valid login - set up the session
		//Get user data
		$row = $this_user->getUserInfo($_POST['email'],$_POST['site_country']);
		
		//Setup sesssion for this user
		$_SESSION['user_logins_id'] = $row['id'];
		$_SESSION['user_name'] = $row['name'];
		$_SESSION['user_login'] = trim($_POST['email']);
		$_SESSION['is_authenticated'] = true;
		$_SESSION['admin_flag'] = $row['admin_flag'];
		
		if($_SESSION['admin_flag']==1)
		{
		  $_SESSION['admin_access_login'] = true;
		  $_SESSION['admin_access_logins_id'] = $row['id'];
		
		}
		else
		{
		  $_SESSION['admin_access_login'] = false;
		  $_SESSION['admin_access_logins_id'] = 0;
		
		}
		//Update the last login time
		$this_user->updateLastLogin($_POST['email'],SITE_COUNTRY);
		// update access logs
		$this_access->updateAccessLogs('Logged In');
		
		//Update the force password change indicator
		
		
		//$this_page->content_area = "<h3>Success!<h3>\n";
		//$this_page->content_area .= "<p>We will now try to automatically take you to the index page.  If nothing seems to happen in a few seconds, just click <a href='index.php'>this link</a> and we will take you there.</p>";
		//$meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS + 2;
		//$meta_refresh_page = "index.php";
		
		//common_header($this_page, "Login Page - Trying...", false,$meta_refresh_sec,$meta_refresh_page);
		if($_SESSION['admin_flag']==1){//for admins
			header("Location: admin_system_status.php");
		}elseif($_SESSION['admin_flag']==2){//for basicsupport
			header("Location: search.php");
		}else{
			if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="localhost"){
				header("Location: http://localhost/sticky/dashboard/index.php");
			}else{
				header("Location: index.php");
			}
		}
	}
	else if($status=='Terminated'){
	  //Account Terminated
	  unset( $_SESSION['wrong_attempt'] ) ;
		$this_page->content_area = "<h3>Account Terminated Contact support@stickynumber.com<h3>\n";
		show_login_page($this_page);
	}
	else if($status=='Suspended'){
	  //Account Suspended
		//$this_page->content_area = "<h3>Account Suspended Contact support@stickynumber.com<h3>\n";
		//show_login_page($this_page);
		if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="localhost"){
			header('location:../../index.php?error=faileds');
		}else{
			header('location:http://'.$_SERVER["HTTP_HOST"].'/index.php?error=faileds');
		}
	}else if($status=='LoginRestricted'){
	  //Account Suspended
		//$this_page->content_area = "<h3>Login Restricted Contact support@stickynumber.com<h3>\n";
		//show_login_page($this_page);
		$_SESSION['wrong_attempt'] = 9 ;
		if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="localhost"){
			header('location:../../index.php?error=failedl');
		}else{
			header('location:https://'.$_SERVER["HTTP_HOST"].'/index.php?error=failedl');
		}
	}else{
		//Invalid login;
		//$this_page->content_area = "<h3>Problem: Incorrect Credentials<h3>\n";
		$_SESSION['wrong_attempt'] = 0 ;
		$error = 'failed';
		$user_data = explode('#', $status) ;
		if(count($user_data) >= 2){
			$user_id = $user_data[0] ;
			$userLoginAttempts = $user_data[1] ;
			$user_name = isset($user_data[2]) ? $user_data[2] : ' ' ;
			$user_status = isset($user_data[3]) ? strtolower($user_data[3]) : ' ' ;
			$_SESSION['wrong_attempt'] =  $userLoginAttempts ;
			if($userLoginAttempts == 9  &&  $user_status == 'active'){
				$this_user->suspendUser($user_id, 'LoginRestricted') ;				
				$this_access->updateAccessLogs('Status changed for 10 times wrong password:', 'Active', 'Login Restricted', false, $user_id);
				// Send Email.
				send_wrong_attempt_email( trim($_POST['email']),$user_name,$user_id,$_POST['site_country'] ) ;
				
			}
			if($user_status == 'suspended'){
				$error = 'faileds' ;
			}
			else if($userLoginAttempts >= 9 || $user_status == 'loginrestricted'){
				$error = 'failedl';
			}
			
			$this_user->updateLoginAttempt($user_id) ;
			
		}
		
		$this_page->content_area .= "<div class='tip_box'>You have Entered an Incorrect User Name or Password. Please try again.</div>";
		if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="localhost" ){
			header('location:http://'.$_SERVER["HTTP_HOST"].'/sticky/index.php?error='.$error);
		}else if( strstr($_SERVER["HTTP_HOST"],"dev.") ){
			header('location:http://'.$_SERVER["HTTP_HOST"].'/index.php?error='.$error);
		}else{
			header('location:https://'.$_SERVER["HTTP_HOST"].'/index.php?error='.$error);
		}
	}
	
}

function build_form_page($this_page){
	
	// first, show existing messages in the system, in order of valid date, creation date and severity
	$msg = new SystemMessages();
	$current_message_list = $msg->get_current_issues();
	$this_page->variable_stack['current_message_list'] = $current_message_list;
#	@this_page.content_area += @this_page.render_html('pages/system_notices_summary_current.inc.rhtml')
	$this_page->content_area .= getLoginPage($this_page);
	
}

function send_wrong_attempt_email($email,$name,$user_id,$site_country){
	
	$member_id = "M".($user_id+10000);
	
	switch($site_country){
		case "AU":
			$site_url = "https://www.stickynumber.com.au/";
			$main_logo = "main-logo.png";
			$help_desk_email = "support@stickynumber.com.au";
			break;
		case "COM":
			$site_url = "https://www.stickynumber.com/";
			$main_logo = "main-logo-r1.png";
			$help_desk_email = "support@stickynumber.com";
			break;
		case "UK":
			$site_url = "https://www.stickynumber.uk/";
			$main_logo = "main-logo-uk.png";
			$help_desk_email = "support@stickynumber.uk";
			break;
		default:
			return "Error: Unknown site_country $site_country for user with email id: $email";
			break;
	}
	
	$from_email = $help_desk_email;

	$subject_line = "Account Suspended";
	$email_html = '
		<html>
			<head>
			  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>Account Suspended Email Notification</title>
			  	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
			  	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
			</head>
			<body>
				<table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: transparent;margin:0 auto;font: 100%/1.6 \'Open Sans\',Verdana, Arial, Helvetica, sans-serif;">
					<tr>
						<td valign="middle" style="background-color:#F5F5F5">
							<img src="'.$site_url.'images/'.$main_logo.'" style="text-align:center; margin:0 auto; display:block" />
						</td>
					</tr>
					<tr>
    					<td style="padding:15px; background-color:#F5F5F5">
    						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#cc0000; color:#fff;">
    							<tr>
    								<td style="text-align:center;">
    									<h1 style="margin-bottom:20px; font-size:40px; line-height:48px;">Online Number Suspended</h1>
    									<h3 style="margin:0 0 20px 0;">Your online number is deactivated</h3></td>
    								</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:20px; background-color:#F5F5F5"><h3 style="margin-bottom:0">Hello '.$name.',</h3>
    						<p>Login to your account is Restricted due to trying more than 10 times with wrong password. Please contact us immediately.</p>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:15px 15px 50px 15px">
    						<table width="100%" border="0" cellspacing="0" cellpadding="0">
    							<tr>
    								<td width="50%" valign="top" style="color:#4B5868">
    									<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:18px">
    										<tr>
    											<td>
    												<h3 style="font-size:20px; margin:0 0 10px 0;">Customer ID# <span style="color:#090">'.$member_id.'</span></h3>
    											</td>
    										</tr>
    										<tr>
										    	<td>
										    		<p style="margin:0">Status</p><p style="margin:0; color:#ff0000"><strong>Suspended</strong></p>
										    	</td>
										  	</tr>
										  	<tr>
										  		<td style="padding-top:15px">
										  			<p style="margin:0;"><strong></strong></p>
										  		</td>
										  	</tr>
    									</table>
    								</td>
    								<td width="50%" valign="top" style="background-color:#4A5865; padding:15px; color:#fff; text-align:center;">
    									<h1 style="font-size:44px; margin:0 0 20px 0;">Need help?</h1>
    									<p style="margin:0 0 20px 0; font-size:24px">Our friendly support agents are ready to help. <br /><br />Chat | Phone | Email</p>
    								</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="text-align:center; font-size:12px; padding:14px 0 8px 0; background:#eee;">
    						Established 2010 <a href="'.trim($site_url).'">Sticky Number</a> All Rights Reserved. | <a href="'.trim($site_url).'terms/">Terms of Service</a> & <a href="'.$site_url.'privacy-policy/">Privacy Policy</a>.</p><p>Phone: (+61) 0291 192 986 | Email: <a href="mailto:'.$help_desk_email.'">'.$help_desk_email.'</a></p>
    					</td>
    				</tr>
				</table>
			</body>
		</html>
	';//$email_html ends here
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
	$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
	
	$status = mail($email, $subject_line, $email_html, $headers, $addl_params);
	if($status==true){
		$status = create_client_notification($user_id, $from_email, $email, $subject_line, $email_html);
		if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
			echo $status;
			return false;
		}else{
			return true;
		}
	}
	
}

require("includes/DRY_authentication.inc.php");

?>