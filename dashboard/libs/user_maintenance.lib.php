<?php

function email_addresses_are_different($first_address, $second_address, $redirect_url = "no"){
	
	
	
}

function password_pair_matches($first_password, $second_password, $redirect_url = "no"){
	
	
	
}

function email_address_pair_matches($first_email_address, $second_email_address, $redirect_url = "no"){
	
	
	
}

function no_prior_account($first_address, $second_address = "cant.be@found.anywhere.com", $redirect_url = "no"){
	
	
	
}

function account_change_mailer($user_id, $email_address, $user_name, $clear_password, $reason_code="new"){


	/*$from_email = ACCOUNT_CHANGE_EMAIL; 
	if(trim($from_email)==""){
		echo "\nERROR: NO FROM EMAIL";
		return false;
	}
	$to_email = $email_address;

	$subject_line = APP_TITLE_SHORT." | Member Account Username & Password";
	
	$email_hmtl_start = '<html> 
  	<body style="padding: 10px 15px; background: #FDFFC0; border: 1px dotted #999; color: #666; margin-bottom: 15px;">'; 

	if(strtolower($reason_code) == "new"){
	$email_top = '
Hello Customer,

This email is to inform you that your new Sticky Number member account has been created.';

	}else{
	$email_top = '
Hello '.$user_name.',';

	}

$email_body = '

<h3 style="color: #AF0515; font-weight: bold; font-size: 14px; line-height: 18px; margin: 0 0 19px 0 !important;"> Your Member Account information.</h3>
-------------------------------------------------------------------------------------------------------<br />
<br />
You will need the following information to Login to your Member Account to manage your products.<br />
 <br />
Your Username is:<br />
'.$email_address.'<br />
Your Password is:<br />
'.$clear_password.'<br />
 <br />
To log in to your Member Account click <a href="'.SERVER_WEB_NAME.'/">here</a>.<br />
 <br />
-------------------------------------------------------------------------------------------------------<br />
<br />
If you have any questions or need assistance with your account management, please contact us.<br />
 <br />
Kind Regards,<br />
 <br />
Sticky Number <br />
Customer Support<br />

e: <a href="mailto:"'.ACCOUNT_CHANGE_EMAIL.'">'.ACCOUNT_CHANGE_EMAIL.'</a><br />
w: <a href="'.SERVER_WEB_NAME.'">'.SERVER_WEB_NAME.'</a><br />';

	$email_hmtl_end = '
  </body> 
</html>';

	$email_message  = $email_hmtl_start . $email_top . $email_body . $email_hmtl_end;
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
	if(SITE_COUNTRY=="AU"){
		$headers .= 'Bcc: Sticky Number <stickyim@gmail.com>' . "\r\n";
	}
	$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
	$status = mail($to_email, $subject_line, $email_message, $headers, $addl_params);
	if($status==true){
		$status = create_client_notification($user_id, $from_email, $to_email, $subject_line, $email_message);
		if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
			echo $status;
			return false;
		}else{
			return true;
		}
	}*/
	
	$member_id = "M".($user_id+10000);
	$site_country = SITE_COUNTRY;
	$email = $email_address;
    	
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
		
		if(strtolower($reason_code) == "new"){
			$name = "Customer";
			$email_top = 'This email is to inform you that your new Sticky Number member account has been <span style="color:#090">created</span>.';
		}else{
			$name = $user_name;
			$email_top = '';
		}
		
		$from_email = $help_desk_email;
		
		$subject_line = APP_TITLE_SHORT." | Member Account Username & Password";
		
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
    								<td style="text-align:center;">
    									<h1 style="margin-bottom:20px; font-size:40px; line-height:48px;">Your username and password information</h1>
    								</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:20px; background-color:#F5F5F5">
    						<h3 style="margin-bottom:0">Hello '.$name.',</h3>
    						<h4 style="margin:0 0 10px 0">'.$email_top.'</span></h4>
    					</td>
    				</tr>
    				<tr>
					  <td style="padding:20px;">
					  <h3 style="color:#0C6">Your Member Account information.</h3>
					  <p>You will need the following information to Login to your Member Account to manage your products.</p>
						<p>Your <strong>Username</strong> is:<br />
					  	<a>'.$email_address.'</a><br />
						Your <strong>Password</strong> is:<br />
						<u>'.$clear_password.'</u></p>
						<p>To log in to your Member Account click <a href="'.$site_url.'">here</a>.</p>
					  </td>
					</tr>
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
				return $status;
			}else{
				return true;
			}
		}else{
			return "Error: Failed to send email out.";
		}

}


function account_change_mailer_frnd($user_id, $email_address, $user_name, $clear_password, $reason_code="new"){

	$from_email = ACCOUNT_CHANGE_EMAIL; 
	if(trim($from_email)==""){
		echo "\nERROR: NO FROM EMAIL";
		return false;
	}
	$to_email = $email_address;

	$subject_line = $user_name.' recommends';
	
	$email_hmtl_start = '<html> 
  	<body style="padding: 10px 15px; background: #FDFFC0; border: 1px dotted #999; color: #666; margin-bottom: 15px;">'; 

	if(strtolower($reason_code) == "new"){
	$email_top = '
Hello,
'.$user_name.' has recently signup to <a href="'.SERVER_WEB_NAME.'/">Sticky Number</a>,  a free UK virtual number supplier.';

	}else{
	$email_top = '
Hello,
'.$user_name.' has recently signup to <a href="'.SERVER_WEB_NAME.'/">Sticky Number</a>,  a free UK virtual number supplier.';

	}

$email_body = '

<br/><br/>
-------------------------------------------------------------------------------------------------------<br />
<br />
He has listed your email address during signup and wants to recommend you to signup for an account with Sticky Number.<br />
 <br />
 To sign up for a free account please click <a href="'.SERVER_WEB_NAME.'/">here</a>.
<br />
<p style="font-weight:bold; color:#ff0000;">
'.SERVER_WEB_NAME.'<br/><br/>

- Free Divert,<br/>
- Free Forwarding<br/>
- e-Instant Activation.<br/> 
- Live 24/7

</p>
 
-------------------------------------------------------------------------------------------------------<br />
<br />
If you have any questions or need assistance with your account management, please contact us.<br />
 <br />
Kind Regards,<br />
 <br />
Sticky Number <br />
Customer Support<br />

e: <a href="mailto:"'.ACCOUNT_CHANGE_EMAIL.'">'.ACCOUNT_CHANGE_EMAIL.'</a><br />
w: <a href="'.SERVER_WEB_NAME.'">'.SERVER_WEB_NAME.'</a><br />';

	$email_hmtl_end = '
  </body> 
</html>';

	$email_message  = $email_hmtl_start . $email_top . $email_body . $email_hmtl_end;
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
	$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
	$status = mail($to_email, $subject_line, $email_message, $headers, $addl_params);
	if($status==true){
		$status = create_client_notification($user_id, $from_email, $to_email, $subject_line, $email_message);
		if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
			echo $status;
			return false;
		}else{
			return true;
		}
	}

}

function account_verify_email($user_id,$email_address,$uniquecode,$user_name)
{
    $from_email = ACCOUNT_CHANGE_EMAIL;
	if(trim($from_email)==""){
		echo "\nERROR: NO FROM EMAIL";
		return false;
	} 
    $to_email = $email_address;

    $subject_line = APP_TITLE_SHORT." | Email Verification";

    $email_hmtl_start = '<html> 
    <body style="padding: 10px 15px; background: #FDFFC0; border: 1px dotted #999; color: #666; margin-bottom: 15px;">'; 

    $email_top = '
    Hello '.$user_name.',';

    $email_body = '

    <h3 style="color: #AF0515; font-weight: bold; font-size: 14px; line-height: 18px; margin: 0 0 19px 0 !important;"> Your Member Account information.</h3>
    -------------------------------------------------------------------------------------------------------<br />
    <br />
    We have received a request to confirm your email address for user:'.$to_email.'<br />
    <br />
    Follow this link to complete your request to verify your email address: <br/>
    '.SERVER_WEB_NAME.'/verify_email.php?id='.$uniquecode.'
    <br />
    <br /> 
    If you didn'."'".'t request to change your email address, please contact us:<br/>
    <a href="'.SERVER_WEB_NAME.'/contact-us/">'.SERVER_WEB_NAME.'/contact-us/</a>
    <br/><br/>
    If this request was made by mistake, you may just ignore this notification.
    <br />
    <br />
    -------------------------------------------------------------------------------------------------------<br />
    <br />
    Kind Regards,<br />
    <br />
    Sticky Number <br />
    Customer Support<br />

    e: <a href="mailto:"'.ACCOUNT_CHANGE_EMAIL.'">'.ACCOUNT_CHANGE_EMAIL.'</a><br />
    w: <a href="'.SERVER_WEB_NAME.'">'.SERVER_WEB_NAME.'</a><br />';

    $email_hmtl_end = '
    </body> 
    </html>';

    $email_message  = $email_hmtl_start . $email_top . $email_body . $email_hmtl_end;
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
    $addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
    $status = mail($to_email, $subject_line, $email_message, $headers, $addl_params);
	if($status==true){
		$status = create_client_notification($user_id, $from_email, $to_email, $subject_line, $email_message);
		if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
			echo $status;
			return false;
		}else{
			return true;
		}
	}
}