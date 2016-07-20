<?php
function getPaymentMethodsPage(){
	
	global $this_page;
	
	$page_vars = $this_page->variable_stack;
        
        $get_CID    =   $_GET['id'];
        if(!empty($get_CID)){
            $get_cidURL =   "id=".$get_CID."&";
        }
        
        //Get the last successful credit card payment details
        $payments_received = new Payments_received();
        if(is_object($payments_received)){
        	$last_pay_details = $payments_received->getLastCCPaymentDetails($_SESSION['user_logins_id']);
        	if($last_pay_details!=0){
        		$last_pay = "Last successful payment on ".$last_pay_details["date_paid"]."<br/>
                             $".$last_pay_details["amount"]." ".$last_pay_details["card_logo"]." xxxx xxxx xxxx ".substr($last_pay_details["card_number"],-4);
        	}else{
        		$last_pay = "";
        	}
        }else{
        	die("Failed to create Payment_received object.");
        }
        
        $page_html = "";
        
        $page_html .= '
        	<script>
	
			$(document).ready(function(){
				
			});
			
			function delete_card(id){
				if(confirm("Are you sure you want to delete this card?")==true){
        			setTimeout(window.location.href="delete_card.php?id="+id,1000);	
        		}
			}
			
			function primary_backup_submit(id){
				var select_id = "#primary-backup-sel-"+id;
				if($(select_id).val()==""){
					alert("Please select a Save As value");
					return;
				}
				$("#primary_backup_cc_id").val(id);
				$("#primary_backup_value").val($(select_id).val());
				$("#frm-primary-backup").submit();
			}
			
        </script>';
        
        
        $page_html .= '
        
        <!-- Main Container -->
                                <div class="contentbox">
                                     <div class="contentbox-head">
                                          <div class="contentbox-head-title">Billing</div>
                                         
                                          <div class="row Mtop20">
                                              <div class="subsection">
                                                   
                                                    
                                                    <div class="row paymentmethod-box">
                                                         <div class="payment-methodbtn"><a href="add_new_card.php" class="greenmedbtn">Add Payment Method</a></div>
                                                         <div class="payment-methodinfo">'.$last_pay.'</div>
                                                    </div>';                                                   
									if(count($page_vars['my_ccs'])>0){
										foreach($page_vars['my_ccs'] as $one_cc){
											//Get the CC Image logo to be used
	                            			$cc_logo = "img/";
	                            			$cc_name = "";
	                            			switch($one_cc["card_type"]){
	                            				case "Visa":
	                            					$cc_logo .= "visa_curved.png";
	                            					$cc_name = "Visa";
	                            					break;
	                            				case "Mastercard":
	                            					$cc_logo .= "mastercard_curved.png";
	                            					$cc_name = "MasterCard";
	                            					break;
	                            				case "Amex":
	                            					$cc_logo .= "american_express_curved.png";
	                            					$cc_name .= "American Express";
	                            					break;
	                            				default:
	                            					$cc_logo .= "unknown.png";
	                            					$cc_name = "Unknown";
	                            					break;
	                            			}
	                            			if(intval(trim($one_cc["is_primary"]))==1){
	                            				$primary_backup_status = "(Primary)";
	                            				$primary_backup = "";
	                            			}elseif(intval(trim($one_cc["is_primary"]))==2){
	                            				$primary_backup_status = "(Back-up)";
	                            				$primary_backup = "
	                            				| <select class='selectbox-reg inputhalf' name='primary-backup-sel-".$one_cc["cc_details_id"]."' id='primary-backup-sel-".$one_cc["cc_details_id"]."' onchange='primary_backup_submit(".$one_cc["cc_details_id"].");'>
	                            					<option value=''>Save As</option>
	                            					<option value='1'>Primary Card</option>
	                            				</select>";
	                            			}else{
	                            				$primary_backup_status = "";
	                            				$primary_backup = "
	                            				| <select class='selectbox-reg inputhalf' name='primary-backup-sel-".$one_cc["cc_details_id"]."' id='primary-backup-sel-".$one_cc["cc_details_id"]."' onchange='primary_backup_submit(".$one_cc["cc_details_id"].");'>
	                            					<option value=''>Save As</option>
	                            					<option value='1'>Primary Card</option>
	                            					<option value='2'>Backup Card</option>
	                            					<option value='0'>Neither</option>
	                            				</select>";
	                            			}
											$page_html .= '<div class="row Mtop10">
															<table cellpadding="0" cellspacing="0" class="paymenttable3">
																<tr>
                                                                   <td width="10%" class="borderleft"><span><img src="'.$cc_logo.'"/></span></td>
                                                                   <td width="45%" class="stronger f14"> XXXX XXXX XXXX '.substr($one_cc["card_number"],-4).' '.$primary_backup_status.'</td>
                                                                   <td width="45%"><a href="update_card.php?id='.$one_cc["cc_details_id"].'">Edit</a> | 
                                                                   		<a id="del_cc_details'.$one_cc["cc_details_id"].'" onclick="delete_card('.$one_cc["cc_details_id"].');" href="#">Remove</a> '.$primary_backup.'</td>
                                                                </tr>
																<tr>
                                                                   <td width="10%" class="borderleft">&nbsp;</td>
                                                                   <td width="45%">
                                                                   Exp:'.$one_cc["expiry"].'
                                                                   </td>
                                                                   <td width="45%">&nbsp;</td>                                    
                                                                </tr>
                                                               </table>                                                    
                                                    		</div>
                                                    		<form name="frm-primary-backup" id="frm-primary-backup" method="post" action="payment_methods.php">
                                                    			<input type="hidden" name="primary_backup_cc_id" id="primary_backup_cc_id" value="">
                                                    			<input type="hidden" name="primary_backup_value" id="primary_backup_value" value="">
                                                    		</form>
                                                    		';
										}
									}else{
										$page_html .= '<caption>You have no Credit/Debit/Payment Cards entered in the system.</caption>';
									}              
                                                       
                                     $page_html .= '        
                                              </div>
                                          </div>                                          
                                           
                                 <!-- Personal Details closed -->         
                                          
                                          
                                     </div>
                                </div>
                                <!-- Main Container closed -->
        
        ';
	return $page_html;
	
}