<?php

class Did_Countries{
	
	function getAvailableCountries(){
		
		$query = "select distinct dco.country_name as country_name,
							dco.country_prefix as country_prefix,
							dco.country_iso as country_iso
						from  did_cities dci,
								did_countries dco
						where dci.did_country_id = dco.id and
								dci.isavailable = 1 and
								ifnull(LOWER(dci.restricted),'no')!='yes' and 
								ifnull(LOWER(dci.blocked),'no')!='yes' 
						order by dco.country_name asc";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting Available Countries: ".mysql_error());
		}
		$ret_arr = array();
		while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
			$ret_arr[]=$row;
		}
		return $ret_arr;
	}
	
	function getAvailableAPICountries(){
		
		$query = "select distinct dco.country_name as country_name,
							dco.country_prefix as country_prefix,
							dco.country_iso as country_iso
						from  did_cities dci,
								did_countries dco
						where dci.did_country_id = dco.id and
								dci.isavailable = 1 and
								ifnull(LOWER(dci.restricted),'no')!='yes' and 
								ifnull(LOWER(dci.non_api_access),'no')!='yes' and
								ifnull(LOWER(dci.blocked),'no')!='yes' 
						order by dco.country_name asc";
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
	
	function getCountryPrefixFromISO($country_iso){
		
		$query = "select country_prefix from did_countries where country_iso='$country_iso'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			die("Error: An error occurred while getting country_prefix for country_iso $country_iso: ".mysql_error());
		}
		if(mysql_num_rows($result)==0){
			die("Error: No record found while getting country_prefix for country_iso: $country_iso.");
		}
		$row = mysql_fetch_array($result);
		return $row["country_prefix"];
	}
	
}