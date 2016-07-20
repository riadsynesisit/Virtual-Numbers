<?php
	require "./includes/DRY_setup.inc.php";
	require("./classes/Auto_Renewal_Failures.class.php");
	
	function get_page_content($this_page){

		global $action_message;
        
		if($_SESSION['admin_flag']!=1){
			echo "Error: You do not have sufficient privileges to access this page.";
			exit;
		}
		
		$auto_renewal_failures = new Auto_Renewal_Failures();
		if(is_object($auto_renewal_failures)==false){
			echo "Error: Failed to create Auto_Renewal_Failures object.";
			exit;
		}
	
		if(isset($_GET["month"])==false || trim($_GET["month"])==""){
			echo "Error: Required parameter 'month' not passed.";
			exit;
		}else{
			$month = trim($_GET["month"]);
		}
		
		if(isset($_GET["year"])==false || trim($_GET["year"])==""){
			echo "Error: Required parameter 'year' not passed.";
			exit;
		}else{
			$year = trim($_GET["year"]);
		}
		
		$renewal_failures = $auto_renewal_failures->getMonthlyFailures($month,$year);
		if(is_array($renewal_failures)==false && substr($renewal_failures,0,6)=="Error:"){
			echo $renewal_failures;
			exit;
		}
		if(is_array($renewal_failures)==false && intval($renewal_failures)==0){
			echo "Error: No Auto Renewal Failure records found.";
			exit;
		}
		//echo print_r($monthly_details);exit;
		$page_vars['renewal_failures']   =   $renewal_failures;
		$page_vars['menu_item'] =   'monthly_summary';
	    $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
		
		$this_page->content_area.=  getMonthlyRenewalFailuresPage($month,$year);
	
	    common_header($this_page,"'Monthly Details'");
		
	}
	
	function getMonthlyRenewalFailuresPage($month,$year){
		
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
                    	<h1 class="page-header">Auto Renewal Failure Details of '.$month.'/'.$year.'</h1>
                    	<div class="table-responsive">
							<table class="table table-hover">
								<thead class="blue-bg">
									<tr>
										<th style="text-align:left">Serial Number</th>
										<th style="text-align:left">Member ID</th>
										<th style="text-align:left">DID Number</th>
										<th style="text-align:left">Failed On</th>
										<th style="text-align:left">Pymt Gateway</th>
										<th style="text-align:left">Reason</th>
									</tr>
								</thead>
								<tbody>';
        				$sl_num = 0;
        				foreach($page_vars['renewal_failures'] as $details){
        					$sl_num = $sl_num + 1;
        					$page_html .= '
        								<tr>
        									<td>'.$sl_num.'</td>
        									<td align="left">'.$details['member_id'].'</td>
        									<td align="left">'.$details['did_number'].'</td>
        									<td align="left">'.$details['date_failed'].'</td>
        									<td align="left">'.$details['pymt_gateway'].'</td>
        									<td align="left">'.$details['error_message'].'</td>
        								</tr>';
        				}//end of foreach
        				$page_html .='</tbody>
      			   			</table>
      					</div>';
         $page_html .='<a href="monthly_summary.php"><button align="center" name="btn_back" value="Back" alt="Back" title="back" class="btn btn-primary" id="btn_back">Back</button></a>';
        $page_html .='</div>
      			</div>
      		</div>
      		';  
    	
    	return $page_html;
		
	}
	
	require("includes/DRY_authentication.inc.php");