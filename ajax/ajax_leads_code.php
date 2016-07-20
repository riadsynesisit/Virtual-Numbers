<?php

require("../dashboard/includes/config.inc.php");
require("../dashboard/includes/db.inc.php");
require("../dashboard/classes/Lead_accounts.class.php");

//Validate inputs
if(isset($_POST["user_id"])==false || trim($_POST["user_id"])==""){
	echo "Error: Required parameter user_id not passed.";
	exit;
}else{
	$user_id = trim($_POST["user_id"]);
}

if(isset($_POST["bg_color"])==false || trim($_POST["bg_color"])==""){
	echo "Error: Required parameter bg_color not passed.";
	exit;
}else{
	$bg_color = trim($_POST["bg_color"]);
}

if(isset($_POST["txt_color"])==false || trim($_POST["txt_color"])==""){
	echo "Error: Required parameter txt_color not passed.";
	exit;
}else{
	$txt_color = trim($_POST["txt_color"]);
}

if(isset($_POST["fontfamily"])==false || trim($_POST["fontfamily"])==""){
	echo "Error: Required parameter fontfamily not passed.";
	exit;
}else{
	$fontfamily = trim($_POST["fontfamily"]);
}

if(isset($_POST["fontsize"])==false || trim($_POST["fontsize"])==""){
	echo "Error: Required parameter fontsize not passed.";
	exit;
}else{
	$fontsize = trim($_POST["fontsize"]);
}

if(isset($_POST["borderwidth"])==false || trim($_POST["borderwidth"])==""){
	echo "Error: Required parameter borderwidth not passed.";
	exit;
}else{
	$borderwidth = trim($_POST["borderwidth"]);
}

if(isset($_POST["form_width"])==false || trim($_POST["form_width"])==""){
	echo "Error: Required parameter form_width not passed.";
	exit;
}else{
	$form_width = trim($_POST["form_width"]);
}

if(isset($_POST["form_height"])==false || trim($_POST["form_height"])==""){
	echo "Error: Required parameter form_height not passed.";
	exit;
}else{
	$form_height = trim($_POST["form_height"]);
}

$lead_accounts_obj = new Lead_accounts();
if(is_object($lead_accounts_obj)==false){
	echo "Error: Failed to create Lead Accounts object.";
	exit;
}

$form_html = "";

//$form_html .="<html><head>";

$form_html .= "
	<!-- saved from url=(0023)http://www.stickynumber.com/ -->
	<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js' type='text/javascript'></script>
	<script>
		
		function submit_lead(){
			
			if($.trim($('#txtsnname').val())==''){
				$('#txtsnname').focus();
				alert('Please enter your name.');
				return false;
			}
			if($.trim($('#txtsnemail').val())==''){
				$('#txtsnemail').focus();
				alert('Please enter your Email id.');
				return false;
			}
			if(validateEmail($.trim($('#txtsnemail').val()))==false){
				$('#txtsnemail').focus();
				alert('Please enter a valid Email id.');
				return false;
			}
			if($.trim($('#txtsnphone1').val())==''){
				$('#txtsnphone1').focus();
				alert('Please enter your Phone Number.');
				return false;
			}
			if(window.confirm('Are you sure you want to submit this information?')==false){
				return false;
			}
			var url='".SITE_URL."process_lead.php';
			var postdata = {'txtsnname':$('#txtsnname').val(),'txtsnemail':$('#txtsnemail').val(),'txtsnphone1':$('#txtsnphone1').val(),'txtsnphone2':$('#txtsnphone2').val(),'sn_user_id':$('#sn_user_id').val()};
			$.post(url, postdata, function(response){
							if(response.substr(0,6)!='Error:'){
								$('#txtsnname').val('');
								$('#txtsnemail').val('');
								$('#txtsnphone1').val('');
								//alert(response);
								$('#div_lead_form').html(response);
								return;
							}else{
								alert(response);
								return;
							}
						});
			/*$.ajax(
				{async: false, 
				contentType: 'text/plain',
				crossDomain: true, 
				data: postdata, 
				success: function(response){
							if(response.substr(0,6)!='Error:'){
								$('#txtsnname').val('');
								$('#txtsnemail').val('');
								$('#txtsnphone1').val('');
								//alert(response);
								$('#div_lead_form').html(response);
								return;
							}else{
								alert(response);
								return;
							}
						},
				type: POST, 
				url: '".SITE_URL."process_lead.php', 
				xhrFields: {withCredentials: false}
				}
			)
				.fail(
					function(jqXHR, textStatus, errorThrown){
						alert('Failed to post data. Error Status is :' +textStatus);
					}
				);*/
		}
		function validateEmail(sEmail) {
		    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		    if (filter.test(sEmail)) {
		        return true;
		    }
		    else {
		        return false;
		    }
		}
		function isNumber(evt) {
	        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
	        if (iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
	            return false;
	
	        return true;
	    }
	</script>
";
//$form_html .= "</head><body>";
$form_html .= "<div id='div_lead_form' style='background-color:".$bg_color.";font-family:".$fontfamily.";font-size:".$fontsize.";color:".$txt_color.";border-width:".$borderwidth."px;border-style:solid;width:".$form_width."px;height:".$form_height."px;'>\n";
$form_html .= "<form id='frmSNLeadForm' method='post' action='".SITE_URL."process_lead.php'>\n";
$form_html .= "<table style='padding:6px;border-spacing:8px;border-collapse:separate;' cellspacing='4px'>\n";
$form_html .= "<tr><td>Name:</td><td><input type='text' name='txtsnname' id='txtsnname' value=''></td></tr>\n";
$form_html .= "<tr><td>Email:</td><td><input type='text' name='txtsnemail' id='txtsnemail' value=''></td></tr>\n";
$form_html .= "<tr><td>Phone:</td><td><input type='text' name='txtsnphone1' id='txtsnphone1' value='' onkeypress='javascript:return isNumber(event)'></td></tr>\n";
//$form_html .= "<tr><td>Phone 2:</td><td><input type='text' name='txtsnphone2' id='txtsnphone2' value='' onkeypress='javascript:return isNumber(event)'></td></tr>";
$form_html .= "<tr><td>&nbsp;</td><td><button type='button' onclick='javascript:submit_lead();'>Submit</button></td></tr>\n";
$form_html .= "</table>\n";
$form_html .= "<input type='hidden' name='sn_user_id' id='sn_user_id' value='".$user_id."'>\n";
$form_html .= "</form>\n";
$form_html .= "</div>\n";
//$form_html .= "</body></html>";

//Now save this code in database field
$status = $lead_accounts_obj->update_lead_form_code($user_id,$form_html);
if(is_bool($status) && $status===true){
	//Now create the code for the iframe - Show this code in the box
	$iframe_width = $form_width + 40;
	$iframe_height = $form_height + 40;
	//$iframe_html = "<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js' type='text/javascript'></script>";
	$iframe_html .= "<iframe src=\"".SITE_URL."get_lead_form.php?id=$user_id\" width=\"".$iframe_width."\" height=\"".$iframe_height."\" style=\"border:none\" frameborder=\"0\"></iframe>";
}else{
	echo $status;
	exit;
}

echo $iframe_html;
//echo $form_html;