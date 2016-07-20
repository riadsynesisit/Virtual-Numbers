<?php
class Country {
	public $id;
    
    public function __construct() {
    
    }
    
    public function getAllCountry() {
        $query = "SELECT iso, country_code, printable_name FROM country ORDER BY printable_name ASC";
        $result = mysql_query($query);
        
        if(mysql_error()==""){
	        $return_data = array();
	        while($row = mysql_fetch_assoc($result)) {
	            $return_data[] = $row;    
	        }
	    
	        return $return_data;
        }else{
        	die("An error occured while getting countries list from pbx_system_db.country table: ".mysql_error());
        }
    }

	public function getCountryCityPrefix( $country_iso = 'AU'){
		$query = "SELECT country_code, prefix, ccp.city_id, city_name, IFNULL(sticky_setup, 0) as sticky_setup, sticky_monthly FROM country_city_prefixes ccp INNER JOIN did_cities dc ON ccp.city_id = dc.city_id INNER JOIN country ON country.iso = ccp.country_iso WHERE country_iso = '".$country_iso."' ORDER BY city_name ASC ";
        $result = mysql_query($query);
        
        if(mysql_error()==""){
	        $return_data = array();
	        while($row = mysql_fetch_assoc($result)) {
	            $return_data[] = $row;    
	        }
	    
	        return $return_data;
        }else{
        	die("An error occured while getting city list: ".mysql_error());
        }
		
	}
}
?>