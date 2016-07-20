<?php

	require("./includes/config.inc.php");
	require("./classes/DidwwAPI.class.php");
	
	echo "DIDWW API WSDL is: ".DIDWW_API_WSDL."<br>";

	$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
	if(is_object($client)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		echo $err_msg;
  		exit;
  	}
  	
  	//Now get the user_id and did from the query string
  	$user_id=trim($_GET["user_id"]);
  	$didww_order_id = trim($_GET["didww_order_id"]);
  	
  	if($user_id==""){
  		$err_msg = "Error: Required parameter user_id not passed.";
  		echo $err_msg;
  		exit;
  	}
  	
  	if($didww_order_id==""){
  		$err_msg = "Error: Required parameter didww_order_id not passed.";
  		echo $err_msg;
  		exit;
  	}
  	
  	//Now call getservicedetails to get the expire date in GMT
	$did_order = $client->getservicedetails($user_id,$didww_order_id);
	if(is_object($did_order)==false){
		$err_msg = $client->getError();
		echo $err_msg;
		exit;
	}
	if($did_order->result!=0){//Failed
		$err_msg = "Error: Failed to getservicedetails. Result is: $did_order->result, Error Code is: $client->getErrorCode(), Error is: $client->getErrorString()";
		echo $err_msg;
		exit;
	}
	$country_name = $did_order->country_name;
	$country_iso = $did_order->country_iso;
	$city_name = $did_order->city_name;
	$city_prefix = $did_order->city_prefix;
	$result_did_number = $did_order->did_number;
	$did_mapping_format = $did_order->did_mapping_format;
	$did_expiry = $did_order->did_expire_date_gmt;
	$didww_order_id = $did_order->order_id;
	$didww_order_status = $did_order->order_status;
	$didww_did_status = $did_order->did_status;
	$didww_did_setup = $did_order->did_setup;
	$didww_did_monthly = $did_order->did_monthly;

	echo "<br>Country Name is: ".$country_name;
	echo "<br>Country ISO is: ".$country_iso;
	echo "<br>City Name is: ".$city_name;
	echo "<br>City Prefix is: ".$city_prefix;
	echo "<br>DID Number is: ".$result_did_number;
	echo "<br>DID Mapping Format is: ".$did_mapping_format;
	echo "<br>Expiry Date is: ".$did_expiry;
	echo "<br>Order ID is: ". $didww_order_id;
	echo "<br>Order Status is: ".$didww_order_status;
	echo "<br>DID Status is: ".$didww_did_status;