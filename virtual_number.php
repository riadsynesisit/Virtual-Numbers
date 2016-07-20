<?php 
if($this_page!="sign-up-now"){
	if(isset($_SERVER["HTTP_REFERER"]) && trim($_SERVER["HTTP_REFERER"])!="" && strpos($_SERVER["HTTP_REFERER"],"google.com.au")!==false && strpos($_SERVER["HTTP_REFERER"],"rd=no")===false){
		if(strpos($_SERVER['QUERY_STRING'],"rd=no")===false){
			header("Location: https://www.stickynumber.com.au/sign-up-now/");
			exit;
		}
	}
	if(isset($_SERVER["HTTP_REFERER"]) && substr($_SERVER["HTTP_HOST"],-15)!="stickynumber.uk" && trim($_SERVER["HTTP_REFERER"])!="" && (strpos($_SERVER["HTTP_REFERER"],"google.co.uk")!==false || strpos($_SERVER["HTTP_REFERER"],"google.uk")!==false) && strpos($_SERVER["HTTP_REFERER"],"rd=no")===false){
		if(strpos($_SERVER['QUERY_STRING'],"rd=no")===false){
			header("Location: https://www.stickynumber.uk/sign-up-now/");
			exit;
		}
	}
	
	if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
		if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=virtual_number" || $_SERVER["REQUEST_URI"]=="/?page=virtual_number" || $_SERVER["REQUEST_URI"]=="/pricing")){
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: pricing/");
			exit;
		}
	}
}//end of this page not equal to sign up now
?>
<?php 
require("./dashboard/classes/Did_Countries.class.php");
require("./dashboard/classes/Country.class.php");
require("./dashboard/classes/Unassigned_DID.class.php");

$this_page="virtual_number";

$unassigned_dids = new Unassigned_DID();
if(is_object($unassigned_dids)==false){
	echo "Failed to create Unassinged_DID object";
	exit;
}

$did_countries_obj = new Did_Countries();
if(is_object($did_countries_obj)==false){
	echo "Failed to create Did_Countries object";
	exit;
}
$country = new Country();
if(is_object($country)==false){
	echo "Failed to create Country object";
	exit;
}

$did_countries = $did_countries_obj->getAvailableCountries();
$countries = $country->getAllCountry();

if(SITE_COUNTRY=="COM"){
	$default_did_country = "GB";
}else{
	$default_did_country = SITE_COUNTRY;
}

if(SITE_COUNTRY=="COM" || SITE_COUNTRY=="UK"){
	$sel_fwd_cntry = "44";
}elseif(SITE_COUNTRY=="AU"){
	$sel_fwd_cntry = "61";
}else{
	$sel_fwd_cntry = "";
}

$free_numbers = $unassigned_dids->retUnassigned_DIDs();

?>
<!doctype html>
<html lang="en">
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="rating" content="general" />
        <!-- meta name="Language" content="en" /-->
        <meta name="robots" content="all" />
        <meta name="robots" content="index,follow" />
        <!-- meta name="re-visit" content="7 days" /-->
        <!-- meta name="distribution" content="global" /-->    
        <?php if(SITE_COUNTRY == "AU"){?>    
        <title>Virtual Phone Number Australia, Virtual Phone Numbers</title>
        <meta name="description" content="Sticky Number provides Australian virtual phone numbers. International virtual phone number forwarding to landline and mobile telephones. Over 1200 cities."/>
		<?php }elseif(SITE_COUNTRY == "UK"){ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title>
		<meta name="description" content="Sticky Number is a leading company provides United Kingdom numbers, UK Virtual numbers with guaranteed performance and reliability. For more:visit us online!"/>
        <?php }else{ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title>
        <meta name="description" content="Sticky Number providing virtual numbers from countries around the world. Our UK number allows you access to our members area and its virtual number features."/>
		<?php } ?>
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->		
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->
        
        <!-- CSS STYLING -->        
        <link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/core.css"/>
        <link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/misc.css"/>
        <link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" type="image/x-icon" />
        <link type="text/css" rel="stylesheet" href="<?php echo SITE_URL; ?>skins/minimal/minimal.css" />
		
        <!-- JS -->
        <script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery-1.9.1.min.js"></script>
        <script src="<?php echo SITE_URL; ?>js/jquery.slides.min.js"></script>
        <script src="<?php echo SITE_URL; ?>js/scrolltopcontrol.js"></script>
        <script src="<?php echo SITE_URL; ?>js/jquery-ui-1.10.3.custom.min.js"></script>
        <script src="<?php echo SITE_URL; ?>js/jquery.idTabs.min.js"></script>
        
        <script type="text/javascript">

        var city_selected = "";
        var plan1_minutes = 0;
        var plan2_minutes = 0;
        var plan3_minutes = 0;
        var plan0_cost_monthly = 0;
        var plan0_cost_annual = 0;
        var plan1_cost_monthly = 0;
        var plan1_cost_annual = 0;
        var plan2_cost_monthly = 0;
        var plan2_cost_annual = 0;
        var plan3_cost_monthly = 0;
        var plan3_cost_annual = 0;

		<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id']) != ""){ ?>
			var user_logged_in = true;
		<?php }else{ ?>
			var user_logged_in = false;
		<?php } ?>
		var site_country = "<?php echo SITE_COUNTRY; ?>";
        
        $(document).ready(function(){
        	
        	//By default on initial load show free plan
        	if(site_country=="UK" || site_country=="COM"){
        		$('#div_sel_free_number').show();
        		$('#div_free_plan').show();
        		$('#div_paid_plan').hide();
            	$('#div_tech_plan').hide();
        	}else{
        		$('#div_sel_free_number').hide();
        		$('#div_free_plan').hide();
        		$('#div_paid_plan').show();
            	$('#div_tech_plan').hide();
        	}

        	$('#did_country').change(function() {
                country_iso = $(this).val();
                
        		$("#radNumberForward").attr('checked', 'checked');
        		$('#country').val("");
        		$('#number').val("+");
        		$('#sipAddress').val("");
                
        		populate_did_cities(country_iso);

                    if($('#did_country').val()==""){
						return false;//Ignore since nothing is selected
                    }
                    
                	if($("#city").val()!=99999 && $("#city").val()!="99999"){
                		$('#div_sel_free_number').hide();
                		$('#div_free_plan').hide();
                	}
                    
	                if($("#city").val()==99999 || $("#city").val()=="99999"){
	                	$('#div_sel_free_number').show();
	                	$('#div_free_plan').show();
	        			$('#div_paid_plan').hide();
	        			$('#div_tech_plan').hide();
	                }else if($("#city").val()==""){
	                	disable_step2_inputs();
	                	$('#div_sel_free_number').hide();
	                	$('#div_free_plan').hide();
	        			$('#div_paid_plan').hide();
	        			$('#div_tech_plan').hide();
	                }else if($('#radSipForward').is(':checked')==true){
	                	$('#div_sel_free_number').hide();
	                	$('#div_free_plan').hide();
	        			$('#div_paid_plan').hide();
	        			$('#div_tech_plan').show();
	                }else if($('#radNumberForward').is(':checked')==true){
	                	if($('#country').val()==""){
	                		var selected_did_country = $("#did_country option:selected").text();
	                        var did_country_prefix = selected_did_country.substring(selected_did_country.lastIndexOf(" ")+1);
	                        $('#country').val(did_country_prefix);
						}
						if($('#country').val()!=""){
							$('#div_sel_free_number').hide();
							$('#div_free_plan').hide();
							if($('#city').val()!=""){
								$('#city').change();
		        				$('#div_paid_plan').show();
							}else{
								$('#div_paid_plan').hide();
							}
		        			$('#div_tech_plan').hide();
						}else{
							$('#div_sel_free_number').hide();
							$('#div_free_plan').hide();
		        			$('#div_paid_plan').hide();
		        			$('#div_tech_plan').hide();
						}
	                }
                  
            }); //on did_country change

            $('#city').change(function() {
                if($("#city").val()==""){
					return false;//Ignore - nothing has changed
                }
            	if($("#city").val()==99999 || $("#city").val()=="99999"){
            		$('#div_sel_free_number').show();
            		$('#div_free_plan').show();
            		$('#div_paid_plan').hide();
            		$('#div_tech_plan').hide();
            	}else{
            		disable_step3_inputs();
            		did_city_code = $("#city").val();
            		$('#div_sel_free_number').hide();
            		$('#div_free_plan').hide();
            		if($('#radSipForward').is(':checked')==true){
                		//Get the tech plan pricing from ajax call
                		var url = '<?php echo SITE_URL; ?>ajax/ajax_get_tech_plan_info.php';
                		$.post(url, {"did_city_code":did_city_code},function(response){
                    		//Populate the plan info
                    		if(response.substring(0,6)=="Error:"){
                    			alert(response);
                    			return false;
                    		}else{
	                    		var arr_info = response.split(",");
	                    		plan0_cost_monthly = arr_info[1];
	                    		plan0_cost_annual = arr_info[2];
	                    		$('#div_plan0_minutes').text(arr_info[0]);
	                    		$('#div_plan0_monthly').html('<?php echo CURRENCY_SYMBOL; ?>'+plan0_cost_monthly);
	                    		$('#div_plan0_annual').html('<?php echo CURRENCY_SYMBOL; ?>'+plan0_cost_annual);
								//Display the plans
			               		$('#div_free_plan').hide();
			            		$('#div_paid_plan').hide();
			            		$('#div_tech_plan').show();
                    		}
		               	});
            			$('#div_tech_plan').show();
            			$('#div_paid_plan').hide();
            		}else{
            			$('#div_tech_plan').hide();//This is not SIP forwarding
            			if($('#country').val()!=""){
            				fwd_cntry = $('#country').val();
            				populate_paid_plans(fwd_cntry, did_city_code);
            				$('#div_paid_plan').show();
            			}else{
            				$('#div_paid_plan').hide();
            			}
            		}
            	}
            });

            if(site_country=="AU"){
                populate_paid_plans("61",get_cheapest_city_id("AU"));
            	populate_did_cities("AU");
            }else{
            	populate_did_cities("GB");
            }
            
        	//$('#radNumberForward').on('ifChecked', function(event){
        	$('#radNumberForward').change(function(event){
        		if($("#city").val()==""){
        			disable_step2_inputs();
               		alert("Please select a city for your virtual number.");
    				$('#city').focus();
    				event.preventDefault();
    				return false;
                }
        		if($("#city").val()==99999 || $("#city").val()=="99999"){
        			if($("#sel_free_number").val()==""){
        				disable_step2_inputs();
                   		alert("Please select a number.");
        				$('#sel_free_number').focus();
        				event.preventDefault();
        				return false;
        			}
        		}
				$('#country').removeAttr("disabled");
				$('#number').removeAttr("disabled");
				$('#sipAddress').val("");
				$('#sipAddress').attr("disabled", true);
				$('#city').change();
				$('#div_forwarding_country').css("visibility", "visible");
				$('#div_forwarding_number').css("visibility", "visible");
				$('#div_forwarding_sip').css("visibility", "hidden");
			});

			//$('#radSipForward').on('ifChecked', function(event){
			$('#radSipForward').change(function(event){
				if($("#city").val()==""){
					disable_step2_inputs();
               		alert("Please select a city for your virtual number.");
    				$('#city').focus();
    				event.preventDefault();
    				return false;
                }
				if($("#city").val()==99999 || $("#city").val()=="99999"){
        			if($("#sel_free_number").val()==""){
        				disable_step2_inputs();
                   		alert("Please select a number.");
        				$('#sel_free_number').focus();
        				event.preventDefault();
        				return false;
        			}
        		}
				$('#country').val("");
				$('#number').val("+");
				$('#country').attr("disabled", true);
				$('#number').attr("disabled", true);
				$('#sipAddress').removeAttr("disabled");
				$('#city').change();
				$('#div_forwarding_country').css("visibility", "hidden");
				$('#div_forwarding_number').css("visibility", "hidden");
				$('#div_forwarding_sip').css("visibility", "visible");
				
			});

			 $('#country').change(function() {
				 if($("#country").val()==""){
					return false;//Ignore - nothing selected
				 }
                 //Validation if City is selected
                 if($("#city").val()==""){
                	 disable_step2_inputs();
                	 $('#number').val("+");
                	alert("Please select a city for your virtual number.");
     				$('#city').focus();
     				event.preventDefault();
     				return false;
                 }
                 if($("#city").val()==99999 || $("#city").val()=="99999"){
         			if($("#sel_free_number").val()==""){
         				disable_step2_inputs();
                    	alert("Please select a number.");
         				$('#sel_free_number').focus();
         				event.preventDefault();
         				return false;
         			}
         		}
               	//Populate the plan pricing information
               	if($("#city").val()!=99999 && $("#city").val()!="99999"){
               		fwd_cntry = $('#country').val();
               		did_city_code = $("#city").val();
                   	if($('#radNumberForward').is(':checked')==true){
                   		populate_paid_plans(fwd_cntry, did_city_code);
                   	}
               	}//End of if not a free number
               	
                 $('#number').focus();
                 $('#number').val( '+' + $(this).val());
                 var selected_country = $("#country option:selected").text(); 
                 selected_country = selected_country.substring(0,selected_country.indexOf("+"));
                 selected_country = selected_country.substring(0,30);//Maximum 30 characters.
                 if($("#city").val()==99999 || $("#city").val()=="99999"){
					//Free Plan
 					$('#div_free_country_name1').text(selected_country);
 					$('#div_free_country_name2').text(selected_country);
                 }else{
					//Paid Plan
 					
                 }
             }); //on country change

             $('#number').focusin(function(event) {
            	 if($("#city").val()==""){
 					disable_step2_inputs();
 					alert("Please select a city for your virtual number.");
 	     			$('#city').focus();
 	     			event.preventDefault();
 	     			return false;
 				 }
            	 if($("#city").val()==99999 || $("#city").val()=="99999"){
         			if($("#sel_free_number").val()==""){
         				disable_step2_inputs();
                    	alert("Please select a number.");
         				$('#sel_free_number').focus();
         				event.preventDefault();
         				return false;
         			}
         		}
             });
             
			 $('#number').keyup(function(event) {
				 if($("#city").val()==""){
					disable_step2_inputs();
					alert("Please select a city for your virtual number.");
	     			$('#city').focus();
	     			event.preventDefault();
	     			return false;
				 }
				 if($("#city").val()==99999 || $("#city").val()=="99999"){
        			if($("#sel_free_number").val()==""){
        				disable_step2_inputs();
                   		alert("Please select a number.");
        				$('#sel_free_number').focus();
        				event.preventDefault();
        				return false;
        			}
        		}
                 var val = $(this).val();
                 if (val.length == 0) {
                     $(this).val('+')
                     return true;
                 }
                 
                 if (val[0] != '+') {
                     val = '+' + val;
                 }
                 
                 var tmp = val;
                 
                 var last_char = val[ val.length -1 ]
                 var char_code = val.charCodeAt(val.length - 1) 
                 if ((char_code < 48 || char_code > 57)) {
                     tmp = '';
                     for(var i=0; i < val.length - 1; i++) {
                         tmp += val[i]         
                     }
                          
                 } 
                 
                 $(this).val( tmp )
                 
                 var option_value = tmp.replace('+','')
                 
                 if ($("#country option[value='" + option_value + "']").length > 0) {
                     $('#country').val( option_value )
                 }  
                 
         	});

			 $('#sipAddress').focusin(function(event) {
            	 if($("#city").val()==""){
 					disable_step2_inputs();
 					alert("Please select a city for your virtual number.");
 	     			$('#city').focus();
 	     			event.preventDefault();
 	     			return false;
 				 }
            	 if($("#city").val()==99999 || $("#city").val()=="99999"){
         			if($("#sel_free_number").val()==""){
         				disable_step2_inputs();
                    		alert("Please select a number.");
         				$('#sel_free_number').focus();
         				event.preventDefault();
         				return false;
         			}
         		}
             });

             $('#btn_choose_free_plan').mousedown(function(event) {
				if(validate_step1("free_plan")==true){
					$('#choosen_plan').val("");
					$('#plan_minutes').val("Unlimited");
					$('#plan_cost').val("0");
            		$('#plan_period').val("M");
					go_to_next_step();
				}
             });

             $('#btn_choose_tech_plan').mousedown(function(event) {
 				if(validate_step1("tech_plan")==true){
 					$('#choosen_plan').val("0");
 					$('#plan_minutes').val("Unlimited");
 					if($('#rad_tech_plan_monthly').is(':checked')==true){
 						$('#plan_cost').val(plan0_cost_monthly);
            			$('#plan_period').val("M");
 					}else if($('#rad_tech_plan_annual').is(':checked')==true){
 						$('#plan_cost').val(Math.round(plan0_cost_annual*12*100)/100);
            			$('#plan_period').val("Y");
 					}
 					go_to_next_step();
 				}
             });

             $('#btn_choose_plan1').mousedown(function(event) {
 				if(validate_step1("plan1")==true){
 					$('#choosen_plan').val("1");
 					$('#plan_minutes').val(plan1_minutes);
 					if($('#rad_plan1_monthly').is(':checked')==true){
 						$('#plan_cost').val(plan1_cost_monthly);
            			$('#plan_period').val("M");
 					}else if($('#rad_plan1_annual').is(':checked')==true){
 						$('#plan_cost').val(Math.round(plan1_cost_annual*12*100)/100);
            			$('#plan_period').val("Y");
 					}
 					go_to_next_step();
 				}
             });

             $('#btn_choose_plan2').mousedown(function(event) {
  				if(validate_step1("plan2")==true){
  					$('#choosen_plan').val("2");
  					$('#plan_minutes').val(plan2_minutes);
  					if($('#rad_plan2_monthly').is(':checked')==true){
 						$('#plan_cost').val(plan2_cost_monthly);
            			$('#plan_period').val("M");
 					}else if($('#rad_plan2_annual').is(':checked')==true){
 						$('#plan_cost').val(Math.round(plan2_cost_annual*12*100)/100);
            			$('#plan_period').val("Y");
 					}
  					go_to_next_step();
  				}
             });

             $('#btn_choose_plan3').mousedown(function(event) {
  				if(validate_step1("plan3")==true){
  					$('#choosen_plan').val("3");
  					$('#plan_minutes').val(plan3_minutes);
  					if($('#rad_plan3_monthly').is(':checked')==true){
 						$('#plan_cost').val(plan3_cost_monthly);
            			$('#plan_period').val("M");
 					}else if($('#rad_plan3_annual').is(':checked')==true){
 						$('#plan_cost').val(Math.round(plan3_cost_annual*12*100)/100);
            			$('#plan_period').val("Y");
 					}
  					go_to_next_step();
  				}
             });

			 $('#btn-continue-step1').mousedown(function(event) {
                 if(validate_step1()==true){
                 	$('#fwd_cntry').val($('#country').val());
					$('#did_country_code').val($("#did_country").val());
					$('#did_city_code').val($("#city").val());
					$('#vnum_country').val($("#did_country option:selected").text());
					$('#vnum_city').val($("#city option:selected").text());

					if($("#city").val()==99999 || $("#city").val()=="99999"){
						if($('#radSipForward').is(':checked')==true){
							$('#forwarding_type').val(2);
							$('#forwarding_number').val($('#sipAddress').val());
						}else{
							$('#forwarding_type').val(1);
							$('#forwarding_number').val($('#number').val());
						}
						<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id']) != ""){?>
						$('#next_page').val('free_number_070_AU.php');
						$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?free_number");
						<?php }else{?>
						$('#next_page').val('create_account.php');
						$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?create_account");
						<?php } ?>
					}else if($('#radSipForward').is(':checked')==true){
						$('#forwarding_type').val(2);
						$('#forwarding_number').val($('#sipAddress').val());
						$('#frm-step1').attr('action','<?php echo SITE_URL?>index.php?sip_forwarding');
						$('#choosen_plan').val(0);	
					}else{
						$('#forwarding_type').val(1);
						$('#forwarding_number').val($('#number').val());
						$('#frm-step1').attr('action','<?php echo SITE_URL?>index.php?choose_plan');
					}
					$('#frm-step1').submit();
                 }
             });
	            
        });//end of document ready function

		function go_to_next_step(){
			$('#fwd_cntry').val($('#country').val());
			$('#did_country_code').val($("#did_country").val());
			$('#did_city_code').val($("#city").val());
			$('#vnum_country').val($("#did_country option:selected").text());
			$('#vnum_city').val($("#city option:selected").text());
			if($('#radSipForward').is(':checked')==true){
				$('#forwarding_type').val(2);
				$('#forwarding_number').val($('#sipAddress').val());
			}else if($('#radNumberForward').is(':checked')==true){
				$('#forwarding_type').val(1);
				$('#forwarding_number').val($('#number').val());
			}
			if(user_logged_in == true){
				if($('#choosen_plan').val()==""){
					$('#next_page').val('free_number_070_AU.php');
					$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?free_number");
				}else{
					$('#next_page').val('make_payment.php');
					$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?make_payment");
				}
			}else{
				$('#next_page').val('create_account.php');
				$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?create_account");
			}
			if($('#choosen_plan').val()==""){
				$('#free_number_id').val($('#sel_free_number').val());
			}
			$('#frm-step1').submit();
        }
        
        function disable_step2_inputs(){
        	$('#radSipForward').removeAttr('checked');
			$('#radNumberForward').removeAttr('checked');
			$('#country').val("");
			$('#country').attr("disabled", true);
			$('#number').val("");
			$('#number').attr("disabled", true);
			$('#sipAddress').val("");
			$('#sipAddress').attr("disabled", true);
			disable_step3_inputs();
        }

        function disable_step3_inputs(){
        	$('#rad_tech_plan_monthly').removeAttr('checked');
			$('#rad_tech_plan_annual').removeAttr('checked');
			$('#rad_plan1_monthly').removeAttr('checked');
			$('#rad_plan1_annual').removeAttr('checked');
			$('#rad_plan2_monthly').removeAttr('checked');
			$('#rad_plan2_annual').removeAttr('checked');
			$('#rad_plan3_monthly').removeAttr('checked');
			$('#rad_plan3_annual').removeAttr('checked');
        }

        function get_cheapest_city_id(country_iso){

        	var url = '<?php echo SITE_URL; ?>ajax/ajax_get_cheapest_did_city.php';
        	var cheapest_city_id = "";
        	jQuery.ajaxSetup({async:false});
        	$.post(url, {"country_iso":country_iso},function(response){
           		//Populate the plan info
        		if(response.substring(0,6)=="Error:"){
        			alert(response);
        			return false;
        		}else{
        			cheapest_city_id = response;
        		}
        	});
            if(cheapest_city_id===false){
				return false;
            }
            return cheapest_city_id;
        }

        function populate_did_cities(country_iso){
        	$('#city option').remove();
            if (country_iso == '') {
                $('#city').append('<option value="">Please select a city</option');
                return true;                        
            }
            
            $('#city').append('<option value="">Loading...</option');
            $('#city').attr('disabled', 'disabled');

            cheapest_city_id = get_cheapest_city_id(country_iso);
            
            var url = '<?php echo SITE_URL; ?>ajax/ajax_load_did_cities.php';
            jQuery.ajaxSetup({async:false});
            $.get(url, {'country_iso' : country_iso}, function(response){
				//console.log(response);
                var data = jQuery.parseJSON(response);  
                $('#city').removeAttr('disabled');
                $('#city option').remove();
                $('#city').append('<option value="">Please select a city</option'); 
                $(data).each(function(index, item) {
                		city_selected = "";//reset
						if($.trim(item['city_id'])==cheapest_city_id){
							city_selected = " selected ";
						}
                	$('#city').append('<option value="'+$.trim(item['city_id'])+'" '+city_selected+' >'+item['city_name']+' '+item['city_prefix']+' (<?php echo CURRENCY_SYMBOL; ?>'+item['sticky_monthly_plan1']+'/month)</option>');
                });
            });
            
        }

        function populate_paid_plans(fwd_cntry, did_city_code){

            //Validate Input parameters
            if(fwd_cntry==""){
                alert("Required parameter fwd_cntry not passed in populate_did_plans.");
                return false;
            }
            if(did_city_code==""){
                alert("Required parameter did_city_code not passed in populate_did_plans.");
                return false;
            }
        	var url = '<?php echo SITE_URL; ?>ajax/ajax_get_plans_info.php';
           	$.post(url, {"fwd_cntry":fwd_cntry, "did_city_code":did_city_code},function(response){
           		//Populate the plan info
        		if(response.substring(0,6)=="Error:"){
        			disable_step2_inputs();
        			alert(response);
        			return false;
        		}else{
            		var arr_plan_info = response.split(",");
					//console.log(arr_plan_info);
            		//Minutes, Monthly and Annual prices of all three plans
            		plan1_minutes = arr_plan_info[0];
            		plan1_cost_monthly = arr_plan_info[1];
            		plan1_cost_annual = arr_plan_info[2];
            		$('#div_plan1_minutes').text(plan1_minutes);
            		$('#div_plan1_monthly').html('<?php echo CURRENCY_SYMBOL; ?>'+plan1_cost_monthly);
            		$('#div_plan1_annual').html('<?php echo CURRENCY_SYMBOL; ?>'+plan1_cost_annual).parent().prev().prev().css('display','none'); //js code added 17-03-2015
					
            		plan2_minutes = arr_plan_info[3];
            		plan2_cost_monthly = arr_plan_info[4];
            		plan2_cost_annual = arr_plan_info[5];
            		$('#div_plan2_minutes').text(plan2_minutes);
            		$('#div_plan2_monthly').html('<?php echo CURRENCY_SYMBOL; ?>'+plan2_cost_monthly);
            		$('#div_plan2_annual').html('<?php echo CURRENCY_SYMBOL; ?>'+plan2_cost_annual);
            		plan3_minutes = arr_plan_info[6];
            		plan3_cost_monthly = arr_plan_info[7];
            		plan3_cost_annual = arr_plan_info[8];
            		$('#div_plan3_minutes').text(plan3_minutes);
            		$('#div_plan3_monthly').html('<?php echo CURRENCY_SYMBOL; ?>'+plan3_cost_monthly);
            		$('#div_plan3_annual').html('<?php echo CURRENCY_SYMBOL; ?>'+plan3_cost_annual);
            		//Additional Minutes rate in Cents
					
					//update on 31st march 2015 for show all currency in dollar/euro instead of cent
            		var spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            		$('#div_plan1_addl_mins').html(spacer+ '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[9]/100).toFixed(2));
            		$('#div_plan2_addl_mins').html(spacer+ '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[9]/100).toFixed(2));
            		$('#div_plan3_addl_mins').html(spacer+ '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[9]/100).toFixed(2));
            		//Mobile and other rates - Plan 1
            		$('#div_plan1_row1_mins').text(arr_plan_info[0]);
            		$('#div_plan1_row1_rate').html( '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[9] /100).toFixed(2));
            		$('#div_plan1_row2_mins').text(arr_plan_info[10]);
            		$('#div_plan1_row2_rate').html( '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[11]/100 ).toFixed(2));
            		//Mobile and other rates - Plan 2
            		$('#div_plan2_row1_mins').text(arr_plan_info[3]);
            		$('#div_plan2_row1_rate').html( '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[9] /100).toFixed(2));
            		$('#div_plan2_row2_mins').text(arr_plan_info[12]);
            		$('#div_plan2_row2_rate').html( '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[13] /100).toFixed(2));
            		//Mobile and other rates - Plan 3
            		$('#div_plan3_row1_mins').text(arr_plan_info[6]);
            		//$('#div_plan3_row1_rate').html(arr_plan_info[9]+'<?php echo CENTS_SYMBOL; ?>');
					$('#div_plan3_row1_rate').html( '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[9] /100).toFixed(2));
            		$('#div_plan3_row2_mins').text(arr_plan_info[14]);
            		//$('#div_plan3_row2_rate').html(arr_plan_info[15]+'<?php echo CENTS_SYMBOL; ?>');
					$('#div_plan3_row2_rate').html( '<?php echo CURRENCY_SYMBOL; ?>'+parseFloat(arr_plan_info[15]/100).toFixed(2));
            		//Country Name in View Mobile and other calls rates mouseover popup
            		var selected_country = $("#country option:selected").text(); 
                 	selected_country = selected_country.substring(0,selected_country.indexOf("+"));
                	selected_country = selected_country.substring(0,30);//Maximum 30 characters.
                	$('#div_plan1_country_name1').text(selected_country);
 					$('#div_plan1_country_name2').text(selected_country);
 					$('#div_plan2_country_name1').text(selected_country);
 					$('#div_plan2_country_name2').text(selected_country);
 					$('#div_plan3_country_name1').text(selected_country);
 					$('#div_plan3_country_name2').text(selected_country);
					//Display the plans
               		$('#div_free_plan').hide();
            		$('#div_paid_plan').show();
            		$('#div_tech_plan').hide();
        		}
           	});
        }

        function validate_step1(which_plan){
			if($('#did_country').val() == ""){
				alert("Please select a country for your virtual number.");
				$('#did_country').focus();
				return false;
			}
			if($('#city').val() == ""){
				alert("Please select a city for your virtual number.");
				$('#city').focus();
				return false;
			}
			if($("#city").val()==99999 || $("#city").val()=="99999"){//Free number selected
				if($("#sel_free_number").val()==""){
					alert("Please select a free virtual number.");
					$("#sel_free_number").focus();
					return false;
				}
			}
			if($('#radNumberForward').is(':checked')==true){
				if($('#country').val() == ""){
					alert("Please select call forwarding country in step 2.");
					$('#country').focus();
					return false;
				}
				//first clean up destination number
				var dest = $('#number').val();
				if(dest.length == 0){
					$('#number').val('+');
					dest = $('#number').val();
				}
				if(dest.charCodeAt(0) != 43){
					$('#number').val('+'+dest);
					dest = $('#number').val();
				}
				var clean_dest = '+';
				for(var i=1;i<=dest.length;i++){
					if(dest.charCodeAt(i)<48 || dest.charCodeAt(i)>57){
						//dont'take this character.
					}else{
						clean_dest += dest.charAt(i);
					}
				}
				$('#number').val(clean_dest);
				//end of clenup destination number
				if($('#number').val().length <= 10){
					alert("Please enter call forwarding number with country code and area code (min 10 digits)");
					$('#number').focus();
					//The following three lines set cursor at end of existing input value
					var tmpstr = $('#number').val();//Put val in a temp variable
	                $('#number').val('');//Blank out the input
	                $('#number').val(tmpstr);//Refill using temp variable value
					return false;
				}
			}else if($('#radSipForward').is(':checked')==true){
				if($.trim($('#sipAddress').val())==""){
					alert("Please enter SIP Address.");
					return false;
				}
				if(validateEmail($.trim($('#sipAddress').val()))==false){
					alert("Please enter a valid SIP Address.");
					return false;
				}
			}else{//Neither PSTN Forwarding is selected nor SIP Forwarding is selected
				alert("Please select either Number forwarding or SIP Address forwarding in step 2. ");
				return false;
			}
			//Except for free plan selection of monthly or annual option is to be validated
			switch(which_plan){
				case "free_plan":
					return true; //No more validations required for this plan.
					break;
				case "tech_plan":
					if($('#rad_tech_plan_monthly').is(':checked')==false && $('#rad_tech_plan_annual').is(':checked')==false){
						$('#rad_tech_plan_monthly').focus();
						alert("Please select either monthly or annual payment option in step 3");
						return false;
					}
					break;
				case "plan1":
					if($('#rad_plan1_monthly').is(':checked')==false && $('#rad_plan1_annual').is(':checked')==false){
						$('#rad_plan1_monthly').focus();
						alert("Please select either monthly or annual payment option in step 3 Starter Plan.");
						return false;
					}
					break;
				case "plan2":
					if($('#rad_plan2_monthly').is(':checked')==false && $('#rad_plan2_annual').is(':checked')==false){
						$('#rad_plan2_monthly').focus();
						alert("Please select either monthly or annual payment option in step 3 Business Plan.");
						return false;
					}
					break;
				case "plan3":
					if($('#rad_plan3_monthly').is(':checked')==false && $('#rad_plan3_annual').is(':checked')==false){
						$('#rad_plan3_monthly').focus();
						alert("Please select either monthly or annual payment option in step 3 Enterprise Plan.");
						return false;
					}
					break;
				default:
					alert("Error: Unknown plan passed to validate_step1");
			}
			return true;
		}//end of function validate_step1

		function validateEmail(sEmail) {
		    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		    if (filter.test(sEmail)) {
		        return true;
		    }
		    else {
		        return false;
		    }
		}
        
        </script>
        <?php if(SITE_COUNTRY!="COM"){?>
        <!--Start of Zopim Live Chat Script-->
		<script type="text/javascript">
		window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
		d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
		_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
		$.src='//v2.zopim.com/?1piIDUavtFoOMB89h2pj9OK8cT8CswZK';z.t=+new Date;$.
		type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
		</script>
		<!--End of Zopim Live Chat Script-->
        <?php } ?>
	</head>
	
	<body lang="en">
        
        <!-- Top Bar -->
	    <div class="topbar">
	         <!-- content -->
	         <div class="wrapper">
	              <div class="container">
	                  <div class="info"><span class="Mtop2"><img alt="email us" src="<?php echo SITE_URL; ?>images/mail.png"/></span>&nbsp;<span class="Mleft5"><?php echo ACCOUNT_CHANGE_EMAIL; ?></span></div>
	                  <div class="logintop">
	                  		<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){ ?>
	                  			Hi <?php echo $_SESSION['user_name']; ?>&nbsp;<a href="<?php echo SITE_URL; ?>dashboard/index.php">My Account</a>
                                		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo SITE_URL; ?>dashboard/logout.php">Logout</a>
	                  		<?php }else{ ?>
	                  			<?php if(isset($_GET['error']) && $_GET['error']=='failed' ) { ?> <div style='color:red;'>Member details incorrect,try again.</div><?php } ?>
		                       	<form name="toplogin" id="toplogin" action="<?php echo SITE_URL; ?>dashboard/login.php" method="post">
		                       	
								<?php require "login_top.php"; ?>
								
								<input type="hidden" name="site_country" value="AU" />
                                <input type="hidden" name="front_page_login" value="2926354" id="front_page_login">
		                       	</form>
		                    <?php } //End of else part of no login session?>
	                  </div>
	              </div>
	         </div>
	         <!-- content closed -->         
	    </div>
	    <!-- Top Bar Closed -->
	    
	    <?php require "menu_and_logo.php"; ?>  
	    
	    <!-- Banner -->
	    <div class="wrapper">
	         <div class="container Mtop40">
	              <img src="<?php echo SITE_URL; ?>images/SF-pricing.jpg" width="1024" height="201" alt="Our Pricing">
	         </div>
	    </div>
	    <!-- Banner Closed -->

	<!-- Main wrapper starts here -->
    <div class="wrapper">
         <div class="container">
         <div id="idtabs" class="idtabs"> 
  			<ul> 
    			<li><a href="#tab1" class="selected">Plans and Pricing</a></li> 
    			<!-- <li><a href="#tab2">How It Works</a></li> 
    			<li><a href="#tab3">Features</a></li> -->
                <!--div class="clearfloat"></div-->
  			</ul> 
            <div class="idtabs-warp">
            	<div id="tab1">
            		<div class="row">
						<div class="plan-box">               
							<!--<div class="planbox-3">
                     			<h1><strong>Forward to mobile or Landline</strong></h1>
                     			<p class="font18">These plans allow you to forward to any mobile or landline number in <?php echo COUNTRY_NAME; ?> and the most numbers around the word. See above for available destinations.</p> 
								<div class="payment-p-wrap Mtop40">
                					<h2>Select your Number</h2>
                					<div class="row">Select the Number you would like:</div>
									<div class="row">
                                			<select id="did_country" name="did_country" class="selectP5">
	                                           <option value="">Please select a country</option>
	                                           <?php foreach($did_countries as $did_country) { ?>
	                                           <option value="<?php echo trim($did_country["country_iso"]); ?>" <?php if(SITE_COUNTRY=="UK" && trim($did_country["country_iso"])=="GB"){ echo "selected"; } ?>><?php echo $did_country["country_name"] . ' ' . $did_country["country_prefix"] ?></option>
	                                           <?php } ?>
   	                                      	</select>
   										</div>
                                		<div class="row">
                                			<select id="city" name="city" class="selectP5">
                                				<option value="" selected="selected">Please select a city</option>
	                                        </select>
                                		</div>
                                        <div class="clearfloat"></div>
									</div>

									<div class="payment-p-wrap Mtop40">
										<h2>Enter Forwading Details</h2>
										<div class="row">
											<span class="memberradio">
											<input type="radio" name="iCheck" id="radNumberForward" checked />
											</span>
											<div class="hint">Enter the Number to forward to:</div>
											<div class="hint-img">
												<div class="form_hint">Use this option to forward your Online Number to an existing mobile or landline number. For example <?php if(SITE_COUNTRY=="AU"){?>+6141234567 or +61212345678 <?php }else{?>+447748742115<?php } ?>.</div>
											</div>
                                        <div class="clearfloat"></div>
        								</div>
										<div class="row">
                                			<select id="country" name="country" class="selectP5">
	                                           <option value="">Please Select Forwarding Country</option>
	                                           <?php foreach($countries as $country) { ?>
	                                                <option value="<?php echo trim($country['country_code']); ?>" <?php if(trim($sel_fwd_cntry) != "" && trim($sel_fwd_cntry)==trim($country["country_code"])){echo " selected "; } ?>><?php echo $country['printable_name'] . '+' . $country['country_code'] ?></option>
	                                           <?php } ?>
	                                        </select>
	                                    </div>
										<div class="row">
										<input type="text" value="" id="number" name="number" placeholder="+" class="input">
   										</div>
   										<div class="row">
   										<span class="memberradio">
   										<input type="radio" name="iCheck" id="radSipForward" />
   										</span>
   										<div class="hint">Enter SIP Address:</div>
   										<div class="hint-img">
   											<div class="form_hint">SIP is for advanced users that wish to forward there number to a network and not a physical phone. SIP stands for Session Initiation Protocol</div>
   										</div>
   										</div> 
   										<div class="row">
   										<input type="text" value="" id="sipAddress" name="sipAddress" placeholder="you@sip_provider.com" class="input" disabled >
   										</div>
										<div class="clearfloat"></div>
             						</div>
                                    <div class="clearfloat"></div>
								</div>
								-->
								
								<h1 class="Mtop30"><strong>Forward to mobile or Landline</strong></h1>
								<!--span class="Mtop30 price_heading_landline"><strong>Forward to mobile or Landline</strong></span-->
                     			<p class="font18">These plans allow you to forward to any mobile or landline number in Australia and the most numbers around the word. See above for available destinations.</p>  
                     			<div class="form-steps Mtop20">
                     				<div class="form-stems-box-1">
										<div class="payment-p-wrap-2">
                							<h2>Select your Number</h2>
                							<!--div class="row">Select the Number you would like:</div-->
											<div class="row">
												<label for="did_country">Select the Number you would like:</label>
                                				<select title="Select Country" id="did_country" name="did_country" class="selectP5">
	                                           		<option value="">Please select a country</option>
	                                           	    <?php foreach($did_countries as $did_country) { ?>
	                                           		<option value="<?php echo trim($did_country["country_iso"]); ?>" <?php if(trim($did_country["country_iso"])==$default_did_country){ echo "selected"; } ?>><?php echo $did_country["country_name"] . ' ' . $did_country["country_prefix"] ?></option>
	                                           		<?php } ?>
   	                                      		</select>
   											</div>
                                			<div class="row">
												<label for="city">Select City:</label>
                                				<select id="city" title="Select City" name="city" class="selectP5">
	                                      			<option value="" selected="selected">Please select a city</option>
	                                      		</select>
                                			</div>
                                			<div id="div_sel_free_number">
												<div class="row">
													<label for="sel_free_number">Select Your Number:</label>
	                                				<select title="Select free number" id="sel_free_number" name="sel_free_number" class="selectP5">
		                                      			<?php foreach($free_numbers as $free_number) { ?>
		                                      			<option value="<?php echo $free_number["id"]; ?>"><?php echo $free_number["did_number"]; ?></option>
		                                      			<?php } ?>
		                                      		</select>
	                                			</div>       
                                			</div>                                 
                                        	<div class="clearfloat"></div>
										</div>
									</div>
                     				<div class="form-stems-box-2">
										<div class="payment-p-wrap-2">
                						<h2>Enter Forwading Details</h2>
										<div class="row">
											<label for="radNumberForward">Enter the Number to forward to:</label>
											<input title="Enter Number" name="icheck" id="radNumberForward" type="radio" value="" class="fltlft radio-btn" checked >
											<!--div class="hint">Enter the Number to forward to:</div-->
											<div class="hint-img float_right"><div class="form_hint">Use this option to forward your Online Number to an existing mobile or landline number. For example +6141234567 or +61212345678.</div></div>
                                        	<div class="clearfloat"></div>
        								</div>
        								
										<div class="row">
											<!--div id="div_forwarding_country"-->
											<label for="country">Forwarding Country:</label>
                                			<select title="Select Forwarding Country" id="country" name="country" class="selectP5">
	                                           <option value="">Please Select Forwarding Country</option>
	                                           <?php foreach($countries as $country) { ?>
	                                                <option value="<?php echo trim($country['country_code']); ?>" <?php if(trim($sel_fwd_cntry) != "" && trim($sel_fwd_cntry)==trim($country["country_code"])){echo " selected "; } ?>><?php echo $country['printable_name'] . '+' . $country['country_code'] ?></option>
	                                           <?php } ?>                                    
	                                      	</select>
	                                      	<!--/div-->
                                		</div>
										<div class="row">
											<div id="div_forwarding_number">
											<label for="number">Enter Number</label>
                                			<input title="Enter Number" type="text" value="" id="number" name="number" placeholder="+" class="input">
                                			</div>
   										</div>
   							
   										<div class="row">
											<label for="radSipForward">Enter SIP Address:</label>
   											<input name="icheck" id="radSipForward" type="radio" value="" class="fltlft radio-btn">
   											<!--div class="hint">Enter SIP Address:</div-->
   											<div class="hint-img float_right">
   												<div class="form_hint">SIP is for advanced users that wish to forward there number to a network and not a physical phone. SIP stands for Session Initiation Protocol</div>
   											</div>
   										</div>
   										
   										<div class="row">
   											<div id="div_forwarding_sip" style="visibility:hidden;">
											<label for="sipAddress">SIP User & Provider</label>
   											<input title="Enter your SIP user and provider" type="text" value="" id="sipAddress" name="sipAddress" placeholder="you@sip_provider.com" class="input" disabled="">
   											</div>
   										</div>
   										
										<div class="clearfloat">&nbsp;</div>
             							</div>                     
                     				</div>
                     				<div class="form-stems-box-3">
                						<h2>Choose Plan</h2>
									</div> 
                     				<div class="clearfloat"></div>
                     
                     <div class="plan-box-2">
<div class="pricing-main Mtop20">

							                            <div id="div_free_plan">
							                            	<div class="planbox2">&nbsp;</div>
                            	                            <div class="planbox1">
                                 <div class="planbox1-top">
                                 <div class="most-popular">
                                   <img src="<?php echo SITE_URL; ?>images/most-popular.png" width="72" height="104" alt="Most Popular Plan"> </div>
                                      <div class="planbox2-head">
                                           <div class="planbox2-mainhead">Free Plan</div>
                                      <div class="clearfloat"></div>
                                               <div class="plans-mins">Unlimited</div>
                                               <div class="clearfloat"></div>
                                               <p><span class="txt-red">*</span>Minutes Included</p>
                                      </div>
                                      <div class="clearfloat"></div>

                                   <div class="plans-sked">
										<div class="row">
										<label for="rad_free_plan_monthly"><?php echo CURRENCY_SYMBOL; ?>0/mo (Free)</label>
										<input title="Free plan" name="rad_free_plan_period" id="rad_free_plan_monthly" type="radio" checked value="" class="fltlft radio-btn" />
										
										<!--div class="hint"><?php echo CURRENCY_SYMBOL; ?>0/mo (Free)</div-->
                                        <div class="clearfloat"></div>
										</div>
        
        <div class="clearfloat"></div>
                                   </div>                                      
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
<br>
											<!--p class="txt-c"><strong>0</strong>&cent;</p-->
											<p class="txt-c"><strong><?echo CURRENCY_SYMBOL ;?>0.00</strong></p>
											<div style="margin:0 0 10% 10%">
												<span class="txt-red">*</span>
												<span>Additional Minutes</span>
											</div>                                           
                                           <!-- <p class="Mtop63">
                                              <a href="" class="choose-btn-green">Choose</a>
                                           </p>  -->
                                      </div>
                                      <div class="clearfloat"></div>
                                      <div class="rates-block"><p class="f14 txt-c"><a href="javascript:void(0);">View Mobile <br>and <br>Other Call Rates</a></p><div class="rates-pop">
						<h3>Free Plan</h3>
						<div style="margin:0px 5px 5px 5px;">
							<table style="width:100%;border:0;border-spacing=0;border-collapse:collapse" class="view_table_n">
							<tbody><tr>
								<th style="width:200px;text-align:left" class="starter_blue">Destination</th>
								<th style="width:200px;text-align:left" class="starter_blue">Minutes Included</th>
								<th style="width:200px;text-align:left" class="starter_blue">Additional Minutes</th>
							</tr>
							
							<tr>
								<td style="white-space:nowrap"><span id="div_free_country_name1">Any</span>&nbsp;-&nbsp;Landline</td>
								<td style="text-align:center" class="view_value_n">Unlimited</td>
								<!--td style="text-align:center" class="view_value_n">
									0&#162;</td-->
								<td style="text-align:center" class="view_value_n">
									<?php echo CURRENCY_SYMBOL; ?>0.00</td>
							</tr>
							
							<tr>
								<td style="white-space:nowrap"><span id="div_free_country_name2">Any</span>&nbsp;-&nbsp;Mobile</td>
								<td style="text-align:center" class="view_value_n">Unlimited</td>
								<!--td style="text-align:center" class="view_value_n">
									0&#162;</td-->
								<td style="text-align:center" class="view_value_n">
									<?php echo CURRENCY_SYMBOL; ?>0.00</td>
							</tr>
							<tr>
								<td>Voice Mail Unlimited Mins</td>
								<td style="text-align:center" class="view_value_n">Unlimited</td>
								<!--td style="text-align:center" class="view_value_n">
									0&#162;</td-->
								<td style="text-align:center" class="view_value_n">
									<?php echo CURRENCY_SYMBOL; ?>0.00</td>
							</tr>							
							</tbody></table>
						</div>
					</div>
                                      <div class="clearfloat"></div>
                                      </div>                                    
<div class="row"><a id="btn_choose_free_plan" class="blue-btn q-button">Choose Plan</a></div>                                      
                                 </div>
                            </div>
                            </div><!-- End of of div_free_plan -->

							<div id="div_tech_plan">
							                            	<div class="planbox2">&nbsp;</div>
                            	                            <div class="planbox1">
                                 <div class="planbox1-top">
                                 <div class="most-popular">
                                   <img src="<?php echo SITE_URL; ?>images/most-popular.png" width="72" height="104" alt="Most Popular Plan"> </div>
                                      <div class="planbox2-head">
                                           <div class="planbox2-mainhead">Tech Plan</div>
                                      <div class="clearfloat"></div>
                                               <div class="plans-mins"><span id="div_plan0_minutes"></span></div>
                                               <div class="clearfloat"></div>
                                               <p><span class="txt-red">*</span>Minutes Included</p>
                                      </div>
                                      <div class="clearfloat"></div>

                                   <div class="plans-sked">
<div class="row">
	<!--label for="rad_tech_plan_monthly"><span id="div_plan0_monthly"></span>/mo (Monthly)</label-->
	<input title="Tech Plan" name="rad_tech_plan_period" id="rad_tech_plan_monthly" type="radio" checked value="" class="fltlft radio-btn">
	<div class="hint"><span id="div_plan0_monthly"></span>/mo (Monthly)</div>
                                        <div class="clearfloat"></div>
        </div>
        <div class="row">
		<!--label for="rad_tech_plan_annual"><span id="div_plan0_annual"></span>/mo (Annual)</label-->
		<input title="Annual Plan" name="rad_tech_plan_period" id="rad_tech_plan_annual" type="radio" value="" class="fltlft radio-btn">
		<div class="hint"><span id="div_plan0_annual"></span>/mo (Annual)</div>
                                        <div class="clearfloat"></div>
        </div>
        <div class="clearfloat"></div>
                                   </div>                                      
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
<br>
                                  <p class="txt-c"><strong>0</strong>&cent;<br>
                                  <span class="txt-red">*</span>Additional Minutes</p><br>  
                                           <!-- <p class="Mtop63">
                                              <a href="" class="choose-btn-green">Choose</a>
                                           </p>  -->
                                      </div>
                                      <div class="clearfloat"></div>
                                      <div class="rates-block"><p class="f14 txt-c"><a href="javascript:void(0);">View Mobile <br>and <br>Other Call Rates</a></p><div class="rates-pop">
						<h3>Tech Plan</h3>
						<div style="margin:0px 5px 5px 5px;">
							<table style="width:100%;border:0;border-spacing=0;border-collapse:collapse" class="view_table_n">
							<tbody><tr>
								<th style="width:80px;text-align:center" class="starter_blue">Destination</th>
								<th style="width:80px;text-align:center" class="starter_blue">Minutes Included</th>
								<th style="width:80px;text-align:center" class="starter_blue">Additional Minutes</th>
							</tr>
							
							<tr>
								<td style="white-space:nowrap">SIP Address</td>
								<td style="text-align:center" class="view_value_n">Unlimited</td>
								<td style="text-align:center" class="view_value_n">
									<?php echo CURRENCY_SYMBOL; ?>0.00</td>
							</tr>
							
							<tr>
								<td>Voice Mail Unlimited Mins</td>
								<td style="text-align:center" class="view_value_n">Unlimited</td>
								<td style="text-align:center" class="view_value_n">
									<?php echo CURRENCY_SYMBOL; ?>0.00</td>
							</tr>							
							</tbody></table>
						</div>
					</div>
                                      <div class="clearfloat"></div>
                                      </div>                                    
<div class="row"><a id="btn_choose_tech_plan" class="blue-btn q-button">Choose Plan</a></div>                                      
                                 </div>
                            </div>
                            </div><!-- End of of div_tech_plan -->


							<div id="div_paid_plan">
                            <div class="planbox2">
                                 <div class="planbox2-top">
                                      <div class="planbox2-head">
                                           <div class="planbox2-mainhead">Starter Plan</div>
                                      <div class="clearfloat"></div>
                                               <div class="plans-mins"><span id="div_plan1_minutes"></span></div>
                                               <div class="clearfloat"></div>
                                               <p><span class="txt-red">*</span>Minutes Included</p>
                                      </div>
                                      <div class="clearfloat"></div>

                                   <div class="plans-sked">
<div class="row">
	<!--label for="rad_plan1_monthly"><span id="div_plan1_monthly"></span>/mo (Monthly)</label-->
	<input title="Plan Monthly" name="rad_plan1_period" id="rad_plan1_monthly" type="radio" checked value="" class="fltlft radio-btn">
	<div class="hint"><span id="div_plan1_monthly"></span>/mo (Monthly)</div>
                                        <div class="clearfloat"></div>
        </div>
        <div class="row">
		
		<label for="rad_plan1_annual">/mo (Annual)</label>
		<input title="Annual Plan" name="rad_plan1_period" id="rad_plan1_annual" type="radio" value="" class="fltlft radio-btn">
		<div class="hint"><span id="div_plan1_annual"></span>/mo (Annual)</div>
                                        <div class="clearfloat"></div>
        </div>
        <div class="clearfloat"></div>
                                     </div>                                                                                     
                                 </div>                                
                                 <div class="planbox2-body">
                                      <div class="plan-list">
                                           <ul>
                                              <li>Call Diversion </li>
                                              <li>Online Call Records
                                              </li>
                                              <li>Voice Mail</li>
                                              <li>Unlimited Diversion Updates</li>
											  <li>Instant Diversion Updates</li>
                                              <li>Forwarding Number Hidden</li>
                                           </ul><br>
											<p class="txt-c"><strong><span id="div_plan1_addl_mins"></span></strong><br>
											<span style="color:red">*</span>Additional Minutes</p><br>                                           
                                           <!-- <p class="Mtop20">
                                              <a href="" class="choose-btn">Choose</a>
                                           </p>  -->
                                      </div>
                                      <div class="clearfloat"></div>
                                      <div class="rates-block"><p class="f14 txt-c"><a href="javascript:void(0);">View Mobile <br>and <br>Other Call Rates</a></p><div class="rates-pop">
						<h3>Starter Plan</h3>
						<div style="margin:0px 5px 5px 5px;">
							<table style="width:100%;border:0;border-spacing=0;border-collapse:collapse" class="view_table_n">
							<tbody><tr>
								<th style="width:200px;text-align:left" class="starter_blue">Destination</th>
								<th style="width:200px;text-align:left" class="starter_blue" >Minutes Included</th>
								<th style="width:200px;text-align:left" class="starter_blue">Additional Minutes</th>
							</tr>
							
							<tr>
								<td><span id="div_plan1_country_name1"></span>&nbsp;-&nbsp;Landline</td>
								<td style="text-align:center" class="view_value_n"><div id="div_plan1_row1_mins"></div></td>
								<td style="text-align:center" class="view_value_n">
									<div id="div_plan1_row1_rate"></div></td>
							</tr>
							
							<tr>
								<td><span id="div_plan1_country_name2"></span>&nbsp;-&nbsp;Mobile</td>
								<td style="text-align:center" class="view_value_n"><div id="div_plan1_row2_mins"></div></td>
								<td style="text-align:center" class="view_value_n">
									<div id="div_plan1_row2_rate"></div></td>
							</tr>
							<tr>
								<td>Voice Mail Unlimited Mins</td>
								<td style="text-align:center" class="view_value_n">Unlimited</td>
								<td style="text-align:center" class="view_value_n">
									<!--0&#162;</td>-->
									<?php echo CURRENCY_SYMBOL; ?>0.00</td>
							</tr>							
							</tbody></table>
						</div>
					</div>
                                      <div class="clearfloat"></div>
                                      </div>
<div class="row"><a id="btn_choose_plan1" class="blue-btn q-button">Choose Plan</a></div>                                      
                                 </div>
                            </div>
                            
                            
                            
                            <div class="planbox1">
                                 <div class="planbox1-top">
                                 <div class="most-popular">
                                   <img src="<?php echo SITE_URL; ?>images/most-popular.png" width="72" height="104" alt="Most Popular Plan"> </div>
                                      <div class="planbox2-head">
                                           <div class="planbox2-mainhead">Business Plan</div>
                                      <div class="clearfloat"></div>
                                               <div class="plans-mins"><span id="div_plan2_minutes"></span></div>
                                               <div class="clearfloat"></div>
                                               <p><span class="txt-red">*</span>Minutes Included</p>
                                      </div>
                                      <div class="clearfloat"></div>

                                   <div class="plans-sked">
<div class="row">
<input title="Plan Monthly" name="rad_plan2_period" id="rad_plan2_monthly" type="radio" checked value="" class="fltlft radio-btn">
<div class="hint"><span id="div_plan2_monthly"></span>/mo (Monthly)</div>
                                        <div class="clearfloat"></div>
        </div>
        <div class="row">
		<input title="Annual Plan" name="rad_plan2_period" id="rad_plan2_annual" type="radio" value="" class="fltlft radio-btn">
		
		<div class="hint"><span id="div_plan2_annual"></span>/mo (Annual)</div>
                                        <div class="clearfloat"></div>
        </div>
        <div class="clearfloat"></div>
                                   </div>                                      
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
<br>
                                  <p class="txt-c"><strong><span id="div_plan2_addl_mins"></span></strong><br>
                                  <span style="color:red">*</span>Additional Minutes</p><br>                                           
                                           <!-- <p class="Mtop63">
                                              <a href="" class="choose-btn-green">Choose</a>
                                           </p>  -->
                                      </div>
                                      <div class="clearfloat"></div>
                                      <div class="rates-block"><p class="f14 txt-c"><a href="javascript:void(0);">View Mobile <br>and <br>Other Call Rates</a></p><div class="rates-pop">
						<h3>Business Plan</h3>
						<div style="margin:0px 5px 5px 5px;">
							<table style="width:100%;border:0;border-spacing=0;border-collapse:collapse" class="view_table_n">
							<tbody><tr>
								<th style="width:200px;text-align:left" class="starter_blue">Destination</th>
								<th style="width:200px;text-align:left" class="starter_blue">Minutes Included</th>
								<th style="width:200px;text-align:left" class="starter_blue">Additional Minutes</th>
							</tr>
							
							<tr>
								<td><span id="div_plan2_country_name1"></span>&nbsp;-&nbsp;Landline</td>
								<td style="text-align:center" class="view_value_n"><div id="div_plan2_row1_mins"></div></td>
								<td style="text-align:center" class="view_value_n">
									<div id="div_plan2_row1_rate"></div></td>
							</tr>
							
							<tr>
								<td><span id="div_plan2_country_name2"></span>&nbsp;-&nbsp;Mobile</td>
								<td style="text-align:center" class="view_value_n"><div id="div_plan2_row2_mins"></div></td>
								<td style="text-align:center" class="view_value_n">
									<div id="div_plan2_row2_rate"></div></td>
							</tr>
							<tr>
								<td>Voice Mail Unlimited Mins</td>
								<td style="text-align:center" class="view_value_n">Unlimited</td>
								<td style="text-align:center" class="view_value_n">
									<?php echo CURRENCY_SYMBOL; ?>0.00</td>
							</tr>							
							</tbody></table>
						</div>
					</div>
                                      <div class="clearfloat"></div>
                                      </div>                                    
<div class="row"><a id="btn_choose_plan2" class="blue-btn q-button">Choose Plan</a></div>                                      
                                 </div>
                            </div>
                          <div class="planbox2">
                              <div class="planbox2-top">
                                <div class="planbox2-head">
                                  <div class="planbox2-mainhead">Enterprise Plan</div>
                                      <div class="clearfloat"></div>
                                               <div class="plans-mins"><span id="div_plan3_minutes"></span></div>
                                               <div class="clearfloat"></div>
                                               <p><span class="txt-red">*</span>Minutes Included</p>
                                      </div>
                                      <div class="clearfloat"></div>

                                   <div class="plans-sked">
<div class="row">
	<input title="Monthly Plan" name="rad_plan3_period" id="rad_plan3_monthly" type="radio" checked value="" class="fltlft radio-btn">
<div class="hint"><span id="div_plan3_monthly"></span>/mo (Monthly)</div>
                                        <div class="clearfloat"></div>
        </div>
        <div class="row">
		<input title="Annual Plan" name="rad_plan3_period" id="rad_plan3_annual" type="radio" value="" class="fltlft radio-btn">
		<div class="hint"><span id="div_plan3_annual"></span>/mo (Annual)</div>
                                        <div class="clearfloat"></div>
        </div>
        <div class="clearfloat"></div>
                                     </div>                                                                           
                              </div>
                              <div class="planbox2-body">
                                <div class="plan-list">
                                  <ul>
                                    <li>Call Diversion </li>
                                    <li>Online Call Records </li>
                                    <li>Voice Mail</li>
                                    <li>Unlimited Diversion Updates</li>                                             <li>Instant Diversion Updates</li>
                                              <li>Forwarding Number Hidden</li>
                                  </ul><br>
                                  <p class="txt-c"><strong><span id="div_plan3_addl_mins"></span></strong><br>
									<span style="color:red">*</span>Additional Minutes</p><br>
                                  <!-- <p class="Mtop20">
                                              <a href="" class="choose-btn">Choose</a>
                                           </p>  -->
                                </div>
                                      <div class="clearfloat"></div>
                                      <div class="rates-block"><p class="f14 txt-c"><a href="javascript:void(0);">View Mobile <br>and <br>Other Call Rates</a></p><div class="rates-pop">
						<h3>Enterprise Plan</h3>
						<div style="margin:0px 5px 5px 5px;">
							<table style="width:100%;border:0;border-spacing=0;border-collapse:collapse" class="view_table_n">
							<tbody><tr>
								<th style="width:200px;text-align:left" class="starter_blue">Destination</th>
								<th style="width:200px;text-align:left" class="starter_blue" >Minutes Included</th>
								<th style="width:200px;text-align:left" class="starter_blue" >Additional Minutes</th>
							</tr>
							
							<tr>
								<td><span id="div_plan3_country_name1"></span>&nbsp;-&nbsp;Landline</td>
								<td style="text-align:center" class="view_value_n"><div id="div_plan3_row1_mins"></div></td>
								<td style="text-align:center" class="view_value_n"><div id="div_plan3_row1_rate"></div></td>
							</tr>
							
							<tr>
								<td><span id="div_plan3_country_name2"></span>&nbsp;-&nbsp;Mobile</td>
								<td style="text-align:center" class="view_value_n"><div id="div_plan3_row2_mins"></div></td>
								<td style="text-align:center" class="view_value_n"><div id="div_plan3_row2_rate"></div></td>
							</tr>
							<tr>
								<td>Voice Mail Unlimited Mins</td>
								<td style="text-align:center" class="view_value_n">Unlimited</td>
								<td style="text-align:center" class="view_value_n">
									<?php echo CURRENCY_SYMBOL; ?>0.00</td>
							</tr>                            
							
							</tbody></table>
						</div>
					</div>
                                      <div class="clearfloat"></div>
                                      </div>                                
                                <div class="row"><a id="btn_choose_plan3" class="blue-btn q-button">Choose Plan</a></div>
                              </div>
                              
                          </div>
                          </div><!-- end div_paid_plan -->
                                                   
                        </div>
                                             <p class="legend-ast"><span class="txt-red">*</span>When forwarding to most landline</p>                                                                     
<div class="clearfloat"></div>
</div>
                     
                     
                     
                     
                     
                     	
                     			</div>
								
                                <!-- <div class="Mtop15">
                                       <a class="payment-p-wrap-btn" id="btn-continue-step1">Continue</a>
                                </div>
                                <div class="clearfloat"></div>
								<p>&nbsp;</p>
								<p>&nbsp;</p>  -->
<div class="inside-wrap">
<div class="plan-list-2">
  <h3>Call Diversion</h3>
  <ul>
  <li>Forward your number 24/7</li>
  <li>Forward to mobile or landline</li>
  </ul>
      <div class="clearfloat"></div>
</div>
<div class="plan-list-2">
  <h3>Online Call Records</h3>
  <ul>
  <li>See who called you</li>
  <li>Full call records</li>
  </ul>
      <div class="clearfloat"></div>
</div>
<div class="plan-list-2">
  <h3>Voice Mail</h3>
  <ul>
  <li>Customised Voice Mail Message</li>
  <li>Emailed Alerts with MP3 Attachment</li>
  </ul>
      <div class="clearfloat"></div>
</div>
<div class="plan-list-2">
  <h3>Unlimited Diversion Updates</h3>
  <ul>
  <li>Online Control Panel</li>
  <li>Update Forwarding Number 24/7</li>
  </ul>
      <div class="clearfloat"></div>
</div>
<div class="plan-list-2">
  <h3>Instant Diversion Updates</h3>
  <ul>
  <li>Updates are processed instantly</li>
  </ul>
      <div class="clearfloat"></div>
</div>
<div class="plan-list-2">
  <h3>Forwarding Number Hidden</h3>
  <ul>
  <li>Destination Number Not Seen
(Clients only see your virtual number)</li>
  </ul>
      <div class="clearfloat"></div>
</div>
</div>
<div class="clearfloat"></div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="nofloat inside-wrap">
<h2>Virtual Numbers We Supply</h2><br>
<div class="fltlft add-pads">
<p>Algeria</p>
<p>Argentina</p>
<p>Australia</p>
<p>Austria</p>
<p>Bahrain</p>
<p>Belgium</p>
<p>Brazil</p>
<p>Bulgaria</p>
<p>Canada</p>
<p>Chile</p>
<p>China</p>
<p>Colombia</p>
<p>Costa Rica</p>
<p>Croatia</p>
<p>Cyprus</p>
<p>Czech Republic</p>
</div> 
<div class="fltlft add-pads">
<p>Denmark</p>
<p>Dominican Republic</p>
<p>El Salvador</p>
<p>Estonia</p>
<p>Finland</p>
<p>France</p>
<p>Georgia</p>
<p>Germany</p>
<p>Greece</p>
<p>Guatemala</p>
<p>Hong Kong</p>
<p>Hungary</p>
<p>Ireland</p>
<p>Israel</p>
<p>Italy</p>
<p>Japan</p>
</div> 
<div class="fltlft add-pads">
<p>Latvia</p>
<p>Liechtenstein</p>
<p>Lithuania</p>
<p>Luxembourg</p>
<p>Malaysia</p>
<p>Malta</p>
<p>Mexico</p>
<p>Netherlands</p>
<p>New Zealand</p>
<p>Norway</p>
<p>Panama</p>
<p>Peru</p>
<p>Poland</p>
<p>Portugal</p>
<p>Puerto Rico</p>
<p>Romania</p>
</div> 
<div class="fltlft add-pads">
<p>Russian Federation</p>
<p>Serbia</p>
<p>Slovakia</p>
<p>Slovenia</p>
<p>South Africa</p>
<p>Spain</p>
<p>Sweden</p>
<p>Switzerland</p>
<p>Taiwan</p>
<p>Thailand</p>
<p>Turkey</p>
<p>Ukraine</p>
<p>United Kingdom</p>
<p>United States</p>
<p>Venezuela 58</p>
</div> 
<div class="clearfloat"></div>
</div>
</div> 
</div> <!-- Plan Box --> 

                                         
                   <!-- Left -->                   
                   
                   <!-- Right -->
<div class="clearfloat"></div>                   
<div class="sq-box">                   
<div class="fltlft"><img src="<?php echo SITE_URL; ?>images/reach-world.jpg" width="482" height="390" alt="Global"></div>

<div class="col fltrt">
<h2>Discover the future of telephone communications with Online Numbers. Be reached anywhere any time.</h2>
                        <div class="feature-list Mtop15">
                            <ul>
                               <li>24/7 access to your online control panel</li>
                               <li>Unlimited phone number routing updates</li>
                               <li>Cancel Anytime</li>
                               <li>View call records</li>
                               <li>View voicemail notification</li>
                            </ul>
                        </div>
</div>
<div class="clearfloat"></div>
</div>
                   
              </div>
              </div> <!-- end of tab1 -->
<p>&nbsp;</p>
<p>&nbsp;</p>
<div class="logo-wrapper">
    <div class="logo-wrap">
      <img src="<?php echo SITE_URL; ?>images/cloudlinux.png" width="252" height="119" alt="CloudLinux">
      <img src="<?php echo SITE_URL; ?>images/ms-windows.png" width="200" height="119" alt="MS Server">
      <img src="<?php echo SITE_URL; ?>images/w-server.png" width="466" height="118" alt="Windows Server 2012">
      <img src="<?php echo SITE_URL; ?>images/pp-plesk.png" width="253" height="118" alt="Plesk">
      <img src="<?php echo SITE_URL; ?>images/dell-tm.png" width="243" height="118" alt="Dell">
      <img src="<?php echo SITE_URL; ?>images/ms-exc.png" width="245" height="119" alt="MS Echange">
      <img src="<?php echo SITE_URL; ?>images/asterisk.png" width="205" height="119" alt="Asterisk">
      </div>
      <div class="clearfloat"></div>
</div>
		<script type="text/javascript"> 
  			$("#idtabs ul").idTabs(); 
		</script>
		</div>
         <div class="clearfloat"></div>
    </div>          
    </div><!-- Main wrapper starts here Closed -->
    <div class="clearfloat"></div>
    
    	<form method="post" name="frm-step1" id="frm-step1">
            <input type="hidden" value="" name="did_country_code" id="did_country_code">
            <input type="hidden" value="" name="did_city_code" id="did_city_code">
            <input type="hidden" value="" name="fwd_cntry" id="fwd_cntry">
            <input type="hidden" value="" name="forwarding_type" id="forwarding_type">
            <input type="hidden" value="" name="forwarding_number" id="forwarding_number">
            <input type="hidden" value="" name="free_number_id" id="free_number_id">
            <input type="hidden" value="" name="vnum_country" id="vnum_country">
            <input type="hidden" value="" name="vnum_city" id="vnum_city">
            <input type="hidden" value="" name="choosen_plan" id="choosen_plan">
            <input type="hidden" value="" name="plan_period" id="plan_period">
            <input type="hidden" value="" name="plan_minutes" id="plan_minutes">
            <input type="hidden" value="" name="plan_cost" id="plan_cost">
            <input type="hidden" value="" name="next_page" id="next_page">
       </form>