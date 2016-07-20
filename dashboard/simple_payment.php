<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
	if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){
		$checkOutID = $_GET['id'];
		$cart_id = $_GET['cart_id'];
		$this_page->content_area .= getSimplePaymentPage($checkOutID, $cart_id);
	
		$page_vars['menu_item'] = 'Add Number';
		$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
		common_header($this_page,"SimplePay");
	}else{
        //Jump to login screen
        $this_page->meta_refresh_sec = READ_SCREEN_BEFORE_REDIRECT_SECONDS+12;
        $this_page->meta_refresh_page = "login.php";

        $this_page->content_area = "";
        $this_page->content_area .= "We're sorry, you must be logged in as a verified user to access this service. </ br>\n";
        $this_page->content_area .= "We will now  try to automatically take you to the login page.  If nothing seems to happen in a few seconds, just click <a href='login.php'>this link</a> and we will take you there. </ br>\n";

        common_header($this_page, "Not Logged In - Login Required", false, $meta_refresh_sec, $meta_refresh_page);
   }
	
}


function getSimplePaymentPage($checkOutID, $cart_id){
	
	//global $this_page;
	
	//$page_vars = $this_page->variable_stack;
	
	
$page_html = '<script src="'.SIMPLEPAY_WIDGET_URL.'/v1/paymentWidgets.js?checkoutId='.$checkOutID.'"></script>';
	
	$page_html .= '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h3 class="page-header">Make Payment :: Amount- '.html_entity_decode(CURRENCY_SYMBOL).$_SESSION['pay_amount'].'</h3></div><div class="clearfloat">&nbsp;</div><br/>';

	
	
	
    //end of pagination
	
	$page_html .= '
		<form action="'.SITE_URL.'?page=payment_confirm&cart_id='.$cart_id.'" class="paymentWidgets">
		VISA MASTER
		</form>';
	
	$page_html .= '</div>';
	$page_html .= '</div>';
	return $page_html;
	
}

require("includes/DRY_authentication.inc.php");

?>