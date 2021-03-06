<?php
	require "./includes/DRY_setup.inc.php";
	
	function get_page_content($this_page){

		global $action_message;
        
		if($_SESSION['admin_flag']!=1){
			echo "Error: You do not have sufficient privileges to access this page.";
			exit;
		}
		
		$deleted_dids = new Deleted_DID();
		if(is_object($deleted_dids)==false){
			echo "Error: Failed to create Deleted_DID object.";
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
		
		$deleted_did_details = $deleted_dids->getMonthlyDeletedDIDs($month,$year);
		if(is_array($deleted_did_details)==false && substr($deleted_did_details,0,6)=="Error:"){
			echo $deleted_did_details;
			exit;
		}
		if(is_array($deleted_did_details)==false && intval($deleted_did_details)==0){
			echo "Error: No deleted DID details records found.";
			exit;
		}
		//echo print_r($monthly_details);exit;
		$page_vars['deleted_did_details']   =   $deleted_did_details;
		$page_vars['menu_item'] =   'monthly_summary';
	    $this_page->variable_stack = array_merge($this_page->variable_stack,$page_vars);
		
		$this_page->content_area.=  getMonthlyDeletedDIDsPage($month,$year);
	
	    common_header($this_page,"'Monthly Details'");
		
	}
	
	function getMonthlyDeletedDIDsPage($month,$year){
		
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
                    	<h1 class="page-header">Deleted DID Details of '.$month.'/'.$year.'</h1>
                    	<div class="table-responsive">
							<table class="table table-hover">
								<thead class="blue-bg">
									<tr>
										<th style="text-align:left">Serial Number</th>
										<th style="text-align:left">Member ID</th>
										<th style="text-align:left">DID Number</th>
										<th style="text-align:left">Deleted On</th>
										<th style="text-align:left">Reason</th>
									</tr>
								</thead>
								<tbody>';
        				$sl_num = 0;
        				foreach($page_vars['deleted_did_details'] as $details){
        					$sl_num = $sl_num + 1;
        					$page_html .= '
        								<tr>
        									<td>'.$sl_num.'</td>
        									<td align="left">'.$details['member_id'].'</td>
        									<td align="left">'.$details['did_number'].'</td>
        									<td align="left">'.$details['did_deleted_on'].'</td>
        									<td align="left">'.$details['delete_reason'].'</td>
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