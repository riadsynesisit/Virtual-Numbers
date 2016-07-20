<?php 
	/*require("./dashboard/classes/Did_Plans.class.php");
    $did_plans = new Did_Plans();
	if(is_object($did_plans)!=true){
		die("Error: Failed to create Did_Plans object.");
	}
	$plans = $did_plans->getDIDPlans();
	//Plan 1 details
	$plan1_annual = $plans[0]["annual_price"];
	$plan1_monthly = number_format($plans[0]["monthly_price"],2);
	$plan1_whole_dollars = substr($plan1_monthly,0,strlen($plan1_monthly)-3);
	$plan1_cents = substr($plan1_monthly,-2,2);
	//Plan 2 details
	$plan2_annual = $plans[1]["annual_price"];
	$plan2_monthly = number_format($plans[1]["monthly_price"],2);
	$plan2_whole_dollars = substr($plan2_monthly,0,strlen($plan2_monthly)-3);
	$plan2_cents = substr($plan2_monthly,-2,2);
	//Plan 3 details
	$plan3_annual = $plans[2]["annual_price"];
	$plan3_monthly = number_format($plans[2]["monthly_price"],2);
	$plan3_whole_dollars = substr($plan3_monthly,0,strlen($plan3_monthly)-3);
	$plan3_cents = substr($plan3_monthly,-2,2);*/
	
	if(isset($_GET["cartId"]) && trim($_GET["cartId"])!=""){
	 	include('confirm.php');
	}else{
		if(isset($_GET["page"]) && trim($_GET["page"])!=""){
			$next_page = trim($_GET["page"]).'.php';
		}else{
			$next_page = 'inc_sel_number.php';
		}
		if(isset($_POST["next_page"]) && trim($_POST["next_page"])!=""){
			$next_page = trim($_POST["next_page"]);
		}
		//echo $next_page;exit;
		include($next_page);
	}
 
    include('inc_footer_AU.php'); 
?> 