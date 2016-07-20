<?php

require("/var/www/html/stickynumber/dashboard/includes/config.inc.php");
require("/var/www/html/stickynumber/dashboard/includes/db.inc.php");
require("/var/www/html/stickynumber/dashboard/classes/SystemConfigOptions.class.php");
require("/var/www/html/stickynumber/dashboard/classes/DidwwAPI.class.php");

$sco = new System_Config_Options();
if(is_object($sco)==false){
  	die("Failed to create System_Config_Options object.");
}

//Get the last time the regions were requested from DIDWW
$last_request_gmt = $sco->getDIDWWRegionsLastRequestGMT();
if($last_request_gmt==0){
	$last_request_gmt = null;
}

echo "Last Request GMT is: ".$last_request_gmt."<br /><br />";
echo "Current Time GMT is: ".time()."<br /><br />";

//echo DIDWW_API_WSDL.", ".DIDWW_USER.". ".DIDWW_PASS."<BR>";
$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
if(is_object($client)==false){
  	die("Failed to create DidwwApi object.");
}

$did_country_city = $client->getRegions(0,0,$last_request_gmt);

if(is_array($did_country_city)){//success
	print_r($did_country_city);
}else{
	echo "Failed to get regions from DIDWW.";
}

