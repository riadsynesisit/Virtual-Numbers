<?php

session_start();//Use sessions
require("./dashboard/includes/config.inc.php");
require("./dashboard/includes/db.inc.php");

$ip_address=$_SERVER["REMOTE_ADDR"];
	  $day_start = date('Y-m-d');
$query = "select count(*) as captcha_count from user_logins where user_created_on='$day_start' and ip_address='$ip_address' ";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$count=$row['captcha_count'];
		if($count>=1)
		{
		  $recaptchvar=true;
		
		}
		else
		{
		
		 $recaptchvar=false;
		}
?>
<div class="sign_up_form">
				
					<form id="form_sign_up" onSubmit="return form_check(this, contact_right_required_fields, contact_right_required_descriptions);" action="dashboard/setup.php" method="post">
			
						<h2><strong>Sign Up</strong> for FREE</h2>
						
						<div class="sign_up_form_wrapper">
						
							<p>Name</p>
							
							<input type="text" class="sign_up_field" name="realname" id="sign_up_name" title="Your name" value="<?php if(isset($_GET['realname'])){echo $_GET['realname'];} ?>"/>
							<input type="hidden" name="form_sign_up_ok" value="74285hda28" id="form_sign_up_ok">

							<p>Email</p>
							
							<input type="text" class="sign_up_field" name="email" id="sign_up_email" title="A valid email address" value="<?php if(isset($_GET['email'])){echo $_GET['email'];} ?>"/>
							
							<p>Password</p>
							
							<input type="password" class="sign_up_field" name="password" id="sign_up_password" title="A pasword with at least 6 characters" />
                            
                            
                          <p>Recommend a friend?</p>
                          <div class="yesorno">
                          
                          <span><input name="recmnd_frnd_option" id="recmnd_frnd_option"  type="radio" value="yes"  onclick="javascript:document.getElementById('recmnd_frnd_email_bx').style.display = '';" 
						  
						  <?php 
						  if(isset($_GET['recmnd_frnd_option']))
						  { 
						  		if($_GET['recmnd_frnd_option'] == 'yes')
								{
							?> 
                            checked="checked" 							
							<?php 
								}
						  } ?>/> Yes </span> 
                          
                          <span><input name="recmnd_frnd_option" id="recmnd_frnd_option" type="radio" value="no"  onclick="javascript:document.getElementById('recmnd_frnd_email_bx').style.display = 'none';" 
						  
						  <?php 
						  if(isset($_GET['recmnd_frnd_option']))
						  { 
						  		if($_GET['recmnd_frnd_option'] == 'no')
								{
						  ?> 
                          checked="checked" 
						  <?php 
								} 
								else 
								{
						
						  		}
						  } 
						  else
						  {
						  ?> 
                          checked="checked" 
						  <?php
						  }?>/> No</span></div>
                            
                            <div id="recmnd_frnd_email_bx" 
                             
							 <?php 
							  if(!isset($_GET['recmnd_frnd_option']))
							  { 
							  ?>                             
                            	style="display:none;"
                              <?php 
							  }
							  else 
							  {
								  if(isset($_GET['recmnd_frnd_option']))
							  	  {
									  if($_GET['recmnd_frnd_option'] == 'no')
									  {							  
								?> style="display:none;" <?php 
									  }
								  }
							  }?> >
                              
                            <p>Email</p>
                            <input type="text" class="sign_up_field" name="recmnd_frnd_email" id="recmnd_frnd_email" title="A recommended friend" value="<?php if(isset($_GET['recmnd_frnd_email'])){echo $_GET['recmnd_frnd_email'];} ?>"/>
                          </div>
                            
<?php 
	$_SESSION["recaptcha_validate"] = "false";
	if($recaptchvar==true)
	{
	$_SESSION["recaptcha_validate"] = "true";
							 	
?>                               

							   <div>  
        <script type="text/javascript"
   src="https://www.google.com/recaptcha/api/challenge?k=6Lffs8kSAAAAAFCr9Zi0lDzZJaNzQ0aS20n1KKDN">
</script>

<noscript>
   <iframe src="https://www.google.com/recaptcha/api/noscript?k=6Lffs8kSAAAAAFCr9Zi0lDzZJaNzQ0aS20n1KKDN"
       height="300" width="500" frameborder="0"></iframe><br>
   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
   </textarea>
   <input type="hidden" name="recaptcha_response_field"
       value="manual_challenge">
</noscript>
       
        
		  <style>
		  			#recaptcha_widget_div{width:220px; margin-left:-9px; margin-top:10px;}
                    #recaptcha_image_cell{width:220px;}
					#recaptcha_table{width:220px;}
					.recaptchatable, #recaptcha_area tr, #recaptcha_area td, #recaptcha_area th{ background:none;}
					#recaptcha_table td{ padding-bottom:20px;}
					#recaptcha_response_field{ width:50px;}
					.recaptcha_r4_c1{ width:100px;}
					.recaptcha_r1_c1{ width:220px; background:none;}
					.recaptcha_r4_c2{ width:0px;}
					.recaptcha_r3_c2{ height:20px;}
					#recaptcha_reload_btn{ margin-top:20px;}
					#recaptcha_area #recaptcha_table tr td .recaptcha_r2_c1{ width:0px;}
					#recaptcha_image{ width:227px; margin-bottom:10px;}
					#recaptcha_image img{ width:227px;}
					
                    </style>
		
        
        </div>
		<?php } ?>
        
        <div style=" color:#C00; font-weight:bold; margin-left:20px;"><?php if(isset($_GET['message'])){echo $_GET['message'];} ?></div>

							
						  <input type="submit" class="sign_up_submit" value="Done" name="submit_form" />
						<input type="hidden" name="site_country" value="UK" />
						</div>
				
					</form>
                    
				
				</div>