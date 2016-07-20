<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Shopping_cart.class.php");
    
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id
    
    $shopping_cart = new Shopping_cart();
	if(is_object($shopping_cart)==false){
		echo "Error: Failed to create Shopping_cart object.";
		exit;
	}
	
	//Validate Inputs
	$amount = "";
	if(isset($_POST['amount']) && trim($_POST['amount'])!=""){
    	$amount = trim($_POST['amount']);
    }
    if($amount==""){
    	echo "Error: Required parameter amount not passed.";
    	exit;
    }
    
    $purchase_type = "";
    $pymt_type = "";
    if(isset($_POST['purchase_type']) && trim($_POST['purchase_type'])!=""){
    	$purchase_type = trim($_POST['purchase_type']);
    	switch($purchase_type){
    		case "new_did":
    			$pymt_type = "DIDPurchase";
    			break;
    		case "renew_did":
    			$pymt_type = "DIDRenew";
    			break;
    		case "restore_did":
    			$pymt_type = "DIDRestore";
    			break;
    		case "account_credit":
    			$pymt_type = "AccountCredit";
    			break;
    		case "leads_tracking":
    			$pymt_type = "LeadsTracking";
    			break;
    		default:
    			echo "Error: Unknown value of parameter purchase_type $purchase_type.";
    			exit;
    	}
    }
    if($purchase_type==""){
    	echo "Error: Required parameter purchase_type not passed.";
    	exit;
    }
    
    $pymt_gateway = "";
    if(isset($_POST["pymt_gateway"]) && trim($_POST["pymt_gateway"])!=""){
    	$pymt_gateway = trim($_POST["pymt_gateway"]);
    }
    if($pymt_gateway==""){
    	echo "Error: Required parameter pymt_gateway not passed.";
    	exit;
    }
    
    $did_id = "";
    $did_number = "";
    if(trim($purchase_type)=="renew_did" || trim($purchase_type) == "restore_did"){
	    if(isset($_POST['did_id']) && trim($_POST['did_id'])!=""){
	    	$did_id = trim($_POST['did_id']);
	    }else{
	    	echo "Error: Required parameter did_id not passed.";
    		exit;
	    }
    	if(isset($_POST['did_number']) && trim($_POST['did_number'])!=""){
	    	$did_number = trim($_POST['did_number']);
	    }else{
	    	echo "Error: Required parameter did_number not passed.";
    		exit;
	    }
    }
    
    $did_plan = "";
    if(isset($_POST['did_plan']) && trim($_POST['did_plan'])!=""){
    	$did_plan = trim($_POST['did_plan']);
    }else{
    	$did_plan = 'NULL';
    }
    $plan_period = "";
    if(isset($_POST['plan_period']) && trim($_POST['plan_period'])!=""){
    	$plan_period = trim($_POST['plan_period']);
    }else{
    	$plan_period = 'NULL';
    }
    $num_of_months = "";
    if(isset($_POST['num_of_months']) && trim($_POST['num_of_months'])!=""){
    	$num_of_months = trim($_POST['num_of_months']);
    }else{
    	$num_of_months = 'NULL';
    }
    $description = "";
    if(isset($_POST['description']) && trim($_POST['description'])!=""){
    	$description = trim($_POST['description']);
    }else{
    	$description = NULL;
    }
    $country_iso = "";
    if(isset($_POST['country_iso']) && trim($_POST['country_iso'])!=""){
    	$country_iso = trim($_POST['country_iso']);
    }else{
    	$country_iso = 'NULL';
    }
    $city_prefix = "";
    if(isset($_POST['city_prefix']) && trim($_POST['city_prefix'])!=""){
    	$city_prefix = trim($_POST['city_prefix']);
    }else{
    	$city_prefix = 'NULL';
    }
    $fwd_number = "";
    if(isset($_POST['fwd_number']) && trim($_POST['fwd_number'])!=""){
    	$fwd_number = trim($_POST['fwd_number']);
    }else{
    	$fwd_number = 'NULL';
    }
    $return_to = "";
    if(isset($_POST['return_to']) && trim($_POST['return_to'])!=""){
    	$return_to = trim($_POST['return_to']);
    }else{
    	echo "Error: Required parameter return_to not passed.";
    	exit;
    }
    
    $forwarding_type = "";
    if(isset($_POST['forwarding_type']) && trim($_POST['forwarding_type'])!=""){
    	$forwarding_type = trim($_POST['forwarding_type']);
    	if(intval($forwarding_type)==2){
    		$fwd_number = 'NULL';
    		$sip_address = trim($_POST['fwd_number']);//fwd_number posted value holds the sip_address in case of Sip Forwarding
    	}elseif(intval($forwarding_type)==1){
    		$fwd_number = trim($_POST['fwd_number']);
    		$sip_address = 'NULL';
    	}else{
    		echo "Error: Unknow forwarding_type = $forwarding_type posted";
    		exit;
    	}
    }else{
    	$forwarding_type = 1;//Set as 1=PSTN forwarding by default - if this value is NOT posted.
    	$sip_address = 'NULL';
    }
	
	$cart_id = $shopping_cart->add_to_shopping_cart($user_id, $amount, $purchase_type, $pymt_type, $pymt_gateway, $did_plan, $plan_period, $num_of_months, $description, $country_iso, $city_prefix, $fwd_number, $return_to, $did_id, $did_number, $forwarding_type, $sip_address);
	
	
		if($conn){
			//echo $conn;
			mysql_close($conn);
		}
	

	if(is_numeric($cart_id)){
		echo $cart_id;
	}else{
		echo $cart_id;
	}