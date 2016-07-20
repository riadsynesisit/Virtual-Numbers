<?php
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Did_Cities.class.php");
	require("../dashboard/classes/Unassigned_087DID.class.php");
	require("../dashboard/classes/SystemConfigOptions.class.php");
	
	//Inputs validation
	$did_city_code = trim($_POST['did_city_code']);
	if($did_city_code==""){
		echo "Error: Required parameter did_city_code not posted.";
		exit;
	}
	
	$did_cities = new Did_Cities();
	if(is_object($did_cities)!=true){
		echo "Error: Failed to create did_cities object.";
		exit;
	}
    
    $unassigned_087did = new Unassigned_087DID();
	if(is_object($unassigned_087did)!=true){
		echo "Error: Failed to create Unassigned_087DID object.";
		exit;
	}
	
	$sco = new System_Config_Options();
	if(is_object($sco)!=true){
		echo "Error: Failed to create System_Config_Options object.";
		exit;
	}
	
	$gbp_to_aud_rate = $sco->getRateGBPToAUD();
	$usd_to_aud_rate = $sco->getRateUSDToAUD();
	if(SITE_CURRENCY=="AUD"){
		$exchange_rate = $gbp_to_aud_rate;
		$exchange_rate_tollfree = $usd_to_aud_rate;
	}else{
		$exchange_rate = 1;
		$exchange_rate_tollfree = 1;
	}
	
	$callCost = 0;//SIP Forwarding there is no call cost
	
	$callCostTF = $unassigned_087did->callCostTollFree($did_city_code,true)*$exchange_rate_tollfree;
	if(is_numeric($callCostTF)==false && is_string($callCostTF)==true && substr($callCostTF,0,6)=="Error:"){
		echo $callCostTF;
		exit;
	}
	
	$sticky_did_monthly_setup = $did_cities->getStickyDIDMonthlyAndSetupFromCityId($did_city_code);
	if(is_array($sticky_did_monthly_setup)==false && substr($sticky_did_monthly_setup,0,6)=="Error:"){
		echo $sticky_did_monthly_setup;
		exit;
	}
    
	//Plan monthly details
	$plan0_monthly = number_format($sticky_did_monthly_setup["sticky_monthly"],2);
	$plan0_amount = $sticky_did_monthly_setup["sticky_monthly"];
	
	//Plan yearly details
	$plan0_yearly = number_format($sticky_did_monthly_setup["sticky_yearly"]/12,2);
	
	$sticky_did_monthly = $sticky_did_monthly_setup["sticky_monthly"];
	$plan0_yearly_amount = $sticky_did_monthly_setup["sticky_yearly"];
	$sticky_setup = $sticky_did_monthly_setup["sticky_setup"];
	if(trim($plan0_amount)==""){
		echo "Error: No sticky did_monthly found in did_cities for DID City ID: ".$did_city_code;
		exit;
	}
	
	if($did_city_code!="99999" && $plan0_yearly==0.00){
		echo "Error: No yearly plan prices found in did_cities for DID City ID: ".$did_city_code;
		exit;
	}
	
	$num_of_months = 1;
	
	//Amount available for SIP Cost after paying for the DID Monthly fees and Set Up costs if any
	//$effective_amount_plan0 = "Unlimited";//SIP Forwarding is unlimited
	$effective_amount_plan0 = $plan0_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	
	if($callCostTF!=0){
		$num_of_minutes_plan0 = floor($effective_amount_plan0/($callCost+$callCostTF));
	}else{
		$num_of_minutes_plan0 = "Unlimited";
	}
	
	//Comma delimited string containing the values in the following order
	//Plan0_minutes, Plan0_monthly, Plan0_Annual, 
	echo "$num_of_minutes_plan0,$plan0_monthly,$plan0_yearly";