<?php 

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]=="/index.php"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /");
		exit;
	}
	
}

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="178.79.173.182"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: http://dev.stickynumber.com");
		exit;
}

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]=="178.79.166.188"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: https://www.stickynumber.com");
		exit;
}

if( isset($_SERVER["HTTP_HOST"]) && !strstr($_SERVER["HTTP_HOST"],"www.") && strstr($_SERVER["HTTP_HOST"],"stickynumber.com.au") && !strstr($_SERVER["HTTP_HOST"],"dev.") ){
	
	
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: https://www.stickynumber.com.au");
	exit;
	
}

session_start();
require("./dashboard/includes/config.inc.php");
require("./dashboard/includes/db.inc.php");

	if(isset($_GET["cartId"]) && trim($_GET["cartId"])!=""){
	 	include('confirm.php');
	}else{
		if(isset($_GET["page"]) && trim($_GET["page"])!=""){
			$next_page = trim($_GET["page"]).'.php';
		}else if( isset($_GET["code"]) && trim($_GET["code"] != "") ){
			$next_page = 'confirm-email.php';
		}else{
			$next_page = 'inc_sel_number.php';
		}
		if(isset($_POST["next_page"]) && trim($_POST["next_page"])!=""){
			$next_page = trim($_POST["next_page"]);
		}else if(strstr( $_SERVER['REQUEST_URI'], 'index.php?free_number' ) && !isset($_POST['next_page'])){
			header("Location: free-uk-number/");
			exit();
		}
		//echo $next_page;exit;
		if($next_page == 'choose_plan.php'){ 
			$inc_footer = false;
		}else{
			$inc_footer = true;
		}
		include($next_page);
		
	}
	if( !strstr( $_SERVER['REQUEST_URI'], 'free-uk-number') &&  $next_page != "inc_sel_number.php" && $next_page != "faq.php"  && $next_page != "features.php" && $next_page != "contact.php" && $next_page != "how.php" && $next_page != "aboutus.php" && $next_page != "feedback.php" && $inc_footer && $next_page != "pricing.php" && $next_page != 'forgot_password.php'  && $next_page != 'privacy.php' && $next_page != 'terms.php' && $next_page != "free_number_070_AU.php" && $next_page != "confirm.php" && $next_page != "confirm-email.php" && $next_page != "voicemail.php" && $next_page != "tutorial.php"){
		//echo $next_page.'<br/>';
		include('inc_footer_AU.php');
	}

?>