<!doctype html>
<?php
/*
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=privacy"  || $_SERVER["REQUEST_URI"]=="/privacy")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: privacy-policy/");
		exit;
	}
	
}else if($_SERVER["HTTP_HOST"]!="localhost"){
	include 'inc_common_header.php';
}*/



require("./dashboard/classes/Shopping_cart.class.php");

require("./dashboard/classes/Blacklist_countries.class.php");
    require("./dashboard/classes/Blacklist_ip.class.php");
    require("./dashboard/classes/Free_email_providers.class.php");
    require("./dashboard/classes/Block_Destination.class.php");
    
require("./dashboard/classes/SystemConfigOptions.class.php");
require("./dashboard/classes/Unassigned_087DID.class.php");
require("./dashboard/classes/Did_Cities.class.php");
require("./dashboard/classes/Member_credits.class.php");
require("./dashboard/classes/Assigned_DID.class.php");
require("./dashboard/classes/Payments_received.class.php");
require("./dashboard/classes/Worldpay_preapprovals.class.php");
require("./dashboard/classes/SimplePay_preapprovals.class.php");
require("./dashboard/classes/Risk_score.class.php");
require("./dashboard/classes/Client_notifications.class.php");
require("./dashboard/classes/Did_Plans.class.php"); //This is already included in index_AU.php


require("./dashboard/classes/User.class.php");
require("./dashboard/classes/DidwwAPI.class.php");
require("./dashboard/classes/Did_Countries.class.php");
require("./dashboard/classes/VoiceMail.class.php");
require("./dashboard/classes/Access.class.php");
require("./dashboard/classes/AST_RT_Extensions.class.php");

require("./dashboard/libs/utilities.lib.php");
require("./dashboard/libs/didww.lib.php");
require("./dashboard/libs/worldpay.lib.php");

?>

<?php require "common_header.php"; ?>
  </head>

  <body>
	
	<?php //include 'login_top_page.php';?>
  
    <?php //include 'top_nav_page.php';?>

    <!-- Banner -->
    <div class="wrapper">	         
    	<div class="container"><img src="images/SF-pricing.jpg" width="1024" height="201" alt="Account Details"></div>
    </div>
    <!-- Banner Closed -->
	
    <?php //echo '<pre>';print_r($_SESSION); echo '</pre>';?>
 
	
	
<?php
	
$blnError = false;

$payment_id = $_GET['id'] ;
$cart_id = $_GET['cart_id'];
if($payment_id == "" || !isset($payment_id) || !isset($cart_id)){
	die("required parameter cart_id not passed...");
}
$resp = get_payment_response($payment_id);
$data = json_decode($resp, true);
if(strstr($_SERVER["HTTP_HOST"], 'localhost') ){
	//echo '<br/><pre>';print_r($data); echo '</pre>';
}

$didOK = false;	$errMsg = "";
if($data['result']['code'] == SIMPLEPAY_SUCCESS_CODE){
	
	//Get shopping_cart details
	$shopping_cart = new Shopping_cart();
	if(is_object($shopping_cart)==false){
		$blnError = true;
		$errMsg = "Error: Failed to create Shopping_cart object";
	}
	
	$risk_score_obj = new Risk_score();
    if( is_object($risk_score_obj)==false){
    	$blnError = true;
    	$errMsg = "Error: Failed to create risk_score object.";
    }
	
	if($blnError == false){
		$transID = isset($data["id"]) ? $data["id"] : "";
		$payment_brand = isset($data["paymentBrand"]) ? $data["paymentBrand"] : '' ;
		$build_number = $data["buildNumber"];
		$time_stamp = $data["timestamp"];
		$card_number = isset($data["card"]["bin"]) ? $data["card"]["bin"].$data["card"]["last4Digits"] : $data["card"]["last4Digits"];
		$regID = isset($data["registrationId"]) ? $data["registrationId"] : "";

	}
	
	$status = $shopping_cart->update_paid($cart_id, $transID, "Y", date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $payment_id,'','','');
	
	if(substr($status,0,6)=="Error:"){
		echo $status;
		$errMsg = $status;
		$blnError = true ;
		//exit;
	}
	
	if($blnError == false){
	//echo '<h2 style="color:green">Payment Received Successfully</h2>';
		$risk_score = process_risk_score($cart_id,"XX",$_SERVER['REMOTE_ADDR']);
		//echo "risk score==== ". $risk_score." ====== <br>";
		if(substr($risk_score,0,6)=="Error:"){
			echo $risk_score;
			$errMsg = "risk score calc error: ".$risk_score;
			$blnError = true ;
			//exit;
		}
	}
	
	$cart_details = $shopping_cart->get_cart_details($cart_id);
	
	// for member_credit add
	$is_acc_credit = (trim($cart_details["return_to"]) == "AccountCredit") ? true : false;
	if($blnError == false){
		$msg = create_payments_received_record($cart_id, false, array("card_number" => $card_number, "payment_brand" => $payment_brand));
		if(substr($msg,0,6)=="Error:"){
			echo $msg;
			$errMsg = $msg;
			$blnError = true ;
			//exit;
		}else{
			// create preapproval if registration success
			if($regID && $is_acc_credit == false){
				$sp_approvals = new SimplePay_preapprovals();
				$status = $sp_approvals->add_record($cart_id,$regID);
				if(substr($status,0,6)=="Error:"){
					echo $status;
					$errMsg = $status;
					$blnError = true ;
				}
			}
		}
	}	
	
	if( $risk_score <= 0 && $blnError == false ){
		
		$status = simplepay_capture_pa($cart_details["transId"],SITE_CURRENCY,$cart_details["amount"]);
		$data = json_decode($status, true);
		if(strstr($_SERVER["HTTP_HOST"], 'dev.') || strstr($_SERVER["HTTP_HOST"], 'localhost') || strstr($_SESSION["user_login"], "riadul.i") ){
	
			echo '<br/><pre>';print_r($data); echo '</pre>';
		}
		if($data['result']['code'] == SIMPLEPAY_SUCCESS_CODE){
			$payID = $data['id'];
			// update payment received done.
			$payments_received = new Payments_received();
			if(is_object($payments_received)==false){
				return "Error: Faile to create Payments_received object.";
			}
		
			$status = $payments_received->updateSPcaptured($cart_id, $payID, $cart_details["user_id"]);
			if($status!=true){
				echo $status;
				$errMsg = $status;
				$blnError = true ;
			}
				
		}else{
			$blnError = true ;
			
			$errMsg = "Error:". $cart_details["pymt_gateway"] . " desc=".$data['result']['description'];
			echo $errMsg ;
		}
		
		$did_details = "";
		
		if($is_acc_credit == false){
			if($blnError==false){
				$did_details = processDIDPurchase($cart_details["user_id"], $cart_details["did_plan"], $cart_details["plan_period"], $cart_details["num_of_months"], $cart_details["purchase_type"], $cart_details["pymt_type"], $cart_details["did_id"], $cart_details["id"], $cart_details["country_iso"], $cart_details["city_prefix"], $cart_details["fwd_number"], $cart_details["forwarding_type"]);
			}
			if($blnError==false && is_array($did_details)==false){
				$blnError = true;
				$errMsg = "Processpurchase ERROR: ".$did_details;
				echo $did_details;
			}
			if($blnError==false ){
				//$risk_score_obj = new Risk_score();
				$status = $risk_score_obj->update_order_status($cart_id, 1);//Mark this order as completed : Order Status = 1
			}
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
				
				if($blnError==false){
					$status = $shopping_cart->update_fulfilled($cart_id, $assigned_did_id);
					$didOK = true;	
					if($sp_approvals){
						$ret_val = $sp_approvals->update_did_id($cart_details["id"], $assigned_did_id);
						if($ret_val!==TRUE){
							echo "Please contact support with this error: ".$ret_val;
							$errMsg = "DID_ID mapping error: ".$ret_val;
						}
					}
				}
			}
			
		}else{
			if($blnError==false ){
				//$risk_score_obj = new Risk_score();
				$status = $risk_score_obj->update_order_status($cart_id, 1);//Mark this order as completed : Order Status = 1
			}
			$sp_return_url = SITE_URL."dashboard/add_account_credit.php?cartId=".$cart_id;
			header("Location: $sp_return_url");exit(0);
		}
		
	}else if($blnError == false){
		$status = $risk_score_obj->update_order_status($cart_id, 5);
		//echo '<br/><h2 style="color:green">Order Received. Please wait for confirmation...</h2><br/>';
		if($cart_details["purchase_type"] == 'new_did'){
			$user_obj = new User();
			send_pending_activation_email($user_obj, $cart_details["user_id"], $cart_details['description'], SITE_COUNTRY);
		}
	}
	
}else{
	$errMsg = $data['result']['description'];
	$blnError = true ;
	echo "<h3>FAILED:: desc=".$data['result']['description']."</h3>";
}

$is_sent = false;
if($didOK == true){
	//echo '<br/><h2 style="color:green">Congratulations. Your Virtual number is : ...'.$did_number.' Expiry date:'.$did_expiry.' </h2>';
	
}
else if($errMsg != ""){
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Sticky Number dev<dev@stickynumber.com>' . "\r\n";
	$headers .= 'Cc: riadsynesisit@gmail.com' . "\r\n";
	
	$is_sent = mail("riadul.i@stickynumber.com", "SIMPLE_PAY ERROR: Cart_ID=".$cart_id, "$errMsg", $headers);
}

if($errMsg != "" && $is_sent == false){
	echo "<h2>Please contact support with the error message.... </h2>";
}else if($is_acc_credit == true){
	$sp_return_url = SITE_URL."dashboard/add_account_credit.php?cartId=".$cart_id;
	//header("Location: $sp_return_url");
	echo '<script type="text/javascript">window.location = "'.$sp_return_url.'"</script>';
}

?>
	   <div class="wrapper">
		<div class="container">
		<div class="stepNav-wrap">
			<ul id="stepNav" class="fourStep">
				<li class="done"><a title=""><em>Step 1: Select Plan</em></a></li>
				<li class="done"><a title=""><em>Step 2: Create Account</em></a></li>
				<li class="lastDone"><a title=""><em>Step 3: Make Payment</em></a></li>
				<li class="current"><a title=""><em>Step 4: Confirm/Finish</em></a></li>
			</ul>
			<div class="clearfloat">&nbsp;</div>
		</div>
		
		
	
		</div>
    </div><!-- /.container -->    
    
  <?php //include 'inc_free_uk_page.php' ;?>
  <?php //include 'inc_footer_page.php' ;?>
<?php
	
	function send_pending_activation_email($user_obj, $user_id, $description, $site_country){
	
	$user_details = $user_obj->getUserInfoById($user_id);
	$email = $user_details["email"];
	$name = $user_details["name"];
	
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
           				<?php if($didOK == true){?>
                        	<h3>Your number successfully created!</h3>
                        	<br/>
                        	Your Virtual Number: <font color="green"><b><?php echo $did_number; ?></b></font>
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
	<?php unset($_SESSION["post_values"]) ; ?>
  </body>
</html>
