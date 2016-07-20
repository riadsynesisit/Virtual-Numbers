<?php

//Check session and login
require("/var/www/html/stickynumber/dashboard/includes/config.inc.php");

$conn = mysql_connect(SQLHOST, SQLUSER, SQLPASS) or die("Database Connection Error!!");
mysql_select_db(SQLDB, $conn) or die("Database not found!!");

//Set default timezone
date_default_timezone_set('Europe/London');
	 
$getfile=$_GET['target'];

$handle = fopen($getfile, "r");//Open the uploaded file for reading
if($handle==false){
	echo "Error: File $getfile could not be opened.";
	exit;
}
//Delete all of the existing data in the table
$trsql='truncate table dcm_destination_costs';
mysql_query($trsql) or die(mysql_error());
//echo "dcm_destination_costs table is truncated.<br />";
$i=0;
while (($data = fgetcsv($handle, 0, ",")) !== FALSE)
{           
	
	$i++;
	
	if($data[4]==""){
		$data[4] = 0;
	}
	
	if( !isset($data[5]) || $data[5] == ""){
		$data[5] = "N" ;
	}
		
	if($i>1 &&  !empty($data[0]) && !empty($data[1]) && !empty($data[2]) && !empty($data[3]) && (!empty($data[4]) || $data[4]==0) )
	{
		//echo "<br />Processing Row $i. $data[0], $data[1], $data[2], $data[3], $data[4], $data[5]";	
		if (is_numeric($data[2]) && is_numeric($data[3]) && is_numeric($data[4])  ) {
			$sql = "INSERT into dcm_destination_costs(prefix,destination,peak_per_min_gbp,offpeak_per_min_gbp,connection_charge_gbp, docs_required ) values('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]', '$data[5]')";
			
            mysql_query($sql) or die(mysql_error());
			
		}
	}
}//end of while loop
   
fclose($handle);
//update the last_changed date in dcm_origination_config - this invalidates all the catched data
$upsql='update dcm_origination_config set last_changed=now()';
mysql_query($upsql) or die(mysql_error());

?>
