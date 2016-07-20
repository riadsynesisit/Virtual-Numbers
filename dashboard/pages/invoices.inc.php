<?php
	function getInvoicesPage(){
		
		global $this_page;
	
		$page_vars = $this_page->variable_stack;
        
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "id=".$get_CID."&";
        }
        
        $page_html = "";
        
        $page_html .= '
        	<script>
		    $(document).ready(function() 
					{ 
						$("#myTable").tablesorter(); 
					} 
				);
        	</script>';
        
        $page_html .= '
        	<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Invoices</h1><p>View payments receipts and auto billing</p>
                    
                    					<div class="row Mtop10 Mbot0">
                                              <div class="subsection">
                                                   <div class="pagenobox">Page: <span class="fontcolor-blue">'.$page_vars['page_current'].'</span></div>
                                                   <div class="pagepagination">
                                                        <span class="pagepagination-result">Showing '.$page_vars['page_current_range_start'].' - '.$page_vars['page_current_range_end'].' of '.$page_vars['payments_count'].' Records</span>
                                                        <span class="pagepagination-resultno">
												        <select class="selectbox-small" name="paginate" onchange="window.location = \'invoices.php?'.$get_cidURL.'page=\'+this.value">'."\n";
														$page_html .= '<option value="">Please select</option>'; 
        												$page_deck = $page_vars['page_deck'];
														$i=0;
														foreach($page_deck as $page_range){
															$i++;
															$page_html .= '<option value="'.$i.'">'.$page_range.'</option>'."\n";
														}
                                                            $page_html .= '</select>
                                                        </span>
                                                   </div>
                                              </div>
                                          </div>
                    
<div class="table-responsive Mtop20">
<table class="table">
<thead class="blue-bg">
<tr>
<th>Date</th>
<th>Invoice Number</th>
<th>Information</th>
<th>Payment Gateway (Trans ID)</th>
<th>Amount</th>
<th>Action</th>
</tr>
</thead>
<tbody>';
$i=0;
$my_payments = $page_vars['my_payments'];
foreach($my_payments as $payment){
	
	$plan_info = "";
    switch(intval($payment["chosen_plan"])){
    	case 0:
        	$plan_info = "Account Credit";
            break;
        case 1:
            $plan_info = "Plan 1 - 250 Minutes Monthly";
            break;
        case 2:
            $plan_info = "Plan 2 - 500 Minutes Monthly";
            break;
        case 3:
            $plan_info = "Plan 3 - 1000 Minutes Monthly";
            break;
        default:
            $plan_info = "Unknown Plan";
    }
    $transid="";
    switch(trim($payment["pymt_gateway"])){
    	case "PayPal":
        	$transid = trim($payment["pp_receiver_trans_id"]);
            break;
        case "WorldPay":
		case "Credit_Card":
            $transid = trim($payment["transId"]);
            break;
        default:
            $transid = "N.A.";
            break;
    }
	
	$page_html.='<tr>
		<td>'.$payment["date_paid"].'</td>
		<td>'.$payment["id"].'</td>
		<td>'.$plan_info.'</td>
		<td>'.$payment["pymt_gateway"].'('.$transid.')</td>
		<td>'.CURRENCY_SYMBOL.$payment["amount"].'</td>
		<td><span><img src="img/print.png"/></span> <a target="_blank" href="print_receipt.php?id='.$payment["id"].'">Print Receipt</a></td>
		</tr>';
}//end of foreach
$page_html.='</tbody>
</table>
</div>                        
                </div>              
            </div>

                    <!-- /.row -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
        ';
        
        return $page_html;
		
	}