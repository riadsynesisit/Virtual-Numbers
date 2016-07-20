<?php

function is_app_online($app_name){
	
	$cmd_result = "";
	$is_app_online = array();
	
	//obviously expects CentOS-type Linux ...
	if($app_name=="asterisk" && ($_SERVER["HTTP_HOST"]=="www.stickynumber.com" || $_SERVER["HTTP_HOST"]=="system.hawkingsoftware.ae")){
		$cmd_result = is_asterisk_online();
	}elseif($app_name=="mysqld" && ($_SERVER["HTTP_HOST"]=="www.stickynumber.com" || $_SERVER["HTTP_HOST"]=="system.hawkingsoftware.ae")){
		$cmd_result = is_mysql_online();
	}else{
		$cmd_result = `ps -C $app_name`;
	}
	
	$temp_result = explode("\n",$cmd_result);

	if(strpos($temp_result[1], $app_name)){
		$is_app_online = array("green", "UP");
	}else{
		$is_app_online = array("red", "DOWN");
	}

	return $is_app_online;

}

function check_load($server){
	
	$cmd_result = "";
	switch($server){
		case "web":
			$cmd_result = `uptime`;
			break;
		case "ast":
			$cmd_result = check_ast_load($server);
			break;
		case "sql":
			$cmd_result = check_sql_load($server);
			break;
	}
	
	$raw_results = array();
	$raw_results = explode(",",$cmd_result.split);	//[" 13:34:25 up 24 days", "  5:40", "  2 users", "  load average: 0.15", " 0.05", " 0.01\n"]
	$arrlen = count($raw_results);
	
	$cooked_results = array();
	$cooked_results[1] = (string)$raw_results[$arrlen - 2];
	$cooked_results[5] = (string)$raw_results[$arrlen - 1 ];
	$cooked_results[15] = (string)$raw_results[$arrlen ];
	
	$load_data = array();
	$load_data[1]  = floatval($cooked_results[1]);	// => 1 minute load average
	$load_data[5]  = floatval($cooked_results[5]); 	// => 5 minute load average
	$load_data[15] = floatval($cooked_results[15]); // => 15 minute load average

	$result_array = array();
	
	$time_intervals = array(1,5,15);
	
	foreach($time_intervals as $pointer){
		if($load_data[$pointer] >= 0.0 && $load_data[$pointer] < 0.6){
			//low load, green
			$result_array[$pointer] = array("green","OK: $load_data[$pointer]");
		}elseif($load_data[$pointer] >= 0.66 && $load_data[$pointer] < 1.32){
			//medium load, yellow
			$result_array[$pointer] = array("yellow","WARN: $load_data[$pointer]");
		}elseif($load_data[$pointer] >= 1.32){
			//high load, red
			$result_array[$pointer] = array("red","HIGH: $load_data[$pointer]");
		}else{
			//something is broken, orange
			$result_array[$pointer] = array("orange","UNKNOWN: $load_data[$pointer]");
		}
	}
	
	return $result_array;

}

function get_bandwidth($device='eth0'){
	// read the two sys files for the requested device and
	//	based on elapsed time and value, calculate the 
	//   resulting usage

	if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="localhost"){
		//Don't worry about the bandwidth usage reporting on localhost - Just use unknown data!
		return array("orange", "UNKNOWN: 0.000kb/sec");
	}
	
	$bandwidth	= array("orange", "UNKNOWN $device");
	$historyset	= array();
	$currentset	= array();
	$stampfile	= "/tmp/get_bandwidth.$device.php.stampfile";
	$rawdata		= "";
	
	if (file_exists($stampfile)==false) {
		$year_ago = mktime()-24*60*60*365;//Year ago from now!
		//create the file
		$temp_handle = fopen($stampfile,'w');
		fwrite($temp_handle,$year_ago.",0,0");
		fclose($temp_handle);
	}
	
	//=> get the history data
	$file = fopen($stampfile, "r") or exit("Unable to open history file!");
	//Output a line of the file until the end is reached
	while(!feof($file))
  	{
  		$rawdata = fgets($file);
  	}
	fclose($file);
	
	$tempdata = explode(",", $rawdata);

	$historyset['time']	= $tempdata[0];
	$historyset['rx']	= intval($tempdata[1]);
	$historyset['tx']	= intval($tempdata[2]);
	
	# => now get the current numbers
	$currentset['time']	= mktime();
	
	//for rx - /sys/class/net/eth0/statistics/rx_bytes
	$file = fopen("/sys/class/net/$device/statistics/rx_bytes", "r") or exit("Unable to open rx_bytes file!");
	while(!feof($file))
  	{
  		$line = fgets($file);
  		$currentset['rx'] = intval($line);
  	}
	fclose($file);
	//for tx - /sys/class/net/eth0/statistics/tx_bytes
	$file = fopen("/sys/class/net/$device/statistics/tx_bytes", "r") or exit("Unable to open tx_bytes file!");
	while(!feof($file))
  	{
  		$line = fgets($file);
  		$currentset['tx'] = intval($line);
  	}
	fclose($file);

	//calculate the delta
	$delta = 0;
	
	$rx = $currentset['rx'] - $historyset['rx'];
	$tx = $currentset['tx'] - $historyset['tx'];
	$timespan = $currentset['time'] - $historyset['time'];
	
	// => debug:  puts "rx #{rx}  | tx #{tx}  | timespan #{timespan} "
	
	$delta = ($rx + $tx) / $timespan / 1024;//KiloBytes Per Second
	
	// => update the history file with the current
	$outfile = fopen($stampfile,'w') or exit("Unable to open file for writing!");
	fwrite($outfile, $currentset['time'].",".$currentset['rx'].",".$currentset['tx']);
	fclose($file);
	
	$delta = floatval($delta);
	
	if ($delta >= 0 && $delta <= 1536){
		// => T1, green
		$bandwidth = array("green", "OK: ".number_format($delta,3)."kb/sec");
	}elseif($delta > 1536 && $delta <= 3972){
		# => 2 T1, yellow
		$bandwidth = array("yellow", "WARN: ".number_format($delta,3)."kb/sec");
	}elseif($delta > 3972 && $delta <= 999999){
		// => more than 2 T1, red
		$bandwidth = array("red", "HIGH: ".number_format($delta,3)."kb/sec");
	}else{
		// => something strange going on, orange
		$bandwidth = array("orange", "UNKNOWN: ".number_format($delta,3)."kb/sec");
	}
	
	# => return the results
	return $bandwidth;
	
} //end of get_bandwidth

function disk_space($path, $what='percent', $server='web'){
	
	if($server =="web"){
		$path_info = `df -Pk $path`;
	}else{
		if($server=="asterisk"){
			$path_info = getAstDiskFree($path);
		}elseif($server=="database"){
			$path_info = getSQLDiskFree($path);
		}else{
			exit("Unknown server: $server to get disk_space from.");
		}
	}
	
	
	$path_info = explode("\n",$path_info);
	$path_info = $path_info[1];
	
	$disk_space = array();
	
	$path_info = preg_replace('!\s+!', ' ', $path_info);//Reduce multiple white spaces into one white space
	$disk_data = explode(" ", $path_info);

	$disk_used = intval($disk_data[2]);
	$disk_free = intval($disk_data[3]);
	$percent_used = intval($disk_data[4]);
	
	$value = 0;
	$word = "";
	
	switch(strtolower($what)){
		case 'free':
			$value	= $disk_free /1024;
			$word	= "Mb free";
			break;
		case 'used':
			$value 	= $disk_used /1024;
			$word	= "Mb used";
			break;
		default :
			$value	= $percent_used;
			$word	= "% used";
			break;
	}
	
	if($percent_used >= 0 && $percent_used < 60){
		// => should be lots of space left, green
		$disk_data = array("green", "OK: $value$word");
	}elseif($percent_used >= 60 && $percent_used < 90){
		// => getting tight, raise a flag, yellow
		$disk_data = array("yellow", "WARN: $value$word");
	}elseif($percent_used >= 90 && $percent_used <= 100){
		// => we're full, red
		$disk_data = array("red", "FULL: $value$word");
	}else{
		// => something is broken, orange
		$disk_data = array("orange", "UNKNOWN: $value$word");
	} //percent_used
	
	return $disk_data;
}//disk_space

function asterisk_check_calls($what='concurrent',$did_type){
	
	$cmd_result = "";
	$cmd_result = connect_to_asterisk('core show channels concise');
	// SIP/MagTel_SIP-00000017!vssp!!1!Up!AppDial!(Outgoing Line)!07024092070!!3!256!SIP/213.166.5.134-00000016!1277396433.23
	// SIP/213.166.5.134-00000016!macro-did_route!s!9!Up!Dial!SIP/0015145121677@MagTel_SIP,30,!0701234567890!!3!256!SIP/MagTel_SIP-00000017!1277396433.22

	$incoming	= 0;
	$outgoing	= 0;
	$unsure		= 0;
	$concurrent	= 0;

	$raw_results = array();
	$raw_results = explode("\n",$cmd_result);
	
	foreach($raw_results as $record){
		$details = explode("!",$record);
		if($did_type == substr($details[8],0,3)){
			if(strpos($record,"AppDial")>0){
				$outgoing++;
			}elseif(strpos($record,"macro-did_route")>0){
				$incoming++;
			}else{
				$unsure++;
			}
		}
	}
	
	$concurrent = $incoming +	$outgoing + ($unsure-1);//substract one for header row
	
	$threshold = 24;	// T1
	
	switch(strtolower($what)){
		case 'incoming':
			$threshold = floor($threshold/2);
			$evaluate = $incoming;
			break;
		case 'outgoing':
			$threshold = floor($threshold/2);
			$evaluate = $outgoing;
			break;
		default :
			$evaluate = $concurrent;
			break;
	}
	
	$results_set = array("orange", "UNKNOWN $what");
	
	if($evaluate >=0 && $evaluate < $threshold){
		// => T1, green
		$results_set = array("green", "OK: $evaluate");
	}elseif($evaluate >= $threshold && $evaluate < $threshold*2){
		// => 2 T1, yellow
		$results_set = array("yellow", "WARN: $evaluate");
	}elseif($evaluate >= $threshold*2 && $evaluate < $threshold*100){
		// => more than 2 T1, red
		$results_set = array("red", "HIGH: $evaluate");
	}else{
		// => something strange going on, orange
		$results_set = array("orange", "UNKNOWN: $evaluate");
	}
	
	// => return the results
	return $results_set;
	
} //asterisk_check_calls

function connect_to_asterisk($command_sting){
	
	if($_SERVER["HTTP_HOST"]!="www.stickynumber.com" && $_SERVER["HTTP_HOST"]!="system.hawkingsoftware.ae"){
		return `/usr/sbin/asterisk -rx "$command_sting"`;
	}
	
	$ssh = new Net_SSH2(ASTERISK_IP);

	if (!$ssh->login(ASTERISK_USER, ASTERISK_PWD)) {
    	exit('Cannot get calls count. SSH Login to asterisk server '.ASTERISK_IP.' Failed');
	}
	
	$command_result = "";
	
	$command_result = $ssh->exec('/usr/sbin/asterisk -rx "'.$command_sting.'"');
	
	return $command_result;
} //connect_to_asterisk(command_sting)

function get_asterisk_hostname(){
	
	if($_SERVER["HTTP_HOST"]!="www.stickynumber.com" && $_SERVER["HTTP_HOST"]!="system.hawkingsoftware.ae"){
		return "dev";
	}
	
	$ssh = new Net_SSH2(ASTERISK_IP);

	if (!$ssh->login(ASTERISK_USER, ASTERISK_PWD)) {
    	exit('Cannot get asterisk hostname. SSH Login to asterisk server '.ASTERISK_IP.' Failed');
	}
	
	return $ssh->exec('hostname');
	
} //end of get_asterisk_hostname()

function get_sql_hostname(){
	
	if($_SERVER["HTTP_HOST"]!="www.stickynumber.com" && $_SERVER["HTTP_HOST"]!="system.hawkingsoftware.ae"){
		return "dev";
	}
	
	$ssh = new Net_SSH2(SQL_IP);

	if (!$ssh->login(SQL_USER, SQL_PWD)) {
    	exit('Cannot get MySQL hostname. SSH Login to MySQL server '.SQL_IP.' Failed');
	}
	
	return $ssh->exec('hostname');
	
} //end of get_asterisk_hostname()

function is_asterisk_online(){
	
	$ssh = new Net_SSH2(ASTERISK_IP);

	if (!$ssh->login(ASTERISK_USER, ASTERISK_PWD)) {
    	exit('Cannot get asterisk server status. SSH Login to asterisk server '.ASTERISK_IP.' Failed');
	}
	
	return $ssh->exec('ps -C asterisk');
	
} //end of get_asterisk_online()

function is_mysql_online(){
	
	$ssh = new Net_SSH2(SQL_IP);

	if (!$ssh->login(SQL_USER, SQL_PWD)) {
    	exit('Cannot get MySQL server status. SSH Login to mysql server '.SQL_IP.' Failed');
	}
	
	return $ssh->exec('ps -C mysqld');
	
} //end of get_mysql_online()

function check_ast_load(){
	
	if($_SERVER["HTTP_HOST"]!="www.stickynumber.com" && $_SERVER["HTTP_HOST"]!="system.hawkingsoftware.ae"){
		return `uptime`;
	}
	
	$ssh = new Net_SSH2(ASTERISK_IP);

	if (!$ssh->login(ASTERISK_USER, ASTERISK_PWD)) {
    	exit('Cannot get asterisk server load. SSH Login to asterisk server '.ASTERISK_IP.' Failed');
	}
	
	return $ssh->exec('uptime');
	
}

function check_sql_load(){
	
	if($_SERVER["HTTP_HOST"]!="www.stickynumber.com" && $_SERVER["HTTP_HOST"]!="system.hawkingsoftware.ae"){
		return `uptime`;
	}
	
	$ssh = new Net_SSH2(SQL_IP);

	if (!$ssh->login(SQL_USER, SQL_PWD)) {
    	exit('Cannot get MySQL server load. SSH Login of user '.SQL_USER.' to mysql server '.SQL_IP.' Failed');
	}
	
	return $ssh->exec('uptime');
	
}

function getAstDiskFree($path){
	
	if($_SERVER["HTTP_HOST"]!="www.stickynumber.com" && $_SERVER["HTTP_HOST"]!="system.hawkingsoftware.ae"){
		return `df -Pk $path`;
	}
	
	$ssh = new Net_SSH2(ASTERISK_IP);

	if (!$ssh->login(ASTERISK_USER, ASTERISK_PWD)) {
    	exit('Cannot get asterisk server load. SSH Login to asterisk server '.ASTERISK_IP.' Failed');
	}
	
	return $ssh->exec("df -Pk $path");
	
}

function getSQLDiskFree($path){
	
	if($_SERVER["HTTP_HOST"]!="www.stickynumber.com" && $_SERVER["HTTP_HOST"]!="system.hawkingsoftware.ae"){
		return `df -Pk $path`;
	}
	
	$ssh = new Net_SSH2(SQL_IP);

	if (!$ssh->login(SQL_USER, SQL_PWD)) {
    	exit('Cannot get MySQL server load. SSH Login of user '.SQL_USER.' to mysql server '.SQL_IP.' Failed');
	}
	
	return $ssh->exec("df -Pk $path");
	
}

?>