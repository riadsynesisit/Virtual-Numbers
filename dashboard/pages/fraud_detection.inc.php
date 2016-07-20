<?php
function getFraudDetectionPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$fraud_values = array();
	
	
	$fraud_values = $page_vars['fraud_values'];
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Fraud Detection Data</h1>
	';	
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	
	
	$page_html .= '
	<form action="admin_fraud_detection_mgr.php" method="post" accept-charset="utf-8">
	<table width="100%" border="0" cellspacing="0" cellpadding="7" class="update-details" >
		<tr >
			<td width="250px"><b>Number OF Times Called With In Seconds</b></td>
		</tr>
		<tr>
			<td>Number OF Times Called:</td><td> <input type="text" name="a_num_times" value="'.$fraud_values['a_num_times'].'" id="a_num_times" class="dcm_input" size="6" > 
		</tr>
        <tr>		
			<td>Seconds:</td><td> <input type="text" name="a_seconds" value="'.$fraud_values['a_seconds'].'" id="a_seconds" class="dcm_input" size="6">
				
			</td>
			
		</tr>
		<tr>
				<td>
				&nbsp;
				</td>
				</tr>
		<tr >
			<td><b>Call Lasts Period</b></td>
		</tr>
		
		<tr>
			<td>Call Lasts Upto Seconds:</td><td> <input type="text" name="b_1_last_from_sec" value="'.$fraud_values['b_1_last_from_sec'].'" id="b_1_last_from_sec" class="dcm_input" size="6" > 
				</td>
				</tr>
        <tr><td>
				With In Seconds:</td><td> <input type="text" name="b_1_period" value="'.$fraud_values['b_1_period'].'" id="b_1_period" class="dcm_input" size="6">
				</td>
				</tr>
        <tr><td>
				Maximum Calls : </td><td><input type="text" name="b_1_seconds" value="'.$fraud_values['b_1_seconds'].'" id="b_1_seconds" class="dcm_input" size="6">
				
			</td>
			
				</tr>
				<tr>
				<td>
				&nbsp;
				</td>
				</tr>
		
		<tr>
		
			<td>Call Lasts From Seconds:</td><td> <input type="text" name="b_2_last_from_sec" value="'.$fraud_values['b_2_last_from_sec'].'" id="b_2_last_from_sec" class="dcm_input" size="6" > 
				</td>
				</tr>
        <tr><td>
				Call Lasts To Seconds:</td><td> <input type="text" name="b_2_last_to_sec" value="'.$fraud_values['b_2_last_to_sec'].'" id="b_2_last_to_sec" class="dcm_input" size="6">
				</td>
				</tr>
        <tr><td>
				With In Seconds: </td><td><input type="text" name="b_2_period" value="'.$fraud_values['b_2_period'].'" id="b_2_period" class="dcm_input" size="6">
				</td>
				</tr>
        <tr><td>
				Maximum Calls :</td><td> <input type="text" name="b_2_seconds" value="'.$fraud_values['b_2_seconds'].'" id="b_2_seconds" class="dcm_input" size="6">
				</td>
				</tr>
		
		<tr>
				<td>
				&nbsp;
				</td>
				</tr>
        
		<tr>
			<td>Call Lasts From Seconds:</td><td> <input type="text" name="b_3_last_from_sec" value="'.$fraud_values['b_3_last_from_sec'].'" id="b_3_last_from_sec" class="dcm_input" size="6" > 
				</td>
				</tr>
        <tr><td>
				Call Lasts To Minutes:</td><td> <input type="text" name="b_3_last_to_sec" value="'.$fraud_values['b_3_last_to_sec'].'" id="b_3_last_to_sec" class="dcm_input" size="6">
				</td>
				</tr>
        <tr><td>
				With In Seconds:</td><td> <input type="text" name="b_3_period" value="'.$fraud_values['b_3_period'].'" id="b_3_period" class="dcm_input" size="6">
				</td>
				</tr>
        <tr><td>
				Maximum Calls :</td><td> <input type="text" name="b_3_seconds" value="'.$fraud_values['b_3_seconds'].'" id="b_3_seconds" class="dcm_input" size="6">
				</td>
				</tr><tr>
				<td>
				&nbsp;
				</td>
				</tr>
		
		<tr>
			
		
			<td>Call Lasts Above Minutes:</td><td> <input type="text" name="b_4_last_from_min" value="'.$fraud_values['b_4_last_from_min'].'" id="b_4_last_from_min" class="dcm_input" size="6" > 
								
				</td>
				</tr>
        <tr><td>
				With In Seconds:</td><td> <input type="text" name="b_4_period" value="'.$fraud_values['b_4_period'].'" id="b_4_period" class="dcm_input" size="6">
				</td>
				</tr>
        <tr><td>
				Maximum Calls :</td><td> <input type="text" name="b_4_seconds" value="'.$fraud_values['b_4_seconds'].'" id="b_4_seconds" class="dcm_input" size="6">
				</td>
				</tr>
				
		
        <tr>
				<td>
				&nbsp;
				</td>
				</tr>
		
        
       
		<tr >
			<td><b>Total Debit Hours</b></td>
		</tr>
		<tr>
			<td>Total Debit Hours:</td><td> <input type="text" name="c_total_debit" value="'.$fraud_values['c_total_debit'].'" id="c_total_debit" class="dcm_input" size="6" > 
				</td>
				</tr>
        <tr><td>
				Bigger Than :</td><td> <input type="text" name="c_value" value="'.$fraud_values['c_value'].'" id="c_value" class="dcm_input" size="6"> &pound;
				</td>
				
			
		</tr>
		<tr>
				<td>
				&nbsp;
				</td>
				</tr>
		<tr >
			<td><b>Total Debit Hours With Percentage</b></td>
		</tr>
		<tr>
			<td>Total Debit Hours:</td><td> <input type="text" name="d_total_debit" value="'.$fraud_values['d_total_debit'].'" id="d_total_debit" class="dcm_input" size="6" > 
				</td>
				</tr>
        <tr><td>
				Average Previous Months Debit  Percentage:</td><td> <input type="text" name="d_debit_percentage" value="'.$fraud_values['d_debit_percentage'].'" id="d_debit_percentage" class="dcm_input" size="6">
				
				 Or <input type="text" name="d_value" value="'.$fraud_values['d_value'].'" id="d_value" class="dcm_input" size="6"> &pound;
				</td>
				</tr>
				<tr >
			<td><b>Maximum Similar Forwarding</b></td>
		</tr>
		<tr>
			<td>Maximum Similar Forwarding:</td><td> <input type="text" name="max_forward" value="'.$fraud_values['max_forward'].'" id="max_forward" class="dcm_input" size="6" > 
				</td>
				</tr>
				<tr>
				<td>
				&nbsp;
				</td>
				</tr>
        
		<tr>
		<td colspan="2" align="center">
		<input type="submit" value="Submit" name="submit">
		</td>
		</tr>
	</table>
	</form>
	
	';
	$page_html .= '</div>';
	$page_html .= '</div>';
	$page_html .= '</div>';
	return $page_html;
	
}