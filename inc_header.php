	<div id="top"></div>
    
<div id="header">
  
<div class="wide_960">
		
			<div class="members_sign_in">
			<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){ ?>
                <p><strong>Hi <?php echo $_SESSION['user_name']; ?>&nbsp;<a href="dashboard/index.php"><font color="black">My Account</font></a>
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="dashboard/logout.php">Logout</a></strong></p>
            <?php }else{ ?>
				<p><strong>Members Sign In</strong></p>
				
				<form class="prefill" action="dashboard/login.php" method="post">
					<label for="email"><input id="email" title="Enter Your Email" name="email" type="text" class="email" /> </label>
					
					<label for="password"><input name="password" type="password" class="password" title="Enter Password" id="password" /></label> <input name="submit" type="submit" class="submit" value="" />
					<br /><br />
					<?php if(isset($_GET['error']) && $_GET['error']=='failed' ) { ?> <div style='color:red;'>Member details incorrect,try again.</div><?php } ?>
					<a href="sticky_reset.php">Forgot Password</a>
					<input type="hidden" name="site_country" value="UK" />
					<input type="hidden" name="front_page_login" value="2926354" id="front_page_login">
				</form>
			<?php } //End of else part of no login session?>	
      		</div>
		
			<a href="/"><img src="images/logo.png" width="227" height="104" alt="Sticky Number" /></a>
		
		</div>
		
	</div>

	<div id="nav">
	
		<div class="wide_960">
	
			<ul>
				<li id="btn_home"><a href="/">Home</a></li>
				<li id="btn_join_free"><a href="join_free.php">Join FREE</a></li>
				<li id="btn_features"><a href="features.php">Features</a></li>
                <li id="btn_features"><a href="voicemail.php">Voicemail</a></li>
				<li id="btn_faqs"><a href="faqs.php">FAQ's</a></li>
				<li id="btn_why_choose_us"><a href="why_choose_us.php">Why Choose Us</a></li>
				<li id="btn_contact_us"><a href="contact_us.php">Contact Us</a></li>
			</ul>

		</div>

	</div>