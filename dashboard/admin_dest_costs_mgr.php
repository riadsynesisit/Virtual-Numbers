<?php

require "./includes/DRY_setup.inc.php";

function process_dcm_thresholds_update(){
	
	global $action_message;
	
	$dcmoriginationconfig = new DCMOriginationConfig();
	
	if(isset($_POST['origin_set_uk'])){
		$dcmoriginationconfig->within_uk = 1;
		$dcmoriginationconfig->did_range=70;
	}elseif(isset($_POST['origin_set_intl'])){
		$dcmoriginationconfig->within_uk = 0;
		$dcmoriginationconfig->did_range=70;
	}elseif(isset($_POST['origin087_set_uk'])){
		$dcmoriginationconfig->within_uk = 1;
		$dcmoriginationconfig->did_range=87;
	}elseif(isset($_POST['origin087_set_intl'])){
		$dcmoriginationconfig->within_uk = 0;
		$dcmoriginationconfig->did_range=87;
	}
	
	$dcmoriginationconfig->peak_limit = $_POST['peak_limit'];
	$dcmoriginationconfig->evening_limit = $_POST['evening_limit'];
	$dcmoriginationconfig->weekend_limit = $_POST['weekend_limit'];
	$dcmoriginationconfig->flag_call_limit = $_POST['flag_call_limit'];
        $dcmoriginationconfig->flag_call_cost = $_POST['flag_call_cost'];
	
	if($dcmoriginationconfig->saveOriginationLimits()==true){
		$action_message .= "<br /> Changes saved successfully!";
	}else{
		$action_message .= "<br /> Changes were NOT saved!";
	}
	
}

function get_page_content($this_page){

	//check if the user is an admin, otherwise redirect to index page
	if($_SESSION['admin_flag']!=1){
		not_admin_user();
		return;
	}
	
	global $action_message;
	$page_vars = array();
	
	$action_message = "";
	
	if(isset($_GET["action_msg"]) && trim($_GET["action_msg"])!=""){
		$action_message .= "<font color=green><b>".$_GET["action_msg"]."</b></font>";
	}
	
	$this_page->content_area = "";
	
	$sco = new System_Config_Options();
	//check to see if we got 087 DID Destination Call Cost Limit sent to us
	if(isset($_POST['call087_cost_limit'])){
		$status = $sco->update087DIDCallCostLimit($_POST['call087_cost_limit']);
		if($status){
			$action_message .= "<font color=green><b>Call Cost Limit to offer 087 Range successfully updated.</b></font>";
		}else{
			$action_message .= "<font color=red><b>Failed to update Call Cost Limit to offer 087 Range.</b></font>";
		}
	}
	//check to see if we got SIP Mark Up Cost sent to us
	if(isset($_POST['sip_mark_up'])){
		$status = $sco->updateSIPMarkUpCost($_POST['sip_mark_up']);
		if($status){
			$action_message .= "<font color=green><b>SIP Mark Up Cost successfully updated.</b></font>";
		}else{
			$action_message .= "<font color=red><b>Failed to update SIP Mark Up Cost.</b></font>";
		}
	}
	
	//check to see if we got currency rate GBP to AUD sent to us
	if(isset($_POST['rate_gbp_aud'])){
		$status = $sco->updateRateGBPToAUD($_POST['rate_gbp_aud']);
		if($status){
			$action_message .= "<font color=green><b> Currency Rate GBP to AUD successfully updated.</b></font>";
		}else{
			$action_message .= "<font color=red><b> Failed to update Currency Rate GBP to AUD.</b></font>";
		}
	}
	//check to see if we got Toll-free Mark Up Cost sent to us
	if(isset($_POST['tf_mark_up'])){
		$status = $sco->updateConfigOption("tollfree_mark_up_cost",$_POST['tf_mark_up']);
		if($status){
			$action_message .= "<font color=green><b>Toll-free Mark Up Cost successfully updated.</b></font>";
		}else{
			$action_message .= "<font color=red><b>Failed to update Toll-free Mark Up Cost.</b></font>";
		}
	}
	
	//check to see if we got currency rate USD to AUD sent to us
	if(isset($_POST['rate_usd_aud'])){
		$status = $sco->updateConfigOption("currency_rate_usd_to_aud",$_POST['rate_usd_aud']);
		if($status){
			$action_message .= "<font color=green><b> Currency Rate USD to AUD successfully updated.</b></font>";
		}else{
			$action_message .= "<font color=red><b> Failed to update Currency Rate USD to AUD.</b></font>";
		}
	}
	
	//check to see if we got form data sent to us
	if(isset($_POST['flag_call_limit'])){
		process_dcm_thresholds_update();
	}

	$action_message .= "<br /> <br /> Either Edit and Save the existing Dial-Cost thresholds, or upload a new CSV";
	$page_vars['action_message'] = $action_message;
	$page_vars['menu_item'] = 'dest_costs_thresholds';
	
	$dcmoriginationconfig = new DCMOriginationConfig();
	
	$origin_set_uk = array();
	$origin_set_intl = array();
	$origin087_set_uk = array();
	$origin087_set_intl = array();
	
	$origin_set_uk = $dcmoriginationconfig->getUKOriginationLimits(70);
	$origin_set_intl = $dcmoriginationconfig->getIntlOriginationLimits(70);
	$origin087_set_uk = $dcmoriginationconfig->getUKOriginationLimits(87);
	$origin087_set_intl = $dcmoriginationconfig->getIntlOriginationLimits(87);
	
	$page_vars['origin_set_uk'] = $origin_set_uk;
	$page_vars['origin_set_intl'] = $origin_set_intl;
	$page_vars['origin087_set_uk'] = $origin087_set_uk;
	$page_vars['origin087_set_intl'] = $origin087_set_intl;
	
	$page_vars['call087_cost_limit'] = $sco->get087DIDCallCostLimit();
	$page_vars['sip_mark_up_cost'] = $sco->getSIPMarkUpCost();
	$page_vars['rate_gbp_aud'] = $sco->getRateGBPToAUD();
	$page_vars['tf_mark_up_cost'] = $sco->getConfigOption("tollfree_mark_up_cost");
	$page_vars['rate_usd_aud'] = $sco->getConfigOption("currency_rate_usd_to_aud");
	
	//build the page
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	$this_page->content_area .= getUploadCSVPage();//This is in pages/dcm_csv_upload.inc.php
	$this_page->content_area .= getDestCostsThresholdsPage();//This is in pages/dest_costs_thresholds.inc.php
	
	common_header($this_page,"'My Numbers'");
	
}

require("includes/DRY_authentication.inc.php");

?>
