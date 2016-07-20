<?php
require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
	if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
		if(isset($_GET["cartId"]) && trim($_GET["cartId"])!=""){
			$cartId = trim($_GET["cartId"]);
			new_did_success_page($this_page, $cartId);
		}else{
			$this_page->content_area = "";
			$this_page->content_area .= "Error: Required parameter cartId not passed.";
			common_header($this_page,"Missing Parameter - cartId");
		}
	}else{
		//Jump to login screen
		$this_page->meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS+12;
		$this_page->meta_refresh_page = "login.php";
		
		$this_page->content_area = "";
		$this_page->content_area .= "We're sorry, you must be logged in as a verified user to access this service. </ br>\n";
		$this_page->content_area .= "We will now  try to automatically take you to the login page.  If nothing seems to happen in a few seconds, just click <a href='login.php'>this link</a> and we will take you there. </ br>\n"; 
		
		common_header($this_page, "Not Logged In - Login Required", false, $meta_refresh_sec, $meta_refresh_page);
	}
	
}

function new_did_success_page($this_page, $cartId){
	
	$blnError=false;
	
	$risk_score_obj = new Risk_score();
    if($blnError==false && is_object($risk_score_obj)==false){
    	$blnError = true;
    	$errMsg = "Error: Failed to create risk_score object.";
    }
	
    if($blnError==false){
		//Get shopping_cart details
		$shopping_cart = new Shopping_cart();
		if(is_object($shopping_cart)==false){
			$blnError = true;
			$errMsg = "Error: Failed to create Shopping_cart object";
		}
    }
	
	$cart_details = "";
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
    $user_details = $user->getUserInfoById(trim($_SESSION['user_logins_id']));
	
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
				switch($cart_details["purchase_type"]){
					case "new_did":
						$did_details = processDIDPurchase($cart_details["user_id"], $cart_details["did_plan"], $cart_details["plan_period"], $cart_details["num_of_months"], $cart_details["purchase_type"], $cart_details["pymt_type"], $cart_details["did_id"], $cart_details["id"], $cart_details["country_iso"], $cart_details["city_prefix"], $fwd_number, $cart_details["forwarding_type"]);
						break;
					case "renew_did":
						$did_details = processDIDPurchase($cart_details["user_id"], $cart_details["did_plan"], $cart_details["plan_period"], $cart_details["num_of_months"], $cart_details["purchase_type"], $cart_details["pymt_type"], $cart_details["did_id"], $cart_details["id"]);
						break;
					case "restore_did":
						$did_details = processDIDPurchase($cart_details["user_id"], $cart_details["did_plan"], $cart_details["plan_period"], $cart_details["num_of_months"], $cart_details["purchase_type"], $cart_details["pymt_type"], $cart_details["did_id"], $cart_details["id"]);
						break;
					default:
						$blnError = true;
						$errMsg = "Error: Unknown purchase_type '".$cart_details["purchase_type"]."' in shopping_cart for id $cartId";
						break;
				}
			}else{
				//Check if this order is authorized but is pending for risk score exceeding allowed threshold limit.
				$order_status = $risk_score_obj->get_order_status($cartId);
				if(substr($order_status,0,6)=="Error:"){
					$blnError = true;
					$errMsg = $order_status;
				}elseif(intval($order_status)==5){//Authorized order - In pending status
					$did_details = array("did_number"=>"Pending Allocation", "num_of_minutes"=>"Pending", "did_expiry"=>"Pending", "assigned_did_id"=>"Pending");
					//Send Pending Activation Email from here
					$status = send_pending_activation_email(trim($user_details["email"]),trim($user_details["name"]),trim($_SESSION['user_logins_id']),$cart_details["description"],trim($user_details["site_country"]));
					if(is_bool($status) && $status === true){
						//Email successfully sent
					}
				}elseif(intval($order_status)==0){//Pending Order - Not supposed to be in this status - Expects order_status=5 if authorized by payment gateway but is pending for high risk score
					$blnError = true;
					$errMsg = "Error: Failed to get payment gateway authorization of this order with cart id: ".$cartId;
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
		if($cart_details["purchase_type"]=="renew_did" || $cart_details["purchase_type"]=="restore_did"){
			$did_number = $cart_details["did_number"];
		}else{
			$did_number = $did_details["did_number"];
		}
		$num_of_minutes = $did_details["num_of_minutes"];
		$did_expiry = $did_details["did_expiry"];
		$assigned_did_id = $did_details["assigned_did_id"];
		
		//Update the fulfilled status
		if($cart_details["fulfilled"]!="Y" && substr($assigned_did_id,0,7)!="Pending"){
			if($cart_details["purchase_type"]=="renew_did" || $cart_details["purchase_type"]=="restore_did"){
				//For a renewal or restore order check for DID_ID
				if(isset($assigned_did_id)==false || trim($assigned_did_id)==""){
					$blnError = true;
					$errMsg = "Error: DID_ID Missing for cartId: $cartId in new_did page.";
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
		}else{
			$status = "success";
		}
		
	}
	if($blnError==false && $status != "success"){
		$blnError = true;
		$errMsg = $status;
	}
	
	if($blnError==false && substr($assigned_did_id,0,7)!="Pending"){
		//Update the preapprovalKey in assigned_dids table - used during auto renewals
		$assigned_dids = new Assigned_DID();
		if(is_object($assigned_dids)==false){
			$blnError = true;
			$errMsg = "Error: Could not create Assigned_DID object.";
		}
		$status = $assigned_dids->updatePreapprovalKey($assigned_did_id,$cartId);
		if(is_bool($status)==false && substr($status,0,6)=="Error:"){
			$blnError = true;
			$errMsg = $status;
		}
	}
	
	if($blnError==false && substr($assigned_did_id,0,7)!="Pending" && $status !==true){
		$blnError = true;
		$errMsg = $status;
	}
	
	$page_vars = array();
		  
	$page_html = "";
	
	$page_html .= '
		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add New Number</h1></div>
                	<div class="row">                
                		<div class="col-lg-12">
							<div class="contentbox">
                            	<div class="contentbox-head">
                                	<div class="content-breadbox">
                                    	<ul class="breadcrumb2">
                                        	<li><a class="disable-breadcrumb" href="#">Forwarding Number</a></li>
                                            <li><a href="#" class="disable-breadcrumb">Choose Plan</a></li>
                                            <li><a href="#" class="disable-breadcrumb">Payment</a></li>
                                            <li><a href="#" class="breadcrumb-page">Finish</a></li>
                                        </ul>
                                    </div>
                                <div class="clearfix"></div>
                                <div class="row">
                                	<div class="col-md-6 col-md-offset-3">
                                    	<div class="alert alert-success  Mtop20">';
											if($blnError==true && trim($errMsg)!=""){
												$page_html .= 'An error occurred while processing your request.<br /><br /><font color="red">'.trim($errMsg).'</font>';
											}else{
												$page_html .= '<h1 class="txt-c">Thank You!</h1><br>';
												if(substr($assigned_did_id,0,7)!="Pending"){
													
												}else{
													$page_html.='<p class="txt-red"><strong>Your Order is being procesed.
													You will get an activiation email Shortly.</strong></p><br>';
												}
												$page_html.='<p>Contact us 24/7 About your order or if you have any other enquires.
												Please Check your email for order confirmation and Invoice</p>';
											}
$page_html.='</div>                                        
   </div>  </div>                                                                                
                                 <!-- Personal Details closed -->         
                                          
                                          
                                     </div>
								</div>                    
                    </div>
                    <!-- /.col-12 -->                 
                </div><!-- /.row -->
            </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
	';
	
    $this_page->content_area .= $page_html;
	$page_vars['menu_item'] = 'add_new_number';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	common_header($this_page,"New DID Success");
	
}//end of function new_did_success_page

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

require("includes/DRY_authentication.inc.php");

?>