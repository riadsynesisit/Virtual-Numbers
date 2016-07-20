<!doctype html>
<?php
//echo '<pre>';print_r($_POST);echo '</pre>';
	require "inc_header_page.php";
	require "login_top_page.php";
	require "top_nav_page.php";
	
		
	require("./dashboard/classes/Did_Cities.class.php");
	require("./dashboard/classes/DCMDestinationCosts.class.php");
	require("./dashboard/classes/Unassigned_087DID.class.php");
	require("./dashboard/classes/SystemConfigOptions.class.php");
	
    $did_cities = new Did_Cities();
	if(is_object($did_cities)!=true){
		echo "Error: Failed to create did_cities object.";
		exit;
	}
	
	$dest_costs = new DCMDestinationCosts();
    if(is_object($dest_costs)==false){
    	echo "Error: Failed to create DCMDestinationCosts object.";
    	exit;
    }
    
    $unassigned_087did = new Unassigned_087DID();
	if(is_object($unassigned_087did)!=true){
		echo "Error: Failed to create Unassigned_087DID object.";
		exit;
	}
	
	$sco = new System_Config_Options();
	if(is_object($sco)!=true){
		echo "Error: Failed to create System_Config_Options object.";
		exit;
	}
	
	$gbp_to_aud_rate = $sco->getRateGBPToAUD();
	$usd_to_aud_rate = $sco->getRateUSDToAUD();
	if(SITE_CURRENCY=="AUD"){
		$exchange_rate = $gbp_to_aud_rate;
		$exchange_rate_tollfree = $usd_to_aud_rate;
	}else{
		$exchange_rate = 1;
		$exchange_rate_tollfree = 1;
	}
	$fwd_cntry = trim($_POST['fwd_cntry']);
	
	//$callCost = $unassigned_087did->callCost($_POST["forwarding_number"],"peak",true)*$exchange_rate;
	$callCost = $dest_costs->get_cheapest_call_rate($fwd_cntry,true,SITE_CURRENCY);
	
	//If the number selected is a Toll-free number need to factor in the incoming call cost
	$callCostTF = $unassigned_087did->callCostTollFree($_POST['did_city_code'],true)*$exchange_rate_tollfree;
	if(is_numeric($callCostTF)==false){
		echo "Error: Failed to get Toll free costs. ".$callCostTF;
		exit;
	}
	
	$sticky_did_monthly_setup = $did_cities->getStickyDIDMonthlyAndSetupFromCityId(trim($_POST['did_city_code']));
	if(is_array($sticky_did_monthly_setup)==false && substr($sticky_did_monthly_setup,0,6)=="Error:"){
		$err_msg = $sticky_did_monthly_setup;
		echo $err_msg;
		exit;
	}
	//Plan 0 details
	$plan0_monthly = number_format($sticky_did_monthly_setup["sticky_monthly"],2);
	$plan0_whole_dollars = substr($plan0_monthly,0,strlen($plan0_monthly)-3);
	$plan0_cents = substr($plan0_monthly,-2,2);
	//Plan 1 details
	$plan1_monthly = number_format($sticky_did_monthly_setup["sticky_monthly_plan1"],2);
	$plan1_whole_dollars = substr($plan1_monthly,0,strlen($plan1_monthly)-3);
	$plan1_cents = substr($plan1_monthly,-2,2);
	//Plan 2 details
	$plan2_monthly = number_format($sticky_did_monthly_setup["sticky_monthly_plan2"],2);
	$plan2_whole_dollars = substr($plan2_monthly,0,strlen($plan2_monthly)-3);
	$plan2_cents = substr($plan2_monthly,-2,2);
	//Plan 3 details
	$plan3_monthly = number_format($sticky_did_monthly_setup["sticky_monthly_plan3"],2);
	$plan3_whole_dollars = substr($plan3_monthly,0,strlen($plan3_monthly)-3);
	$plan3_cents = substr($plan3_monthly,-2,2);
	
	//Plan 0 yearly details
	$plan0_yearly = number_format($sticky_did_monthly_setup["sticky_yearly"],2);
	$plan0_yearly_whole_dollars = substr($plan0_yearly,0,strlen($plan0_yearly)-3);
	$plan0_yearly_cents = substr($plan0_yearly,-2,2);
	//Plan 1 yearly details
	$plan1_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan1"],2);
	$plan1_yearly_whole_dollars = substr($plan1_yearly,0,strlen($plan1_yearly)-3);
	$plan1_yearly_cents = substr($plan1_yearly,-2,2);
	//Plan 2 yearly details
	$plan2_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan2"],2);
	$plan2_yearly_whole_dollars = substr($plan2_yearly,0,strlen($plan2_yearly)-3);
	$plan2_yearly_cents = substr($plan2_yearly,-2,2);
	//Plan 3 yearly details
	$plan3_yearly = number_format($sticky_did_monthly_setup["sticky_yearly_plan3"],2);
	$plan3_yearly_whole_dollars = substr($plan3_yearly,0,strlen($plan3_yearly)-3);
	$plan3_yearly_cents = substr($plan3_yearly,-2,2);
	
	$sticky_did_monthly = $sticky_did_monthly_setup["sticky_monthly"];
	$plan1_amount = $sticky_did_monthly_setup["sticky_monthly_plan1"];
	$plan2_amount = $sticky_did_monthly_setup["sticky_monthly_plan2"];
	$plan3_amount = $sticky_did_monthly_setup["sticky_monthly_plan3"];
	$sticky_did_yearly = $sticky_did_monthly_setup["sticky_yearly"];
	$plan1_yearly_amount = $sticky_did_monthly_setup["sticky_yearly_plan1"];
	$plan2_yearly_amount = $sticky_did_monthly_setup["sticky_yearly_plan2"];
	$plan3_yearly_amount = $sticky_did_monthly_setup["sticky_yearly_plan3"];
	$sticky_setup = $sticky_did_monthly_setup["sticky_setup"];
	if(trim($sticky_did_monthly)==""){
		$err_msg = "Error: No sticky did_monthly found in did_cities for DID City ID: ".trim($_POST['did_city_code']);
		echo $err_msg;
		exit;
	}
	
	if(trim($_POST['did_city_code'])!="99999" && ($plan0_yearly==0.00 || $plan1_yearly==0.00 || $plan2_yearly==0.00 || $plan3_yearly==0.00)){
		$err_msg = "Error: No yearly plan prices found in did_cities for DID City ID: ".trim($_POST['did_city_code']);
		echo $err_msg;
		exit;
	}
	
	$num_of_months = 1;
	
	//Amount available for SIP Cost after paying for the DID Monthly fees and Set Up costs if any
	$effective_amount_plan1 = $plan1_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	$effective_amount_plan2 = $plan2_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	$effective_amount_plan3 = $plan3_amount - ($sticky_did_monthly*$num_of_months) - $sticky_setup;
	
	if($callCostTF!=0){
		$num_of_minutes_plan1 = floor($effective_amount_plan1/($callCost+$callCostTF));
		$num_of_minutes_plan2 = floor($effective_amount_plan2/($callCost+$callCostTF));
		$num_of_minutes_plan3 = floor($effective_amount_plan3/($callCost+$callCostTF));
	}else{
		$num_of_minutes_plan1 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan1);
		$num_of_minutes_plan2 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan2);
		$num_of_minutes_plan3 = $dest_costs->get_cheapest_call_minutes($fwd_cntry, $effective_amount_plan3);
	}
	
	$cost_info_plan1 = $dest_costs->get_mobile_other_call_rates($fwd_cntry, $effective_amount_plan1);
	$cost_info_plan2 = $dest_costs->get_mobile_other_call_rates($fwd_cntry, $effective_amount_plan2);
	$cost_info_plan3 = $dest_costs->get_mobile_other_call_rates($fwd_cntry, $effective_amount_plan3);

	if(isset($_POST["forwarding_type"])==false || trim($_POST["forwarding_type"])==""){
		echo "Error: Required parameter forwarding_type not passed.";
		exit;
	}
	
?>

	<script>
		  var country_iso = "";
		  var pay_amount = 0;
		  var purchase_type = 'new_did';
		  
            $(document).ready(function(){            	

            	$('#btn-choose-plan0').mousedown(function(event) {
                	$('#choosen_plan').val("0");
                	if($('#sel_plan0_period').val()=="M"){
                		$('#pay_amount').val('<?php echo $plan0_monthly;?>');
                		$('#plan_cost').val('<?php echo $plan0_monthly;?>');
                		$('#plan_period').val("M");
                	}else{
                		$('#pay_amount').val('<?php echo $plan0_yearly;?>');
                		$('#plan_cost').val('<?php echo $plan0_yearly;?>');
                		$('#plan_period').val("Y");
                	}
                	$('#plan_minutes').val("Unlimited");
                	$('#frm-step2').submit();
                });
                $('#btn-choose-plan1').mousedown(function(event) {
                	$('#choosen_plan').val("1");
                	if($('#sel_plan1_period').val()=="M"){
                		$('#pay_amount').val('<?php echo $plan1_monthly;?>');
                		$('#plan_cost').val('<?php echo $plan1_monthly;?>');
                		$('#plan_period').val("M");
                	}else{
                		$('#pay_amount').val('<?php echo $plan1_yearly;?>');
                		$('#plan_cost').val('<?php echo $plan1_yearly;?>');
                		$('#plan_period').val("Y");
                	}
                	$('#plan_minutes').val(<?php echo $num_of_minutes_plan1; ?>);
                	$('#frm-step2').submit();
                });
                $('#btn-choose-plan2').mousedown(function(event) {
                	$('#choosen_plan').val("2");
                	if($('#sel_plan2_period').val()=="M"){
                		$('#pay_amount').val('<?php echo $plan2_monthly;?>');
                		$('#plan_cost').val('<?php echo $plan2_monthly;?>');
                		$('#plan_period').val("M");
                	}else{
                		$('#pay_amount').val('<?php echo $plan2_yearly;?>');
                		$('#plan_cost').val('<?php echo $plan2_yearly;?>');
                		$('#plan_period').val("Y");
                	}
                	$('#plan_minutes').val(<?php echo $num_of_minutes_plan2; ?>);
                	$('#frm-step2').submit();
                });
                $('#btn-choose-plan3').mousedown(function(event) {
                	$('#choosen_plan').val("3");
                	if($('#sel_plan3_period').val()=="M"){
                		$('#pay_amount').val('<?php echo $plan3_monthly;?>');
                		$('#plan_cost').val('<?php echo $plan3_monthly;?>');
                		$('#plan_period').val("M");
                	}else{
                		$('#pay_amount').val('<?php echo $plan3_yearly;?>');
                		$('#plan_cost').val('<?php echo $plan3_yearly;?>');
                		$('#plan_period').val("Y");
                	}
                	$('#plan_minutes').val(<?php echo $num_of_minutes_plan3; ?>);
                	$('#frm-step2').submit();
                });
            });

    </script>
	<!--link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/core.css"/-->
	<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/misc.css"/>
	<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/override_bootstrap.css"/>
</head>
<body>
	<!-- Banner -->
    <!--div class="wrapper">	         
    	<div class="container"><img src="images/SF-pricing.jpg" width="1024" height="201" alt="Account Details"></div>
    </div-->
    <!-- Banner Closed -->
    <div class="container">
    <img class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL;?>images/SF-pricing.jpg">
    </div><!-- /.container -->
    
    <!-- Main wrapper starts here -->
    <div class="wrapper">
         <div class="container"> 
           		<div class="stepNav-wrap">
                    <ul id="stepNav" class="fourStep">
                        <li class="current"><a title=""><em>Step 1: Select Plan</em></a></li>
                        <li><a title=""><em>Step 2: Create Account</em></a></li>
                        <li><a title=""><em>Step 3: Make Payment</em></a></li>
                        <li class="stepNavNoBg"><a title=""><em>Step 4: Confirm/Finish</em></a></li>
                    </ul>
                    <div class="clearfloat">&nbsp;</div>
    			</div>
    		<div class="plan-box">
    			<div class="plan-box-2">
    				<div class="pricing-main Mtop20">
    				<?php if($_POST["forwarding_type"]==1){?>
              			<div class="planbox2">
	                    	<div class="planbox2-top">
	                        	<div class="planbox2-head">
	                            	<div class="planbox2-mainhead">Starter Plan</div>
	                                <div class="row Mtop10">
	                                	<div class="plan-price-dollar"><strong><?php echo CURRENCY_SYMBOL." ".$plan1_whole_dollars; ?></strong></div>
	                                	<div class="plan-price-cents">.<?php echo $plan1_cents; ?>/monthly</div>
	                                </div> 
	                            </div>
	                            <div class="clearfloat"></div>
	                            <div class="plan-drp">
	                            	<select class="selectP5" id="sel_plan1_period">
	                            		<option value="M">1 Month <?php echo CURRENCY_SYMBOL.$plan1_monthly;?></option>
	                            		<option value="Y">12 months  <?php echo CURRENCY_SYMBOL.number_format($plan1_yearly,2); ?>/Yearly</option>
	                            	</select>
	                            </div>
	                            <div class="num-minutes" id="plan1_effective_minutes"><?php echo $num_of_minutes_plan1; ?> Minutes *</div>
	                        </div>
	                        <div class="planbox2-body">
	                        	<div class="plan-list">
	                            	<ul>
	                                    <li>Call Diversion </li>
	                                    <li>Online Call Records </li>
	                                    <li>Voice Mail</li> 
	                                    <li>Unlimited Diversion Updates</li>
	                                    <li>Instant Diversion Updates</li>
	                                    <li>Forwarding Number Hidden</li>
	                                </ul>
	                                
	                            </div>
	                            <div class="clearfloat"></div>
                                	<div class="rates-block">
                                    	<p class="f14 txt-c"><a href="javascript:void(0);">View Mobile <br>and <br>Other Call Rates</a></p>
                                    	<div class="rates-pop">
											<h3>Starter Plan</h3>
											<div style="margin:0px 5px 5px 5px;">
												<table width="100%" border="0" cellspacing="0" cellpadding="0" class="view_table_n">
													<tbody>
													<tr>
														<th width="200" align="left" class="starter_blue">Destination</th>
														<th width="80" class="starter_blue" align="center">Minutes Included</th>
														<th width="80" class="starter_blue" align="center">Additional Minutes</th>
													</tr>
													<?php 
														foreach($cost_info_plan1 as $rates){
													?>	
															<tr>
																<td><?php echo $rates["destination"]; ?></td>
																<td align="center" class="view_value_n"><?php echo $rates["minutes"]; ?></td>
																<td align="center" class="view_value_n"><?php echo $rates["rate"]; ?>&cent;</td>
															</tr>
													<?php	
														}
													?>
													</tbody>
												</table>
											</div>
										</div>
	                            		<div class="clearfloat"></div>
	                				</div>
	                            	<p class="Mtop20">
	                                	<a class="blue-btn q-button" id="btn-choose-plan1">Choose Plan</a>
	                                </p>
	                        </div>
	                    </div>
	                    <div class="planbox1">
	                    	<div class="planbox1-top">
	                        	<div class="planbox2-head">
	                            	<div class="planbox2-mainhead">Business Plan</div>
	                                <div class="row Mtop10">
	                                	<div class="plan-price-dollar"><strong><?php echo CURRENCY_SYMBOL." ".$plan2_whole_dollars; ?></strong></div>
	                                	<div class="plan-price-cents">.<?php echo $plan2_cents; ?>/monthly</div>
	                                </div> 
	                            </div>
	                            <div class="clearfloat"></div>
	                            <div class="plan-drp">
	                            	<select class="selectP5" id="sel_plan2_period">
	                            		<option value="M">1 Month <?php echo CURRENCY_SYMBOL.$plan2_monthly;?></option>
	                            		<option value="Y">12 months  <?php echo CURRENCY_SYMBOL.number_format($plan2_yearly,2); ?>/Yearly</option>
	                            	</select>
	                            </div>
	                            <div class="num-minutes" id="plan2_effective_minutes"><?php echo $num_of_minutes_plan2; ?> Minutes *</div>
	                        </div>
	                        <div class="planbox1-body">
	                        	<div class="plan-list">
	                            	<ul>
	                                    <li>Call Diversion </li>
	                                    <li>Online Call Records </li>
	                                    <li>Voice Mail</li>
	                                    <li>Unlimited Diversion Updates</li>
	                                    <li>Instant Diversion Updates</li>
	                                    <li>Forwarding Number Hidden</li>                                             
	                                </ul>
	                                
	                            </div>
	                            <div class="clearfloat"></div>
                                	<div class="rates-block">
                                    	<p class="f14 txt-c"><a href="javascript:void(0);">View Mobile <br>and <br>Other Call Rates</a></p>
                                    	<div class="rates-pop">
											<h3>Business Plan</h3>
											<div style="margin:0px 5px 5px 5px;">
												<table width="100%" border="0" cellspacing="0" cellpadding="0" class="view_table_n">
													<tbody>
													<tr>
														<th width="200" align="left" class="starter_blue">Destination</th>
														<th width="80" class="starter_blue" align="center">Minutes Included</th>
														<th width="80" class="starter_blue" align="center">Additional Minutes</th>
													</tr>
													<?php 
														foreach($cost_info_plan2 as $rates){
													?>	
															<tr>
																<td><?php echo $rates["destination"]; ?></td>
																<td align="center" class="view_value_n"><?php echo $rates["minutes"]; ?></td>
																<td align="center" class="view_value_n"><?php echo $rates["rate"]; ?>&cent;</td>
															</tr>
													<?php	
														}
													?>
													
													</tbody>
												</table>
											</div>
										</div>
	                            		<div class="clearfloat"></div>
	                            	</div>
	                            	<p class="Mtop20">
	                                	<a class="blue-btn q-button" id="btn-choose-plan2">Choose Plan</a>
	                                </p>
	                        </div>
	                    </div>
	                    <div class="planbox2">
	                    	<div class="planbox2-top">
	                        	<div class="planbox2-head">
	                            	<div class="planbox2-mainhead">Enterprise Plan</div>
	                                <div class="row Mtop10">
	                                	<div class="plan-price-dollar"><strong><?php echo CURRENCY_SYMBOL." ".$plan3_whole_dollars; ?></strong></div>
	                                	<div class="plan-price-cents">.<?php echo $plan3_cents; ?>/monthly</div>
	                                </div> 
	                                </div>
	                                <div class="clearfloat"></div>
		                            <div class="plan-drp">
		                            	<select class="selectP5" id="sel_plan3_period">
		                            		<option value="M">1 Month <?php echo CURRENCY_SYMBOL.$plan3_monthly;?></option>
		                            		<option value="Y">12 months  <?php echo CURRENCY_SYMBOL.number_format($plan3_yearly,2); ?>/Yearly</option>
		                            	</select>
		                            </div>
		                            <div class="num-minutes" id="plan3_effective_minutes"><?php echo $num_of_minutes_plan3; ?> Minutes *</div>
	                        </div>
	                        <div class="planbox2-body">
	                        	<div class="plan-list">
	                            	<ul>
	                                    <li>Call Diversion </li>
	                                    <li>Online Call Records </li>
	                                    <li>Voice Mail</li>
	                                    <li>Unlimited Diversion Updates</li>
	                                    <li>Instant Diversion Updates</li>
	                                    <li>Forwarding Number Hidden</li>                                        
	                                </ul>
	                                
	                            </div>
	                            <div class="clearfloat"></div>
                                	<div class="rates-block">
                                    	<p class="f14 txt-c"><a href="javascript:void(0);">View Mobile <br>and <br>Other Call Rates</a></p>
                                    	<div class="rates-pop">
											<h3>Enterprise Plan</h3>
											<div style="margin:0px 5px 5px 5px;">
												<table width="100%" border="0" cellspacing="0" cellpadding="0" class="view_table_n">
													<tbody>
													<tr>
														<th width="200" align="left" class="starter_blue">Destination</th>
														<th width="80" class="starter_blue" align="center">Minutes Included</th>
														<th width="80" class="starter_blue" align="center">Additional Minutes</th>
													</tr>
													<?php 
														foreach($cost_info_plan3 as $rates){
													?>	
															<tr>
																<td><?php echo $rates["destination"]; ?></td>
																<td align="center" class="view_value_n"><?php echo $rates["minutes"]; ?></td>
																<td align="center" class="view_value_n"><?php echo $rates["rate"]; ?>&cent;</td>
															</tr>
													<?php	
														}
													?>
													
													</tbody>
												</table>
											</div>
										</div>
	                            		<div class="clearfloat"></div>
	                            	</div>
	                            	<p class="Mtop20">
	                                	<a class="blue-btn q-button" id="btn-choose-plan3">Choose Plan</a>
	                                </p>
	                        </div>
	                    </div>
	                    <?php }elseif($_POST["forwarding_type"]==2){//SIP Forwarding?>
	                    <div class="planbox1 margin-c">
	                    	<div class="planbox1-top">
	                        	<div class="planbox2-head">
	                            	<div class="planbox2-mainhead">Tech Plan</div>
	                                <div class="row Mtop10">
	                                	<div class="plan-price-dollar"><strong><?php echo CURRENCY_SYMBOL." ".$plan0_whole_dollars; ?></strong></div>
	                                	<div class="plan-price-cents">.<?php echo $plan0_cents; ?>/monthly</div>
	                                </div> 
	                            </div>
	                            <div class="clearfloat"></div>
	                            <div class="plan-drp">
	                            	<select class="selectP5" id="sel_plan0_period">
	                            		<option value="M">1 Month <?php echo CURRENCY_SYMBOL.$plan0_monthly;?></option>
	                            		<option value="Y">12 months  <?php echo CURRENCY_SYMBOL.number_format($plan0_yearly/12,2); ?>/Monthly</option>
	                            	</select>
	                            </div>
	                            <div class="num-minutes" id="plan0_effective_minutes">Unlimited Minutes *</div>
	                        </div>
	                        <div class="planbox1-body">
	                        	<div class="plan-list">
	                            	<ul>
	                                    <li>Call Diversion </li>
	                                    <li>Online Call Records </li>
	                                    <li>Voice Mail</li>
	                                    <li>Unlimited Diversion Updates</li>
	                                    <li>Instant Diversion Updates</li>
	                                    <li>Forwarding Number Hidden</li>                                             
	                                </ul>
	                                
	                            </div>
	                            <div class="clearfloat"></div>
                                	
	                            	<p class="Mtop20">
	                                	<a class="blue-btn q-button" id="btn-choose-plan0">Choose Plan</a>
	                                </p>
	                        </div>
	                    </div>
	                    <?php }else{//Unknown forwarding_type
	                    	echo "Error: Unknown Forwarding Type :".$_POST["forwarding_type"];
	                    	}?>
	                </div><!-- end of pricing-main-home div tag -->
	            </div>
    		</div>
    	</div>
    </div>
	
	<?php include 'inc_free_uk_page.php';?>
	<?php include 'inc_footer_page.php';?>
    	<?php 
    		if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id']) != ""){
    			$next_page_id = "make_payment";
       			$next_page = "make_payment.php";
    		}else{
    			$next_page_id = "create_account";
       			$next_page = "create_account.php";
    		}
    	?>
    	
    	<form method="post" action="<?php echo SITE_URL?>index.php?<?php echo $next_page_id; ?>" name="frm-step2" id="frm-step2">
            <input type="hidden" value="<?php echo trim($_POST['did_country_code']); ?>" name="did_country_code" id="did_country_code">
            <input type="hidden" value="<?php echo trim($_POST['did_city_code']); ?>" name="did_city_code" id="did_city_code">
            <input type="hidden" value="<?php echo trim($_POST['fwd_cntry']); ?>" name="fwd_cntry" id="fwd_cntry">
            <input type="hidden" value="<?php echo trim($_POST['forwarding_type']); ?>" name="forwarding_type" id="forwarding_type">
            <input type="hidden" value="<?php echo trim($_POST['forwarding_number']); ?>" name="forwarding_number" id="forwarding_number">
            <input type="hidden" value="<?php echo trim($_POST['vnum_country']); ?>" name="vnum_country" id="vnum_country">
            <input type="hidden" value="<?php echo trim($_POST['vnum_city']); ?>" name="vnum_city" id="vnum_city">
            <input type="hidden" value="" name="choosen_plan" id="choosen_plan">
            <input type="hidden" value="" name="plan_period" id="plan_period">
            <input type="hidden" value="" name="plan_minutes" id="plan_minutes">
            <input type="hidden" value="" name="plan_cost" id="plan_cost">
            <input type="hidden" value="<?php echo $next_page; ?>" name="next_page" id="next_page">
       </form>
