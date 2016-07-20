<?php

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]=="/voicemail_au.php"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /voicemail.php");
		exit;
	}
	
}

?>