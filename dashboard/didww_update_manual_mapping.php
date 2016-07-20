<?php

require("./includes/config.inc.php");
require("./classes/DidwwAPI.class.php");
require("./libs/didww.lib.php");

//Manually set the following variables;

//Now get the user_id and did from the query string
$user_id=trim($_GET["user_id"]);
$did_number = trim($_GET["did_number"]);
  	
if($user_id==""){
  	$err_msg = "Error: Required parameter user_id not passed.";
  	echo $err_msg;
  	exit;
}
  	
if($did_number==""){
  	$err_msg = "Error: Required parameter did_number not passed.";
  	echo $err_msg;
  	exit;
}

$status = updateDIDmapping($user_id, $did_number);

echo $status;