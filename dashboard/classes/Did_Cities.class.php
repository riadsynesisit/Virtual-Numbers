<?php

class Did_Cities{
	
	function populateDID_Country_City(){
		
		$sco = new System_Config_Options();
		if(is_object($sco)==false){
		  	die("Failed to create System_Config_Options object.");
		}
		//Get the last time the regions were requested from DIDWW
		$last_request_gmt = $sco->getDIDWWRegionsLastRequestGMT();
		if($last_request_gmt==0){
			$last_request_gmt = null;
		}
		//echo DIDWW_API_WSDL.", ".DIDWW_USER.". ".DIDWW_PASS."<BR>";
		$client = new DidwwApi(DIDWW_API_WSDL, DIDWW_USER, DIDWW_PASS);
		if(is_object($client)==false){
		  	die("Failed to create DidwwApi object.");
		}
		$current_unixtime_gmt = time();//Grab the unixtime in GMT now to update in the end of this function.
		$did_country_city = $client->getRegions(0,0,$last_request_gmt);
		$current_country = "";
		$new_cities_added = "";
		$cities_removed = "";
		if(is_array($did_country_city)){//success
			foreach($did_country_city as $region){
				$current_country = $region->country_name."(".$region->country_prefix.")";
				//Check if a country record is already present in the did_countries countries
				$query = "select id from did_countries where country_iso='".$region->country_iso."'";
				$result = mysql_query($query);
				if(mysql_error()!=""){
					die("Error: An error occurred while checking the existence of record in did_countries table. ".mysql_error());
				}
				$did_country_id = 0;
				if(mysql_num_rows($result)==0){//No record found - so insert it
					$query = "insert into did_countries(country_name, country_prefix, country_iso) 
								values ('".$region->country_name."', ".$region->country_prefix.", '".$region->country_iso."')";
					$result = mysql_query($query);
					if(mysql_error()!=""){
						die("Error: An error occurred while in inserting a record in did_countries table. ".mysql_error());
					}
					$did_country_id = mysql_insert_id();
				}else{//Record Exists - so update it
					$row = mysql_fetch_array($result);
					$query = "update did_countries set country_name='".$region->country_name."', 
												country_prefix=".$region->country_prefix.",
												country_iso='".$region->country_iso."'
											where id=".$row['id'];
					$result = mysql_query($query);
					if(mysql_error()!=""){
						echo $query."\n\n";
						die("Error: An error occurred while in updating a record in did_countries table. ".mysql_error());
					}
					$did_country_id = $row['id'];
				}
				$did_cities = $region->cities;
				foreach($did_cities as $city){
					//Now check if a city record is already present in the did_cities table
					$query = "select id, isavailable from did_cities where did_country_id=$did_country_id and city_id=$city->city_id";
					$result = mysql_query($query);
					if(mysql_error()!=""){
						die("Error: An error occurred while checking the existence of record in did_cities table. ".mysql_error());
					}
					//Insert a record in did_cities_temp
					$ins_query = "insert into did_cities_temp (city_id, unix_time) values ($city->city_id, $current_unixtime_gmt)";
					mysql_query($ins_query);
					if(mysql_error()!=""){
						echo $query."\n\n";
						die("Error: An error occurred while in inserting a record in did_cities_temp table. Query is: $ins_query. Error is: ".mysql_error());
					}
					if(mysql_num_rows($result)==0){//No record found - so insert it
						//Sticky Pricing Automation
						//First check if it has Toll-free or Virtual or VOIP
						if(stripos($city->city_name,"Toll-free")===FALSE && stripos($city->city_name,"Virtual")===FALSE && stripos($city->city_name,"VOIP")===FALSE){
							$sticky_prices = $this->getStickyPrices($did_country_id,$city->monthly,$city->setup);
							if(is_array($sticky_prices)==false && substr($sticky_prices,0,6)=="Error:"){
								//Price could not be found - Either no records exists or an error occurred
								$sticky_monthly = "NULL";
								$sticky_setup = "NULL";
								$sticky_monthly_plan1 = "NULL";
								$sticky_monthly_plan2 = "NULL";
								$sticky_monthly_plan3 = "NULL";
								$sticky_yearly = "NULL";
								$sticky_yearly_plan1 = "NULL";
								$sticky_yearly_plan2 = "NULL";
								$sticky_yearly_plan3 = "NULL";
								$restricted = "yes";
							}elseif(is_array($sticky_prices)==true){
								$sticky_monthly = $sticky_prices["sticky_monthly"];
								$sticky_setup = $sticky_prices["sticky_setup"];
								$sticky_monthly_plan1 = $sticky_prices["sticky_monthly_plan1"]; 
								$sticky_monthly_plan2 = $sticky_prices["sticky_monthly_plan2"];
								$sticky_monthly_plan3 = $sticky_prices["sticky_monthly_plan3"];
								$sticky_yearly = $sticky_prices["sticky_yearly"];
								$sticky_yearly_plan1 = $sticky_prices["sticky_yearly_plan1"]; 
								$sticky_yearly_plan2 = $sticky_prices["sticky_yearly_plan2"];
								$sticky_yearly_plan3 = $sticky_prices["sticky_yearly_plan3"];
								$restricted = "no";
							}else{
								die("Error: Unexpected condition encountered after running getStickyPrices : ". print_r($sticky_prices));
							}
						}else{//If Toll-free or Virtual or VOIP
							$sticky_monthly = "NULL";
							$sticky_setup = "NULL";
							$sticky_monthly_plan1 = "NULL";
							$sticky_monthly_plan2 = "NULL";
							$sticky_monthly_plan3 = "NULL";
							$sticky_yearly = "NULL";
							$sticky_yearly_plan1 = "NULL";
							$sticky_yearly_plan2 = "NULL";
							$sticky_yearly_plan3 = "NULL";
							$restricted = "yes";
						}
						if($sticky_setup == ''){
							$sticky_setup = '0.0000';
						}
						$query = "insert into did_cities(did_country_id, 
															city_id, 
															city_name,
															city_prefix,
															city_nxx_prefix,
															isavailable,
															islnrrequired,
															didww_setup,
															didww_monthly,
															sticky_monthly, 
															sticky_setup, 
															sticky_monthly_plan1, 
															sticky_monthly_plan2, 
															sticky_monthly_plan3, 
															sticky_yearly, 
															sticky_yearly_plan1, 
															sticky_yearly_plan2, 
															sticky_yearly_plan3,
															restricted) 
										values (".$did_country_id.", 
													".$city->city_id.", 
													'".$city->city_name."',
													".$city->city_prefix.",
													".($city->city_nxx_prefix==""?'null':$city->city_nxx_prefix).",
													".$city->isavailable.",
													".$city->islnrrequired.",
													".($city->setup==""?'null':$city->setup).",
													".($city->monthly==""?'null':$city->monthly).",
													".$sticky_monthly.", 
													".$sticky_setup.", 
													".$sticky_monthly_plan1.", 
													".$sticky_monthly_plan2.", 
													".$sticky_monthly_plan3.", 
													".$sticky_yearly.", 
													".$sticky_yearly_plan1.", 
													".$sticky_yearly_plan2.", 
													".$sticky_yearly_plan3.", 
													'".$restricted."' 
												)";
						$result = mysql_query($query);
						if(mysql_error()!=""){
							echo $query."\n\n";
							die("Error: An error occurred while inserting a record in did_cities table. ".mysql_error());
						}
						if($restricted=="yes"){
							$new_cities_added .= $current_country." ".$city->city_name."(".$city->city_prefix.") - MANUAL EDITING REQUIRED<br>";
						}else{
							$new_cities_added .= $current_country." ".$city->city_name."(".$city->city_prefix.")<br>";
						}
					}else{//Record Exists - so update it
						$row = mysql_fetch_array($result);
						$query = "update did_cities set city_name='".$city->city_name."', 
													city_prefix=".$city->city_prefix.", 
													city_nxx_prefix=".($city->city_nxx_prefix==""?'null':$city->city_nxx_prefix).", 
													isavailable=".$city->isavailable.", 
													islnrrequired=".$city->islnrrequired.", 
													didww_setup=".($city->setup==""?'null':$city->setup).", 
													didww_monthly=".($city->monthly==""?'null':$city->monthly)." 
												where id=".$row['id'];
						$result = mysql_query($query);
						if(mysql_error()!=""){
							echo $query."<br><br>";
							die("Error: An error occurred while in updating a record in did_cities table. ".mysql_error());
						}
						if(trim($row["isavailable"])==1 && intval(trim($city->isavailable))!=1){
							$cities_removed .= $current_country." ".$city->city_name."(".$city->city_prefix.")<br>";
						}
					}
				}//end of cities foreach loop
			}//end of regions foreach loop
			//Now delete the old records from the did_cities_temp table
			$del_query = "delete from did_cities_temp where unix_time!=$current_unixtime_gmt;";
			mysql_query($del_query);
			if(mysql_error()!=""){
				echo $query."\n\n";
				die("Error: An error occurred while deleting records in did_cities_temp table. Query is: $del_query. Error is: ".mysql_error());
			}
			//Now check if any existing cities are no longer in the new result set obtained from this DIDWW API Call
			$check_query = "select dc.city_id as city_id, dc.city_name as city_name, dc.city_prefix as city_prefix, dco.country_name as country_name from did_cities dc, did_countries dco where dc.did_country_id=dco.id and dc.city_id NOT IN (select city_id from did_cities_temp where unix_time=$current_unixtime_gmt)";
			$result = mysql_query($check_query);
			if(mysql_error()!=""){
				echo $query."\n\n";
				die("Error: An error occurred while checking for records that exists in did_cities table but no in did_cities_temp table. Query is: $check_query. Error is: ".mysql_error());
			}
			if(mysql_num_rows($result)>0){
				while($row=mysql_fetch_array($result)){
					if(intval($row["city_id"])!=17 && intval($row["city_id"])!=135 && intval($row["city_id"])!=99999){//Except these three lowest priced city IDs
						$del_query = "delete from did_cities where city_id=".$row["city_id"];
						mysql_query($del_query);
						if(mysql_error()!=""){
							echo $query."\n\n";
							die("Error: An error occurred while deleting a record in did_cities table. Query is: $del_query. Error is: ".mysql_error());
						}else{
							$cities_removed .= $row["country_name"]." ".$row["city_name"]."(".$row["city_prefix"].")<br>";
						}
					}
				}
			}
			//Send an E-mail here
			$email_message = "";
			if(trim($new_cities_added)!="" || trim($cities_removed)!=""){
				if(trim($new_cities_added)!=""){
					$email_message .= "Cities Added to .com.au site through DIDWW update process are:<br>".$new_cities_added;
				}else{
					$email_message .= "No new cities were added.<br>";
				}
				if(trim($cities_removed)!=""){
					$email_message .= "<br>Cities REMOVED FROM .com.au site through DIDWW update process are:<br>".$cities_removed;
				}else{
					$email_message .= "<br>No cities were removed.";
				}
			}else{
				$email_message .= "<br>No new cities were added and none of the existing cities were removed.";
			}
			$from_email = "system@stickynumber.com";
			$to_email = "info@stickynumber.com";
			$subject_line = "Countries/Cities ADDED or REMOVED through DIDWW update process";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: Sticky Number <' . $from_email .'>' . "\r\n";
			mail($to_email, $subject_line, $email_message, $headers);
		}else{
			if(is_array($did_country_city)==true){
				die("Failed to get regions. Result is: ".$did_order->result." Error Code is: ".$client->getErrorCode()." Error is: ".$client->getErrorString());
			}else{
				die("did_country_city returned by getRegions is NOT an array.");
			}
		}
		//Now update the last time the regions were requested from DIDWW in SystemConfigOptions
		$status = $sco->updateDIDWWRegionsLastRequestGMT($current_unixtime_gmt);
		if($status!=true){
			echo "\nCould not update the last_request_gmt in config_options.";
		}
		return true;
	}
	
	function getStickyPrices($did_country_id, $didww_price, $didww_setup){
		
		//Validate all input parameters
		if(trim($did_country_id)==""){
			return "Error: Required parameter did_country_id not passed.";
		}
		if(trim($didww_price)==""){
			return "Error: Required parameter didww_price not passed.";
		}
		if(trim($didww_setup)==""){
			return "Error: Required parameter didww_setup not passed.";
		}
		
		//See if we have a DID in the same country and also with the same DIDWW Price - If yes, just use the same prices for this new city being added
		$query = "select id, 
					sticky_monthly, 
					sticky_setup, 
					sticky_monthly_plan1, 
					sticky_monthly_plan2, 
					sticky_monthly_plan3, 
					sticky_yearly, 
					sticky_yearly_plan1, 
					sticky_yearly_plan2, 
					sticky_yearly_plan3, 
					min(didww_monthly) as min_cost 
				from did_cities 
				where isavailable=1 and 
					islnrrequired=0 and 
					(restricted!='yes' or restricted is null) and 
					(blocked!='yes' or blocked is null) and 
					did_country_id=$did_country_id and 
					didww_monthly=$didww_price and 
					didww_setup=$didww_setup  
				group by didww_monthly;";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while checking the existence of record in did_cities table. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			//Now try if a price can be found from another country
			$query = "select id, 
					sticky_monthly, 
					sticky_setup, 
					sticky_monthly_plan1, 
					sticky_monthly_plan2, 
					sticky_monthly_plan3, 
					sticky_yearly, 
					sticky_yearly_plan1, 
					sticky_yearly_plan2, 
					sticky_yearly_plan3, 
					min(didww_monthly) as min_cost 
				from did_cities 
				where isavailable=1 and 
					islnrrequired=0 and 
					(restricted!='yes' or restricted is null) and 
					(blocked!='yes' or blocked is null) and 
					didww_monthly=$didww_price and 
					didww_setup=$didww_setup  
				group by didww_monthly;";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while checking the existence of record in did_cities table for any country. ".mysql_error();
			}
			if(mysql_num_rows($result)==0){
				return "Error: No rows returned.";
			}else{
				return mysql_fetch_array($result);
			}
		}
		return mysql_fetch_array($result);
		
	}
	
	function getDIDCostsCSV($file_location){
		
		$query = "select concat(a.country_name,'(',a.country_prefix,')') as country_name,
							concat(b.city_name,'(',b.city_prefix,')') as city_name,
							b.city_id as city_id,
							ifnull(b.didww_setup,'') as didww_setup,
							ifnull(b.sticky_setup,'') as sticky_setup,
							ifnull(b.didww_monthly,'') as didww_monthly,
							ifnull(b.sticky_monthly,'') as sticky_monthly,
							ifnull(b.sticky_monthly_plan1,'') as sticky_monthly_plan1,
							ifnull(b.sticky_monthly_plan2,'') as sticky_monthly_plan2,
							ifnull(b.sticky_monthly_plan3,'') as sticky_monthly_plan3, 
							ifnull(b.sticky_yearly,'') as sticky_yearly,
							ifnull(b.sticky_yearly_plan1,'') as sticky_yearly_plan1,
							ifnull(b.sticky_yearly_plan2,'') as sticky_yearly_plan2,
							ifnull(b.sticky_yearly_plan3,'') as sticky_yearly_plan3,
							ifnull(b.restricted,'') as restricted, 
							ifnull(b.non_api_access,'') as non_api_access, 
							ifnull(b.blocked,'') as blocked 
						from did_countries a, did_cities b
						where a.id=b.did_country_id and 
							b.isavailable=1
						order by a.country_name asc, b.city_name asc";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred in Did_Cities.getDIDCostsCSV. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("No records found did_cities/did_countries tables in Did_Cities.getDIDCostsCSV. ");
		}
		//Create the CSV file
		$csvcontent = "\"Country\",\"City\",\"Record Id\",\"DIDWW Setup\",\"Sticky Setup\",\"DIDWW Monthly\",\"Sticky Monthly Plan 0\",\"Sticky Monthly Plan1\",\"Sticky Monthly Plan2\",\"Sticky Monthly Plan3\",\"Sticky Yearly Plan 0\",\"Sticky Yearly Plan1\",\"Sticky Yearly Plan2\",\"Sticky Yearly Plan3\",\"Restricted - Manual Verification\",\"Reseller - Non API Access\",\"Blocked\"\n";
		while($row=mysql_fetch_array($result)){
			$csvcontent .= "\"".$row["country_name"]."\",";
			$csvcontent .= "\"".$row["city_name"]."\",";
			$csvcontent .= "\"".$row["city_id"]."\",";
			$csvcontent .= "\"".$row["didww_setup"]."\",";
			$csvcontent .= "\"".$row["sticky_setup"]."\",";
			$csvcontent .= "\"".$row["didww_monthly"]."\",";
			$csvcontent .= "\"".$row["sticky_monthly"]."\",";
			$csvcontent .= "\"".$row["sticky_monthly_plan1"]."\",";
			$csvcontent .= "\"".$row["sticky_monthly_plan2"]."\",";
			$csvcontent .= "\"".$row["sticky_monthly_plan3"]."\",";
			$csvcontent .= "\"".$row["sticky_yearly"]."\",";
			$csvcontent .= "\"".$row["sticky_yearly_plan1"]."\",";
			$csvcontent .= "\"".$row["sticky_yearly_plan2"]."\",";
			$csvcontent .= "\"".$row["sticky_yearly_plan3"]."\",";
			$csvcontent .= "\"".$row["restricted"]."\",";
			$csvcontent .= "\"".$row["non_api_access"]."\",";
			$csvcontent .= "\"".$row["blocked"]."\"\n";
		}
		$fp = fopen($file_location, 'w');
		fwrite($fp, $csvcontent);
		fclose($fp);
		return true;
	}
	
	function processDIDPricesCSVFile($csv_file_path){
		
		$handle = fopen($csv_file_path, "r");//Open the uploaded file for reading
		$i=0;
		while (($data = fgetcsv($handle, 0, ",")) !== FALSE){            
			$i++;
			$setup_sql = "";
			$monthly_sql = "";
			$monthly_plan1_sql = "";
			$monthly_plan2_sql = "";
			$monthly_plan3_sql = "";
			$yearly_sql = "";
			$yearly_plan1_sql = "";
			$yearly_plan2_sql = "";
			$yearly_plan3_sql = "";
			$restricted_sql = "";
			$non_api_access_sql = "";
			$blocked_sql = "";
			if($i>1 &&  !empty($data[2]) && is_numeric($data[2]) ){//City ID must be there
				if(!empty($data[4]) && is_numeric($data[4]) && trim($data[4])!=""){
					$setup_sql = "sticky_setup=$data[4]";
				}
				if(!empty($data[6]) && is_numeric($data[6]) && trim($data[6])!=""){
					if($setup_sql != ""){
						$monthly_sql = ", ";
					}
					$monthly_sql .= "sticky_monthly=$data[6]";
				}
				if(!empty($data[7]) && is_numeric($data[7]) && trim($data[7])!=""){
					if($setup_sql.$monthly_sql != ""){
						$monthly_plan1_sql = ", ";
					}
					$monthly_plan1_sql .= "sticky_monthly_plan1=$data[7]";
				}
				if(!empty($data[8]) && is_numeric($data[8]) && trim($data[8])!=""){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql != ""){
						$monthly_plan2_sql = ", ";
					}
					$monthly_plan2_sql .= "sticky_monthly_plan2=$data[8]";
				}
				if(!empty($data[9]) && is_numeric($data[9]) && trim($data[9])!=""){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql != ""){
						$monthly_plan3_sql = ", ";
					}
					$monthly_plan3_sql .= "sticky_monthly_plan3=$data[9]";
				}
				
				if(!empty($data[10]) && is_numeric($data[10]) && trim($data[10])!=""){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql != ""){
						$yearly_sql = ", ";
					}
					$yearly_sql .= "sticky_yearly=$data[10]";
				}
				if(!empty($data[11]) && is_numeric($data[11]) && trim($data[11])!=""){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql != ""){
						$yearly_plan1_sql = ", ";
					}
					$yearly_plan1_sql .= "sticky_yearly_plan1=$data[11]";
				}
				if(!empty($data[12]) && is_numeric($data[12]) && trim($data[12])!=""){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql != ""){
						$yearly_plan2_sql = ", ";
					}
					$yearly_plan2_sql .= "sticky_yearly_plan2=$data[12]";
				}
				if(!empty($data[13]) && is_numeric($data[13]) && trim($data[13])!=""){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql.$yearly_plan2_sql != ""){
						$yearly_plan3_sql = ", ";
					}
					$yearly_plan3_sql .= "sticky_yearly_plan3=$data[13]";
				}
				
				if(!empty($data[14]) && trim($data[14])!="" && strtolower(trim($data[14]))=="yes"){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql.$yearly_plan2_sql.$yearly_plan3_sql != ""){
						$restricted_sql = ", ";
					}
					$restricted_sql .= "restricted='$data[14]'";
				}else{//UNRestrict the restriction previously placed
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql.$yearly_plan2_sql.$yearly_plan3_sql != ""){
						$restricted_sql = ", ";
					}
					$restricted_sql .= "restricted=NULL";
				}
				if(!empty($data[15]) && trim($data[15])!="" && strtolower(trim($data[15]))=="yes"){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql.$yearly_plan2_sql.$yearly_plan3_sql.$restricted_sql != ""){
						$non_api_access_sql = ", ";
					}
					$non_api_access_sql .= "non_api_access='$data[15]'";
				}else{
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql.$yearly_plan2_sql.$yearly_plan3_sql.$restricted_sql != ""){
						$non_api_access_sql = ", ";
					}
					$non_api_access_sql .= "non_api_access=NULL";
				}
				if(!empty($data[16]) && trim($data[16])!="" && strtolower(trim($data[16]))=="yes"){
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql.$yearly_plan2_sql.$yearly_plan3_sql.$restricted_sql.$non_api_access_sql != ""){
						$blocked_sql = ", ";
					}
					$blocked_sql .= "blocked='$data[16]'";
				}else{
					if($setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql.$yearly_plan2_sql.$yearly_plan3_sql.$restricted_sql.$non_api_access_sql != ""){
						$blocked_sql = ", ";
					}
					$blocked_sql .= "blocked=NULL";
				}
				if($setup_sql!="" || $monthly_sql !="" || $monthly_plan1_sql !=""  || $monthly_plan2_sql !="" || $monthly_plan3_sql !="" || $yearly_sql !="" || $yearly_plan1_sql !=""  || $yearly_plan2_sql !="" || $yearly_plan3_sql !="" || $restricted_sql != "" || $non_api_access_sql != "" || $blocked_sql != ""){
					$query = "update did_cities set ".$setup_sql.$monthly_sql.$monthly_plan1_sql.$monthly_plan2_sql.$monthly_plan3_sql.$yearly_sql.$yearly_plan1_sql.$yearly_plan2_sql.$yearly_plan3_sql.$restricted_sql.$non_api_access_sql.$blocked_sql." where city_id=$data[2]";
					 mysql_query($query) or die(mysql_error()." Query is: ".$query);
				}
			}
		}//end of while loop
		   
		fclose($handle);
		
		//Now determine the cheapest Plan 1 DID and update in System Config Options
		$cheapest_city = $this->getCheapestCityId('');
		$cheapest_city_au = $this->getCheapestCityId('AU');
		$cheapest_city_gb = $this->getCheapestCityId('GB');
		$cheapest_city_us = $this->getCheapestCityId('US');
		
		$sco = new System_Config_Options();
		if(is_object($sco)==false){
		  	die("Failed to create System_Config_Options object.");
		}
		$sco->updateConfigOption('cheapest_did_city',$cheapest_city);
		$sco->updateConfigOption('cheapest_did_city_au',$cheapest_city_au);
		$sco->updateConfigOption('cheapest_did_city_gb',$cheapest_city_gb);
		$sco->updateConfigOption('cheapest_did_city_us',$cheapest_city_us);
		
		return true;
		
	}
	
	function getCheapestCityId($country_iso=""){
		
		$country_sql = "";
		if($country_iso!=""){
			$country_sql = " and dco.country_iso='$country_iso' ";
		}
		$query = "select dci.city_id as city_id  
					from did_cities dci, did_countries dco 
					where dci.did_country_id=dco.id and 
							dci.sticky_monthly_plan1 is not null and
							dci.isavailable = 1 and
							ifnull(LOWER(dci.restricted),'no')!='yes' and 
							ifnull(LOWER(dci.blocked),'no')!='yes' $country_sql and 
							dci.sticky_monthly_plan1=(select min(dci.sticky_monthly_plan1)
												from did_cities as dci, did_countries dco  
												where dci.did_country_id=dco.id and 
														dci.sticky_monthly_plan1 is not null and
														dci.isavailable = 1 and
														ifnull(LOWER(dci.restricted),'no')!='yes' and 
														ifnull(LOWER(dci.blocked),'no')!='yes' $country_sql)
					order by dci.city_name asc limit 1";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting sticky did price. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No record found while getting sticky did price for did_cities.city_id: $city_id.";
		}
		$row = mysql_fetch_array($result);
		return $row["city_id"];
		
	}
	
	function getCheapestCountry($city_id){//Returns Country_iso
		
		$query = "select dco.country_iso as country_iso 
					from did_cities dci, 
						did_countries dco
					where dci.did_country_id=dco.id and
						dci.city_id=$city_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while cheapest country_iso of city_id $city_id. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No record found for cheapest country_iso of city_id: $city_id.");
		}
		$row = mysql_fetch_array($result);
		return $row["country_iso"];
		
	}
	
	function getStickyDIDMonthlyAndSetupFromCityId($city_id,$user_currency=SITE_CURRENCY){
		
		$exchange_rate = $this->getExchangeRate($user_currency);
		
		//Blank sticky_setup is considered as zero setup cost or no setup cost
		$query = "select ifnull(dci.sticky_monthly/$exchange_rate,'') as sticky_monthly,
							ifnull(dci.sticky_monthly_plan1/$exchange_rate,'') as sticky_monthly_plan1,
							ifnull(dci.sticky_monthly_plan2/$exchange_rate,'') as sticky_monthly_plan2,
							ifnull(dci.sticky_monthly_plan3/$exchange_rate,'') as sticky_monthly_plan3, 
							ifnull(dci.sticky_yearly/$exchange_rate,'') as sticky_yearly,
							ifnull(dci.sticky_yearly_plan1/$exchange_rate,'') as sticky_yearly_plan1,
							ifnull(dci.sticky_yearly_plan2/$exchange_rate,'') as sticky_yearly_plan2,
							ifnull(dci.sticky_yearly_plan3/$exchange_rate,'') as sticky_yearly_plan3, 
							ifnull(dci.sticky_setup/$exchange_rate,0) as sticky_setup, 
							dci.city_id as city_id
						from  did_cities dci
						where dci.city_id=$city_id";
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting sticky did monthly/yearly and setup price for city_id $city_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No record found while getting sticky did monthly/yearly and setup price for city_id $city_id.";
		}
		$row = mysql_fetch_array($result);
		return $row;
		
	}
	
	function getCityPrefixFromCityId($city_id){
		
		$query = "select city_prefix from did_cities where city_id=$city_id";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting city_prefix for city_id $city_id. ".mysql_error();
		}
		if(mysql_num_rows($result)==0){
			return "Error: No record found while getting city_prefix for city_id $city_id.";
		}
		$row = mysql_fetch_array($result);
		return $row["city_prefix"];
		
	}
	
	function getAvailableCities($country_iso,$user_currency=SITE_CURRENCY){
		
		$exchange_rate = $this->getExchangeRate($user_currency);
		
		$query = "select dci.city_id as city_id, 
							dci.city_prefix as city_prefix,
							dci.city_name as city_name, 
							round(dci.sticky_monthly,2) as sticky_monthly, 
							round(dci.sticky_monthly_plan1/$exchange_rate,2) as sticky_monthly_plan1 
						from  did_cities dci,
								did_countries dco
						where dci.did_country_id = dco.id and
								dci.isavailable = 1 and
								ifnull(LOWER(dci.restricted),'no')!='yes' and 
								ifnull(LOWER(dci.blocked),'no')!='yes' and 
								dco.country_iso='$country_iso'
						order by dci.city_name asc";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting Available Cities in country_iso $country_iso: ".mysql_error());
		}
		$ret_arr = array();
		while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
	}
	
	function getTopRateDidCities($country_iso,$user_currency=SITE_CURRENCY){
		
		$exchange_rate = $this->getExchangeRate($user_currency);
		
		$query = "select dci.city_id as city_table_id, 
						dco.country_prefix as country_code,
							dci.city_prefix as city_id,
							dci.city_name as city_name, 
							round(IFNULL(dci.sticky_setup,0)/$exchange_rate,2) as sticky_setup, 
							round( IFNULL( dci.sticky_monthly, 0 )/$exchange_rate,2 ) as sticky_monthly
						from  did_cities dci,
								did_countries dco
						where dci.did_country_id = dco.id and
								dci.isavailable = 1 and
								ifnull(LOWER(dci.restricted),'no')!='yes' and 
								ifnull(LOWER(dci.blocked),'no')!='yes' and 
								dco.country_iso='$country_iso'
						order by dci.sticky_monthly, dci.city_name asc LIMIT 20";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting Available Cities in country_iso $country_iso: ".mysql_error());
		}
		$ret_arr = array();
		while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
	}
	
	function getAvailableAPICities($country_iso){
		
		$query = "select dci.city_prefix as city_prefix,
							dci.city_name as city_name
						from  did_cities dci,
								did_countries dco
						where dci.did_country_id = dco.id and
								dci.isavailable = 1 and
								ifnull(LOWER(dci.restricted),'no')!='yes' and 
								ifnull(LOWER(dci.non_api_access),'no')!='yes' and 
								ifnull(LOWER(dci.blocked),'no')!='yes' and 
								dco.country_iso='$country_iso'
						order by dci.city_name asc";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting Available Cities for API in country_iso $country_iso: ".mysql_error());
		}
		$ret_arr = array();
		while($row=mysql_fetch_array($result)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
	}
	
	function getCountryAndCityNameFromISO_Prefix($country_iso,$city_prefix){
		
		$query = "select dco.country_name as country_name, 
							dci.city_name as city_name
						from  did_cities dci,
								did_countries dco
						where dci.did_country_id = dco.id and
								dco.country_iso='$country_iso' and 
								dci.city_prefix=$city_prefix";
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting Country and City names for country_iso $country_iso and city_prefix $city_prefix. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No record found while getting Counry and City names for country_iso $country_iso and city_prefix $city_prefix.");
		}
		$row = mysql_fetch_array($result);
		return $row;
		
	}
	
	function getCountryAndCityNameFromCityId($city_id){
		
		$query = "select dco.country_name as country_name, 
							dco.country_prefix as country_prefix, 
							dci.city_name as city_name, 
							dci.city_prefix as city_prefix 
						from  did_cities dci,
								did_countries dco
						where dci.did_country_id = dco.id and
								dci.city_id=$city_id";
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting Country and City names for country_iso $country_iso and city_id $city_id. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No record found while getting Counry and City names for country_iso $country_iso and city_id $city_id.");
		}
		$row = mysql_fetch_array($result);
		return $row;
		
	}
	
	function getPlanPricesForCityId($city_id){
		
		$query = "select round(sticky_monthly,2) as sticky_monthly, 
							round(sticky_monthly_plan1,2) as sticky_monthly_plan1,
							round(sticky_monthly_plan2,2) as sticky_monthly_plan2,
							round(sticky_monthly_plan3,2) as sticky_monthly_plan3, 
							round(sticky_yearly,2) as sticky_yearly, 
							round(sticky_yearly_plan1,2) as sticky_yearly_plan1,
							round(sticky_yearly_plan2,2) as sticky_yearly_plan2,
							round(sticky_yearly_plan3,2) as sticky_yearly_plan3
						from  did_cities
						where city_id=$city_id";
		
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting plan prices for city id $city_id. ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No record found while getting plan prices for city_id $city_id.");
		}
		$row = mysql_fetch_array($result);
		return $row;
		
	}
	
	private function getExchangeRate($user_currency=SITE_CURRENCY){
		
		if($user_currency=="AUD"){
			$exchange_rate = 1;
		}else{
			switch($user_currency){
				case "GBP":
					$rate_option = 'currency_rate_gbp_to_aud';
					break;
				case "default";
					die("Error: Unknown currency $currency paramater passed to Did_Cities.getAvailableCities.");
					break;
			}
			$query = "select config_value from  system_config_options where config_option='".$rate_option."'";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				die("Error: An error occurred while getting Exchange rate of currency $currency: ".mysql_error());
			}
			if(mysql_num_rows($result)==0){
				die("Error: No record found while getting Exchange rate for currency $currency in system config options.");
			}
			$row=mysql_fetch_array($result,MYSQL_ASSOC);
			$exchange_rate = $row["config_value"];
		}
		return $exchange_rate;
	}
	
}