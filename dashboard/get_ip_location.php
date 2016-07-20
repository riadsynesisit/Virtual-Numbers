<?php

if(isset($_POST["process"])){
	include("./includes/config.inc.php");
	include("./libs/utilities.lib.php");
	
	if(isset($_GET["ip"])==true && trim($_GET["ip"])!=""){
		$ip_address = trim($_GET["ip"]);
	}elseif(isset($_POST["ip"])==true && trim($_POST["ip"])!=""){
		$ip_address = trim($_POST["ip"]);
	}else{
		echo "Error: Required parameter ip not passed.";
		exit;
	}
	
	$ip_location = get_ip_location($ip_address);
	
	echo "IP Location is: ".$ip_location."<br /><br /><br />";
}

?>
<html>
<head>
<title>IP Location Finder</title>
</head>
<body>
<form method="post" name="frmIP">
<input type="hidden" name="process" />
Please enter the IP Address: <input type="text" name="ip" />
<input type="submit">
</form>
</body>
</html>