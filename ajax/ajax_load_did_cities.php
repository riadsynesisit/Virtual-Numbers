<?php
	$referer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
	if( $referer != 'localhost' && !strstr(trim( $referer ),'stickynumber') ){
		header('Location: ../');
		exit;
	}
    require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Did_Cities.class.php");
    
    $country_iso = $_GET['country_iso'];
    $did_cities_obj = new Did_Cities();
    
    $did_cities = $did_cities_obj->getAvailableCities($country_iso);
    
	if($conn){
		//echo $conn;
		mysql_close($conn);
	}
    echo json_encode($did_cities);