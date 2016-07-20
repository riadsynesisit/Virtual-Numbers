<?php

	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Did_Cities.class.php");
    
    $city_id = "";
    
    //Check for city id
    if(isset($_POST["city_id"])==false || trim($_POST["city_id"])==""){
    	echo "Error: Required parameter DID City id not passed";
    	exit;
    }else{
    	$city_id = trim($_POST["city_id"]);
    }
    
    $did_cities = new Did_Cities();
	if(is_object($did_cities)==false){
	  	echo "Error: Failed to create Did_Cities object.";
    	exit;
	}
	
	$plan_prices = $did_cities->getPlanPricesForCityId($city_id);
	
	echo $plan_prices["sticky_monthly"].",".$plan_prices["sticky_monthly_plan1"].",".$plan_prices["sticky_monthly_plan2"].",".$plan_prices["sticky_monthly_plan3"];