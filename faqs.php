<?php 

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]=="/faqs.php"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: faq/");
		exit;
	}
	
}

include('inc_head_tags.php'); 

?>

</head>

<title>Free UK VOIP Number | Telephone Number England</title>
<meta name="description" content="Sticky Number connect people together worldwide. We are a free UK number supplier with international forwarding and instant account activation. Our UK servers provide free VOIP forwards to phone numbers across the globe."/>
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
			
				<h2>Frequently <strong>Asked Questions</strong></h2>
                
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
				
				<p>Below are FAQ's about our system and the services we provide. If you do not find the answer you are looking for, then  please do not hesitate to contact us 24/7 via email: <a href="mailto:info@stickynumber.com">info@stickynumber.com</a></p>
				<p><strong>How do I login?</strong></p>
		    <p>To  manage your virtual UK number, log in to your account:<br>
		      <br>
1. Go to the top of any page<br>
2. Type your email and password</p>
			  <p><strong>How  do I add another virtual UK number?</strong></p>
			  <p>To add another number, log in to your account:</p>
1. Log in to your Sticky Number account<br>
2. Click on &ldquo;Add New Number&rdquo;<br>
3. Enter your destination number<br>
4. Click &ldquo;Continue&rdquo;<br>
5. Done<br>
<p><strong>Why  do I have to call my number every 3 months?</strong></p>
			  <p>We  added a policy to remove out dated or unused phone numbers giving you the  latest in the UK numbering service. We also added an additional 7 day policy to  confirm your are wishing to use the 070 number, if either policy is not met the vitual UK number is released back into the UK telecommunications network.</p>
			  <p><strong>How do I sign up?</strong></p>
<p>Its  very simple to sign up, simply fill out the registration form situation on the left  side of our website. Your account will be activated instantly.</p>
			  <p><strong>Do I  have to pay any fee’s?</strong></p>
				<p>Sticky  Number do not charge any fee’s to use our service. We are here to push forward  the progression of international telecommunications and make every effort to  provide high quliy service that is free.</p>
				<p><strong>Do you have an example of the virtual number use?</strong></p>
				<p>You have the ability to provide a direct number that  forwards to yourself, once the business or personal transaction is complete you can easily re-route the number 24/7 through our control panel or delete the <a href="/">free UK number</a> altogether and add a brand new 070 number to your account.</p>
</div>
		
		</div>
	
	</div>

	<?php include('inc_footer.php'); ?>

</body>
</html>