<?php 

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/features.php" || $_SERVER["REQUEST_URI"]=="/features" )){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: features/");
		exit;
	}
	
}

include('inc_head_tags.php'); 

?>

</head>

<title>Virtual UK Numbers, Free 070 Numbers, Virtual UK Number For Free</title>
<meta name="description" content="Stickynumber.com offers you to protect your identity online by using a virtual UK numbers or virtual number and making overseas business enquiries without displaying your real phone number."/>
</head>

<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

	<?php include('inc_header.php'); ?>
	
	<div id="hero">
	
		<div class="wide_960">
		
			<img src="images/hero_02.jpg" width="960" height="200" />
		
		</div>
	
	</div>
	
	<div id="content">
	
		<div class="wide_960">
		
			<div class="column1">
			
				<?php include('inc_sign_up_form.php'); ?>
			
			</div>

			<div class="column4">
			
				<h2>Sticky <strong>Features</strong></h2>
                
                <div class="topsocial">
                
                    <div class="topsocial-items">
                    <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://twitter.com/stickynumber" data-via="stickynumber" data-count="none">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                    </div>

                   <div class="topsocial-items">
                   <div class="g-plusone" data-size="medium"></div>                  
                   <script type="text/javascript">
					  (function() {
						var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
						po.src = 'https://apis.google.com/js/plusone.js';
						var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
                    </div>
                    
                    <div class="topsocial-items">
                    <div class="fb-like" data-href="http://www.facebook.com/stickynumber" data-send="false" data-layout="button_count" data-width="75" data-show-faces="false"></div>
                    </div>

                </div><br/><br/>
				
				<p>Protect your identity online by using a <strong>virtual UK number</strong> while advertising an ad online, in a newspaper, signing up to a website, selling a car, or making overseas business enquiries while maintaining a UK presence without displaying your real phone number. Furthermore to protecting your identity will not divulge any personal identifiable information with other company’s or publically. Below is a list of features we include in our UK numbering system.</p>
				<ul>
				  <li>Registration Completely FREE</li>
					<li>24/7 Server Monitoring</li>
					<li>Complete Easy Management Tools</li>
					<li>24/7 access to your <strong>online control panel</strong></li>
					<li>Unlimited virtual <strong>UK number routing</strong></li>
					<li>Availability to choose from a selection of numbers</li>
					<li>Zero rental costs, no contract or minimum subscription</li>
					<li><strong>View call records</strong></li>
              </ul>
		  </div>
		
		</div>
	
	</div>

	<?php include('inc_footer.php'); ?>

</body>
</html>