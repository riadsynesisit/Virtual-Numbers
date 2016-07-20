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
        <?php if(SITE_COUNTRY == "AU"){?>   
        <title>Virtual Phone Number Australia, Virtual Phone Numbers</title> 
	        <?php if($this_page=="aboutus"){?>
	        <meta name="description" content="Sticky Number connect people together worldwide. We are a Australia number supplier with international forwarding and instant account activation. Our Australia servers provide free VOIP forwards to phone numbers across the globe."/>
	        <?php }elseif($this_page=="features_new"){?>
	        <meta name="description" content="Sticky Nnumber offers you to protect your identity online by using virtual Australia numbers or online number and making overseas business enquiries without displaying your real phone number."/>
	        <?php }else{?>
	        <meta name="description" content="Sticky Number provides Australian virtual phone numbers. International virtual phone number forwarding to landline and mobile telephones. Over 1200 cities."/>
	        <?php } ?>
	    <?php }elseif(SITE_COUNTRY == "UK"){ ?>
	    <title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title> 
	        <?php if($this_page=="aboutus"){?>
	        <meta name="description" content="Sticky Number connect people together worldwide. We are a United Kingdom number supplier with international forwarding and instant account activation. Our United Kingdom servers provide free VOIP forwards to phone numbers across the globe."/>
	        <?php }elseif($this_page=="features_new"){?>
	        <meta name="description" content="Sticky Nnumber offers you to protect your identity online by using virtual United Kingdom numbers or online number and making overseas business enquiries without displaying your real phone number."/>
	        <?php }else{?>
	        <meta name="description" content="Sticky Number is a leading company provides United Kingdom numbers, United Kingdom Virtual phone numbers with guarantee of best performance and reliability. For more information visit us online!"/>
	        <?php } ?>
		<?php }else{ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Number </title>
			<?php if($this_page=="aboutus"){?>
			<meta name="description" content="Sticky Number connect people together worldwide. We are a USA number supplier with international forwarding and instant account activation. Our USA servers provide free VOIP forwards to phone numbers across the globe."/>
			<?php }elseif($this_page=="features_new"){?>
			<meta name="description" content="Sticky Nnumber offers you to protect your identity online by using virtual USA numbers or online number and making overseas business enquiries without displaying your real phone number."/>
			<?php }else{?>
        	<meta name="description" content="<?php echo SITE_DOMAIN; ?> is a leading company provides <?php echo COUNTRY_DEMONYM; ?> number, <?php echo COUNTRY_DEMONYM; ?> Virtual phone numbers with guarantee of best performance and reliability. For more information visit us online!"/>
        	<?php } ?>
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
        <script src="<?php echo SITE_URL; ?>js/unslider.js"></script>
        
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
	    