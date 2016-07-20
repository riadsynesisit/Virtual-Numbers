<!doctype html>
<?php

require "inc_header_page.php";

/*
require("./dashboard/classes/Shopping_cart.class.php");

require("./dashboard/classes/SystemConfigOptions.class.php");
require("./dashboard/classes/Unassigned_087DID.class.php");
require("./dashboard/classes/Did_Cities.class.php");
require("./dashboard/classes/Member_credits.class.php");
require("./dashboard/classes/Payments_received.class.php");
require("./dashboard/classes/Worldpay_preapprovals.class.php");
require("./dashboard/classes/Risk_score.class.php");
require("./dashboard/classes/Client_notifications.class.php");
require("./dashboard/classes/Did_Plans.class.php"); //This is already included in index_AU.php
require("./dashboard/libs/utilities.lib.php");
require("./dashboard/libs/didww.lib.php");
require("./dashboard/libs/worldpay.lib.php");

require("./dashboard/classes/User.class.php");
require("./dashboard/classes/DidwwAPI.class.php");
require("./dashboard/classes/Did_Countries.class.php");
require("./dashboard/classes/VoiceMail.class.php");
require("./dashboard/classes/Access.class.php");
require("./dashboard/classes/AST_RT_Extensions.class.php");*/
require("./dashboard/classes/Assigned_DID.class.php");
require("./dashboard/classes/User.class.php");

	$blnError=false;
	
	$code = $_GET['code'] ;
	$action = isset($_GET['action']) ? $_GET['action'] : 'verify';
	$user = new User();
	if($action == 'verify'){
		$userRow = $user->updateUserByUniqueCode($code);
		if($userRow >= 1){
			$successMsg ="Your Account successfully verified!";
		}else{
			$blnError=true;
			$errMsg = "Your Email can't be verified...Please try again.";
		}
	}
	/*else if($action == 'cancel'){
		$successMsg ="Your Account has been Cancelled.";//need to delete all the user data?
		/*$userRow = $user->updateUserByUniqueCode($code, $action);
		if(is_array($userRow) && $userRow != false){
			$userID = $userRow[0];
			$assigned_dids = new Assigned_DID();
			$assigned_dids->terminate_dids($userID,"User Cancelled Email");
		}
	}*/
	else{
		$blnError=true;
		$errMsg = "Your Email can't be verified...Please try again.";
	}
	

?>
	
</head>
<body>
	<?php 
	include 'login_top_page.php';
  
	include 'top_nav_page.php';?>

	<div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL; ?>images/SF-pricing.jpg">
    </div><!-- /.container -->

    <!-- Main wrapper starts here -->
     <div class="wrapper">
         <div class="container"> 
           		<!--div class="stepNav-wrap">
                    <ul id="stepNav" class="fourStep">
                        <li class="done"><a title=""><em>Step 1: Select Plan</em></a></li>
                        <li class="done"><a title=""><em>Step 2: Create Account</em></a></li>
                        <li class="lastDone"><a title=""><em>Step 3: Make Payment</em></a></li>
                        <li class="current"><a title=""><em>Step 4: Confirm/Finish</em></a></li>
                    </ul>
                    <div class="clearfloat">&nbsp;</div>
    			</div-->
    		<div class="plan-box">
           		<div class="alert-box alert-box-g centeredImage">
           			
           			<?php if($blnError==false){//When there is no error ?>
						<h1>Thank You!</h1><br>
           				<h3 style="color:green"><?php echo $successMsg; ?></h3>
                        	<br/>
                        	
							<?php if($action == 'verify'){
								echo 'Please login Now';
								//send_new_member_signup_email($email,$name,$user_id,$site_country,$passwd="", $is_free_number = 'N');
							}?>
                        	<br/>
                        	<br/>
           			<?php }else{ //If there is any error?>
						<h1 style="color:red">Sorry!</h1><br>
	         		<?php 	echo "<font color='red'>".$errMsg."</font><script>alert('".$errMsg."')</script>"; ?>
	         		<?php }//End of else part of error check?>

					<p>Contact us 24/7 About your order or if you have any other enquires.
					Please Check your email for order confirmation and Invoice</p>
				</div>
           </div><!-- .Plan Box -->
    		</div>
    	</div>
		
	<?php include('inc_footer_page.php');?>
		
    </body>
</html>

		
<?php
	function send_new_member_signup_email($email,$name,$user_id,$site_country,$passwd="", $is_free_number = 'N'){
    	
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
    								<td style="text-align:center;">
    									<h1 style="margin-bottom:20px; font-size:40px; line-height:48px;">Congratulations! Member Account Created</h1>
    								</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:20px; background-color:#F5F5F5">
    						<h3 style="margin-bottom:0">Hello '.$name.',</h3>
    						<h4 style="margin:0 0 10px 0">This email is to inform you that your new Sticky Number member account has been <span style="color:#090">activated.</span></h4>
    					</td>
    				</tr>
    				<tr>
					  <td style="padding:20px;">
					  <h3 style="color:#0C6">Your Member Account information.</h3>
					  <p>You will need the following information to Login to your Member Account to manage your products.</p>
						<p>Your <strong>Username</strong> is:<br />
					  	<a>'.$email.'</a><br />
						</p>
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
				echo $status;
				return false;
			}else{
				return true;
			}
		}
    	
    }
	
	
?>