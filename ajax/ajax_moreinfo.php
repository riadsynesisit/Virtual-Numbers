<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Risk_score.class.php");
    require("../dashboard/classes/DCMDestinationCosts.class.php");
    
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id
    
    if($_SESSION['admin_flag']!=1 && $_SESSION['admin_flag']!=2){
    	echo "Error: Unauthorised to access moreinfo data.";
    	exit;
    }
    
    $cart_id = "";
    if(isset($_POST["cart_id"])==false || trim($_POST["cart_id"])==""){
    	echo "Error: Required parameter cart_id not passed.";
    	exit;
    }else{
    	$cart_id = trim($_POST["cart_id"]);
    }
    
    $risk_score = new Risk_score();
    
    if(is_object($risk_score)==false){
    	echo "Error: Failed to create Risk_score object.";
    	exit;
    }
    
    $risk_info = $risk_score->get_risk_info($cart_id);
    if(is_array($risk_info)==false && substr($risk_info,0,6)=="Error:"){
    	echo $risk_info;
    	exit;
    }
    
    if($risk_info["high_purchase_activity"]=="Y"){
    	$high_purchase_activity = 5;
    }else{
    	$high_purchase_activity = 0;
    }
    
    if($risk_info["country_blacklisted"]=="Y"){
    	$country_black_listed = 3;
    }else{
    	$country_black_listed = 0;
    }
    
    if($risk_info["ip_not_matches_country"]=="Y"){
    	$ip_not_matches_country = 2;
    }else{
    	$ip_not_matches_country = 0;
    }
    
    if($risk_info["free_provider_email"]=="Y"){
    	$free_provider_email = 1;
    }else{
    	$free_provider_email = 0;
    }
    
    if($risk_info["ip_blacklisted"]=="Y"){
    	$ip_blacklisted = 1;
    }else{
    	$ip_blacklisted = 0;
    }
    
    if($risk_info["fwd_num_not_match_site"]=="Y"){
    	$fwd_num_not_match_site = 2;
    }else{
    	$fwd_num_not_match_site = 0;
    }
    
    if($risk_info["multiple_fwd_numbers"]=="Y"){
    	$multiple_fwd_numbers = 1;
    }else{
    	$multiple_fwd_numbers = 0;
    }
    
    if($risk_info["new_member"]=="Y"){
    	$new_member = 1;
    }else{
    	$new_member = 0;
    }
    
    if($risk_info["order_over_20_dollars"]=="Y"){
    	$order_over_20_dollars = 2;
    }else{
    	$order_over_20_dollars = 0;
    }
    
    if($risk_info["over_2_nums_in_7_days"]=="Y"){
    	$over_2_nums_in_7_days = 3;
    }else{
    	$over_2_nums_in_7_days = 0;
    }
    
    if($risk_info["over_1_acct_credit_in_7_days"]=="Y"){
    	$over_1_acct_credit_in_7_days = 3;
    }else{
    	$over_1_acct_credit_in_7_days = 0;
    }
    
    $risk_score_total = $high_purchase_activity +
    					$country_black_listed + 
    					$ip_not_matches_country + 
    					$free_provider_email + 
    					$ip_blacklisted + 
    					$fwd_num_not_match_site + 
    					$multiple_fwd_numbers + 
    					$new_member + 
    					$order_over_20_dollars + 
    					$over_2_nums_in_7_days + 
    					$over_1_acct_credit_in_7_days;
    					
    if($risk_info["forwarding_type"]==1){//For PSTN Forwarding
	    //Get the forwarding destination name
	    $dest_costs = new DCMDestinationCosts();
	    if(is_object($dest_costs)==false){
	    	echo "Error: Failed to create DCMDestinationCosts object.";
	    	exit;
	    }
	    if(trim($risk_info["fwd_number"])!=""){
	    	$destination = $dest_costs->get_destination_from_number($risk_info["fwd_number"]);
	    }else{
	    	$destination = "Unknown";
	    }
    }else{
    	$destination = "SIP Forwarding";
    }
    
  	$moreinfo = "<div style='float:left;'>";
  	$moreinfo .= "<div>Signup Site:</div>";
  	$moreinfo .= "<div>IP Location:</div>";
  	$moreinfo .= "<div>IP Address:</div>";
  	$moreinfo .= "<div>Member ID:</div>";
  	$moreinfo .= "<div>Member Name:</div>";
  	$moreinfo .= "<div>Member Email:</div>";
  	if(trim($risk_info["pymt_gateway"])=="PayPal"){
  		$moreinfo .= "<div>PayPal Sender Email:</div>";
  	}
  	$moreinfo .= "<div>Member Address:</div>";
  	$moreinfo .= "<div>Member City:</div>";
  	$moreinfo .= "<div>Forwarding To:</div>";
  	$moreinfo .= "<div>Cart ID:</div>";
  	$moreinfo .= "<div>&nbsp;</div>";
  	$moreinfo .= "</div>";
    $moreinfo .= "<div style='float:left;'>";
    $moreinfo .= "<div>&nbsp;StickyNumber.com.au</div>";
    $moreinfo .= "<div>&nbsp;".$risk_info["ip_location"]."</div>";
    $moreinfo .= "<div>&nbsp;".$risk_info["ip_address"]."</div>";
    $moreinfo .= "<div>&nbsp;".$risk_info["member_id"]."</div>";
    $moreinfo .= "<div>&nbsp;".$risk_info["name"]."</div>";
    $moreinfo .= "<div>&nbsp;".$risk_info["email"]."</div>";
    if(trim($risk_info["pymt_gateway"])=="PayPal"){
    	$moreinfo .= "<div>&nbsp;".$risk_info["pp_sender_mail"]."</div>";
    }
    $moreinfo .= "<div>&nbsp;".$risk_info["address1"].", ".$risk_info["address2"]."</div>";
    $moreinfo .= "<div>&nbsp;".$risk_info["user_city"].", ".$risk_info["user_state"].", ".$risk_info["user_country"].", ".$risk_info["postal_code"]."</div>";
    $moreinfo .= "<div>&nbsp;".$risk_info["fwd_number"]."($destination)</div>";
    $moreinfo .= "<div>&nbsp;".$cart_id."</div>";
    $moreinfo .= "<div>&nbsp;</div>";
    $moreinfo .= "</div>";
    
    $moreinfo .= "<div style='float:left;'>";
    $moreinfo .= "<div>High Purchase Activity:</div>";
    $moreinfo .= "<div>Member's country is in the black list:</div>";
    $moreinfo .= "<div>IP does NOT match member's country:</div>";
    $moreinfo .= "<div>Email account from free provider:</div>";
    $moreinfo .= "<div>IP in blacklist:</div>";
    $moreinfo .= "<div>Forwarding number does NOT match signup site:</div>";
    $moreinfo .= "<div>Multiple forwarding numbers found:</div>";
    $moreinfo .= "<div>New member:</div>";
    $moreinfo .= "<div>Order's total exceeds $20:</div>";
    $moreinfo .= "<div>More than 2 numbers added within 7 days:</div>";
    $moreinfo .= "<div>More than 1 account credit added within 7 days:</div>";
    $moreinfo .= "<div>&nbsp;</div>";
    $moreinfo .= "<div>Risk Score Total:</div>";
    $moreinfo .= "</div>";
    
    $moreinfo .= "<div style='float:left;'>";
    $moreinfo .= "<div>&nbsp;".$high_purchase_activity."</div>";
    $moreinfo .= "<div>&nbsp;".$country_black_listed."</div>";
    $moreinfo .= "<div>&nbsp;".$ip_not_matches_country."</div>";
    $moreinfo .= "<div>&nbsp;".$free_provider_email."</div>";
    $moreinfo .= "<div>&nbsp;".$ip_blacklisted."</div>";
    $moreinfo .= "<div>&nbsp;".$fwd_num_not_match_site."</div>";
    $moreinfo .= "<div>&nbsp;".$multiple_fwd_numbers."</div>";
    $moreinfo .= "<div>&nbsp;".$new_member."</div>";
    $moreinfo .= "<div>&nbsp;".$order_over_20_dollars."</div>";
    $moreinfo .= "<div>&nbsp;".$over_2_nums_in_7_days."</div>";
    $moreinfo .= "<div>&nbsp;".$over_1_acct_credit_in_7_days."</div>";
    $moreinfo .= "<div>&nbsp;</div>";
    $moreinfo .= "<div>&nbsp;".$risk_score_total."</div>";
    $moreinfo .= "</div>";
    
    echo $moreinfo;