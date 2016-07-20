<?php 

$this_page = "home";

$sel_did_country_code = "";
$sel_did_city_code = "";
$sel_fwd_cntry = "";
$forwarding_number = "";
                	
if(isset($_POST["fwd_cntry"]) && trim($_POST["fwd_cntry"])!=""){
	$sel_fwd_cntry = trim($_POST["fwd_cntry"]);
}

if(isset($_POST["did_country_code"]) && trim($_POST["did_country_code"])!=""){
	$sel_did_country_code = trim($_POST["did_country_code"]);
}

if(isset($_POST["did_city_code"]) && trim($_POST["did_city_code"])!=""){
	$sel_did_city_code = trim($_POST["did_city_code"]);
}

if(isset($_POST["forwarding_number"]) && trim($_POST["forwarding_number"])!=""){
	$forwarding_number = trim($_POST["forwarding_number"]);
}

require("./dashboard/classes/Did_Cities.class.php");
$did_cities = new Did_Cities();
if(is_object($did_cities)==false){
  	die("Failed to create Did_Cities object.");
}

require("./dashboard/classes/SystemConfigOptions.class.php");
$sco = new System_Config_Options();
if(is_object($sco)==false){
  	die("Failed to create System_Config_Options object.");
}
$cheapest_city = $sco->getConfigOption('cheapest_did_city');
//$cheapest_city_au = $sco->getConfigOption('cheapest_did_city_au');
//$cheapest_city_gb = $sco->getConfigOption('cheapest_did_city_gb');
//$cheapest_city_us = $sco->getConfigOption('cheapest_did_city_us');

if($sel_did_country_code==""){
	$sel_did_country_code = $did_cities->getCheapestCountry($cheapest_city);
}

require("./dashboard/classes/Did_Countries.class.php");
require("./dashboard/classes/Country.class.php");
$did_countries_obj = new Did_Countries();
$did_countries = $did_countries_obj->getAvailableCountries();

$country = new Country();
$countries = $country->getAllCountry();
//var_dump($did_countries);
?>