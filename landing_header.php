<?php 
	if(isset($_SERVER["HTTP_REFERER"]) && substr($_SERVER["HTTP_HOST"],-19)!="stickynumber.com.au" && trim($_SERVER["HTTP_REFERER"])!="" && strpos($_SERVER["HTTP_REFERER"],"google.com.au")!==false && strpos($_SERVER["HTTP_REFERER"],"rd=no")===false){
		if(strpos($_SERVER['QUERY_STRING'],"rd=no")===false){
			header("Location: https://www.stickynumber.com.au");
			exit;
		}
	}
	if(isset($_SERVER["HTTP_REFERER"]) && substr($_SERVER["HTTP_HOST"],-15)!="stickynumber.uk" && trim($_SERVER["HTTP_REFERER"])!="" && (strpos($_SERVER["HTTP_REFERER"],"google.co.uk")!==false || strpos($_SERVER["HTTP_REFERER"],"google.uk")!==false) && strpos($_SERVER["HTTP_REFERER"],"rd=no")===false){
		if(strpos($_SERVER['QUERY_STRING'],"rd=no")===false){
			header("Location: https://www.stickynumber.uk");
			exit;
		}
	}
	/*if(SITE_COUNTRY == "AU"){
		header("Location: https://www.stickynumber.com.au/pricing/");
		exit;
	}else
	if(SITE_COUNTRY == "UK" && $_SERVER['HTTP_HOST'] != "localhost" ){
		header("Location: https://www.stickynumber.uk/pricing/");
		exit;
	}*/
	
	if(SITE_COUNTRY=="UK"){ 
		$logo_revision = "-uk"; 
	}elseif(SITE_COUNTRY=="AU"){
		$logo_revision = "";
	}else{
		$logo_revision = "-r1";
	}
?>
<html lang="en">
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="rating" content="general" />
        <!--meta name="Language" content="en" /-->
        <meta name="robots" content="all" />
        <meta name="robots" content="index,follow" />
        <!--meta name="re-visit" content="7 days" /-->
        <!--meta name="distribution" content="global" /-->
        <?php if(SITE_COUNTRY == "AU"){
			//header("Location: https://www.stickynumber.com.au/pricing/");
			//exit;
			?>   
        <title>Virtual Phone Number Australia, Virtual Phone Numbers</title> 
	        <?php if($this_page=="aboutus"){?>
	        <meta name="description" content="Sticky Number connect people together worldwide. We are a Australia number supplier with international forwarding and instant account activation. Our Australia servers provide free VOIP forwards to phone numbers across the globe."/>
	        <?php }elseif($this_page=="features_new"){?>
	        <meta name="description" content="Sticky Nnumber offers you to protect your identity online by using virtual Australia numbers or online number and making overseas business enquiries without displaying your real phone number."/>
	        <?php }else{?>
	        <meta name="description" content="Sticky Number provides Australian virtual phone numbers. International virtual phone number forwarding to landline and mobile telephones. Over 1200 cities."/>
	        <?php } ?>
	    <?php }elseif(SITE_COUNTRY == "UK"){ ?>
	    <!--title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title--> 
			<title>Free UK Number | Instant Activation, Sticky Number</title>
	        <meta name="description" content="Sticky Number provides 070 UK phone numbers. We activate your number instantly with worldwide phone forwarding."/>
		<?php }else{ ?>
		<!--title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Number </title-->
			<title>Free UK Number | Instant Activation, Sticky Number</title>
	        <meta name="description" content="Sticky Number provides 070 UK phone numbers. We activate your number instantly with worldwide phone forwarding."/>
		<?php } ?>
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->		
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->
        
        <!-- CSS STYLING -->        
        <!--link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/core.css"/-->
		
        <!--link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/misc.css"/-->
        <link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" type="image/x-icon" />
		<link type="text/css" rel="stylesheet" href="<?php echo SITE_URL; ?>skins/minimal/minimal.css" />
		<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/landing-style.css"/>
		<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/bootstrap.css"/>
		<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/animate.css"/>
		<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/google-api.css"/>
		<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/jquery.realperson.css"/>
		
        <!-- JS -->
        <script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery-1.9.1.min.js"></script>
        <script src="<?php echo SITE_URL; ?>js/jquery.bootstrap.min.js"></script>
        <!--script src="<?php echo SITE_URL; ?>js/scrolltopcontrol.js"></script>
        <script src="<?php echo SITE_URL; ?>js/jquery-ui-1.10.3.custom.min.js"></script-->
        
		<script src="<?php echo SITE_URL; ?>js/jquery.plugin.min.js"></script>
        <script src="<?php echo SITE_URL; ?>js/jquery.realperson.js"></script>
		
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
        
		<script>
		
			$(document).ready(function(){
				
				checkDidCountToady('<?php echo $_SERVER['REMOTE_ADDR'];?>');
				
				$('#fwd_country').change(function() {
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
                        
                        if ($("#fwd_country option[value='" + option_value + "']").length > 0) {
                            $('#fwd_country').val( option_value )
                        }  
                        
                });
				
				$('.btnActivateFree').click(function(){
					
					if( validate_fields() == true ){
						
						// SET HIDDEN FIELDS VALUES 
						//console.log($('#udids').val());
						$('#free_number_fwd_cntry').val( $('#fwd_country').val() );
						$('#free_number_forwarding_number').val( $('#number').val() ) ;
						$('#free_number_id').val( $('#udids').val() ) ;
						// HIDDEN FIELDS SET
						
						jQuery.ajaxSetup({async:false});
                        //Create account ajax call
                    	//var posturl = '<?php echo SITE_URL; ?>ajax/ajax_create_au_acct.php';
						
                    	var fname = $('#txtName').val() ;
                    	var lname = ' ';
                    	var email = $.trim($('#txtEmail').val());
                    	var pword = $('#pword').val();
                    	var cname = '';
                    	var country = '';
                    	var address = '';
                    	var address2 = '';
                    	var postal_code ='';
                    	var city = '';
                    	var state = '';
                    	var site_country = "<?php echo SITE_COUNTRY; ?>";
                    	var postdata = {'fname' : fname, 
                				'lname' : lname, 
                				'email' : email, 
                				'pword' : pword, 
                				'new_or_current' : 'new',
								'user_type' : 'P',
								'company' : cname,
								'user_country' : country,
								'address1' : address,
								'address2' : address2,
								'postal_code' : postal_code,
								'user_city' : city, 
								'user_state': state, 
								'site_country': site_country,
								'free_number' : 'Y'
                				};
        				$.ajax({type:"POST",
            					url: "<?php echo SITE_URL; ?>ajax/ajax_create_au_acct.php",
            					data: postdata, 
            					dataType:"text", 
            					async: false,
            					success: function(response){
                            		var response_array = response.split(",");
        							if(response_array[0]=="OK"){
        								//code to go to the next page
        								blnNewAccountSuccess = true;
        							}else{
										blnNewAccountSuccess = false;
        								alert(response);
        								return;
        							}
                            	},
                            	error: function(jqxhr, textStatus, errorThrown){
									alert("Error in ajax_create_au_acct.php: Status: "+textStatus+", Error Thrown: "+errorThrown);
                            	},
                            	complete: function(jqxhr, textStatus){
									//alert("Completed :"+textStatus);
									if(blnNewAccountSuccess==true){
                            			$("#frm-free-num-070").submit();
									}else{
										return;
									}
                            	}
                    	});
                    }else{
						return false;
					}
				});
			});
			
			function validate_fields(){
				
				if($.trim( $('#udids').val()) == ""){
					alert('Please Select a free number');
					$('#udids').focus();
					
					return false;
				}
				
				if($.trim($('#fwd_country').val()) == ""){
					alert("Please select your country");
					$('#fwd_country').focus();
					return false;
				}
								
				if($('#number').val().length <= 10){
					alert("Please enter call forwarding number with area code (min 10 digits)");
					$('#number').focus();
					//The following three lines set cursor at end of existing input value
					var tmpstr = $('#number').val();//Put val in a temp variable
                    $('#number').val('');//Blank out the input
                    $('#number').val(tmpstr);//Refill using temp variable value
					return false;
				}
				
				if($.trim($('#txtName').val()) == ""){
					alert("Please Enter your name");
					$('#txtName').focus();
					return false;
				}
				if(validateEmail($('#txtEmail').val())==false){
					alert("Please enter your valid email id");
					$('#txtEmail').focus();
					return false;
				}
				if($('#pword').val().length < 6){
					alert("Please enter a password (min 6 characters)");
					$('#pword').focus();
					return false;
				}
				if($('#captcha').is(':visible')){
					var hasValdef = $('#real_person').realperson('getHash');
					
					var user_input = $('input[name="real_person"]').val();
					
					var user_input_hash = returnHash(user_input);
					if(user_input_hash == hasValdef){
						return true;
					}
					
					alert('Please fill up the captcha correctly!');
					$('input[name="real_person"]').val('').focus();
					return false;
				}
				
				return true;
			}
			
			function validateEmail(sEmail) {
			    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
				
			    if (filter.test(sEmail)) {
			        return true;
			    }
			    else {
			        return false;
			    }
			}
			
			function checkDidCountToady(ip_adrs){
				//console.log(ip_adrs);
				
				$.ajax({type:"POST",
						url: "<?php echo SITE_URL; ?>ajax/ajax_check_same_ip.php",
						data: {ip_address:ip_adrs}, 
						dataType:"text", 
						//async: false,
						success: function(response){
							//console.log(response);
							if(response > 0){
								$('#captcha').show();
								$('#real_person').realperson();
						
							}
						},
						error: function(jqxhr, textStatus, errorThrown){
							alert("Error in same ip check: Status: "+textStatus+", Error Thrown: "+errorThrown);
						},
						complete: function(jqxhr, textStatus){
							//alert("Completed :"+textStatus);
							
						}
				});
				
				
				
			}
			
			function returnHash(value) {
				var hash = 5381;
				for (var i = 0; i < value.length; i++) {
					hash = ((hash << 5) + hash) + value.charCodeAt(i);
				}
				return hash;
			}
			
		</script>

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
	    
	    <div class="wrapper">
	         <div class="container">
	              <div class="menucontain">
	                   <div class="logo"><a href="<?php echo SITE_URL; ?>index.php"><img alt="Main Logo" src="<?php echo SITE_URL; ?>images/main-logo<?php echo $logo_revision; ?>.png"/></a></div>
	                   <!--div id="top-nav">
	                   		<ul>
	                        	<li><a href="<?php echo SITE_URL; ?>index.php">Home</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>about-us/">About Us</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>how-to-get-a-phone-number/">How it Works</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>features/">Features</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>pricing/">Pricing</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>faq/">FAQ's</a></li>
	                        	<li><a href="<?php echo SITE_URL; ?>contact-us/">Contact Us</a>
	                        </ul>
	                        <div class="clearfloat"></div>
	                   </div-->
	              </div>
	         </div>
	    </div>
	    