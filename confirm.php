<!doctype html>
<?php

require "inc_header_page.php";

require("./dashboard/classes/Shopping_cart.class.php");

require("./dashboard/classes/SystemConfigOptions.class.php");
require("./dashboard/classes/Unassigned_087DID.class.php");
require("./dashboard/classes/Did_Cities.class.php");
require("./dashboard/classes/Member_credits.class.php");
require("./dashboard/classes/Assigned_DID.class.php");
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
require("./dashboard/classes/AST_RT_Extensions.class.php");

	$blnError=false;
	
	//Check if there is any error from WorldPay
	if(isset($_GET["errMsg"]) && trim($_GET["errMsg"])!=""){
		$blnError = true;
		$errMsg = trim($_GET["errMsg"]);
	}
	
	//Get shopping_cart details
	$shopping_cart = new Shopping_cart();
	if($blnError==false && is_object($shopping_cart)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Shopping_cart object";
	}
	
	$risk_score_obj = new Risk_score();
    if($blnError==false && is_object($risk_score_obj)==false){
    	$blnError = true;
    	$errMsg = "Error: Failed to create risk_score object.";
    }
	
	$cart_details = "";
	$cartId = trim($_GET["cartId"]);
	if($cartId==""){
		$cartId = trim($_POST["cartId"]);
	}
	if($blnError==false && $cartId==""){
		$blnError = true;
		$errMsg = "Error: Required parameter cartId not passed.";
	}
	if($blnError==false){
		$cart_details = $shopping_cart->get_cart_details($cartId);
	}
	
	if($blnError==false && is_array($cart_details)==false){
		$blnError = true;
		$errMsg = $cart_details;
	}
	if($blnError==false){
		//Get user details
		$user = new User();
		if(is_object($user)==false){
			$blnError = true;
			$errMsg = "Error: Failed to create User object";
		}
    }
	
	//Process the DID Purchase - Get a new DID from DIDWW and provision it in StickyNumber
	$did_details = "";
	if($blnError==false){
		if($cart_details["fulfilled"]=="Y"){//Get the did details from the join of shopping_cart and assigned_dids table
			$did_details = $shopping_cart->getDIDDetails($cartId);
		}else{
			if($cart_details["paid"]=="Y"){
				if($cart_details["forwarding_type"]==2){
					$fwd_number = $cart_details["sip_address"];
				}else{
					$fwd_number = $cart_details["fwd_number"];
				}
				$did_details = processDIDPurchase($cart_details["user_id"], $cart_details["did_plan"], $cart_details["plan_period"], $cart_details["num_of_months"], $cart_details["purchase_type"], $cart_details["pymt_type"], $cart_details["did_id"], $cart_details["id"], $cart_details["country_iso"], $cart_details["city_prefix"], $fwd_number, $cart_details["forwarding_type"]);
			}else{
				//Check if this order is authorized but is pending for risk score exceeding allowed threshold limit.
				$order_status = $risk_score_obj->get_order_status($cartId);
				if(substr($order_status,0,6)=="Error:"){
					$blnError = true;
					$errMsg = $order_status;
				}elseif(intval($order_status)==5){//Authorized order - In pending status
					$did_details = array("did_number"=>"Pending Allocation", "num_of_minutes"=>"Pending", "did_expiry"=>"Pending", "assigned_did_id"=>"Pending");
					
					$user_details = $user->getUserInfoById(trim($cart_details["user_id"])); 
					
					$status = send_pending_activation_email(trim($user_details["email"]),trim($user_details["name"]),trim($cart_details["user_id"]),$cart_details["description"],trim($user_details["site_country"]));
					
				}elseif(intval($order_status)==0){//Pending Order - Not supposed to be in this status - Expects order_status=5 if authorized by payment gateway but is pending for high risk score
					$blnError = true;
					$errMsg = "Error: Failed to get payment gateway authorization of this order with cart id: ".$cartId;
				}elseif(intval($order_status)==4){
					$blnError = true;
					$errMsg = "Error: This order with cart id: ".$cartId." was cancelled.";
				}else{
					$blnError = true;
					$errMsg = "Error: Unexpected order_status: ".$order_status;
				}
			}
		}
	}
	
	if($blnError==false && is_array($did_details)==false){
		$blnError = true;
		$errMsg = $did_details;
	}
	
	$status = "";
	if($blnError==false){
		$docs_required = $did_details["docs_required"];
		$did_number = $did_details["did_number"];
		if($cart_details["forwarding_type"]==2){
			$num_of_minutes = "Unlimited";
		}else{
			$num_of_minutes = $did_details["num_of_minutes"];
		}
		if($cart_details["city_prefix"]==99999){
			$did_expiry = "N/A";
		}else{
			$did_expiry = $did_details["did_expiry"]." GMT";
		}
		$assigned_did_id = $did_details["assigned_did_id"];
		
		//Update the fulfilled status
		if($cart_details["fulfilled"]!="Y" && substr($assigned_did_id,0,7)!="Pending"){
			if($cart_details["purchase_type"]=="renew_did" || $cart_details["purchase_type"]=="restore_did"){
				//For a renewal or restore order check for DID_ID
				if(isset($assigned_did_id)==false || trim($assigned_did_id)==""){
					$blnError = true;
					$errMsg = "Error: DID_ID Missing for cartId: $cartId in confirm page.";
				}
			}
			if($blnError==false){
				$status = $shopping_cart->update_fulfilled($cartId, $assigned_did_id);
				if($status == "success" && $cart_details["pymt_gateway"]=="WorldPay"){
					$wp_preapprovals = new Worldpay_preapprovals();
					if(is_object($wp_preapprovals)==false){
						$blnError = true;
						$errMsg = "Error: Could not create Worlppay_preapprovals object.";
					}
					if($blnError==false){
						$ret_val = $wp_preapprovals->update_did_id($cartId,$assigned_did_id);
						if($ret_val!==TRUE){
							$blnError = true;
							$errMsg = $ret_val;
						}
					}
				}
			}
		}
	}
	if($blnError==false && substr($assigned_did_id,0,7)!="Pending" && $status != "success"){
		$blnError = true;
		$errMsg = $status;
	}
	
	if($blnError==false && substr($assigned_did_id,0,7)!="Pending"){
		//Update the preapprovalKey in assigned_dids table - used during auto renewals
		$assigned_dids = new Assigned_DID();
		if(is_object($assigned_dids)==false){
			return "Error: Could not create Assigned_DID object.";
		}
		$status = $assigned_dids->updatePreapprovalKey($assigned_did_id,$cartId);
		if(is_bool($status)==false && substr($status,0,6)=="Error:"){
			return $status;
		}
	}
	
	if($blnError==false && substr($assigned_did_id,0,7)!="Pending" && $status !==true){
		$blnError = true;
		$errMsg = $status;
	}

	
function send_pending_activation_email($email, $name, $user_id, $description, $site_country){
	
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

	$subject_line = "Your order is pending activation";
	$email_html = '
		<html>
			<head>
			  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>Your Order is Pending Activation Notification</title>
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
    						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#e88300; color:#fff;">
    							<tr>
    								<td style="text-align:center;">
    									<h1 style="margin-bottom:20px; font-size:40px; line-height:48px;">Your Order is Pending Activation</h1>
    								</td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    				<tr>
    					<td style="padding:20px; background-color:#F5F5F5"><h3 style="margin-bottom:0">Hello '.$name.',</h3>
    						<p>This online number is pending activation. Your order will be proccesed in the next 4 business hours.</p>
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
										    		<p style="margin:0">Status</p><p style="margin:0; color:#ff0000"><strong>Pending Activation</strong></p>
										    	</td>
										  	</tr>
										  	<tr>
										  		<td style="padding-top:15px">
										  			<p style="margin:0;"><strong>'.$description.'</strong></p>
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
           			<h1>Thank You!</h1><br>
           			<?php if($blnError==false){//When there is no error ?>
           				<?php if(substr($assigned_did_id,0,7)!="Pending"){?>
                        	<h3>Your new number successfully created!</h3>
                        	<br/>
                        	Your New Virtual Number: <font color="green"><b><?php echo $did_number; ?></b></font>
                        	<br/>
                        	Expiry Date: <font color="green"><b><?php echo $did_expiry; ?></b></font>
                        	<br/>
						<?php 
							if($docs_required){
								echo '<p class="txt-red"><strong>Your order is pending further information, a request for more information has been emailed to you.</strong></p><br>';
							}
						?>
                        	<br/>
                        <?php }else{
								 
							?>
                        	<p class="txt-red"><strong>Your Order is being procesed.
							You will get an activiation email Shortly.</strong></p><br>
							<?php
							
							}
							?>
           			<?php }else{ //If there is any error?>
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