<?php
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/SystemConfigOptions.class.php");
    require("../dashboard/classes/Unassigned_087DID.class.php");
    require("../dashboard/classes/Did_Cities.class.php");
    require("../dashboard/classes/Member_credits.class.php");
    require("../dashboard/classes/DidwwAPI.class.php");
    require("../dashboard/classes/Assigned_DID.class.php");
    require("../dashboard/classes/Payments_received.class.php");
    require("../dashboard/classes/Access.class.php");
    require("../dashboard/classes/VoiceMail.class.php");
    require("../dashboard/classes/Did_Plans.class.php");
    require("../dashboard/classes/Did_Countries.class.php");
    require("../dashboard/classes/AST_RT_Extensions.class.php");
    require("../dashboard/classes/Shopping_cart.class.php");
    require("../dashboard/classes/Risk_score.class.php");
    require("../dashboard/classes/User.class.php");
    require("../dashboard/libs/didww.lib.php");
    require("../dashboard/libs/utilities.lib.php");
    require("../dashboard/libs/worldpay.lib.php");  
    
    $cart_id = "";
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id

    if(isset($_POST["cart_id"])==false || trim($_POST["cart_id"])==""){
    	echo "Error: Required parameter cart_id not passed.";
    	exit;
    }else{
    	$cart_id = trim($_POST["cart_id"]);
    }
    if(isset($_POST["city_id"])==false || trim($_POST["city_id"])==""){
    	echo "Error: Required parameter city_id not passed.";
    	exit;
    }
    
    if($_POST["purchase_type"]=="new_did"){
    	$status = validateNewDIDInputs($user_id, trim($_POST["country_iso"]), trim($_POST["city_id"]), trim($_POST["forwarding_type"]), trim($_POST["fwd_number"]), trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]));
    }else{
    	$status = validateRenewOrRestoreInputs($user_id, trim($_POST["choosen_plan"]), trim($_POST["plan_period"]), trim($_POST["num_of_months"]), trim($_POST["did_id"]));
    }
    if($status!==true){
    	echo $status;
    	exit;
    }
    
    $status = chargeAccountCredit($user_id, trim($_POST["choosen_plan"]), trim($_POST["country_iso"]), trim($_POST["city_id"]));
    if($status!==true){
    	echo $status;
    	exit;
    }
    
    //Create a payments_received record from here
    $status = create_payments_received_record($cart_id);
    if(substr($status,0,6)=="Error:"){
		echo $status;
		exit;
	}
    
    //Update the shopping_cart record
	$status = "";
	$shopping_cart = new Shopping_cart();
	if(is_object($shopping_cart)==false){
		echo "Failed to create shopping cart object.";
		exit;
	}
	if($_POST["purchase_type"]=="new_did"){
		$status = $shopping_cart->update_paid($cart_id, "AccountCredit", "COMPLETED", date('Y-m-d H:i:s'), $ipAddress);
	}else{
		//For renewals or restore by account credit do not alter the transaction details
		$status = $shopping_cart->update_paid_renewals($cart_id, date('Y-m-d H:i:s'), $ipAddress);
	}
 
	if($status=="success"){
		echo "Success";
		exit;
	}
	
?>