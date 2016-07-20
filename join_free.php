<?php 

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]=="/join_free.php"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: pricing/");
		exit;
	}
	
}

include('inc_head_tags.php'); 

?>
	<title>Virtual UK Number | UK Virtual Numbers, Free E-instant UK Number</title>
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
			
				<h2>Join <strong>FREE</strong></h2>
                
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
				
				<ul>
					<li>Registration Completely FREE</li>
					<li>24/7 Server Monitoring</li>
					<li>Complete Easy Management Tools</li>
					<li>Accredited UK Number Supplier</li>
					<li>Instant Account Activation</li>
					<li>Worldwide UK Number Routing</li>
				</ul>
				
				<p>Signing up for a <a href="/">free UK number</a> you can gain a UK presence without the cost of relocation and  flexibility of communicating between your growing business. Our 070 numbers can  be routed to any destination across the world with unlimited international  forwarding. Signup now for a <strong>free  e-instant uk number</strong>.</p>
				
			</div>
		
		</div>
	
	</div>

	<?php include('inc_footer.php'); ?>

</body>
</html>