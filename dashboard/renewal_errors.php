<?php
	require "./includes/DRY_setup.inc.php";
	
	
	function get_page_content($this_page){
		global $action_message;
        
		if($_SESSION['admin_flag']!=1){
			echo "Error: You do not have sufficient privileges to access this page.";
			exit;
		}
		
		$dids_count = 0; //Initialize the number of DIDs to zero
		$auto_renewal_failures = new Auto_Renewal_Failures();
		if(is_object($auto_renewal_failures)==false){
			echo "Error: Failed to create Auto_Renewal_Failures object.";
			exit;
		}

		if(isset($_POST['btn_renew_error'])){
			$year = $_POST['renew_year'];
			$error_type = $_POST['error_type'];
		}else{
			$year = date('Y');
			$error_type = 'DIDWW';
		}
		$active_paid_dids_count = $auto_renewal_failures->getRenewalFailuresByYear(true,$year,$error_type);
		if(is_int($active_paid_dids_count)==false && substr($active_paid_dids_count,0,6)=="Error:"){
			echo $active_paid_dids_count;
			exit;
		}
		//echo $active_paid_dids_count;exit;
		if($active_paid_dids_count>0){
			// sort out pagination
	        // first, figure out the page selector data
	        $records_per_page   =   50;//RECORDS_PER_PAGE;
	        $page_count         =   ceil($active_paid_dids_count / $records_per_page);
	        
	    	$page_deck  =   array();
	        
	        for($page=1;$page<=$page_count;$page++)
	        {
	            $first_record_in_range  =   $page * $records_per_page - ($records_per_page -1 );
	            $last_record_in_range   =   $page * $records_per_page;
	            if($last_record_in_range>   $active_paid_dids_count)
	            {
	                $last_record_in_range   =   $active_paid_dids_count;
	            }
	        
	            $page_deck[$page]       =   $first_record_in_range ." - ".$last_record_in_range;
	        }
	        
	    	//now figure out what page we are on
	        $page_current       =   1;
	        $safe_page_select   =   $_POST['paginate'];
	        if($safe_page_select!=  "")
	        {
	            $safe_page_select   =   intval($safe_page_select);
	        }
	
	        if($safe_page_select > 0 && $safe_page_select != $page_current)
	        {
	            $page_current   =    $safe_page_select;
	        }
	
	        $page_current_range_start   =   $page_current * $records_per_page - ($records_per_page -1 );
	        $page_current_range_end     =   $page_current * $records_per_page;
	        if ($page_current_range_end >   $active_paid_dids_count)
	        {
	            $page_current_range_end =   $active_paid_dids_count;
	        }
			
	        //now update the variable stack
	        $page_vars['page_count']    =   $page_count;
	        $page_vars['page_deck']     =   $page_deck;
	
	        $page_vars['page_current']  =   $page_current;
	        $page_vars['page_current_range_start']  =   $page_current_range_start;
	        $page_vars['page_current_range_end']    =   $page_current_range_end;
	        
	        # => now, only get the records for this page
	        $renewal_failures_data = $auto_renewal_failures->getRenewalFailuresByYear(false,$year,$error_type,$page_current_range_start - 1, $records_per_page);
	        if(is_array($renewal_failures_data)===false && substr($renewal_failures_data,0,6)=="Error:"){
	        	echo $renewal_failures_data;
	        	exit;
	        }
			if(is_array(renewal_failures_data)===false && intval($renewal_failures_data)==0){
	        	echo "Error: No active paid DIDs exists.";
	        	exit;
	        }
	        $page_vars['renewal_failures_data']   =   $renewal_failures_data;
	        $page_vars['active_paid_dids_count']   =   $active_paid_dids_count;
	        $page_vars['menu_item'] =   'renewal_error';
	        $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	        $this_page->content_area.=  getRenewalErrorsPage();
	
	        common_header($this_page,"'Renewal Errors DIDs'");
		}//End of if active paid did count is > 0
        else{
			$this_page->content_area.=  getRenewalErrorsPage();
			common_header($this_page,"'Renewal Errors DIDs'");
			
		}
    }
	
    function getRenewalErrorsPage(){
    	
    	global $this_page;
	
		$page_vars = $this_page->variable_stack;
		
		$page_html = "";
        
        $page_html .= '
        	<script>
				function hideRow(af_id){
					var this_row = $(this).parent().parent();
					if(confirm("Are you sure?")){
						
						console.log(this_row.html());
						$.get("../ajax/ajax_hide_auto_renew_failures.php", {"id":af_id}, function(data){
							
							if(data == "success"){
								var button_id = "btn"+af_id;
								alert("Hidden");
								$("#"+button_id).closest("tr").hide("slow");
							}else{
								alert(data);
							}
						});
					}
				}
        	</script>';
		$error_type = isset($_POST['error_type']) ? $_POST['error_type'] : "DIDWW";
		$year = isset($_POST['year']) ? $_POST['year'] : date("Y") ;
        $page_html .='
        	<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">Renewal Errors</h1>
						<div>
							<form method="post">
								<label>Error Type: </label>
								<select name="error_type">
									<option '. ($error_type == "DIDWW" ? "selected" : " ") .' value="DIDWW">DIDWW Error</option>
									<option '. ($error_type == "billing" ? "selected" : " ") .' value="billing">Billing Failed</option>
									<option '. ($error_type == "" ? "selected" : " ") .' value="">ALL</option>
								</select>
								<label>&nbsp;&nbsp;Year</label>
								<select name="renew_year">
									<option '. ($year == date("Y") ? "selected" : "" ) .' value="'.date("Y").'">'.date('Y').'</option>
									<option '. ($year == date("Y",strtotime("-1 year")) ? "selected" : "" ) .' value="'.date("Y",strtotime("-1 year")).'">'.date("Y",strtotime("-1 year")).'</option>
									
								</select>&nbsp;&nbsp;
								<input type="submit" value="Show" name="btn_renew_error" />
							</form>
						</div>
						<div>&nbsp;</div>
                    	<div class="table-responsive">
							<table class="table table-hover">
								<thead class="blue-bg">
									<tr>
										<th>DID Number</th>
										<th align="center">Member ID</th>
										
										<th>Date Failed</th>
										<th>Error Message</th>
										<th>Payment Gateway</th>
										<th>Reason</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>';
        					if(intval($page_vars['active_paid_dids_count'])>0){
        						foreach($page_vars['renewal_failures_data'] as $my_DID){
        							
        							$page_html .= '
		        								<tr>
		        									<td>'.$my_DID['did_number'].'</td>
		        									<td>'.$my_DID['member_id'].'</td>
		        									
		        									<td>'.$my_DID['date_failed'].'</td>
		        									<td>'.$my_DID['error_message'].'</td>
													<td>'.$my_DID['pymt_gateway'].'</td>
													<td>'.$my_DID['failure_reason'].'</td>
													<td><input id="btn'.$my_DID['id'].'" type="button" value="Hide" onclick="hideRow('.$my_DID['id'].');" /></td>
		        								</tr>';
        						}//End of foreach loop
        					}else{
        						$page_html .= '<tr><td colspan="9"><font color="red">No Billing errors Found.</font></td></tr>';
        					}
      			   $page_html .='</tbody>
      			   			</table>
      					</div>';
      			   		if($page_vars['page_count'] > 1){	
						$page_html .= '<form name="frmPaginate" id="frmPaginate" action="reneal_errors.php" method="post">';
						$page_html .= '<p align="right">Showing '.$page_vars['page_current_range_start'].'-'.$page_vars['page_current_range_end'].' of '.$page_vars['active_paid_dids_count']."\n"; 
						$page_html .= '<select name="paginate" onchange="javascript:document.frmPaginate.submit();">'."\n";
						$i=0;
						$page_html .= '<option value="'.$i.'">Please select</option>'."\n";
						foreach($page_vars['page_deck'] as $page_range){
							$i++;
							$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
						}			 
						$page_html .= '</select>
					</p></form>';
					}//end of if page count > 1
      	$page_html .='</div>
      			</div>
      		</div>
      		';  
    	
    	return $page_html;
    }
	
	require("includes/DRY_authentication.inc.php");