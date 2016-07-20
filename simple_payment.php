<!doctype html>
<?php
/*
if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=privacy"  || $_SERVER["REQUEST_URI"]=="/privacy")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: privacy-policy/");
		exit;
	}
	
}else if($_SERVER["HTTP_HOST"]!="localhost"){
	include 'inc_common_header.php';
}*/
?>

<?php //include 'inc_header_page.php'; ?>

<?php require "common_header.php";

if(isset($_GET['id'])){?>
        
<script src="<?php echo SIMPLEPAY_WIDGET_URL ; ?>v1/paymentWidgets.js?checkoutId=<?php echo $_GET['id']; ?>"></script>
	
<?php } ?>
  </head>

  <body>
	
	<?php //include 'login_top_page.php';?>
  
    <?php //include 'top_nav_page.php';?>

    <!-- Banner -->
    <div class="wrapper">	         
    	<div class="container"><img src="images/SF-pricing.jpg" width="1024" height="201" alt="Account Details"></div>
    </div>
    <!-- Banner Closed -->
	
    <?php //echo '<pre>';print_r($_SESSION); echo '</pre>';?>
    <div class="wrapper">
		<div class="container">
		<div class="stepNav-wrap">
			<ul id="stepNav" class="fourStep">
				<li class="done"><a title=""><em>Step 1: Select Plan</em></a></li>
				<li class="lastDone"><a title=""><em>Step 2: Create Account</em></a></li>
				<li class="current"><a title=""><em>Step 3: Make Payment</em></a></li>
				<li class="stepNavNoBg"><a title=""><em>Step 4: Confirm/Finish</em></a></li>
			</ul>
			<div class="clearfloat">&nbsp;</div>
		</div>
		
		<div class="payment-p-wrap Mtop40">
                <h2>Shopping Cart</h2>
                <div class="row Mtop15">
                <div class="payment-label-2">Plan</div>
               <div class="payment-value txt-b"><?php if(isset($_SESSION["post_values"]["personal_or_business"]) && trim($_SESSION["post_values"]["personal_or_business"])=="B"){ echo "Business"; }else{ echo "Personal";} ?></div>
               </div>
                <div class="row Mtop15">
                <div class="payment-label-2">Product</div>
               		<div class="payment-value txt-b">
               			<?php 
               				echo "Online Number - ".trim($_SESSION["post_values"]["vnum_country"])." ".trim($_SESSION["post_values"]["vnum_city"]);
               			?>
               		</div>
               </div>
                <div class="row Mtop15">
                <div class="payment-label-2">Minutes Included</div>
               <div class="payment-value txt-b"><?php echo $_SESSION["post_values"]["plan_minutes"]; ?></div>
               </div>  
                <div class="row Mtop15">
                <div class="payment-label-2">Plan Length</div>
               <div class="payment-value txt-b"><?php if(isset($_SESSION["post_values"]["plan_period"]) && trim($_SESSION["post_values"]["plan_period"])=="Y"){ echo "Yearly"; }else{ echo "Monthly";} ?></div>
               </div>  
                <div class="row Mtop15">
                <div class="payment-label-2">Plan Cost</div>
               <div class="payment-value txt-b"><?php echo CURRENCY_SYMBOL.$_SESSION["post_values"]["plan_cost"]; ?></div>
               </div>  
                <div class="row Mtop15">
                <div class="payment-label-2">Activation Fee</div>
               <div class="payment-value txt-b"><?php echo CURRENCY_SYMBOL; ?>00</div>
               </div>
                <div class="row Mtop15 payment-t">
                <div class="payment-label-2 font18">Total Cost</div>
               <div class="payment-value txt-b font18"><?php echo CURRENCY_SYMBOL." ".$_SESSION["post_values"]["plan_cost"]." ".SITE_CURRENCY; ?>  per <?php if(trim($_SESSION["post_values"]["plan_period"])=="M"){echo "Month";}elseif(trim($_SESSION["post_values"]["plan_period"])=="Y"){echo "Year";}?></div>
               </div>                                                                                            
               <div class="clearfloat"></div>
        </div>
		   
		<h1>Make Payment</h1>
	
		<form action="<?php echo SITE_URL;?>?page=payment_confirm&cart_id=<?php echo $_GET['cart_id'];?>" class="paymentWidgets">
		VISA MASTER
		</form>
	</div>
	

    </div><!-- /.container -->    
    
  <?php //include 'inc_free_uk_page.php' ;?>
  <?php //include 'inc_footer_page.php' ;?>

    
  </body>
</html>
