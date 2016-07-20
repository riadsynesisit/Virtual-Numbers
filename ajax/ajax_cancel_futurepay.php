<?php	
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Paypal_preapprovals.class.php");
    require("../dashboard/classes/Worldpay_preapprovals.class.php");
	require("../dashboard/classes/SimplePay_preapprovals.class.php");
    require("../dashboard/libs/worldpay.lib.php");
    
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id
    
    if($_SESSION['admin_flag']!=1 && $_SESSION['admin_flag']!=2){
    	echo "Error: Unauthorised to cancel future pay agreements.";
    	exit;
    }
    
    //Validate Inputs
    $pymt_gtwy = "";
    
    if(isset($_POST['pymt_gtwy']) && trim($_POST['pymt_gtwy'])!=""){
    	$pymt_gtwy = trim($_POST['pymt_gtwy']);
    }else{
    	echo "Error: Required parameter pymt_gtwy not passed.";
    	exit;
    }
    
    if($pymt_gtwy!="PayPal" && $pymt_gtwy!="WorldPay" && $pymt_gtwy!="Credit_Card"){
    	echo "Error: Unknown payment gateway : $pymt_gtwy";
    	exit;
    }
    
    $did_id = "";
    
    if(isset($_POST['did_id']) && trim($_POST['did_id'])!=""){
    	$did_id = trim($_POST['did_id']);
    }else{
    	echo "Error: Required parameter did_id not passed.";
    	exit;
    }
    
    $cancel_reason = "";
    if(isset($_POST['cancel_reason']) && trim($_POST['cancel_reason'])!=""){
    	$cancel_reason = trim($_POST['cancel_reason']);
    }else{
    	echo "Error: Required parameter cancel_reason not passed.";
    	exit;
    }
    
    //Now validate if the future pay ID exists and not already cancelled
    switch($pymt_gtwy){
    	case "PayPal":
			    $pp_preapprovals = new Paypal_preapprovals();
			    if(is_object($pp_preapprovals)==false){
			    	echo "Error: Failed to create Paypal_preapprovals object.";
			    	exit;
			    }
			    $status = $pp_preapprovals->cancel_preapproval($did_id);
			    echo $status;
			    exit;
    		break;
    	case "WorldPay":
    			$wp_preapprovals = new Worldpay_preapprovals();
    			if(is_object($wp_preapprovals)==false){
    				echo "Error: Failed to create Worldpay_preapprovals object.";
    				exit;
    			}
    			//Need to call iadmin here
    			$status = $wp_preapprovals->cancel_preapproval($did_id,$cancel_reason);
    			echo $status;
    			exit;
    		break;
		case "Credit_Card":
    			$sp_preapprovals = new SimplePay_preapprovals();
    			if(is_object($sp_preapprovals)==false){
    				echo "Error: Failed to create Worldpay_preapprovals object.";
    				exit;
    			}
    			//Need to call iadmin here
    			$status = $sp_preapprovals->cancel_preapproval($did_id,$cancel_reason);
    			echo $status;
    			exit;
    		break;
    }//End of Switch case
    