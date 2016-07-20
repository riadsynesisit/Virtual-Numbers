<?php 
	if(isset($_SERVER["HTTP_REFERER"]) && trim($_SERVER["HTTP_REFERER"])!="" && strpos($_SERVER["HTTP_REFERER"],"google.com.au")!==false && strpos($_SERVER["HTTP_REFERER"],"rd=no")===false){
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="rating" content="general" />
	<!--meta name="lan" content="en" /-->
	<meta name="robots" content="all" />
	<meta name="robots" content="index,follow" />
	<!--meta name="re-visit" content="7 days" /-->
	<!--meta name="distribution" content="global" /-->
	
	<link rel="SHORTCUT ICON" href="favicon.ico" /> 
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo SITE_URL; ?>skins/minimal/minimal.css" />
	
	<script src="scripts/prototype.js" type="text/javascript"></script>
	<script src="scripts/scriptaculous.js?load=effects" type="text/javascript"></script>
	<script src="scripts/site.js" type="text/javascript"></script>
	<script src="scripts/livevalidation.js" type="text/javascript"></script>
	<script type="text/javascript">document.observe("dom:loaded", function() { setup_request_forms(); });</script>
	<script src="scripts/form_check.js" type="text/javascript"></script>
	<script type="text/javascript">
		var contact_right_required_fields = Array("realname", "email", "password");
		var contact_right_required_descriptions = Array("Name", "Email", "password");
	</script>