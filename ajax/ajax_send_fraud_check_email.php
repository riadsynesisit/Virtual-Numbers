<?php

if(isset($_POST["cart_id"])==false || trim($_POST["cart_id"])==""){
    echo "Error: Cart ID not entered.";
    exit;
}else{
	$cart_id = trim($_POST["cart_id"]);
}

require("../dashboard/includes/config.inc.php");
require("../dashboard/includes/db.inc.php");
require("../dashboard/classes/Shopping_cart.class.php");
require("../dashboard/classes/Client_notifications.class.php");
require("../dashboard/classes/Risk_score.class.php");
require("../dashboard/libs/user_maintenance.lib.php");
require("../dashboard/libs/utilities.lib.php");
    
$shopping_cart = new Shopping_cart();

$data = $shopping_cart->getUserDetailsByCartID($cart_id);

if(!is_array($data)){
	echo $data;
	exit;
}

$order_number = rand(1,100).$data['user_id'].'00'.$cart_id;
$user_id = $data['user_id'];
$name = $data['name'];
$site_country = $data['site_country'];
$credit_amount = $data['amount'];
$email = $data['email'];
		
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
				echo "Error: Unknown site_country $site_country for user with email id: $email";
				break;
		}
		
		
		$from_email = $help_desk_email;
		
		$subject_line = "Order #".$order_number." | Further Information";
		
		$email_html = '
			<head>
			  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>StickyNumber - Payment Verification Required</title>
			  	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
			  	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" type="text/css">
			</head>
			<body>
				<table width="650" border="0" cellspacing="0" cellpadding="0" style="background-color: transparent;margin:0 auto;font: 100%/1.6 \'Open Sans\',Verdana, Arial, Helvetica, sans-serif;">
					<tr>
						<td style="background-color:#F5F5F5">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#fff;">
								<tr>
									<td style="text-align:left;">
										<img src="'.$site_url.'images/'.$main_logo.'" style="display:block" />
									</td>
									<td style="text-align:right;vertical-align:bottom">
										24/7 Sales & Support
										<h2 style="color: #C5C730;padding-top:15px">02 9119 2986</h2>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					
					<tr>
    					<td style="padding:10px; background-color:#FFF">
    						<div style="margin-top:0;margin-bottom:0;"><font color="#595959" size="2px">Hello '.$name.', <br/>Your order is pending payment verification below.</font></div>
							
    					</td>
    				</tr>
					
					<tr>
    					<td style="padding:10px; background-color:#F5F5F5">
    						<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tbody><tr>
<td style="background-color:#E88300;padding:7.5pt 0;"><span style="background-color:#E88300;">
<div align="center" style="text-align:center;margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="5" color="white"><span style="font-size:16.5pt;"><b>Payment Verification
Required </b></span></font><font face="Verdana,sans-serif" size="5" color="white"><span style="font-size:16.5pt;"><b><br>

</b></span></font><font face="Verdana,sans-serif" color="white"><span style="text-transform:none;">Please verify your payment information below</span></font><font face="Verdana,sans-serif" size="5" color="white"><span style="font-size:16.5pt;"><b> </b></span></font></span></font></div>
</span></td>
</tr>
<tr height="64" style="height:38.8pt;">
<td style="height:38.8pt;background-color:#F9F5E6;padding:15pt;border:1pt solid #C8C28F;">
<span style="background-color:#F9F5E6;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#BA8C48"><span style="font-size:9pt;">As a part of our payment security process your order has been selected for a random security check and we require some extra information to authenticate your order. Please send within 48 hours to avoid losing </span></font><font face="Verdana,sans-serif" size="2" color="#BF9000"><span style="font-size:9pt;">your 
Virtual Numbers</span></font><font face="Verdana,sans-serif" size="2" color="#BA8C48"><span style="font-size:9pt;">. </span></font></span></font></div>
</span></td>
</tr>
</tbody></table>
    					</td>
    				</tr>
    				<tr>
						<td>
						&nbsp;
						</td>
    				</tr>
					<tr>
						<td style="background-color:#F5F5F5">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#fff;">
								<tr>
									<td style="text-align:center;">
										Order #'.$order_number.'
									</td>
									<td style="text-align:center;vertical-align:bottom">
										Customer ID# '.$member_id.'
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
						&nbsp;
						</td>
    				</tr>
					<tr>
						<td>
							<table width="100%" border="1" cellspacing="0" cellpadding="0" style="width:100%;border-style:none none solid none;border-bottom-width:1pt;border-bottom-color:#BED0E4;">
<tbody><tr>
<td width="37" style="width:22.45pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#484848"><span style="font-size:9pt;">&nbsp;</span></font></span></font></div>
</span></td>
<td width="203" valign="top" style="width:122.1pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:9pt;"><b>Status</b></span></font></span></font></div>
</span></td>
<td width="535" valign="top" style="width:321.25pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="red"><span style="font-size:9pt;">Pending Verification</span></font></span></font></div>
</span></td>
<td width="37" style="width:22.5pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#484848"><span style="font-size:9pt;">&nbsp;</span></font></span></font></div>
</span></td>
</tr>
<tr>
<td width="37" style="width:22.45pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#484848"><span style="font-size:9pt;">&nbsp;</span></font></span></font></div>
</span></td>
<td width="203" valign="top" style="width:122.1pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:9pt;"><b>Order Total</b></span></font></span></font></div>
</span></td>
<td width="535" valign="top" style="width:321.25pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:9pt;">$'.$credit_amount.'</span></font></span></font></div>
</span></td>
<td width="37" style="width:22.5pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#484848"><span style="font-size:9pt;">&nbsp;</span></font></span></font></div>
</span></td>
</tr>
<tr>
<td width="37" style="width:22.45pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:9pt;">&nbsp;</span></font></span></font></div>
</span></td>
<td width="203" valign="top" style="width:122.1pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:9pt;"><b>Holder Name</b></span></font></span></font></div>
</span></td>
<td width="535" valign="top" style="width:321.25pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:10pt;background-color:#EAF1EE;">'.$name.'</span></font></span></font></div>
</span></td>
<td width="37" style="width:22.5pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#484848"><span style="font-size:9pt;">&nbsp;</span></font></span></font></div>
</span></td>
</tr>
<tr>
<td width="37" style="width:22.45pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:9pt;">&nbsp;</span></font></span></font></div>
</span></td>
<td width="203" valign="top" style="width:122.1pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:9pt;"><b>Product</b></span></font></span></font></div>
</span></td>
<td width="535" valign="top" style="width:321.25pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#595959"><span style="font-size:9pt;">Online Number</span></font></span></font></div>
</span></td>
<td width="37" style="width:22.5pt;background-color:#E5EFF9;padding:9pt 0;border-style:solid none none none;border-top-width:1pt;border-top-color:#BED0E4;">
<span style="background-color:#E5EFF9;">
<div style="margin:0;"><font face="Times New Roman,serif" size="3" color="windowtext"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="#484848"><span style="font-size:9pt;">&nbsp;</span></font></span></font></div>
</span></td>
</tr>
</tbody></table>
						</td>
    				</tr>
					
					<tr>
				    	<td style="padding:10px 15px 10px 15px">
				    		<div style="margin:0 0 11.25pt 0;"><font face="Verdana,sans-serif" size="3" color="#484848"><span style="font-size:11.5pt;"><font face="Verdana,sans-serif" color="#595959"><b>Please send payment verification:</b></font></span></font></div>
						</td>
				  	</tr>
					
					<tr>
				    	<td style="padding:0 1px 5px 15px">
				    		<ul style="margin-top:14pt;margin-bottom:0;">
<li style="margin:0;"><font face="Times New Roman,serif" size="3" color="black"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2" color="windowtext"><span style="font-size:9pt;">Front of your credit card (you can hide the first 8 digits)</span></font></span></font></li><li style="margin:0;"><font face="Times New Roman,serif" size="3" color="#595959"><span style="font-size:12pt;"><font face="Verdana,sans-serif" size="2"><span style="font-size:9pt;">A copy of valid photo ID (drivers license, passport, etc)</span></font></span></font></li></ul>
						</td>
				  	</tr>
					
					<tr>
				    	<td style="padding:0 1px 5px 15px">
				    		<table width="100%" border="0" cellpadding="0" >
<tbody><tr height="68" style="height:41.25pt;">
<td valign="top" style="width:24.78%;height:41.25pt;padding:0.75pt;">
<div style="margin:0 0 11.25pt 0;"><font face="Verdana,sans-serif" size="2" color="#484848"><span style="font-size:9pt;"><font face="Verdana,sans-serif" color="#595959"><b>Send by Email</b></font></span></font></div>
</td>
<td valign="top" style="height:41.25pt;padding:0.75pt;">
<div style="margin-top:0;margin-bottom:0;"><font style="color:black;font-size:14px">You can send an email with attached copies of the card and photo ID, select reply in this email and include attachments.</font></div>
</td>
</tr>
</tbody></table>
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
		
		//echo $email_html;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
		$addl_params = '-f '. $from_email.' -r '.$from_email."\r\n";
		$status = mail($email, $subject_line, $email_html, $headers, $addl_params);// problem here
	    if($status==true){
			
			$risk_score = new Risk_score();
			if(is_object($risk_score)==false){
				echo "Error: Failed to create Risk score object.";
			}
			
			$is_sent_updated = $risk_score->updateMailStatus($cart_id, "Y");
			
			$status = create_client_notification($user_id, $from_email, $email, $subject_line, $email_html);
			if(is_numeric($status)==false && substr($status,0,6)=="Error:"){
				echo "Mail Sent...but ".$status;
			}else{
				echo "Success";
			}
		}else{
			echo $status;
		}
    	
if($conn){
	mysql_close($conn);
}

?>