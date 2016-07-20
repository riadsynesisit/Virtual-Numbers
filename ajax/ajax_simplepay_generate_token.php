<?php

	session_start();
	require("../dashboard/includes/config.inc.php");
    $amount = $_POST['amount'];
	
	if(strstr( $amount, '.')){
		$decimal_points = substr($amount, strpos($amount, ".")+1);
		if(strlen($decimal_points) < 2){
				$amount = $amount ."0";
		}
	}else{
			$amount = $amount .".00";
	}
	$_SESSION['pay_amount'] = $amount;

	$is_recurrent = false;
	$plan_period = $_POST['did_plan_period'];
	if($plan_period == 'M'){
		$is_recurrent = true;
	}
	$url = SIMPLEPAY_URL;
	$data = "authentication.userId=" . SIMPLEPAY_USER . 
		"&authentication.password=" . SIMPLEPAY_PASS . 
 		"&authentication.entityId=" . SIMPLEPAY_ENTITYID . 
		"&amount=" . $amount .
		"&currency=" . SITE_CURRENCY . 
		"&paymentType=PA&createRegistration=" . $is_recurrent;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$resp = curl_exec($ch);
	if(curl_errno($ch)) {
		echo curl_error($ch);
	}
	curl_close($ch);
	//$data = json_decode($resp, true);
	echo $resp;
	/*$data = json_decode($resp, true);
	//echo "==url==";
	$js_url = "https://test.oppwa.com/v1/paymentWidgets.js?checkoutId=".$data['id'];
	//echo $js_url;
	
	//$html = file_get_contents($js_url, false);
	
	$ch = curl_init($js_url);
	//curl_setopt($ch, CURLOPT_URL, $js_url ); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
	$html = curl_exec($ch); 

	//$html .= '<form action=localhost/sticky/?page=payment_confirm&cart_id=555 class="paymentWidgets">VISA MASTER AMEX</form>';
	
	curl_close($ch);
	sleep(2);
	echo $html;
		*/
		
	/*
		[ Sandbox ]
Reporting credentials
https://test.simplepays.com/bip/entitylogin.link
user:   steve.s@stickynumber.com
pass:   MaP7XsXJ

API credentials
entityId:  8a82941850424bcb015045d62f85045f  //from site: 8a8294184e542a5c014e691d340708cc
user:  8a82941850424bcb015045d5971e045d // 8a8294184e542a5c014e691d33f808c8
pass:  rzyFdPr6 // from site: 2gmZHAeSWK

[ Live ]
 
Reporting credentials
https://simplepays.com/bip/entitylogin.link
user:   steve.s@stickynumber.com
pass:   (emailed out from system)

API credentials
entityId:  8a8394c14eb0a531014ed2c2c1e07527
user:  8a8394c54c97dbca014c9c51aaf4164c
pass:  J2x6CmnS

============== CARDS for TESTING =================

VISA	4200000000000000 (no 3D)
4012001037461114 (3D enrolled)
MASTER	5454545454545454 (no 3D)
5212345678901234 (3D enrolled)
AMEX	377777777777770 (no 3D)
375987000000005 (3D enrolled)

*/

?>