<?php
	/*$referer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
	if( $referer != 'localhost' && !strstr(trim( $referer ),'stickynumber') ){
		header('Location: ../');
		exit;
	}*/
    require("../dashboard/includes/config.inc.php");
    require("../dashboard/includes/db.inc.php");
    require("../dashboard/classes/Assigned_DID.class.php");
    
    $ip_address = $_POST['ip_address'];
    $did_obj = new Assigned_DID();
    
    $did_ips = $did_obj->getIPCount($ip_address);
    
    echo $did_ips;