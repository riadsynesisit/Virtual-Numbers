<?php 
	header("Location:index.php"); // redirect, not required this page...09th April 2015
	exit;
    include('inc_head_tags.php');
    
    //session_start();//Use sessions
    require("./dashboard/includes/config.inc.php");
    require("./dashboard/includes/db.inc.php");
    
    $uniquecode =   mysql_real_escape_string(trim($_GET['id']));
    
    if(!empty($uniquecode))
    {
        $chkQry =   mysql_query("SELECT id FROM user_logins WHERE uniquecode='$uniquecode' AND verify_email='N'");
        if(mysql_num_rows($chkQry) > 0){
            mysql_query("UPDATE user_logins SET verify_email = 'Y' WHERE uniquecode='$uniquecode'");
            
            $succmsg    =   "Thank you, your email has been verified successfully.";
        }else{
        	$chkQry =   mysql_query("SELECT id FROM user_logins WHERE uniquecode='$uniquecode' AND verify_email='Y'");
        	if(mysql_num_rows($chkQry) > 0){
        		$verifiedmsg = "Our records indicate that your email was already verified. Thanks for trying again.";
        	}else{
            	$errmsg =   "Sorry, invalid link.";
        	}
        }
    }else{
        header("Location:index.php");
    }
?>
	<title>Virtual UK Number | UK Virtual Numbers, Free E-instant UK Number</title>
	<meta name="description" content="Sticky Number connect people together worldwide. We are a free UK number supplier with international forwarding and instant account activation. Our UK servers provide free VOIP forwards to phone numbers across the globe."/>
	<meta name="keywords" content="Free UK Number, UK Numbers For Free, virtual UK number, free uk number, uk virtual phone number, free phone numbers uk, free uk numbers, free uk phone number, uk virtual number, uk phone numbers, uk telephone numbers, free telephone numbers uk, uk numbers, free uk voip number, uk virtual numbers, free uk phone numbers, free uk virtual number, uk number, uk phone numbers free, get free uk number, uk free number, uk free phone numbers"/>
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
			
				<?php //include('inc_sign_up_form.php'); ?>
			
			</div>

			<div class="column4" style="margin-left: 172px">
                        <?php if(!empty($succmsg)){?><p style="font-family: arial;font-size: 13px;margin-bottom: 16px;text-align: center;font-weight: bold" >Thank you for verifying your email address.<br/><br/>To access your account please use login above.<br/>If you have any questions our support teams are at hand to help 24/7.</p><?php } ?>
                        <?php if(!empty($verifiedmsg)){?><p style="font-family: arial;font-size: 13px;margin-bottom: 16px;text-align: center;font-weight: bold" >Our records indicate that your email was previously verified. Thanks for trying again.<br/><br/>To access your account please use login above.<br/>If you have any questions our support teams are at hand to help 24/7.</p><?php } ?>
                        <?php if(!empty($errmsg)){?><p style="font-family: arial;font-size: 13px;margin-bottom: 16px;text-align: center;font-weight: bold">Sorry, invalid  link.</p><?php } ?>
			</div>
		
		</div>
	
	</div>

	<?php include('inc_footer.php'); ?>

</body>
</html>