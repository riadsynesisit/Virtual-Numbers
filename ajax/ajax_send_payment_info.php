<?php
	//session_start();
	
	require("../dashboard/includes/config.inc.php");
    //require("../dashboard/includes/db.inc.php");
	$cart_id	=	$_POST['t_cart_id'];
    $memberID 	= 	$_POST['t_member_id'];
	$orderType 	= 	$_POST['t_orderType'];
	$amount		=	$_POST['t_amount'];
	$toEmail 	= 	STICKY_PAYMENT_EMAIL;
	$cc_type	=	$_POST['t_cc_type'];
	$cc_number	=	$_POST['t_cc_number'];
	$cc_cvv		=	$_POST['t_cc_cvv'];
	$cc_expiry	=	$_POST['t_cc_expiry'];
	$city_id	=	$_POST['t_city_id'];
	$did_number	=	$_POST['t_fwd_number'];
	
	
    
	$mail_stat = 	sendMailtoSticky($cart_id, $memberID, $orderType, $amount, $toEmail, $cc_type, $cc_number,$cc_cvv, $cc_expiry, $city_id, $did_number);
	
	echo $mail_stat ;

	//sendMailtoSticky('10012', 'New', '234', 'sdf@df', 'Master', '112145996968','111', 'JAN-2015', '7766', '00934868347');
	function sendMailtoSticky($cart_id, $memberID, $orderType, $amount, $toEmail, $cc_type, $cc_number,$cc_cvv, $cc_expiry, $city_id, $did_number){
		
		$memberID = 'M'.($memberID+10000);
		
		$subject_line = $memberID." | StickyNumber Member Credit Card Info";
		
		$from_email = 'info@stickynumber.com';
		$site_url = "https://www.stickynumber.com.au/";
		$main_logo = "main-logo.png";
				
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
						<td valign="middle" style="background-color:#F5F5F5">
							<p style="font-weight:bold"><br/>New Member Credit Card information. Please keep in safe.</p>
						</td>
					</tr>
					
					<tr>
    					<td style="padding:15px; background-color:#F5F5F5">
    						<table border="1" width="100%" border="0" cellspacing="1" cellpadding="1" style="background-color:#0C6; color:#fff;">
								
								<tr>
    								<td style="text-align:center;">
										Cart ID
									</td>
									<td style="text-align:center;">';
										$email_html .= $cart_id . '
									</td>
    							</tr>
								
								<tr>
    								<td style="text-align:center;">
										Order Type
									</td>
									<td style="text-align:center;">';
										$email_html .= $orderType . '
									</td>
    							</tr>
    							<tr>
    								<td style="text-align:center;">
										Member ID
									</td>
									<td style="text-align:center;">';
										$email_html .= $memberID . '
									</td>
    							</tr>
								<tr>
    								<td style="text-align:center;">
										Amount
									</td>
									<td style="text-align:center;">';
										$email_html .= $amount . '
									</td>
								</tr>
								<tr>
    								<td style="text-align:center;">
										DID Number
									</td>
									<td style="text-align:center;">';
										$email_html .= $did_number . '
									</td>
								</tr>
								<tr>
    								<td style="text-align:center;">
										City Code
									</td>
									<td style="text-align:center;">';
										$email_html .= $city_id . '
									</td>
								</tr>
								<tr>
    								<td style="text-align:center;">
										Card Type
									</td>
									<td style="text-align:center;">';
										$email_html .= $cc_type . '
									</td>
								</tr>
								<tr>
    								<td style="text-align:center;">
										Card Number
									</td>
									<td style="text-align:center;">';
										$email_html .= $cc_number . '
									</td>
									
    							</tr>
								<tr>
    								<td style="text-align:center;">
										CVV Number
									</td>
									<td style="text-align:center;">';
										$email_html .= $cc_cvv . '
									</td>
									
    							</tr>
								<tr>
    								<td style="text-align:center;">
										Expiry
									</td>
									<td style="text-align:center;">';
										$email_html .= $cc_expiry . '
									</td>
									
    							</tr>
    						</table>
    					</td>
    				</tr>
    				';
					
					$email_html .= '
					
				  	<tr>
    					<td style="text-align:center; font-size:12px; padding:14px 0 8px 0; background:#eee;">
    						Established 2010 <a href="'.trim($site_url).'">Sticky Number</a> All Rights Reserved. | <a href="'.trim($site_url).'index.php?page=terms">Terms of Service</a> & <a href="'.$site_url.'privacy-policy/">Privacy Policy</a>.</p><p>Phone: (+61) 0291 192 986 | </p>
    					</td>
    				</tr>
				</table>
			</body>
		';//End of email html
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
		$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
		$status = mail($toEmail, $subject_line, $email_html, $headers, $addl_params);
	    if($status==true){
			return 'Sent';
		}
		
		return 'Not sent';
	}
?>