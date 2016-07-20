<?php

require("./includes/DRY_setup.inc.php");

if($_SESSION['admin_flag']!=1){
	echo "Please login as admin to access this page";
	exit;
}

if(isset($_POST['APC_UPLOAD_PROGRESS']) && $_POST['APC_UPLOAD_PROGRESS']=="4e78d40e25711"){
	processDIDPricesFile();
	exit;
}

function processDIDPricesFile(){
	
	$fname = $_FILES['did_csv_file']['name'];
    $chk_ext = explode(".",$fname);
    if(strtolower($chk_ext[1]) == "csv"){
		$target_path = "_uploads/".basename( $_FILES['did_csv_file']['name']);
		 if(move_uploaded_file($_FILES['did_csv_file']['tmp_name'], $target_path)){
		 	$did_cities = new Did_Cities();
			if(is_object($did_cities)==false){
				die("Failed to create Did_Cities object.");
			}
		 	$status = $did_cities->processDIDPricesCSVFile($target_path);
		 	if($status===true){
		 		echo "Your file was successfully uploaded.<br><br>Please wait... while you are being redirected...";
		 		header("Location: admin_dest_costs_mgr.php?action_msg=Your file was successfully uploaded.");
		 	}else{
		 		echo "ERROR: Failed to process your uploaded CSV file. Please try again.";
		 	}
		 }else{
		 	echo "ERROR: The file upload was NOT successful. Please try again.";
		 }
    }else{
    	 echo "ERROR: INVALID file Type. Only CSV files are accepted.";
    }
	
}

$did_cities = new Did_Cities();
if(is_object($did_cities)==false){
	die("Failed to create Did_Cities object.");
}
$status = true;//$did_cities->populateDID_Country_City();

if($status==true){
	//Create the CSV file
	if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="localhost"){
		$file_location_name = getcwd()."/did_costs.csv";
	}else{
		$file_location_name = "/tmp/did_costs.csv";
	}
	$status = $did_cities->getDIDCostsCSV($file_location_name);
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
			echo "CSV File not found";
		}
	}else{
		echo "Failed to create the CSV file";
	}
}else{
	echo "Failed to populate the DID countries and cities";
}