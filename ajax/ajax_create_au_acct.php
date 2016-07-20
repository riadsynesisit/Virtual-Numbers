<?php
    session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/User.class.php");
    require("../dashboard/classes/Daily_Stats.class.php");
    require("../dashboard/classes/Client_notifications.class.php");
    require("../dashboard/libs/user_maintenance.lib.php");
    require("../dashboard/libs/utilities.lib.php");
    
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $pword = $_POST['pword'];
    $new_or_current = $_POST['new_or_current'];
    $user_type = $_POST['user_type'];
    $company = $_POST['company'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $postal_code = $_POST['postal_code'];
    $user_city = $_POST['user_city'];
    $user_state = $_POST['user_state'];
    $user_country = $_POST['user_country'];
    $site_country = $_POST['site_country'];
    $is_free_number = isset($_POST['free_number']) ? $_POST['free_number'] : 'N' ;
	
    if(trim($site_country)==""){
    	echo "ERROR: Required parameter site_country not passed.";
    	exit;
    }
    
    //Validate the inputs (Server side validation)
    if(trim($new_or_current)=="new" && trim($fname)==""){
    	echo "ERROR: Please enter your first name";
    	exit;
    }
    
    if(trim($new_or_current)=="new" && trim($lname)==""){
    	/*echo "ERROR: Please enter your last name"; // commented as last name not required @steve : 24-03-2015
    	exit;*/
    }
    
    if(trim($email)=="" || !domain_exists($email) || substr($email,-3) == ".ru" || strstr($email, 'guerrillamail') ){
    	echo "ERROR: Please enter your email id";
    	exit;
    }
    
    if(filter_var($email,FILTER_VALIDATE_EMAIL)==false){
    	echo "ERROR: Please enter a valid email id";
    	exit;
    }
    
    if(trim($pword)==""){
    	echo "ERROR: Please enter your password";
    	exit;
    }
    
    if(strlen($pword)<6){
    	echo "ERROR: Please enter a password of minimum 6 characters";
    	exit;	
    }
    
    $user = new User();
    
    //Check if the email id entered by the user already exists
    if(trim($new_or_current)=="new" && $user->checkEmailExists($email,$site_country)==true){
    	echo "ERROR: An account with the email id entered by you already exists.\n\nPlease use the forgot password link if required or choose 'Current Member'.";
    	exit;
    }
    
    //Now create the account IF NEW
    if(trim($new_or_current)=="new"){
	    $name = $fname." ".$lname; //Concatenate First and Last Names - we have only one field in the DB
	    $insertuser_id = $user->insertUser($name, $email, $pword, $site_country, $user_type, $company, $address1, $address2, $postal_code, $user_city, $user_state, $user_country, $is_free_number);
	    if($insertuser_id!=0){
			//step 2 - setup the session for this user
			$_SESSION['user_logins_id'] = $insertuser_id;
			$_SESSION['user_name'] = $name;
			$_SESSION['user_login'] = $email;
			$_SESSION['is_authenticated'] = true;
			$_SESSION['admin_flag'] = 0;
			
			//update daily_system_stats.signup_emails table
			$daily_stats = new daily_stats();
			$daily_stats->updateSignupEmails(1);
			$daily_stats->updateConfirmEmails(1);
			
			//Step 3 - Send new member sign-up email from here
			$status = send_new_member_signup_email($email,$name,$insertuser_id,trim($site_country),trim($pword), $is_free_number);
	    	echo "OK,$name";
	    }else{
	    	echo "ERROR: An unknown error occured while creating your account. Please try again.";
	    }
    }else{//Existing account
    	//Check validity of email and password 
    	if($user->isValidLogin($email,$pword,$site_country)=="Active"){
    		$user_info = $user->getUserInfo($email,$site_country);
    		//step 3 - setup the session for this user
			$_SESSION['user_logins_id'] = $user_info["id"];
			$_SESSION['user_name'] = $user_info["name"];
			$_SESSION['user_login'] = $email;
			$_SESSION['is_authenticated'] = true;
			$_SESSION['admin_flag'] = $user_info["admin_flag"];
			
			echo "OK,".$user_info["name"];
    	}else{
    		echo "ERROR: No active account with the email id and password combination you entered was found. Please try again.";
    	}	
    }

	function domain_exists($email, $record = 'MX'){
        list($user, $domain) = explode('@', $email);
        return checkdnsrr($domain, $record);
	}


    function send_new_member_signup_email($email,$name,$user_id,$site_country,$passwd, $is_free_number = 'N'){
    	
		$code = md5($email.$passwd);
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
		
		if($is_free_number == 'Y'){
			$verify_code = "?code=$code";
		}else{
			$verify_code = "";
		}
		$from_email = $help_desk_email;
		
		$subject_line = $member_id." | StickyNumber Member Account Created";
		$email_html = '
			<head>
			  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>StickyNumber - New Member Account Created</title>
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
    						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#0C6; color:#fff;">
    							<tr>
    								<td style="text-align:center;">';
									if($is_free_number == 'Y'){
										$email_html .= '<h1 style="color:black;margin-bottom:20px; font-size:40px; line-height:48px;">VERIFY EMAIL ADDRESS</h1>';
									}else{
    									$email_html .= '<h1 style="margin-bottom:20px; font-size:40px; line-height:48px;">Congratulations! Member Account Created</h1>';
									}
    								$email_html .= '
									</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:20px; background-color:#F5F5F5">
    						<h3 style="margin-bottom:0">Hello '.$name.',</h3>';
							if($is_free_number == 'Y'){
								$email_html .= '<h4 style="margin:0 0 10px 0">To finish setting up your Sticky Number account, we just need to make sure this email address is yours.</h4>';
							}else{
								$email_html .= '<h4 style="margin:0 0 10px 0">This email is to inform you that your new Sticky Number member account has been <span style="color:#090">activated.</span></h4>';
							}
							$email_html .= '
    					</td>
    				</tr>';
					if($is_free_number == 'Y'){
						$email_html .= '
						<tr>
							<td style="padding:20px;border:1px solid;background-color:rgb(207, 76, 76);">
								<p><a style="color:white" href="'.$site_url.$verify_code.'&action=verify">Verify</a> '. $email .' </p>
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;<br/>
							</td>
						</tr>
						<tr>
							<td>
								<p>If you didn\'t make this request please click <a href="'.$site_url.$verify_code.'&action=cancel">here </a> to cancel.</p><br/>
							</td>
						</tr>';
					}else{
						$email_html .= '
						<tr>
							<td style="padding:20px;">
								<h3 style="color:#0C6">Your Member Account information.</h3>
								<p>You will need the following information to Login to your Member Account to manage your products.</p>
								<p>Your <strong>Username</strong> is:<br />
								<a>'.$email.'</a><br />
								Your <strong>Password</strong> is:<br />
								<u>'.$passwd.'</u></p>
								<p>To log in to your Member Account click <a href="'.$site_url.'">here</a>.</p>
							</td>
						</tr>';
					}
					$email_html .= '
					<tr>
				    	<td style="padding:0 15px 50px 15px">
				    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  				<tr>
				    				<td width="50%" valign="top" style="background-color:#4A5865; padding:15px; color:#fff; text-align:center;">
				    					<h1 style="font-size:38px; margin:0;">Need help?</h1>
										<p style="margin:0; font-size:24px">Our friendly support agents are ready to help. Chat | Phone | Email</p>
									</td>
				  				</tr>
							</table>
						</td>
				  	</tr>
				  	<tr>
    					<td style="text-align:center; font-size:12px; padding:14px 0 8px 0; background:#eee;">
    						Established 2010 <a href="'.trim($site_url).'">Sticky Number</a> All Rights Reserved. | <a href="'.trim($site_url).'index.php?page=terms">Terms of Service</a> & <a href="'.$site_url.'privacy-policy/">Privacy Policy</a>.</p><p>Phone: (+61) 0291 192 986 | Email: <a href="mailto:'.$help_desk_email.'">'.$help_desk_email.'</a></p>
    					</td>
    				</tr>
				</table>
			</body>
		';//End of email html
		
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
	
	