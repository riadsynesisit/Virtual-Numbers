<?php

require "./includes/DRY_setup.inc.php";

function get_page_content($this_page){
	
	global $action_message;
	global $this_user;
	
	//check if the user is an admin, otherwise redirect to index page
	if($_SESSION['admin_flag']==1){
		show_index_page();
	}else{
		not_admin_user();
	}
	
}

function show_index_page(){
	
	global $action_message;
	global $this_page;
	$page_vars = array();
	
	# => build the page
	$this_page->content_area .= getPendingOrdersPage();
	
	$page_vars['menu_item'] = 'pending_orders';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	common_header($this_page,"Pending Orders");
	
}

function getPendingOrdersPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
	
	$page_html = '
		<div id="page-wrapper">
			<div class="row">
            	<div class="col-lg-12">
                	<h1 class="page-header">Order Overview</h1>
	';

	$risk_score = new Risk_score();
	if(is_object($risk_score)==false){
		die("Error: Failed to create Risk_score object.");
	}
	$pending_orders_count = $risk_score->get_orders_count_of_status(5);//Authorized orders
	$cancelled_orders_count = $risk_score->get_cancelled_orders_count();
	$deleted_orders_count = $risk_score->get_deleted_orders_count();
	$refunded_orders_count = $risk_score->get_refunded_orders_count();
	$approved_orders_count = $risk_score->get_approved_orders_count();
	$search_orders_count = 0;//Initialize
	$is_search_result = false;//Initialize
	if(isset($_POST["input_action"]) && trim($_POST["input_action"])!=""){
		if(trim($_POST["input_action"])=="search"){
			$is_search_result = true;
			$searchSelected = " selected ";
			$search_orders_count = $risk_score->get_search_orders_count(trim($_POST["input_type"]),trim($_POST["input_value"]));
			if(is_numeric($search_orders_count)==false && substr($search_orders_count,0,6)=="Error:"){
				die("Error: Failed to get count of records as per search criteria. ".$search_orders_count);
			}
		}
	}
	
	if($page_vars['action_message'] != ""){
		$page_html .= '<div class="tip_box">';
		$page_html .= $page_vars['action_message'];
		$page_html .= '</div>';
	}
	
	$page_html .= '
		<script type="text/javascript">
			
			function approve_order(cart_id){
				if(confirm(\'Are you sure you want to approve this order?\')==false){
					return;
				}
				jQuery.ajaxSetup({async:false});
				var url = "../ajax/ajax_process_order.php";
				$.post(url, {"cart_id":cart_id, "action":"approve"},function(response){
					if($.trim(response.toString())=="Success"){
						alert("This order is successfully approved.");
						location.reload();
					}else{
						alert(response);
					}
				});
			}
			
			function send_fraud_check_email(cart_id, btnThis){
				console.log(btnThis);
				//return false;
				jQuery.ajaxSetup({async:false});
				var url = "../ajax/ajax_send_fraud_check_email.php";
				$.post(url, {"cart_id":cart_id},function(response){
					alert(response);
					if(response == "Success"){
						btnThis.css("background","#D2DC33");
					}
				});
			}
			
			function cancel_order(cart_id){
				if(confirm(\'Are you sure you want to cancel this order?\')==false){
					return;
				}
				jQuery.ajaxSetup({async:false});
				var url = "../ajax/ajax_process_order.php";
				$.post(url, {"cart_id":cart_id, "action":"cancel"},function(response){
					if($.trim(response.toString())=="Success"){
						alert("This order is successfully cancelled.");
						location.reload();
					}else{
						alert(response);
					}
				});
			}
			
			function delete_order(cart_id){
				if(confirm(\'Are you sure you want to delete this order?\')==false){
					return;
				}
				jQuery.ajaxSetup({async:false});
				var url = "../ajax/ajax_process_order.php";
				$.post(url, {"cart_id":cart_id, "action":"delete"},function(response){
					if($.trim(response.toString())=="Success"){
						alert("This order is successfully deleted.");
						location.reload();
					}else{
						alert(response);
					}
				});
			}
			
			function refund_order(cart_id){
			
				if(confirm(\'Are you sure you want to refund this order?\')==false){
					return;
				}
				jQuery.ajaxSetup({async:false});
				var url = "../ajax/ajax_process_order.php";
				$.post(url, {"cart_id":cart_id, "action":"refund"},function(response){
					if($.trim(response.toString())=="Success"){
						alert("This order is successfully refunded.");
						location.href="pending_orders.php";
					}else{
						alert(response);
					}
				});
			
			}
			
			function validate_inputs(){
				if($.trim($("#cart_id").val())!="" && isNaN($.trim($("#cart_id").val()))==true){
					alert("Please enter a valid Cart ID (numeric input only).");
					$("#cart_id").focus();
					return false;
				}
				if($.trim($("#did_number").val())!="" && isNaN($.trim($("#did_number").val()))==true){
					alert("Please enter a valid DID (numeric input only).");
					$("#did_number").focus();
					return false;
				}
				if($.trim($("#dest_number").val())!="" && isNaN($.trim($("#dest_number").val()))==true){
					alert("Please enter a valid Destination Number (numeric input only).");
					$("#dest_number").focus();
					return false;
				}
				if($.trim($("#email").val())!="" && validateEmail($.trim($("#email").val()))==false){
					alert("Please enter a valid Email id.");
					$("#email").focus();
					return false;
				}
				if($.trim($("#member_id").val())!="" && validate_member_id($.trim($("#member_id").val()))==false){
					alert("Please enter a valid Member ID (Letter \'M\' followed by numerals).");
					$("#member_id").focus();
					return false;
				}
				if($.trim($("#ip_address").val())!="" && ValidateIPaddress($.trim($("#ip_address").val()))==false){
					alert("Please enter a valid IP Address.");
					$("#ip_address").focus();
					return false;
				}
				if($.trim($("#cart_id").val())=="" && 
					$.trim($("#did_number").val())=="" && 
					$.trim($("#dest_number").val())=="" && 
					$.trim($("#email").val())=="" && 
					$.trim($("#member_id").val())=="" && 
					$.trim($("#ip_address").val())==""){
					alert("No search text entered.\n\nPlease enter a value in any one of the inputs.");
					return false;
				}
				return true;
			}
			
			function search_order(){
				if(is_only_one_input()==false){
					alert("More than input box filled in.\n\nPlease enter value in only one input box.");
					return;
				}
				if(validate_inputs()==false){
					return;
				}
				var filled_input = get_filled_input_type();
				var filled_value = get_filled_input_value();
				$("#input_type").val(filled_input);
				$("#input_value").val(filled_value);
				$("#input_action").val("search");
				$("#inputs_form").submit();
				
			}
			
			function black_list(){
				if(is_only_one_input()==false){
					alert("More than input box filled in.\n\nPlease enter value in only one input box.");
					return;
				}
				//Check which input is filled to be added to black list
				var input_type = "";
				var input_value = "";
				if($.trim($("#dest_number").val())!=""){
					input_type = "dest_number";
					input_value = $.trim($("#dest_number").val());
				}else if($.trim($("#ip_address").val())!=""){
					input_type = "ip_address";
					input_value = $.trim($("#ip_address").val());
				}
				if(input_type==""){
					alert("Either a Destination Number or an IP Address can be black listed.\n\nPlease enter desired input.");
					return;
				}
				//Now validate the input as entered
				if(validate_inputs()==false){
					return;
				}
				
				if(confirm("Are you sure you want to black list "+input_type+" "+input_value+"?")==false){
					return;
				}
				
				//Use ajax to black list the desired input value
				jQuery.ajaxSetup({async:false});
				var url = "../ajax/ajax_blacklist.php";
				$.post(url, {"input_type":input_type, "input_value":input_value},function(response){
					if($.trim(response.toString())=="Success"){
						alert("This value is successfully blacklisted.");
						location.reload();
					}else{
						alert(response);
					}
				});
			}
			
			function white_list(){
				if(is_only_one_input()==false){
					alert("More than input box filled in.\n\nPlease enter value in only one input box.");
					return;
				}
				
				//Check which input is filled to be white list
				var input_type = "";
				var input_value = "";
				if($.trim($("#dest_number").val())!=""){
					input_type = "dest_number";
					input_value = $.trim($("#dest_number").val());
				}else if($.trim($("#ip_address").val())!=""){
					input_type = "ip_address";
					input_value = $.trim($("#ip_address").val());
				}
				if(input_type==""){
					alert("Either a Destination Number or an IP Address can be white listed.\n\nPlease enter desired input.");
					return;
				}
				//Now validate the input as entered
				if(validate_inputs()==false){
					return;
				}
				
				if(confirm("Are you sure you want to white list "+input_type+" "+input_value+"?")==false){
					return;
				}
				
				//Use ajax to black list the desired input value
				jQuery.ajaxSetup({async:false});
				var url = "../ajax/ajax_whitelist.php";
				$.post(url, {"input_type":input_type, "input_value":input_value},function(response){
					if($.trim(response.toString())=="Success"){
						alert("This value is successfully white listed.");
						location.reload();
					}else{
						alert(response);
					}
				});
				
			}
			
			function reset_inputs(){
			
				$("#cart_id").val("");
				$("#did_number").val("");
				$("#dest_number").val("");
				$("#email").val("");
				$("#member_id").val("");
				$("#ip_address").val("");
			
			}
			
			function change_order_status(){
				$("#paginate").val("1");//Set the pagination to first page when the order status drop down is changed
				$("#order_status_form").submit();
			}
			
			function change_page(){
				$("#order_status_form").submit();
			}
			
			function is_only_one_input(){
				var input_filled = false;
				if($.trim($("#cart_id").val())!=""){
					if(input_filled == true){
						return false;
					}else{
						input_filled = true;
					}
				}
				if($.trim($("#did_number").val())!=""){
					if(input_filled == true){
						return false;
					}else{
						input_filled = true;
					}
				}
				if($.trim($("#dest_number").val())!=""){
					if(input_filled == true){
						return false;
					}else{
						input_filled = true;
					}
				}
				if($.trim($("#email").val())!=""){
					if(input_filled == true){
						return false;
					}else{
						input_filled = true;
					}
				}
				if($.trim($("#member_id").val())!=""){
					if(input_filled == true){
						return false;
					}else{
						input_filled = true;
					}
				}
				if($.trim($("#ip_address").val())!=""){
					if(input_filled == true){
						return false;
					}else{
						input_filled = true;
					}
				}
				return true;
			}
			
			function get_filled_input_type(){
				if($.trim($("#cart_id").val())!=""){
					return "cart_id";
				}else if($.trim($("#did_number").val())!=""){
					return "did_number";
				}else if($.trim($("#dest_number").val())!=""){
					return "dest_number";
				}else if($.trim($("#email").val())!=""){
					return "email";
				}else if($.trim($("#member_id").val())!=""){
					return "member_id";
				}else if($.trim($("#ip_address").val())!=""){
					return "ip_address";
				}
			}
			
			function get_filled_input_value(){
				if($.trim($("#cart_id").val())!=""){
					return $.trim($("#cart_id").val());
				}else if($.trim($("#did_number").val())!=""){
					return $.trim($("#did_number").val());
				}else if($.trim($("#dest_number").val())!=""){
					return $.trim($("#dest_number").val());
				}else if($.trim($("#email").val())!=""){
					return $.trim($("#email").val());
				}else if($.trim($("#member_id").val())!=""){
					return $.trim($("#member_id").val());
				}else if($.trim($("#ip_address").val())!=""){
					return $.trim($("#ip_address").val());
				}
			}
			
			function remove_non_numerics(val){
				if (val.length == 0) {
                	return "";
                }
                        
                var tmp = val;
                        
                var last_char = val[ val.length -1 ]
                var char_code = val.charCodeAt(val.length - 1) 
                if ((char_code < 48 || char_code > 57)) {
                	tmp = "";
                    for(var i=0; i < val.length - 1; i++) {
                    	tmp += val[i]         
                    }                 
                } 
                return tmp;
			}
			
			function validate_member_id(member_id){
				var first_char = member_id[0];
				if(first_char!="M"){
					return false;
				}
				
				for(var i=1;i < member_id.length - 1; i++){
					var char_code = member_id.charCodeAt(i);
					if ((char_code < 48 || char_code > 57)) {
						return false;
					}
				}
				
				return true;
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
			
			function ValidateIPaddress(ipaddress){  
 				if(/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ipaddress)){  
    				return true;
  				}  
				return false;
			}
			
			function remove_member_id_non_numerics(val){
				if (val.length == 0) {
                	return "";
                }
                
                if (val.length == 1) {
                	if(val=="M"){
                		return val;
                	}else{
                		return "";
                	}
                }
                        
                var tmp = val;
                        
                var last_char = val[ val.length -1 ]
                var char_code = val.charCodeAt(val.length - 1) 
                if ((char_code < 48 || char_code > 57)) {
                	tmp = "";
                    for(var i=0; i < val.length - 1; i++) {
                    	tmp += val[i]         
                    }                 
                } 
                return tmp;
			}
			
			function remove_ip_address_non_numerics(val){
				if (val.length == 0) {
                	return "";
                }
                
                if (val.length == 1) {
                	if(val=="."){
                		return "";
                	}
                }
                        
                var tmp = val;
                        
                var last_char = val[ val.length -1 ]
                if(last_char=="."){
                	return tmp;
                }
                
                var char_code = val.charCodeAt(val.length - 1) 
                if ((char_code < 48 || char_code > 57)) {
                	tmp = "";
                    for(var i=0; i < val.length - 1; i++) {
                    	tmp += val[i]         
                    }                 
                } 
                return tmp;
			}
			
			$(document).ready(function(){
			    //Call the tooltip function on mouseover of any element having
			    //class of .moreinfo
			    $(".moreinfo").mouseover(function(e){
			    	//Get the tooltip text
			    	var cart_id=$(this).attr("cart-id");
			    	//Make ajax call to get the more-info html from the server for this cart_id
			    	jQuery.ajaxSetup({async:false});
			    	var url = "../ajax/ajax_moreinfo.php";
					$.post(url, {"cart_id" : cart_id}, function(response){
						//Inject the tooltip text and Show tooltip_container div
			    		$("#moreinfo_container").html(response).fadeIn(100);		
                    });
			    }).mousemove(function(e){
			    	//On mouse move trace the x and y axis
			    	// Set the position of tooltip div to cursor position
			    	//Set left position
			    	$("#moreinfo_container").css("left",(e.pageX+30)+"px");
			    	//Set top Position
			    	$("#moreinfo_container").css("top",(e.pageY-240)+"px");
			    }).mouseout(function(e){
			    	// On mouse out hide the tooltip div
			    	$("#moreinfo_container").css("display","none").html("");
			    });
			    
			    $("#cart_id").keyup(function(event) {
                        var val = remove_non_numerics($(this).val());
                        $(this).val(val);
                });
                
                $("#did_number").keyup(function(event) {
                        var val = remove_non_numerics($(this).val());
                        $(this).val(val);
                });
                
                $("#dest_number").keyup(function(event) {
                        var val = remove_non_numerics($(this).val());
                        $(this).val(val);
                });
                
                $("#member_id").keyup(function(event) {
                        var val = remove_member_id_non_numerics($(this).val());
                        $(this).val(val);
                });
                
                $("#ip_address").keyup(function(event) {
                        var val = remove_ip_address_non_numerics($(this).val());
                        $(this).val(val);
                });
		    });

		</script>
	';
	
	if(isset($_POST["input_type"]) && trim($_POST["input_type"])!=""){
		switch(trim($_POST["input_type"])){
			case "cart_id":
				$cart_id_val = trim($_POST["input_value"]);
				break;
			case "did_number":
				$did_number_val = trim($_POST["input_value"]);
				break;
			case "dest_number":
				$dest_number_val = trim($_POST["input_value"]);
				break;
			case "email":
				$email_val = trim($_POST["input_value"]);
				break;
			case "member_id":
				$member_id_val = trim($_POST["input_value"]);
				break;
			case "ip_address":
				$ip_address_val = trim($_POST["input_value"]);
				break;
			default:
				
				break;
		}
	}
	
	$selOrderStatus = 5;//Set to Authorized but not yet caputured orders by default (Status = 5 for Pending orders).
	$pendingSelected = "";//Initialize
	$approvedSelected = "";
	$refundedSelected = "";
	$deletedSelected = "";
	$cancelledSelected = "";
	
	$total_count = 0;//Initialize
	
	if(isset($_POST["selOrderStatus"]) && trim($_POST["selOrderStatus"])!=""){
		$selOrderStatus = intval(trim($_POST["selOrderStatus"]));
		switch($selOrderStatus){
			case 1:
				$approvedSelected = " selected ";
				$total_count = $approved_orders_count;
				break;
			case 2:
				$refundedSelected = " selected ";
				$total_count = $refunded_orders_count;
				break;
			case 3:
				$deletedSelected = " selected ";
				$total_count = $deleted_orders_count;
				break;
			case 4:
				$cancelledSelected = " selected ";
				$total_count = $cancelled_orders_count;
				break;
			case 5:
				$pendingSelected = " selected ";
				$total_count = $pending_orders_count;
				break;
		}
	}else{
		if($is_search_result!==true){
			$pendingSelected = " selected ";//Default selection
			$total_count = $pending_orders_count;
		}else{
			$total_count = $search_orders_count;
		}
	}
	
	// sort out pagination
    // first, figure out the page selector data
    $records_per_page   =   100;//RECORDS_PER_PAGE; changed 
    $page_count         =   ceil($total_count / $records_per_page);
    
    //Create the page deck array
	$page_deck  =   array();
        
    for($page=1;$page<=$page_count;$page++){
    	
        $first_record_in_range  =   $page * $records_per_page - ($records_per_page -1 );
        $last_record_in_range   =   $page * $records_per_page;
        if($last_record_in_range>   $total_count){
            $last_record_in_range   =   $total_count;
        }
        
        $page_deck[$page]       =   $first_record_in_range ." - ".$last_record_in_range;
    }
    
	//now figure out what page we are on
    $page_current       =   1;
    $safe_page_select   =   $_POST["paginate"];
    if($safe_page_select!=  ""){
        $safe_page_select   =   intval($safe_page_select);
    }
    
	if($safe_page_select > 0 && $safe_page_select != $page_current){
        $page_current   =    $safe_page_select;
    }
    
	$page_current_range_start   =   $page_current * $records_per_page - ($records_per_page -1 );
    $page_current_range_end     =   $page_current * $records_per_page;
    if ($page_current_range_end >   $total_count){
        $page_current_range_end =   $total_count;
    }
    
    //end of pagination
	
	$page_html .= '
		<style>
			#moreinfo_container{
				padding: 1em;
				background:rgba(100,100,100,1);
				color:#FFF;
				width:550px;
				border-radius: 12px;
				font-size: 1em;
				position: absolute;
				display: none;
				text-decoration: none;
			} 
		</style>
		 <div id="moreinfo_container">
			More Info
		</div> 
		<table>
			<tr>
				<td>Pending Fraud:</td>
				<td>'.$pending_orders_count.'</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td>&nbsp;</td>
				<td><input name="cart_id" id="cart_id" type="text" value="'.$cart_id_val.'"></td>
				<td>Cart ID</td>
			</tr>
			<tr>
				<td>Cancelled:</td>
				<td>'.$cancelled_orders_count.'</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td>&nbsp;</td>
				<td><input name="did_number" id="did_number" type="text" value="'.$did_number_val.'"></td>
				<td>DID</td>
			</tr>
			<tr>
				<td>Fraud Deleted:</td>
				<td>'.$deleted_orders_count.'</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td><a href="bl_dest_number.php">View Black List</a></td>
				<td><input name="dest_number" id="dest_number" type="text" value="'.$dest_number_val.'"></td>
				<td>Destination Number</td>
			</tr>
			<tr>
				<td>Refunded:</td>
				<td>'.$refunded_orders_count.'</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td>&nbsp;</td>
				<td><input name="email" id="email" type="text" value="'.$email_val.'"></td>
				<td>Email</td>
			</tr>
			<tr>
				<td>Approved:</td>
				<td>'.$approved_orders_count.'</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td>&nbsp;</td>
				<td><input name="member_id" id="member_id" type="text" value="'.$member_id_val.'"></td>
				<td>Member ID</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td><a href="bl_ip_address.php">View Black List</a></td>
				<td><input name="ip_address" id="ip_address" type="text" value="'.$ip_address_val.'"></td>
				<td>IP Address</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td>&nbsp;</td>
				<td colspan="2">
				<input type="button" name="btnSearch" value="Search" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:search_order();" >
				<input type="button" name="btnBlackList" value="Black List" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:black_list();" >
				<input type="button" name="btnWhiteList" value="White List" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:white_list();" >
				<input type="button" name="btnResetInputs" value="Reset Inputs" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:reset_inputs();" >
				<input type="button" name="btnShowAll" value="Show All" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:location.href=\'pending_orders.php\';" >
				</td>
			</tr>
		</table>
		
		<form name="order_status_form" id="order_status_form" action="pending_orders.php" method="post">
		&nbsp;&nbsp;&nbsp;Order Status:
		<select name="selOrderStatus" id="selOrderStatus" onchange="javascript:change_order_status();">';
			if($is_search_result==true){
				$page_html .= '<option value="" '.$searchSelected.'>All Searched</option>';
			}
			$page_html .= '<option value="5" '.$pendingSelected.'>Pending Fraud</option>
			<option value="1" '.$approvedSelected.'>Approved</option>
			<option value="2" '.$refundedSelected.'>Refunded</option>
			<option value="3" '.$deletedSelected.'>Fraud Deleted</option>
			<option value="4" '.$cancelledSelected.'>Cancelled</option>
		</select>';
		if($page_count>1){
			$page_html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Showing '.$page_current_range_start.'-'.$page_current_range_end.' of '.$total_count."\n"; 
			$page_html .= '<select name="paginate" id="paginate" onchange="javascript:change_page();">'."\n";
			$i=0;
			foreach($page_deck as $page_range){
				$i++;
				$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
			}			 
			$page_html .= '</select>';
		}
		$page_html .= '</form>
		<br />
	';
	
	if($is_search_result==true || $pending_orders_count>0 || $cancelled_orders_count>0 || $deleted_orders_count>0 || $refunded_orders_count>0 || $approved_orders_count>0){
		if($is_search_result==true){
			if($search_orders_count>0){
				$orders = $risk_score->get_search_orders(trim($_POST["input_type"]),trim($_POST["input_value"]),$records_per_page, $page_current_range_start - 1);//Search criteria
			}else{
				switch(trim($_POST["input_type"])){
					case "cart_id":
						$search_field = "Cart ID";
						break;
					case "did_number":
						$search_field = "DID";
						break;
					case "dest_number":
						$search_field = "Destination Number";
						break;
					case "email":
						$search_field = "Email";
						break;
					case "member_id":
						$search_field = "Member ID";
						break;
					case "ip_address":
						$search_field = "IP Address";
						break;
				}
				$page_html .= '<b><font color="red">&nbsp;&nbsp;&nbsp;No orders found matching to your search values of <u>'.$search_field.' = '.trim($_POST["input_value"]).'</u></font><b>';
			}
		}elseif($pending_orders_count>0 || $cancelled_orders_count>0 || $deleted_orders_count>0 || $refunded_orders_count>0 || $approved_orders_count>0){
			$orders = $risk_score->get_risk_orders($selOrderStatus, $records_per_page, $page_current_range_start - 1);//Status 5=Pending (Authorized by Payment Gateway but not yet captured).
		}
		if(is_array($orders)==false && trim($orders)==""){
			$page_html .= '<b><font color="red">&nbsp;&nbsp;&nbsp;No orders found.</font><b>';
		}else{
			foreach($orders as $order){
				$docHtml = "Pending Fraud";
				if($order['docs_required'] == 'Y'){
					$docHtml = "Documents Required";
				}
				$page_html .= '<br />
					<table style="background:#facacc" width="96%">
						<tr>
							<td nowrap>
							<a class="moreinfo" cart-id="'.$order["cart_id"].'">More Info</a>
							</td>
							<td colspan=2>';
							$display_status = "";
							if($selOrderStatus==5){//Pending High Risk Score Orders
								if($order['is_mail_sent'] == 'Y'){
									$bg_color = "#D2DC33";
								}else
									$bg_color = "#359DD1";
								
								$page_html .= '<input type="button" name="btnCancel" value="Cancel Order" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:cancel_order('.$order["cart_id"].');" >
												<input type="button" name="btnDelete" value="Fraud Delete" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:delete_order('.$order["cart_id"].');" >
												<input type="button" name="btnApprove" value="Approve Order" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:approve_order('.$order["cart_id"].');" >
												<input type="button" name="btnSendFrdEmail" value="Send Fraud Email" class="logged-button" style="background:'.$bg_color.'; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:send_fraud_check_email('.$order["cart_id"].', $(this));" >';
								$display_status = "Authorised";
							}elseif($selOrderStatus==1){//Approved Orders
								$page_html .= '<input type="button" name="btnRefund" value="Refund Order" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:refund_order('.$order["cart_id"].');" >';
								$display_status = "Captured";
							}elseif($selOrderStatus==2){//Refunded Orders
								$page_html .= 'Order Refunded';
								$display_status = "Refunded";
							}elseif($selOrderStatus==3){//Fraud Deleted Orders
								$page_html .= 'Fraud Deleted Order';
								$display_status = "Deleted";
							}elseif($selOrderStatus==4){//Cancelled Orders
								$page_html .= 'Cancelled Order';
								$display_status = "Cancelled";
							}
							if($order["member_status"]=="Active"){
								if($_SERVER["HTTP_HOST"]=="system.hawkingsoftware.ae"){
									$login_as_member_html = "<a href=\"https://www.stickynumber.com.au/dashboard/admin_login.php?id=".$order["member_id"]."\" target=\"blank\">Login as Member</a>";
								}elseif($_SERVER["HTTP_HOST"]=="dev.stickynumber.com"){
									$login_as_member_html = "<a href=\"http://dev.stickynumber.com.au/dashboard/admin_login.php?id=".$order["member_id"]."\" target=\"blank\">Login as Member</a>";
								}else{
									$login_as_member_html = "Login As Member";
								}
							}else{
								$login_as_member_html = "<font color='red'>Login Restricted</font>";
							}
			$page_html .= '</td><td nowrap>'.$login_as_member_html.'</td><td colspan=2 nowrap><b>'.$order["pymt_gateway"].' - '.$display_status.'</b></td>
						</tr>
						<tr>
							<td nowrap>
							'.$order["amount"].' AUD
							</td>
							<td>
							'.$order["description"].' '.$order["did_number"].'
							</td>
							<td>
							'.$order["transTime"].'
							</td>
							<td>
							'.$docHtml.'
							</td>
							<td>
							'.$order["pymt_type"].'
							</td>
							<td nowrap>Risk Score:
							'.$order["risk_score"].'
							</td>
						</tr>
					</table>
				<br />';
			}//end of foreach loop
		}//end of else part of empty array check - no orders check
	}
	
	$page_html .= "<form name='inputs_form' id='inputs_form' action='pending_orders.php' method='post'>";
	$page_html .= "<input type='hidden' name='input_type' id='input_type' value='' />";
	$page_html .= "<input type='hidden' name='input_value' id='input_value' value='' />";
	$page_html .= "<input type='hidden' name='input_action' id='input_action' value='' />";
	$page_html .= "</form>";
	
	$page_html .= '</div>';
	$page_html .= '</div>';
	return $page_html;
	
}

require("includes/DRY_authentication.inc.php");

?>