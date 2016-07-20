<?php

require("./includes/DRY_setup.inc.php");

if($_SESSION['admin_flag']!=1){
	echo "Please login as admin to access this page";
	exit;
}

$dcm_destination_costs = new DCMDestinationCosts();
if(is_object($dcm_destination_costs)==false){
	die("Error: Failed to create DCMDestinationCosts object.");
}
//Create the CSV file
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="localhost"){
	$file_location_name = getcwd(). DIRECTORY_SEPARATOR . "sip_costs.csv";
}else{
	$file_location_name = "/tmp/sip_costs.csv";
}
$status = $dcm_destination_costs->getSIPCostsCSV($file_location_name);
if($status==true){
		if (file_exists($file_location_name)){
			// send the right headers
			header("Content-Type: text/csv");
			header('Content-Disposition: attachment; filename='.basename($file_location_name));
			header("Content-Length: " . filesize($file_location_name));
			ob_clean();// clean the output buffer
    		flush();
    		readfile($file_location_name);//dump the file to the output
			unlink($file_location_name);//When done remove the file
		}else{
			echo "SIP COSTS CSV File not found";
		}
}else{
	echo "Error: Failed to create SIP Costs CSV file";
}