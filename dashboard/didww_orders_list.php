<?php

	require("./includes/config.inc.php");
	require("./classes/DidwwAPI.class.php");
	
	echo "DIDWW API WSDL is: ".DIDWW_API_WSDL."<br>";

	$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
	if(is_object($client)==false){
  		$err_msg = "Error: Failed to create DidwwApi object.";
  		echo $err_msg;
  		exit;
  	}else{
  		echo "DIDWW Client Object created.<br />";
  	}
  	
  	//Now get the user_id from the query string
  	$user_id=trim($_GET["user_id"]);
  	
  	if($user_id==""){
  		$err_msg = "Error: Required parameter user_id not passed.";
  		echo $err_msg;
  		exit;
  	}else{
  		echo "User ID passed is: $user_id <br />";
  	}
  	
  	//Now call getservicelist to get the orderids
	$did_orders = $client->getservicelist($user_id);
	if(is_null($did_orders)==true){
		echo "Error: Get Service List returned NULL. <br />";
		exit;
	}else{
		echo "Call to Get Service List Completed. <br />";
	}
	
	if(is_array($did_orders)==false){
		echo "Error:Get Service Details retuned value is NOT an array.<br />";
		exit;
	}
	
	$counter = 0;
	foreach($did_orders as $did_order){
		$counter = $counter + 1;
		echo "<br /><br />Order : ". $counter;
		echo "<br />Result is: ".$did_order->result;
		echo "<br />Country Name is: ".$did_order->country_name;
		echo "<br />Country ISO is: ".$did_order->country_iso;
		echo "<br />City Name is: ".$did_order->city_name;
		echo "<br />City Prefix is: ".$did_order->city_prefix;
		echo "<br />City ID is: ".$did_order->city_id;
		echo "<br />DID Number is: ".$did_order->did_number;
		echo "<br />DID Status is: ".$did_order->did_status;
		echo "<br />DID Timeleft is: ".$did_order->did_timeleft;
		echo "<br />DID Expire Date GMT is: ".$did_order->did_expire_date_gmt;
		echo "<br />DIDWW Order ID is: ".$did_order->order_id;
		echo "<br />DIDWW Order Status is: ".$did_order->order_status;
		echo "<br />DID Mapping Format is: ".$did_order->did_mapping_format;
		echo "<br />Setup Cost is: ".$did_order->did_setup;
		echo "<br />Monthly Cost is: ".$did_order->did_monthly;
		echo "<br />DID Period is: ".$did_order->did_period;
	}