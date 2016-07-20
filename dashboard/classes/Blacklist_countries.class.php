<?php
class Blacklist_countries{
	
	function is_country_black_listed($country_iso){
		
		$query = "select count(*) as rec_count from blacklist_countries where country_iso='$country_iso'";
		$result = mysql_query($query);
		if(mysql_error()!=""){
			return "Error: An error occurred while getting count from blacklist_countries : ".mysql_error();
		}
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		return $row["rec_count"];
		
	}
	
}