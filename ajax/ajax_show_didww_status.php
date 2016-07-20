<?php	
	session_start();
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
//    require("../dashboard/classes/Worldpay_preapprovals.class.php");
	require("../dashboard/classes/DidwwAPI.class.php");
   // require("../dashboard/libs/worldpay.lib.php");
    
    $user_id = $_SESSION['user_logins_id'];//Catch userid in a variable - Even if the session expires while in this process we still have the user_id
    
    if($_SESSION['admin_flag']!=1 && $_SESSION['admin_flag']!=2){
    	echo "Error: Unauthorised to get DIDWW....";
    	exit;
    }
    
    //Validate Inputs
    
    if(isset($_POST['did_no']) && trim($_POST['did_no'])!=""){
    	$did_number = trim($_POST['did_no']);
    }else{
    	echo "Error: Required parameter did_no not passed.";
    	exit;
    }
	if(isset($_POST['customer_id']) && trim($_POST['customer_id'])!=""){
    	$customer_id = trim($_POST['customer_id']);
    }else{
    	echo "Error: Required parameter customer_id not passed.";
    	exit;
    }
    
	$dapi = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
	$ret = $dapi->getservicedetails($customer_id,0, $did_number);
	echo json_encode($ret);
	//echo json_encode(array("country_name" => "United States", "order_id" => "4291461", "did_expire_date_gmt" => "2015-12-03 17:07:48"));
	
	if($conn){
		mysql_close($conn);
	}
?>