<?php
	require "./includes/DRY_setup.inc.php";
	
	function get_page_content($this_page){

		global $action_message;
        
		if($_SESSION['admin_flag']!=1){
			echo "Error: You do not have sufficient privileges to access this page.";
			exit;
		}
		
		$payments_received = new Payments_received();
		if(is_object($payments_received)==false){
			echo "Error: Failed to create Payemnts_received object.";
			exit;
		}
		$monthly_summary = $payments_received->getMonthlySummary();
		if(is_array($monthly_summary)==false && substr($monthly_summary,0,6)=="Error:"){
			echo $monthly_summary;
			exit;
		}
		if(is_array($monthly_summary)==false && intval($monthly_summary)==0){
			echo "Error: No records found.";
			exit;
		}
		$page_vars['monthly_summary']   =   $monthly_summary;
		$page_vars['menu_item'] =   'monthly_summary';
	    $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
		
		$this_page->content_area.=  getMonthlySummaryPage();
	
	    common_header($this_page,"'Monthly Summary'");
		
	}
	
	function getMonthlySummaryPage(){
		
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
                    	<h1 class="page-header">Monthly Summary</h1>
                    	<div class="table-responsive">
							<table class="table table-hover">
								<thead class="blue-bg">
									<tr>
										<th>Month</th>
										<th style="text-align:right">New DID Orders</th>
										<th style="text-align:right">DID Renewals</th>
										<!--<th style="text-align:right">Deleted DIDs</th>-->
										<th style="text-align:right">Renewal Failures</th>
										<th style="text-align:right">GBP Amount</th>
										<th style="text-align:right">AUD Amount</th>
									</tr>
								</thead>
								<tbody>';
        				foreach($page_vars['monthly_summary'] as $summary){
        					$page_html .= '
        								<tr>
        									<td>'.$summary['month_name'].' '.$summary['year'].'</td>
        									<td align="right"><a href="monthly_details.php?month='.$summary['month'].'&year='.$summary['year'].'&did_order_type=1">'.$summary['new_did_orders'].'</a></td>
        									<td align="right"><a href="monthly_details.php?month='.$summary['month'].'&year='.$summary['year'].'&did_order_type=0">'.$summary['renew_did_orders'].'</a></td>
        									<!--<td align="right"><a href="monthly_deleted_dids.php?month='.$summary['month'].'&year='.$summary['year'].'">'.$summary['deleted_dids'].'</a></td>-->
        									<td align="right"><a href="monthly_renewal_failures.php?month='.$summary['month'].'&year='.$summary['year'].'">'.$summary['auto_renewal_failures'].'</a></td>
        									<td align="right">'.$summary['gbp_amount'].'</td>
        									<td  align="right">'.$summary['aud_amount'].'</td>
        								</tr>';
        				}//end of foreach
        				$page_html .='</tbody>
      			   			</table>
      					</div>';
        $page_html .='</div>
      			</div>
      		</div>
      		';  
    	
    	return $page_html;
		
	}
	
	require("includes/DRY_authentication.inc.php");