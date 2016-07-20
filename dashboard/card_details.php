<?php
$wp_url = "";

if(isset($_POST["wp_url"]) && trim($_POST["wp_url"])!=""){
	$wp_url = trim($_POST["wp_url"]);
}

if($wp_url == ""){
	echo "Error: Required parameter wp_url not passed.";
	exit;
}

?>
<iframe scrolling="no" style="border: 0px none; height: 620px; width: 840px;" seamless="seamless" src="<?php echo $wp_url; ?>"></iframe>