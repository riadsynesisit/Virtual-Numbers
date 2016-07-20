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
		if(isset($_GET["did_order_type"])==false || trim($_GET["did_order_type"])==""){
			echo "Error: Required parameter 'did_order_type' not passed.";
			exit;
		}else{
			$did_order_type = trim($_GET["did_order_type"]);
		}
		
		$monthly_details = $payments_received->getMonthlyDetails($month,$year,$did_order_type);
		if(is_array($monthly_details)==false && substr($monthly_details,0,6)=="Error:"){
			echo $monthly_details;
			exit;
		}
		if(is_array($monthly_details)==false && intval($monthly_details)==0){
			echo "Error: No details records found.";
			exit;
		}
		//echo print_r($monthly_details);exit;
		$page_vars['monthly_details']   =   $monthly_details;
		$page_vars['menu_item'] =   'monthly_summary';
	    $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
		
		$this_page->content_area.=  getMonthlyDetailsPage($month,$year,$did_order_type);
	
	    common_header($this_page,"'Monthly Details'");
		
	}
	
	function getMonthlyDetailsPage($month,$year,$did_order_type){
		
		global $this_page;
		
		$page_vars = $this_page->variable_stack;
		
		if($did_order_type==1){
			$order_type="New DIDs";
		}else{
			$order_type="Renew DIDs";
		}
		
		$page_html = "";
        
        $page_html .= '
        	<script>
		    
        	</script>';
        $page_html .='
        	<div id="page-wrapper">
            	<div class="row">
                	<div class="col-lg-12">
                    	<h1 class="page-header">Monthly Details of '.$month.'/'.$year.' for '.$order_type.' orders. </h1>
                    	<div class="table-responsive">
							<table class="table table-hover">
								<thead class="blue-bg">
									<tr>
										<th style="text-align:left">Serial Number</th>
										<th style="text-align:left">Member ID</th>
										<th style="text-align:right">Amount</th>
										<th style="text-align:left">Date Paid</th>
										<th style="text-align:left">DID Number</th>
										<th style="text-align:left">Pymt Gateway</th>
										<th style="text-align:left">Trans ID</th>
									</tr>
								</thead>
								<tbody>';
        				$sl_num = 0;
        				foreach($page_vars['monthly_details'] as $details){
        					$sl_num = $sl_num + 1;
        					$page_html .= '
        								<tr>
        									<td>'.$sl_num.'</td>
        									<td align="left">'.$details['member_id'].'</td>
        									<td align="right">'.$details['currency'].$details['amount'].'</td>
        									<td align="left">'.$details['date_paid'].'</td>
        									<td align="left">'.$details['did_number'].'</td>
        									<td align="left">'.$details['pymt_gateway'].'</td>
        									<td align="left">'.$details['trans_id'].'</td>
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