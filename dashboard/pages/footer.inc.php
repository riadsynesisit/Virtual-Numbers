<?php
function getFooterPage(){
	
	$copywrite_start = 2010;
	$copywrite_now = date('Y');
	$copy_stamp = "";
	if($copywrite_now == $copywrite_start){
		$copy_stamp = $copywrite_start;
	}else{
		$copy_stamp = $copywrite_start." - ".$copywrite_now;
	}
	
	$page_html = '';
	
	$page_html .= '
        	<footer>
				<div class="container">
                   <div class="footer-last-fine"><span class="fine-print">&copy; 2014 Sticky Number - </span> <span>'.COUNTRY_DEMONYM.' Virtual Number</span> <span class="fine-print"> All rights reserved - Sticky Number&reg;</span> <span>| <a href="terms/" class="termslinks">Terms of Service</a> &amp; <a href="privacy-policy/" class="termslinks">Privacy Policy</a></span></div>
                   <div class="footer-social">
                       <div class="footer-social-items text-right"><img src="../images/facebook.png"></div>
                       <div class="footer-social-items"><img src="../images/googleplus.png"></div>
                       <div class="footer-social-items"><img src="../images/linkedin.png"></div>
                       <div class="footer-social-items"><img src="../images/twitter.png"></div>
                   </div>
              </div>
			</footer>   
        ';

	if(!isset($_SESSION['user_login']))
	{
	$page_html.='<script type="text/javascript">
		var f1 = new LiveValidation("sign_up_name", {onlyOnSubmit: true } );
		f1.add( Validate.Presence );
		var f2 = new LiveValidation("sign_up_email", {onlyOnSubmit: true } );
		f2.add( Validate.Email );
		f2.add( Validate.Presence );
		var f3 = new LiveValidation("sign_up_password", {onlyOnSubmit: true } );
		f3.add( Validate.Length, { minimum: 6 } );
		f3.add( Validate.Presence );
	</script>';
    }
$page_html.='<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(["_setAccount", "UA-19817890-2"]);
  _gaq.push(["_trackPageview"]);

  (function() {
    var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
    ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	
	';
	$page_html .="</body>\n";

$page_html .="\n";

$page_html .="</html>\n";
	return $page_html;
}
?>
