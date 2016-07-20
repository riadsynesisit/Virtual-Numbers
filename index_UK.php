<?php 
    include('inc_head_tags.php'); 
?>
	<title>Free UK Number, Free UK Virtual Phone Number | Sticky Numbers</title>
	<meta name="description" content="Stickynumber.com is a leading company provides free UK number, Free UK Virtual phone numbers with guarantee of best performance and reliability our free range disposal. For more information visit us online!"/>
		<script src="./scripts/jquery.js" type="text/javascript"></script>
	<script>
	$(document).ready(function(){
		$("#submit_form_news").click(function(){      
			var emailid = document.getElementById("email_news").value;
			var submit_url = "./mcapi/inc/store-address.php?ajax=1&email="+emailid;
			  $.ajax({url:submit_url, success:function(result){
			    alert(result);
			  }});
			});
	});
	</script>

</head>

<body>

	<?php include('inc_header.php'); ?>
	
	<div id="hero">
	
		<div class="wide_960">
		
			<img src="images/hero_01.jpg" width="960" height="364" />
		
		</div>
	
	</div>
	
	<div id="content">
	
		<div class="wide_960">
		
			<div class="column1">
			
				<?php include('inc_sign_up_form.php'); ?>
			
			</div>

			<div class="column2">
			
				<h2><strong>Why</strong> Choose Us</h2>
				
				<p>No-Fees. Sticky Number is a <b><a href="/">free UK number</a></b> supplier, we guaranteed the best performance & reliability at our disposal. Our client base expands across the globe, reaching 100,000's of users daily. All <strong>070 free UK numbers</strong> come with international forwarding.<br><br>

				1. <strong>Signup</strong><br>
				2. <strong>Choose free UK number</strong><br>
				3. <strong>Divert Phone Number Free</strong></p>
				
				<ul>
					<li>Registration Completely FREE</li>
					<li>24/7 Server Monitoring</li>
					<li>Complete Easy Management Tools</li>
					<li>Accredited UK Phone Number Supplier</li>
					<li>Instant Account Activation</li>
					<li>Worldwide free UK Phone Number Routing</li>
				</ul>
				
			</div>
			
			<div class="column3">
			
				<img src="images/247_support.png" width="221" height="91" />
			
				<h3>What's <strong>New</strong></h3>
				
				<div class="news_article">
					<p><strong>New 070 Range Available</strong></p>
					<p>Our engineers have integrated the new 0702 UK phone number range into the internal PST UK network.</p>
				</div>
				
				<div class="news_article">
					<p><strong>Free International Divert 24/7</strong></p>
					<p>Now all 070 UK phone numbers can be routed to thousands of destinations around the world.</p>
				</div>
				
				<div class="news_article">
					<p><strong>Worldwide Instant Activation</strong></p>
					<p>All accounts are now activated in real time giving you an instant free uk number.</p>
				</div>
				
			</div>
		
		</div>
	
	</div>

	<?php include('inc_footer.php'); ?>
	
</body>
</html>