<?php 

	$forgotPassLink = true;
	$reqUri = $_SERVER["REQUEST_URI"]; 
	if(strstr( $reqUri, 'forgot') ){
		$forgotPassLink = false;
	}
?>
	<div class="login-input">
		<label for="useremail">Username</label>
		<input title="Enter Username" id="useremail" name="email" type="text" class="input" placeholder="email" />
		
	</div>
	<div class="login-input">
		<label for="password">Password</label>
		<input title="Enter Password" id="password" name="password" type="password" class="input" placeholder="password" />
		
		<?php if($forgotPassLink){?><a href="<?php echo SITE_URL; ?>forgot-password/">Forgot Password</a><?php }?>
	</div>
	<div class="loginbtn-box">
		<input onclick="javascript:document.getElementById('toplogin').submit();" type="button" class="loginbtn" value="Login"/>
	</div>
