<?php ?>
<script>
		  var country_iso = "";
		  var plan_period = 'M';//Hard coded until the annual payment is enabled in the system
		  var choosen_plan = 0;
		  var pay_amount = 0;
		  var purchase_type = 'new_did';
			var site_country = '<?php echo SITE_COUNTRY; ?>';
			
            $(document).ready(function(){
				
				//home page pricing data load initial
				
				if( site_country == 'AU'){
					$('#cheap_did_country').val(61);
					get_cheapest_rate_country(61);
				}else if( site_country == 'UK'){
					$('#cheap_did_country').val(44);
					get_cheapest_rate_country(44);
				}
				 
                $('#did_country').change(function() {
                    country_iso = $(this).val();

					$('#div-free-trials').hide();
                    
                    $('#city option').remove();
                    if (country_iso == '') {
                        $('#city').append('<option value="">Please select a city</option');
                        return true;                        
                    }
                    
                    $('#city').append('<option value="">Loading...</option');
                    $('#city').attr('disabled', 'disabled');
                    
                    var url = '<?php echo SITE_URL; ?>ajax/ajax_load_did_cities.php';
                
                    $.get(url, {'country_iso' : country_iso}, function(response){
                        var data = jQuery.parseJSON(response);  
                        $('#city').removeAttr('disabled');
                        $('#city option').remove();
                        $('#city').append('<option value="">Please select a city</option'); 
                        $(data).each(function(index, item) {
							
							city_selected = "";//reset
							if($.trim(item['city_id'])=="99999" && initial_load==true){
								initial_load = false;
								city_selected = " selected ";
							}
							
							$('#city').append('<option value="'+$.trim(item['city_id'])+'" '+city_selected+' >'+item['city_name']+' '+item['city_prefix']+' (<?php echo CURRENCY_SYMBOL; ?>'+item['sticky_monthly_plan1']+'/month)</option>');
							
                            //$('#city').append('<option value="'+$.trim(item['city_id'])+'">'+item['city_name']+' '+item['city_prefix']+' ($'+item['sticky_monthly_plan1']+'/mo)</option>');
                        });
                    });    
                    
                }); //on did_country change

                                		 /*$('#did_country').change();
                		 setTimeout(function(){$('select[name="city"]').find('option[value=""]').attr("selected",true);
	 						$('#city option[value=4]').attr('selected', true);},700);*/
                
				<?php 
                	if(trim($sel_did_country_code)!=""){
                ?>
                	initial_load = true;
       		 		$('#did_country').change();
                <?php		
                	}//end of if(trim($sel_did_country_code)!=""){
                ?> 
                
                $('#country').change(function() {
                	$('#number').focus();
                    $('#number').val( '+' + $(this).val())
                }); //on country change
                
                $('#number').keyup(function(event) {
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

                $('#btn-continue-step1').mousedown(function(event) {
                    if(validate_step1()==true){
                    	$('#fwd_cntry').val($('#country').val());
						$('#forwarding_number').val($('#number').val());
						$('#did_country_code').val($("#did_country").val());
						$('#did_city_code').val($("#city").val());
						$('#vnum_country').val($("#did_country option:selected").text());
						$('#vnum_city').val($("#city option:selected").text());
						if($("#city").val()==99999 || $("#city").val()=="99999"){
							<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id']) != ""){?>
							$('#next_page').val('free_number_070_AU.php');
							$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?free_number");
							<?php }else{?>
							$('#next_page').val('create_account.php');
							$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?create_account");
							<?php } ?>
						}else{
							$('#next_page').val('choose_plan.php');
							$('#frm-step1').attr("action","<?php echo SITE_URL?>index.php?choose_plan");
						}
						$('#frm-step1').submit();
                    }
                });
				
				
				/** CONTACT US PAGE SCRIPTS  **/
				
				$('#send_feedback').click(function(event){
                	if($.trim($('#sender_name').val()) == ""){
    					alert("Please enter your name.");
    					$('#sender_name').focus();
    					return false;
    				}
                	if($.trim($('#sender_email').val()) == ""){
    					alert("Please enter your email.");
    					$('#sender_email').focus();
    					return false;
    				}
                	if(validateEmail($('#sender_email').val())==false){
    					alert("Please enter your valid email id");
    					$('#sender_email').focus();
    					return false;
    				}
                	if($.trim($('#sender_message').val()) == ""){
    					alert("Please enter your message.");
    					$('#sender_message').focus();
    					return false;
    				}
                	jQuery.ajaxSetup({async:false});
                    //Send contact us email ajax call
                	var url = '<?php echo SITE_URL; ?>ajax/ajax_send_contact_email.php';
                	var sender_name = $.trim($('#sender_name').val());
                	var sender_email = $.trim($('#sender_email').val());
                	var sender_message = $.trim($('#sender_message').val());
                	$.post(url, {'sender_name' : sender_name, 'sender_email' : sender_email, 'sender_message' : sender_message}, function(response){
						if(response=="success"){
							alert("Your message was successfully sent. We will get back to you shortly.");
							$('#sender_name').val("");
							$('#sender_email').val("");
							$('#sender_message').val("");
						}else{
							alert(response);
							return;
						}
                	});
                });
				
				/***** HOME PAGE SCRIPTS *****/
				
				$('.banner-slide').unslider({
					dots: true               //  Display dot navigation            //  
				});
				$('.collapse').on('shown.bs.collapse', function(){
				$(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
				}).on('hidden.bs.collapse', function(){
				$(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
				});			
							
            	$("#submit_form_news").click(function(){
            		var emailid = document.getElementById("email_news").value;
            		var submit_url = "./mcapi/inc/store-address.php?ajax=1&email="+emailid;
            		  $.ajax({url:submit_url, success:function(result){
            		    alert(result);
            		  }});
            	});
                   
            });

			function validate_step1(){
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
				if($('#country').val() == ""){
					alert("Please select call forwarding country.");
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
					alert("Please enter call forwarding number with area code (min 10 digits)");
					$('#number').focus();
					//The following three lines set cursor at end of existing input value
					var tmpstr = $('#number').val();//Put val in a temp variable
                    $('#number').val('');//Blank out the input
                    $('#number').val(tmpstr);//Refill using temp variable value
					return false;
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
			}  // end of validate email
			
			function get_cheapest_rate_country(country_prefix){
				if(country_prefix){
					var country_text = $('#cheap_did_country :selected').text()
					console.log(country_text);
					var country_text = country_text.substr(0,country_text.lastIndexOf(' '));
					var url = '<?php echo SITE_URL; ?>ajax/ajax_get_cheapest_country_rate.php';
                
					$.ajaxSetup({
					   beforeSend: function(){$('.tr_set_country_data td').html('');
					   $('.tr_set_country_data').children(':eq(1)').html('<img src="<?php echo SITE_URL;?>images/ajax-loader.gif" />');}
					});

                    $.post(url, {'fwd_cntry' : country_prefix}, function(response){
                        //console.log(response);
						$('.tr_set_country_data').children(':eq(0)').html(country_text);
						$('.tr_set_country_data').children(':eq(1)').html('Mobile');
						$('.tr_set_country_data').children(':eq(2)').html(response);
                    });
				}
			}

    </script>