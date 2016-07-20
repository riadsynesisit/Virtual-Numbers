<?php

class DCMDestinationCosts{
	
function getSIPCostsCSV($file_location){
		
		$query = "select prefix,
							destination,
							peak_per_min_gbp,
							offpeak_per_min_gbp,
							connection_charge_gbp,
							docs_required 
						from dcm_destination_costs
						order by id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in DCMDestinationCosts.getSIPCostsCSV. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("No records found dcm_destination_costs table in DCMDestinationCosts.getSIPCostsCSV.");
		}
		//Create the CSV file
		$csvcontent = "\"Prefix\",\"Destination\",\"Peak per min GBP\",\"Offpeak per min GBP\",\"Connection charge GBP\",\"Docs Required\"\n";
		while($row=mysql_fetch_array($result)){
			$csvcontent .= "\"".$row["prefix"]."\",";
			$csvcontent .= "\"".$row["destination"]."\",";
			$csvcontent .= "\"".$row["peak_per_min_gbp"]."\",";
			$csvcontent .= "\"".$row["offpeak_per_min_gbp"]."\",";
			$csvcontent .= "\"".$row["connection_charge_gbp"]."\",";
			$csvcontent .= "\"".$row["docs_required"]."\"\n";
		}
		$fp = fopen($file_location, 'w');
		fwrite($fp, $csvcontent);
		fclose($fp);
		return true;
	}
	
	function get_mobile_other_call_rates($fwd_cntry, $effective_amount){
		
		//1. Get SIP Markup cost
		$query = "select ifnull(1+config_value/100,0) as config_value from system_config_options where config_option='sip_mark_up_cost'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred in System_Config_Options.getSIPMarkUpCost. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No System Config record found in System_Config_Options.getSIPMarkUpCost";
		}
		$row = mysql_fetch_array($result);
		$sipMarkUpFactor = $row['config_value'];
		
		//2. Get GBP to AUD rate
		$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='currency_rate_gbp_to_aud'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred in System_Config_Options.getRateGBPToAUD. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No System Config record found in System_Config_Options.getRateGBPToAUD";
		}
		$row = mysql_fetch_array($result);
		$GBPtoAUDFactor = $row['config_value'];
		
		//3. Get the rates in cents
		$query = "select distinct(dc.destination) as destination, 
							ROUND(($effective_amount*100)/ROUND(dc.peak_per_min_gbp*$sipMarkUpFactor*$GBPtoAUDFactor*100)) as minutes, 
							ROUND(dc.peak_per_min_gbp*$sipMarkUpFactor*$GBPtoAUDFactor*100) as rate 
						from dcm_destination_costs dc 
						where dc.prefix like '$fwd_cntry%' and dc.destination like '%Mobile%' order by rate asc limit 3";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting mobile and other call rates. ".mysql_error()." Query is: ".$query;
		}
		if(mysql_num_rows($result)==0){
			return "Error: No destination cost rows for forwarding country code $fwd_cntry";
		}
		$ret_arr = array();
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
		
	}
	
	function get_cheapest_call_minutes($fwd_cntry, $effective_amount){
		
		//1. Get SIP Markup cost
		$query = "select ifnull(1+config_value/100,0) as config_value from system_config_options where config_option='sip_mark_up_cost'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred in System_Config_Options.getSIPMarkUpCost. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No System Config record found in System_Config_Options.getSIPMarkUpCost";
		}
		$row = mysql_fetch_array($result);
		$sipMarkUpFactor = $row['config_value'];
		
		//2. Get GBP to AUD rate
		$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='currency_rate_gbp_to_aud'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred in System_Config_Options.getRateGBPToAUD. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No System Config record found in System_Config_Options.getRateGBPToAUD";
		}
		$row = mysql_fetch_array($result);
		$GBPtoAUDFactor = $row['config_value'];
		
		//3. Get the cheapest call minutes
		$query = "select MAX(ROUND(($effective_amount*100)/ROUND(dc.peak_per_min_gbp*$sipMarkUpFactor*$GBPtoAUDFactor*100))) as minutes
						from dcm_destination_costs dc 
						where dc.prefix like '$fwd_cntry%'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting cheapest call minutes. ".mysql_error()." Query is: ".$query;
		}
		if(mysql_num_rows($result)==0){
			return "Error: No destination cost rows for forwarding country code $fwd_cntry";
		}
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return $row["minutes"];
		
	}
	
	function get_cheapest_call_rate($country_code,$sip_markup,$currency){
		
		//1. Get SIP Markup cost
		if($sip_markup==true){
			$query = "select ifnull(1+config_value/100,0) as config_value from system_config_options where config_option='sip_mark_up_cost'";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred in System_Config_Options.getSIPMarkUpCost. ".mysql_error();
			}
			if(mysql_num_rows($result)==0){
				return "Error: No System Config record found in System_Config_Options.getSIPMarkUpCost";
			}
			$row = mysql_fetch_array($result);
			$sipMarkUpFactor = $row['config_value'];
		}else{
			$sipMarkUpFactor = 1;
		}
		
		//2. Get GBP to AUD rate
		if($currency=="AUD"){
			$query = "select ifnull(config_value,0) as config_value from system_config_options where config_option='currency_rate_gbp_to_aud'";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred in System_Config_Options.getRateGBPToAUD. ".mysql_error();
			}
			if(mysql_num_rows($result)==0){
				return "Error: No System Config record found in System_Config_Options.getRateGBPToAUD";
			}
			$row = mysql_fetch_array($result);
			$GBPtoAUDFactor = $row['config_value'];
		}else{
			$GBPtoAUDFactor = 1;
		}
		
		//Get the cheapest call cost of the forwarding country
		$query = "select min(peak_per_min_gbp)*$sipMarkUpFactor*$GBPtoAUDFactor as rate from dcm_destination_costs where prefix like '$country_code%';";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred in get_cheapest_call_rate. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No destination cost found for prefix $country_code";
		}
		$row = mysql_fetch_array($result);
		return $row["rate"];
		
	}
	
	function get_destination_from_number($phone_number){
		
		$num_len = strlen($phone_number);
		
		for($i=0;$i<$num_len;$i++){
			$prefix = substr($phone_number,0,$num_len - $i);//chop one digit from the right side for each iteration of the loop
			$query = "select destination from dcm_destination_costs where prefix='$prefix'";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "ERROR: ".mysql_error();
			}
			if(mysql_num_rows($result) > 0){
				$row = mysql_fetch_array($result);
				return $row["destination"];
				break;//As soon as you know the number blocked - get outta here!
			}
		}
		
	}
	
}