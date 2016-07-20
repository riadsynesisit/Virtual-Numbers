<?php

	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/SystemConfigOptions.class.php");
    require("../dashboard/classes/Unassigned_087DID.class.php");
    require("../dashboard/classes/Did_Cities.class.php");
	require("../dashboard/classes/Did_Plans.class.php");
	require("../dashboard/libs/didww.lib.php");
    
    //Check for forwarding number
    if(isset($_POST["fwd_number"])==false || trim($_POST["fwd_number"])==""){
    	echo "Error: Destination Number not passed";
    	exit;
    }
     //Check for country iso
    if(isset($_POST["country_iso"])==false || trim($_POST["country_iso"])==""){
    	echo "Error: DID Country ISO code not passed";
    	exit;
    }
     //Check for city prefix
    if(isset($_POST["city_id"])==false || trim($_POST["city_id"])==""){
    	echo "Error: DID City id not passed";
    	exit;
    }
    
    $plan_minutes = getPlanMinutesNewDID(trim($_POST["fwd_number"]), trim($_POST["country_iso"]), trim($_POST["city_id"]));
    
    $plan1_minutes = $plan_minutes["plan1_minutes"];
    $plan2_minutes = $plan_minutes["plan2_minutes"];
    $plan3_minutes = $plan_minutes["plan3_minutes"];
	
	echo "Success,$plan1_minutes,$plan2_minutes,$plan3_minutes";