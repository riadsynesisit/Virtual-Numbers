<?php
	require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Did_Cities.class.php");
	require("../dashboard/classes/DCMDestinationCosts.class.php");
	/*require("../dashboard/classes/Unassigned_087DID.class.php");
	require("../dashboard/classes/SystemConfigOptions.class.php");
	*/
	//Inputs validation
	$fwd_cntry = trim($_POST['fwd_cntry']);
	if($fwd_cntry==""){
		echo "Error: Required parameter fwd_cntry not posted.";
		exit;
	}
	sleep(1);
	/*
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
	*/
	$dest_costs = new DCMDestinationCosts();
    if(is_object($dest_costs)==false){
    	echo "Error: Failed to create DCMDestinationCosts object.";
    	exit;
    }
	
	$callCost = $dest_costs->get_cheapest_call_rate($fwd_cntry,true,SITE_CURRENCY);
	if(is_numeric($callCost)==false && is_string($callCost)==true && substr($callCost,0,6)=="Error:"){
		echo $callCost;
		exit;
	}
	$callCostCents = number_format($callCost, 3);
	
	if($conn){
		//echo $conn;
		mysql_close($conn);
	}
	
	echo $callCostCents;
	/*
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
	$plan1_monthly = number_format($sticky_did_monthly_setup["sticky_monthly_plan1"],2);
	$plan2_monthly = number_format($sticky_did_monthly_setup["sticky_monthly_plan2"],2);
	$plan3_monthly = number_format($sticky_did_monthly_setup["sticky_monthly_plan3"],2);
	
	//Plan yearly details
	$plan0_yearly = number_format($sticky_did_monthly_setup["sticky_yearly"]/12,2);
	$plan1_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan1"]/12,2);
	$plan2_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan2"]/12,2);
	$plan3_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan3"]/12,2);
	
	$sticky_did_monthly = $sticky_did_monthly_setup["sticky_monthly"];
	$plan1_amount = $sticky_did_monthly_setup["sticky_monthly_plan1"];
	$plan2_amount = $sticky_did_monthly_setup["sticky_monthly_plan2"];
	$plan3_amount = $sticky_did_monthly_setup["sticky_monthly_plan3"];
	$plan0_yearly_amount = $sticky_did_monthly_setup["sticky_yearly"];
	$plan1_yearly_amount = $sticky_did_monthly_setup["sticky_yearly_plan1"];
	$plan2_yearly_amount = $sticky_did_monthly_setup["sticky_yearly_plan2"];
	$plan3_yearly_amount = $sticky_did_monthly_setup["sticky_yearly_plan3"];
	$sticky_setup = $sticky_did_monthly_setup["sticky_setup"];
	if(trim($sticky_did_monthly)==""){
		echo "Error: No sticky did_monthly found in did_cities for DID City ID: ".$did_city_code;
		exit;
	}
	
	if($did_city_code!="99999" && ($plan0_yearly==0.00 || $plan1_yearly==0.00 || $plan2_yearly==0.00 || $plan3_yearly==0.00)){
		echo "Error: No yearly plan prices found in did_cities for DID City ID: ".$did_city_code;
		exit;
	}
	
	$num_of_months = 1;
	
	//Amount available for SIP Cost after paying for the DID Monthly fees and Set Up costs if any
	//$effective_amount_plan0 = "Unlimited";//SIP Forwarding is unlimited
	$effective_amount_plan1 = $plan1_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	$effective_amount_plan2 = $plan2_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	$effective_amount_plan3 = $plan3_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	
	if($callCostTF!=0){
		//$num_of_minutes_plan0 = "Unlimited";//SIP Forwarding is unlimited
		$num_of_minutes_plan1 = floor($effective_amount_plan1/($callCost+$callCostTF));
		$num_of_minutes_plan2 = floor($effective_amount_plan2/($callCost+$callCostTF));
		$num_of_minutes_plan3 = floor($effective_amount_plan3/($callCost+$callCostTF));
	}else{
		//$num_of_minutes_plan0 = "Unlimited";//SIP Forwarding is unlimited
		$num_of_minutes_plan1 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan1);
		$num_of_minutes_plan2 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan2);
		$num_of_minutes_plan3 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan3);
	}
	
	$cost_info_plan1 = $dest_costs->get_mobile_other_call_rates($fwd_cntry, $effective_amount_plan1);
	$cost_info_plan2 = $dest_costs->get_mobile_other_call_rates($fwd_cntry, $effective_amount_plan2);
	$cost_info_plan3 = $dest_costs->get_mobile_other_call_rates($fwd_cntry, $effective_amount_plan3);
	
	$row_counter = 0;
	foreach($cost_info_plan1 as $cost){
		$row_counter = $row_counter + 1;
		if($row_counter==1){
			$plan1_mobile_mins = $cost["minutes"];
			$plan1_mobile_rate = $cost["rate"];
		}
	}
	$row_counter = 0;//reset
	foreach($cost_info_plan2 as $cost){
		$row_counter = $row_counter + 1;
		if($row_counter==1){
			$plan2_mobile_mins = $cost["minutes"];
			$plan2_mobile_rate = $cost["rate"];
		}
	}
	$row_counter = 0;//reset
	foreach($cost_info_plan3 as $cost){
		$row_counter = $row_counter + 1;
		if($row_counter==1){
			$plan3_mobile_mins = $cost["minutes"];
			$plan3_mobile_rate = $cost["rate"];
		}
	}
	*/
	//Comma delimited string containing the values in the following order
	//0:Plan1_minutes, 1:Plan1_monthly, 2:Plan1_Annual, 3:Plan2_minutes, 4:Plan2_monthly, 5:Plan2_Annual, 6:Plan3_minutes, 7:Plan3_monthly, 8:Plan3_Annual, 9:CallCostCents, 10: $plan1_mobile_mins, 11: $plan1_mobile_rate, 12: $plan2_mobile_mins, 13: $plan2_mobile_rate, 14: $plan3_mobile_mins, 15: $plan3_mobile_rate
	//echo "$num_of_minutes_plan1,$plan1_monthly,$plan1_yearly,$num_of_minutes_plan2,$plan2_monthly,$plan2_yearly,$num_of_minutes_plan3,$plan3_monthly,$plan3_yearly,$callCostCents,$plan1_mobile_mins,$plan1_mobile_rate,$plan2_mobile_mins,$plan2_mobile_rate,$plan3_mobile_mins,$plan3_mobile_rate";
?>