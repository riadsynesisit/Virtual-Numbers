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
	
	$this_page->content_area = "";
	$this_page->content_area .= get_bl_ip_addresses_html();
	//@this_user.update_last_login!
	$page_vars['menu_item'] = 'pending_orders';
	$this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	common_header($this_page, "Black Listed IP Addresses");
	
}

function get_bl_ip_addresses_html(){
	
	global $this_page;
	$page_vars = $this_page->variable_stack;
	
	$page_html='<div class="title-bg"><h1>&nbsp;Black listed IP Addresses</h1></div>
				<div class="mid-section">
				
				<!-- Date section start here -->
					<div class="date-bar">
					<div class="left-info"><b></b></div>
					
					</div>
					<!-- Date section end here -->';
	
	$page_html .= '
		<script type="text/javascript">
			function white_list(input_value){
			
				var input_type="ip_address";
				
				if(confirm("Are you sure you want to white list IP Address: "+input_value+"?")==false){
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
		</script>';
	
	$blacklist_ip = new Blacklist_ip();
    if(is_object($blacklist_ip)==false){
    	$page_html .= "Error: Unable to create Blacklist_ip object.";
    	return $page_html;
    }
    
    $total_count = $blacklist_ip->get_black_listed_ips_count();
    if(is_numeric($total_count)==false && substr($total_count,0,6)=="Error:"){
    	$page_html .= "Error: Unable to get count of black listed IP addresses. ".$total_count;
    	return $page_html;
    }
    
	// sort out pagination
    // first, figure out the page selector data
    $records_per_page   =   RECORDS_PER_PAGE;
    $page_count         =   ceil($total_count / $records_per_page);

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
    $safe_page_select   =   $_GET['page'];
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
    
    //now update the variable stack
    $page_vars['page_count']    =   $page_count;
    $page_vars['page_deck']     =   $page_deck;

    $page_vars['page_current']  =   $page_current;
    $page_vars['page_current_range_start']  =   $page_current_range_start;
    $page_vars['page_current_range_end']    =   $page_current_range_end;
    
    //end of pagination
    
    # => now, only get the records for this page
    if($total_count>0){
    	$blacklisted_ips = $blacklist_ip->get_black_listed_ips($records_per_page, $page_current_range_start - 1);
    	if(is_array($blacklisted_ips)==false && substr($blacklisted_ips,0,6)=="Error:"){
    		$page_html .= "Error: Unable to get count of black listed IP addresses. ".$blacklisted_ips;
    		return $page_html;
    	}
    }else{
    	$page_html .= '<font color="red">No black listed IP addresses found.</font>';
    	return $page_html;
    }
    	
    $page_html .= '<table width="100%" border="0" cellspacing="0" cellpadding="7" class="update-details">';
    $page_html .= '<tr class="title"><td>IP Address</td><td>Date Added</td><td>Actions</td></tr>';
    foreach($blacklisted_ips as $row){
    	$page_html .='<tr><td>'.$row["ip_address"].'</td><td>'.$row["update_date"].'</td><td><input type="button" name="btnWhiteList" value="White List" class="logged-button" style="background:#359DD1; padding:0px 6px 2px 6px; border-radius:4px; width:auto; margin-top:7px;" onclick="javascript:white_list(\''.$row["ip_address"].'\');" ></td></tr>';
    }
    $page_html .= '</table>';
    
    if($page_vars['page_count'] > 1){	
		$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$total_count."\n"; 
		$page_html .= '<select name="paginate" onchange="window.location = \'bl_ip_address.php?page=\'+this.value">'."\n";
		$page_deck = $page_vars['page_deck'];
		$i=0;
		foreach($page_deck as $page_range){
			$i++;
			$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
		}			 
		$page_html .= '</select></p>';
	}//end of if page count > 1
	
	return $page_html;
	
}

require("includes/DRY_authentication.inc.php");

?>