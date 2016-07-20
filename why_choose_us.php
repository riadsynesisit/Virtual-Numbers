<?php 

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"]=="/why_choose_us.php"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: features/");
		exit;
	}
	
}

include('inc_head_tags.php'); 

?>
	<title>Free UK Phone Number | Free Telephone Numbers UK</title>
	<meta name="description" content="Sticky Number connect people together worldwide. We are a free UK number supplier with international forwarding and instant account activation. Our UK servers provide free VOIP forwards to phone numbers across the globe." />
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
			
				<h2>Why<strong> Choose Us</strong></h2>
                
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
				
				<p>Sticky Number has provided <a href="/">free UK Number</a> to 100,000's of individuals and businesses worldwide since 2011. Sticky Number is a privately-owned UK company. Our offices and data centres are located throughout UK USA and NZ.</p>
				
				<p>We have more than 50 full-time developers, engineers, service and support specialists whose mission is to serve you. We've made our name by offering great service. But that doesn't mean that you get less. We want you to have it all – best technology and best service.</p>
				
				<h3>The Technology</h3>
				
				<p>We use only the latest, cutting edge hardware and software to give you superior performance with minimal downtime. Sticky Numbers robust, reliable network features Dell PowerEdge high speed servers, Cisco network hardware, and multiple fibre optic connections to the Internet backbone.</p>
				
				<p>We also use load balancing technology to distribute web traffic between multiple servers, further increasing availability, performance  and security.</p>
				
				<h3>The Speed</h3>
				
				<p>The Sticky Number gigabit (or better) network enables the fastest possible data transmission rates within our network. High capacity uplinks to both Tier 1 and Tier 2 bandwidth providers offers the best possible domestic and international performance.</p>
				
				<h3>Reliability</h3>
				
				<p>Our network is built with  reliability in mind, with redundancy at every possible point. We utilise the  latest in Cisco and Juniper routers and switches for our network and industry  leading DELL Blade servers for content and service provision. Running both Linux (Unix) and Microsoft Windows server software, reliability and performance is achieved through the use of industry standard load balancing technologies.</p>
				
				<h3>Service</h3>
				
				<p>Rely on strong, dedicated tech support and customer service from our experienced consultants. We're here to make web hosting and email hosting easy.</p>
				
		  </div>
		</div>
	
	</div>

	<?php include('inc_footer.php'); ?>

</body>
</html>