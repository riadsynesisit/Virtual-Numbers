<?php
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Auto_Renewal_Failures.class.php");
    
    
    
    if(isset($_GET["id"])==false || trim($_GET["id"])==""){
    	echo "Error: Required parameter id not passed.";
    	exit;
    }
	
	$id = $_GET["id"] ;
	
    $arFailures = new Auto_Renewal_Failures();
    $status = $arFailures->updateFailureVisible($id);
    
	echo $status;
	
    if($conn){
		mysql_close($conn);
	}