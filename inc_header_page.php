<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    
	<!--title>Virtual Phone Number Australia, Virtual Phone Numbers</title>
	<meta name="description" content="Sticky Number provides Australian virtual phone numbers. International virtual phone number forwarding to landline and mobile telephones. Over 1200 cities."-->
	<!--
	<?php if(SITE_COUNTRY == "AU"){?> 
        <title>Virtual Phone Number Australia, Virtual Phone Numbers</title>   
        <meta name="description" content="Sticky Number provides Australian virtual phone numbers. International virtual phone number forwarding to landline and mobile telephones. Over 1200 cities."/>
		<?php }elseif(SITE_COUNTRY == "UK"){ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title>
		<meta name="description" content="Sticky Number is a leading company provides United Kingdom numbers, United Kingdom Virtual phone numbers with guarantee of best performance and reliability. For more information visit us online!"/>
        <?php }else{ ?>
		<title><?php echo COUNTRY_DEMONYM; ?> Number, <?php echo COUNTRY_DEMONYM; ?> Virtual Phone Number | Sticky Numbers <?php echo COUNTRY_NAME; ?> </title>
        <meta name="description" content="Sticky Number is a leading company provides <?php echo COUNTRY_DEMONYM; ?> number, <?php echo COUNTRY_DEMONYM; ?> Virtual phone numbers with guarantee of best performance and reliability. For more information visit us online!"/>
		<?php } ?>
	-->
	
	<?php 
		$server_uri = $_SERVER['REQUEST_URI'];
		if(strstr($server_uri, 'pricing')){
			$title_html = '<title>Virtual Phone Number '.COUNTRY_DEMONYM.' | Virtual Number Pricing</title>
	<metaname="description" content="Sign Up to Sticky Number services. Providing virtual numbers from countries around the world. Our phone numbers allow you access to our members area and its calling features."/>' ;

		}else if(strstr($server_uri, 'features')){
			$title_html = '<title>Features | Sticky Number Calling Features</title>
	<metaname="description" content="Sticky Number offers you to protect your identity online by using virtual '.COUNTRY_DEMONYM . ' numbers or online number and making overseas business enquiries without displaying your real phone number."/>';

		}else if(strstr($server_uri, 'how')){
			$title_html = '<title>How Virtual Numbers Work, How To Use Virtual Numbers</title>
	<metaname="description" content="Step by step guide on how to sign-up and setup your virtual number. Providing screen shots with each number setup process."/>';

		}else if(strstr($server_uri, 'faq')){
			$title_html = '<title>FAQ About Sticky Number Services | How To Questions</title>
	<meta name="description" content="Our frequently asked questions page. Answering all your virtual number questions. For further information about virtual phone numbers contact us by email."/>';

		}else if(strstr($server_uri, 'about')){
			$title_html = '<title>About Sticky Number | Company History</title>
	<meta name="description" content="Sticky Number connect people together worldwide. We are a number supplier with international forwarding and instant account activation. Our servers provide VOIP forwarding to phone numbers across the globe."/>';

		}else if(strstr($server_uri, 'contact')){
			$title_html = '<title>Contact Us | Customer Support</title>
	<meta name="description" content="Our support representatives are ready to take your enquiry, our aims are to provide quality service and products."/>';

		}else if(strstr($server_uri, 'feedback')){
			$title_html = '<title>Feedback Form, Have Your Input, Provide Suggestions</title>
	<meta name="description" content="We want to listen to your suggestions on features and service improvements so we can support your business better."/>';

		}else{
			$title_html = '<title>'. COUNTRY_DEMONYM . ' Virtual Number, Virtual Phone Number | Sticky Number</title>
	<metaname="description" content="Sticky Number is a leading company providing virtual numbers. Our virtual phone numbers come with high call quality and reliability. For more information visit us online!"/>';

		}
		
		echo $title_html;
	?>
	
	<meta name="keywords" content="Virtual Phone Number Australia, Virtual Phone Numbers UK, USA Virtual Numbers"> 

	
	<link rel="icon" href="<?php echo SITE_URL; ?>favicon.ico" type="image/x-icon" />
	
    <!-- Bootstrap -->
    <link href="<?php echo SITE_URL;?>css/bootstrap/bootstrap.css" rel="stylesheet">
    
    <!-- Custom Styling -->
    <link href="<?php echo SITE_URL;?>css/additional-styles.css" rel="stylesheet">
    
    <!--link href="<?php echo SITE_URL;?>css/font-awesome-4.0.3/css/font-awesome.css" rel="stylesheet"-->
    <link href="<?php echo SITE_URL;?>css/font-awesome/css/font-awesome.css" rel="stylesheet">
    
    <!-- Google Font -->
	<link href="<?php echo SITE_URL;?>css/google-fonts.css" rel="stylesheet">
    <!--link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700' rel='stylesheet' type='text/css'-->
  	
	<!-- OLD Styles >
	
	<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/core.css"/>
    <link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/misc.css"/>
    
    <link type="text/css" rel="stylesheet" href="<?php echo SITE_URL; ?>skins/minimal/minimal.css" />
		
	<!-- OLD Styles -->
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	
    <script src="<?php echo SITE_URL;?>js/jquery-1.11.3.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo SITE_URL;?>js/bootstrap.min.js"></script>
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  