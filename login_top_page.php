<?php 

	$forgotPassLink = true;
	$reqUri = $_SERVER["REQUEST_URI"]; 
	if(strstr( $reqUri, 'forgot') ){
		$forgotPassLink = false;
	}
?>
	<section class="topbar-login">
  <div class="container">
  	<div class="row">
  		<div class="col-md-7 mtop20 col-md-width-mail" >
			<div class="info-support">
				<img width="18" height="11" src="<?php echo SITE_URL;?>images/mail.png" alt="support mail" /> support@stickynumber.com.au
			</div>
        </div>
  		<div class="col-md-5 col-md-width-login">
		<?php if(isset($_SESSION['user_logins_id']) && trim($_SESSION['user_logins_id'])!=""){ ?>
	                  			Hi <?php echo $_SESSION['user_name']; ?>&nbsp;<a href="<?php echo SITE_URL; ?>dashboard/index.php">My Account</a>
                                		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo SITE_URL; ?>dashboard/logout.php">Logout</a>
	                  		<?php }else{ 
									if(isset($_GET['error'])) {
										if($_GET['error']=='faileds'){
											echo "<div style='color:red;'>Your account has been suspended. Please contact support.</div>";
										}else if($_GET['error']=='failedv'){
											echo "<div style='color:red;'>Please verify your email to login.</div>";
										}else if( $_GET['error'] == 'failedl' ){
											echo "<div style='color:red;'>Login Restricted. Please contact support.</div>";
										}else if( $_GET['error'] == 'failed' ){
											echo "<div style='color:red;'>Member details incorrect,try again.</div>";
										}
									}
								 ?>
		
	<form class="form-inline" name="toplogin" id="toplogin" action="<?php echo SITE_URL; ?>dashboard/login.php" method="post">
  <div class="form-group">
    <label class="sr-only" for="useremail">Email address</label>
    <input type="email" class="form-control" id="useremail" name="email" placeholder="Username">
  </div>
  <div class="form-group mtop17">
    <label class="sr-only" for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
    <div class="clearfix"></div>
    <span class="small"><a href="<?php echo SITE_URL; ?>forgot-password/">Forgot Password</a></span>
  </div>
	<div class="loginbtn-box">
		<input onclick="javascript:document.getElementById('toplogin').submit();" type="button" class="loginbtn" value="Login"/>
	</div>
								
		                       	<input type="hidden" name="site_country" value="<?php echo SITE_COUNTRY; ?>" />
                                <input type="hidden" name="front_page_login" value="2926354" id="front_page_login">
</form>
<?php } //End of else part of no login session?>
</div>
	</div>
  </div><!-- /.container -->
  </section>
  