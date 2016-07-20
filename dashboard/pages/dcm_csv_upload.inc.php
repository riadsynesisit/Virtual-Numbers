<?php
function getUploadCSVPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Destination Costs - Upload Rate Table</h1>
	';	
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	
	
	$page_html .= '
	
	<!--Progress Bar and iframe Styling-->
	<link href="../css/style_progress.css" rel="stylesheet" type="text/css" />
	
	<!--display bar only if file is chosen-->
	<script>
	
	$(document).ready(function() { 
	//
	
	//show the progress bar only if a file field was clicked
		var show_bar = 0;
	    $("#dcm_csv_file").click(function(){
			show_bar = 1;
	    });
	    
	    var show_bar_did = 0;
	    $("#did_csv_file").click(function(){
			show_bar_did = 1;
	    });
	
	//show iframe on form submit
	    $("#fileupload").submit(function(){
	
			if (show_bar === 1) { 
				$("#upload_frame").show();
				function set () {
					$("#upload_frame").attr("src","upload_frame.php?up_id=4e78d40e25711");
				}
				setTimeout(set);
			}
	    });
	    
	    $("#did_csv_upload").click(function(){
	    	if($("#did_csv_file").val() == ""){
	    		alert("Please select a file to upload");
	    		return;
	    	}
	    	if($("#did_csv_file").val().substr(-4,4) != ".csv"){
	    		alert("Incorrect file format.\n\nPlease select a CSV file");
	    		return;
	    	}
			$("#fileupload_did").submit();
			if (show_bar_did === 1) { 
				$("#upload_frame_did").show();
				function set () {
					$("#upload_frame_did").attr("src","upload_frame.php?up_id=4e78d40e25711");
				}
				setTimeout(set);
			}
	    });
	    
	    $("#sip_cost_submit").click(function(){
	    	var sip_mark_up = $("#sip_mark_up").val();
	    	if(sip_mark_up == ""){
	    		alert("Please enter SIP Mark Up Cost in percentage");
	    		$("#sip_mark_up").focus();
	    		return;
	    	}
	    	if(isNaN(sip_mark_up)){
	    		alert("Please enter a numeric SIP Mark Up Cost in percentage (0 to 1000)");
	    		$("#sip_mark_up").focus();
	    		return;
	    	}
	    	if(Number(sip_mark_up) < 0 || Number(sip_mark_up) > 1000){
	    		alert("Please enter a positive numeric SIP Mark Up Cost percentage between 0 to 1000");
	    		$("#sip_mark_up").focus();
	    		return;
	    	}
	    	
	    	var rate_gbp_aud = $("#rate_gbp_aud").val();
	    	if(rate_gbp_aud == ""){
	    		alert("Please enter Currency Rate GBP to AUD");
	    		$("#rate_gbp_aud").focus();
	    		return;
	    	}
	    	if(isNaN(rate_gbp_aud)){
	    		alert("Please enter a numeric Currency Rate GBP to AUD");
	    		$("#rate_gbp_aud").focus();
	    		return;
	    	}
	    	if(Number(rate_gbp_aud) <= 0 || Number(rate_gbp_aud) > 1000){
	    		alert("Please enter a positive numeric Currency Rate GBP to AUD greater than 0  up to 1000");
	    		$("#rate_gbp_aud").focus();
	    		return;
	    	}
	    	$("#frm_sip_cost").submit();
	    });
	    
	    $("#tf_cost_submit").click(function(){
	    	var tf_mark_up = $("#tf_mark_up").val();
	    	if(tf_mark_up == ""){
	    		alert("Please enter Toll-free Mark Up Cost in percentage");
	    		$("#tf_mark_up").focus();
	    		return;
	    	}
	    	if(isNaN(tf_mark_up)){
	    		alert("Please enter a numeric Toll-free Mark Up Cost in percentage (0 to 1000)");
	    		$("#tf_mark_up").focus();
	    		return;
	    	}
	    	if(Number(tf_mark_up) < 0 || Number(tf_mark_up) > 1000){
	    		alert("Please enter a positive numeric Toll-free Mark Up Cost percentage between 0 to 1000");
	    		$("#tf_mark_up").focus();
	    		return;
	    	}
	    	
	    	var rate_usd_aud = $("#rate_usd_aud").val();
	    	if(rate_usd_aud == ""){
	    		alert("Please enter Currency Rate USD to AUD");
	    		$("#rate_usd_aud").focus();
	    		return;
	    	}
	    	if(isNaN(rate_usd_aud)){
	    		alert("Please enter a numeric Currency Rate USD to AUD");
	    		$("#rate_usd_aud").focus();
	    		return;
	    	}
	    	if(Number(rate_usd_aud) <= 0 || Number(rate_usd_aud) > 1000){
	    		alert("Please enter a positive numeric Currency Rate USD to AUD greater than 0  up to 1000");
	    		$("#rate_usd_aud").focus();
	    		return;
	    	}
	    	$("#frm_tf_cost").submit();
	    });
	    
	//
	
	});
	
	</script>

	<form name="fileupload" id="fileupload" enctype="multipart/form-data" action="csvupload.php" method="post">
		<a href="download_sip_costs_csv.php">Download SIP Costs CSV template file</a> 
		<br/>
		<input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key" value="4e78d40e25711" />
		<input type="file" name="dcm_csv_file" id="dcm_csv_file" size="40" />
		<input type="submit" name="submit" value="ok"/>
		
		<!--Include the iframe-->
    	<iframe id="upload_frame" name="upload_frame" frameborder="0" border="0" src="" scrolling="no" scrollbar="no" > </iframe>
    	<br />
		
	</form>
	<br />
	
	';
	
	$page_html .= '</div>';
	$page_html .= '</div>';
	
	return $page_html;
}

?>