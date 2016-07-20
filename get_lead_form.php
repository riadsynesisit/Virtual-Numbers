<?php

require("./dashboard/includes/config.inc.php");
require("./dashboard/includes/db.inc.php");
require("./dashboard/classes/Lead_accounts.class.php");

if(isset($_GET["id"])==false || trim($_GET["id"])==""){
	echo "Error: Required parameter 'id' not passed.";
	exit;
}

$user_id = trim($_GET["id"]);

//Now check if the id passed is numeric
if(filter_var($user_id,FILTER_VALIDATE_INT)==false){
	echo "Error: Invalid format of id :".$user_id;
	exit;
}

$lead_accounts_obj = new Lead_accounts();
if(is_object($lead_accounts_obj)==false){
	echo "Error: Failed to create Lead Accounts object.";
	exit;
}

//Now check if a lead account exists for the passed in id
$lead_form_code = $lead_accounts_obj->get_lead_form_code($user_id);
if(substr($lead_form_code,0,6)=="Error:"){
	echo $lead_form_code;
	exit;
}elseif($lead_accounts_obj==0){
	echo "Error: No lead form forund for id: ".$user_id;
	exit;
}else{
	echo $lead_form_code;
	exit;
}