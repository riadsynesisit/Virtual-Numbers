<?php 

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]=="/sticky_reset.php"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: forgot-password/");
		exit;
	}
	
}

include('inc_head_tags.php'); 
session_start();//Use sessions
require("./dashboard/includes/config.inc.php");
require("./dashboard/includes/db.inc.php");
?>
<script src="./scripts/jquery.js" type="text/javascript"></script>
<script>

$(document).ready(function(){
	$("#submit_form3").click(function(){
		var emailid = document.getElementById('email_address').value;
		var recaptcha_challenge_field = document.getElementById('recaptcha_challenge_field').value;
		var recaptcha_response_field = document.getElementById('recaptcha_response_field').value;
		
		
		var reset_url = "./dashboard/reset.php?email_address="+emailid+"&recaptcha_challenge_field="+recaptcha_challenge_field+"&recaptcha_response_field="+recaptcha_response_field;
		  $.ajax({url:reset_url, success:function(result){
		    var site_url='<?php echo SERVER_WEB_NAME ;?>';
			switch(result){
		    case "1":
				alert("Your password was successfully reset. An email containing the new password was sent to you.");
			    break;
		    case "-1":
				alert("Missing email id parameter. Please enter your email address and try again.");
			    break;
		    case "-2":
				alert("Email address entered by you was NOT found in the database. Please make sure that the email entered is correct. Please try again.");
			    break;
		    case "-3":
				alert("ERROR: Failied to reset your password. Please try again or contact us if the problem persists.");
			    break;
			case "-4":
				alert("Account Terminated Contact support@stickynumber.com");
			    break;
				case "-5":
				alert("Account Suspended Contact support@stickynumber.com");
			    break;
				case "-6":
				alert("Captcha Code Is Incorrect. Please Try Again.");
				 window.location =site_url+'/sticky_reset.php?message=Security code is not correct!&email='+emailid;
				<?php //header('Location:'.SERVER_WEB_NAME.'/sticky_reset.php?message=Security code is not correct!&email='.$email);?>
			    break;
		    }
		  }});
		});
});
</script>
</head>

<body>

	<?php include('inc_header.php'); ?>
	
	<div id="hero">
	
		<div class="wide_960">
		
			<img src="images/hero_02.jpg" width="960" height="200" />
		
		</div>
	
	</div>
	
	<div id="content">
	
		<div class="wide_960">
			
			<div class="column1">
			
				<?php //include('inc_sign_up_form.php'); 
				
				$ip_address=$_SERVER["REMOTE_ADDR"];
	  $day_start = date('Y-m-d');
$query = "select count(*) as captcha_count from user_logins where password_reset_date='$day_start' and password_ip_address='$ip_address' ";
		
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$forgotcount=$row['captcha_count'];
		if($forgotcount>=1)
		{
		  $recaptchvarforgot=true;
		
		}
		else
		{
		
		 $recaptchvarforgot=false;
		}
				
				?>
			
			</div>

			<div class="column4">

				<form id="form_reset_pwd" action="dashboard/reset.rbx" method="post">
				
					<input type="hidden" name="sticky_reset_ok" value="hzf8465jd56" id="sticky_reset_ok">
					
					<h2>Sticky <strong>Reset</strong></h2>
					
					<p>Forgotten your password?</p>
					
					<p>To reset your password, type the full email address you use to sign in to your Sticky Number account.</p>
					
					<p><strong>Email Address</strong></p>
					
					<input type="text" class="sign_up_field" value="<?php if(isset($_GET['email'])) { echo $_GET['email']; }?>" name="email_address" id="email_address" title="A valid email address" />
					<?php 
	$_SESSION["recaptcha_validate_forgot"] = "false";
	if($recaptchvarforgot==true)
	{
	$_SESSION["recaptcha_validate_forgot"] = "true";
							 	
?>                               

							   <div>  
        <script type="text/javascript"
   src="https://www.google.com/recaptcha/api/challenge?k=6Lffs8kSAAAAAFCr9Zi0lDzZJaNzQ0aS20n1KKDN">
</script>

<noscript>
   <iframe src="https://www.google.com/recaptcha/api/noscript?k=6Lffs8kSAAAAAFCr9Zi0lDzZJaNzQ0aS20n1KKDN"
       height="300" width="500" frameborder="0"></iframe><br>
   <textarea id="recaptcha_challenge_field" name="recaptcha_challenge_field" rows="3" cols="40">
   </textarea>
   <input type="hidden" id="recaptcha_response_field" name="recaptcha_response_field"
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
		<?php } else { ?> 
		
		<input id="recaptcha_challenge_field" name="recaptcha_challenge_field" type="hidden" value=''>
   
   <input type="hidden" id="recaptcha_response_field" name="recaptcha_response_field"   value="">
		<?php } ?>
					<div style=" color:#C00; font-weight:bold; margin-left:20px;"><?php if(isset($_GET['message'])){echo $_GET['message'];} ?></div>
					<input type="button" class="buttonSend" value="Reset Password" name="submit_form3" id="submit_form3" />
				
				</form>
		
			</div>
		
		</div>
	
	</div>

	<?php include('inc_footer.php'); ?>

</body>
</html>