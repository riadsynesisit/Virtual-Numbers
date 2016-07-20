<?php
	require "./includes/DRY_setup.inc.php";
	
	function get_page_content($this_page){

		global $action_message;
        
		if($_SESSION['admin_flag']!=1){
			echo "Error: You do not have sufficient privileges to access this page.";
			exit;
		}
		
		$dids_count = 0; //Initialize the number of DIDs to zero
		
		$assigned_dids = new Assigned_DID();
		if(is_object($assigned_dids)==false){
			echo "Error: Failed to created Assigned_DID object.";
		}
		$active_paid_dids_count = $assigned_dids->get_active_paid_dids_count();
		if(is_int($active_paid_dids_count)==false && substr($active_paid_dids_count,0,6)=="Error:"){
			echo $active_paid_dids_count;
			exit;
		}
		//echo $active_paid_dids_count;exit;
		if($active_paid_dids_count>0){
			// sort out pagination
	        // first, figure out the page selector data
	        $records_per_page   =   500;//RECORDS_PER_PAGE;
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
	        $active_paid_dids = $assigned_dids->get_active_paid_dids($page_current_range_start - 1, $records_per_page);
	        if(is_array($active_paid_dids)===false && substr($active_paid_dids,0,6)=="Error:"){
	        	echo $active_paid_dids;
	        	exit;
	        }
			if(is_array($active_paid_dids)===false && intval($active_paid_dids)==0){
	        	echo "Error: No active paid DIDs exists.";
	        	exit;
	        }
	        $page_vars['active_paid_dids']   =   $active_paid_dids;
	        $page_vars['active_paid_dids_count']   =   $active_paid_dids_count;
	        $page_vars['menu_item'] =   'active_paid_dids';
	        $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
	
	        $this_page->content_area.=  getActivePaidDIDsPage();
	
	        common_header($this_page,"'Active Paid DIDs'");
		}//End of if active paid did count is > 0
        
    }
	
    function getActivePaidDIDsPage(){
    	
    	global $this_page;
	
		$page_vars = $this_page->variable_stack;
		
		$page_html = "";
        
        $page_html .= '
        	<script>
		    
        	</script>';
        $page_html .='
        	<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">Active Paid DID Numbers</h1>
                    	<div class="table-responsive">
							<table class="table table-hover">
								<thead class="blue-bg">
									<tr>
										<th>DID Number</th>
										<th align="center">Member ID</th>
										<th>DID Expiry(GMT)</th>
										<th>Status</th>
										<th>Auto Billing</th>
										<th>Order Type</th>
										<th>Last Trans</th>
										<th>Last Revenue</th>
										<th>Last Bill Date</th>
									</tr>
								</thead>
								<tbody>';
        					if(intval($page_vars['active_paid_dids_count'])>0){
        						foreach($page_vars['active_paid_dids'] as $my_DID){
        							if(intval($my_DID["secs_to_expire"]) > 0){
        								$status = '<font color="green">Active</font>';
        							}else{
        								$status = '<font color="red">Expired</font>';
        							}
        							$page_html .= '
		        								<tr>
		        									<td>'.$my_DID['did_number'].'</td>
		        									<td>'.$my_DID['member_id'].'</td>
		        									<td>'.$my_DID['did_expires_on'].'</td>
		        									<td>'.$status.'</td>
		        									<td>'.$my_DID['pymt_gateway'].'</td>
		        									<td>'.$my_DID['pymt_type'].'</td>
		        									<td>'.$my_DID['transaction_id'].'</td>
		        									<td>'.$my_DID['currency_symbol'].$my_DID['amount'].'</td>
		        									<td>'.$my_DID['last_bill_date'].'</td>
		        								</tr>';
        						}//End of foreach loop
        					}else{
        						$page_html .= '<tr><td colspan="9"><font color="red">No paid DID numbers currently active in the system.</font></td></tr>';
        					}
      			   $page_html .='</tbody>
      			   			</table>
      					</div>';
      			   		if($page_vars['page_count'] > 1){	
						$page_html .= '<form name="frmPaginate" id="frmPaginate" action="active_paid_dids.php" method="post">';
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