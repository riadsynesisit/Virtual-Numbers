<?php
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Did_Cities.class.php");
    
    //Inputs validation
	$country_iso = trim($_POST['country_iso']);
	if($country_iso==""){
		echo "Error: Required parameter country_iso not posted.";
		exit;
	}
	
	$did_cities = new Did_Cities();
	if(is_object($did_cities)!=true){
		echo "Error: Failed to create did_cities object.";
		exit;
	}
	
	$cheapest_did_city_id = $did_cities->getCheapestCityId($country_iso);
	
	if($conn){
		//echo $conn;
		mysql_close($conn);
	}
		
	/*if(substr($cheapest_did_city_id,0,6)=="Error:"){
		echo $cheapest_did_city_id;
		exit;
	}*/
	
	echo $cheapest_did_city_id;