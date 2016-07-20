<?php

require "./includes/DRY_setup.inc.php";

function process_fraud_detection_update(){
	
	global $action_message;
	
	$frauddetection = new FraudDetection();
	
	
	
	$frauddetection->a_num_times = $_POST['a_num_times'];	
	$frauddetection->a_seconds = $_POST['a_seconds'];
	$frauddetection->b_1_last_from_sec = $_POST['b_1_last_from_sec'];
	$frauddetection->b_1_period = $_POST['b_1_period'];
	$frauddetection->b_1_seconds = $_POST['b_1_seconds'];
	$frauddetection->b_2_last_from_sec = $_POST['b_2_last_from_sec'];
	$frauddetection->b_2_last_to_sec = $_POST['b_2_last_to_sec'];
	$frauddetection->b_2_period = $_POST['b_2_period'];
	$frauddetection->b_2_seconds = $_POST['b_2_seconds'];
	$frauddetection->b_3_last_from_sec = $_POST['b_3_last_from_sec'];
	$frauddetection->b_3_last_to_sec = $_POST['b_3_last_to_sec'];
	$frauddetection->b_3_period = $_POST['b_3_period'];
	$frauddetection->b_3_seconds = $_POST['b_3_seconds'];
	$frauddetection->b_4_last_from_min = $_POST['b_4_last_from_min'];
	$frauddetection->b_4_period = $_POST['b_4_period'];
	$frauddetection->b_4_seconds = $_POST['b_4_seconds'];
	$frauddetection->c_total_debit = $_POST['c_total_debit'];
	$frauddetection->c_value = $_POST['c_value'];
	$frauddetection->d_total_debit = $_POST['d_total_debit'];
	$frauddetection->d_debit_percentage = $_POST['d_debit_percentage'];
	$frauddetection->d_value = $_POST['d_value'];
	$frauddetection->max_forward = $_POST['max_forward'];
	
	
	if($frauddetection->saveFraudDetection()==true){
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
	$this_page->content_area = "";
	
	//check to see if we got form data sent to us
	if(isset($_POST['submit'])){
		process_fraud_detection_update();
	}

	$action_message .= "<br /> <br /> Either Edit and Save the existing Fraud Detection Data";
	$page_vars['action_message'] = $action_message;
	$page_vars['menu_item'] = 'fraud_detection';
	
	$frauddetection = new FraudDetection();
	
	
	
	$fraud_values = $frauddetection->getFraudDetection();
	
	
	$page_vars['fraud_values'] = $fraud_values;
	
	
	//build the page
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	$this_page->content_area .= getFraudDetectionPage();//This is in pages/fraud_detection.inc.php
	
	common_header($this_page,"'Fraud Detection'");
	
}

require("includes/DRY_authentication.inc.php");

?>
