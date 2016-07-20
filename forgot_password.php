<?php 

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=forgot_password" || $_SERVER["REQUEST_URI"]=="/forgot-password")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: forgot-password/");
		exit;
	}
	
}
?>
<?php include 'inc_header_page.php'; ?>

        
         <!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
		  <script>
		  var country_iso = "";
		  var plan_period = 'M';//Hard coded until the annual payment is enabled in the system
		  var choosen_plan = 0;
		  var pay_amount = 0;
		  var purchase_type = 'new_did';

            
			function reset_password(){

				var email = $.trim(document.getElementById("email_reset").value);
				
				if(email == ""){
					alert("Please enter your email id");
					$('#email_reset').focus();
					return false;
				}

				if(validateEmail(email)==false){
					alert("Please enter your valid email id");
					$('#email_reset').focus();
					return false;
				}
				
				var url = '<?php echo SITE_URL; ?>ajax/ajax_reset_password.php';
				jQuery.ajaxSetup({async:false});

				$.post(url, {'email' : email}, function(response){
					alert(response);
                });
				
			}

			function validateEmail(sEmail) {
			    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			    if (filter.test(sEmail)) {
			        return true;
			    }
			    else {
			        return false;
			    }
			}

          </script>
          <!-- End SlidesJS Required -->
        <?php if(SITE_COUNTRY!="COM"){?>
        <!--Start of Zopim Live Chat Script-->
		<script type="text/javascript">
		window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
		d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
		_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
		$.src='//v2.zopim.com/?1piIDUavtFoOMB89h2pj9OK8cT8CswZK';z.t=+new Date;$.
		type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
		</script>
		<!--End of Zopim Live Chat Script-->
        <?php } ?>
		
		
	</head>
	
	<body>
	
	<?php include 'login_top_page.php';?>
  
    <?php include 'top_nav_page.php';?>
    <!-- Top Bar -->
	    <div class="container">
    <img width="1170" height="230" class="img-responsive" alt="Our Pricing" src="<?php echo SITE_URL;?>images/inner-banner.jpg">
    </div><!-- /.container -->
	
	<div class="container mtop50">
		<div class="contact-leftbox">
			<h2 class="heading_normal_h1">Forgot Password</h2>

			<div class="row Mtop20 f14" style="margin-left:1px"> 

		<!--p class="Mtop30"-->
			<form name="pwd_reset" id="pwd_reset" action="<?php echo SITE_URL; ?>dashboard/reset_AU.php" method="post">
				<span style="float:left;width:70%">
					<label for="email_reset">Please enter your email id:<br/><br/> </label>
						<input name="email_reset" id="email_reset" size="60" type="text" placeholder="Email id"/>
					
				</span>
				<div class="resetbtn-box"><input onclick="javascript:reset_password();" type="button" class="loginbtn" 
				value="Reset"/></div>
				
			</form>
			<div class="forgot_pass_tips">Please make sure when copying the password that there are no spaces included. Try typing the password in manually.</div>
		<!--/p-->
						  
			</div>

		</div><!-- Left box end -->
                   
    </div><!-- /.container -->
	
       
       <!-- Main Content starts here -->
    <!--div class="container mtop50">
    <div class="row">
      <div class="col-md-7">
      <h1>Why choose Sticky Number</h1>
<p class="text-justify">With your Sticky Number you can receive, record, manage and view call records from the comfort of any computer, tablet or smart phone in the world! You can get a local virtual phone number from over 1220 cities in over 60 countries and have it forward to just about any phone in the world. We aim to provide high performance and reliability using our global network. We also provide unlimited minutes with our free UK numbers.</p>

<p class="text-justify">Here at Sticky Number we continue to add features to our systems so we can provide clients with all their virtual number needs. Our client base expands across the globe. All Australian virtual phone numbers come with international forwarding, voice mail and IVR secure access. The Signup process is simple, choose a country and city then divert phone number free to most destinations around the world.</p>
      
      </div>
      <div class="col-md-5"><img src="<?php echo SITE_URL;?>images/why-c.jpg" class="pull-right img-responsive" alt="Why choose Us"></div>
    </div>

<div class="alert alert-success mtop30 text-center" role="alert"><strong>24/7 Monitoring | Complete Easy Manangement Tools | Australian Number Supplier | Worldwide Phone Number Routing</strong></div>
<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
        <span class="glyphicon glyphicon-plus"></span>
          How to forward virtual numbers?
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
Diverting or forwarding a virtual number is a standard feature with most phone number suppliers. Where you are wanting to divert the number depends on the plan you should be choosing. With Sticky Number we have two types of forwarding, to either a physical mobile or landline number where we include calling minutes per month, the other option is SIP forwarding where you use another service to handle the outbound calling. The advantage with StickyNumber is we allow 24/7 access to updating the forwarding destination which isapplied immediately upon saving your new settings.
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          <span class="glyphicon glyphicon-plus"></span>
          How to get a virtual phone number in Australia
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
When finding a virtual telephone number supplier make sure they are able to port numbers. Porting a number is the ability to move your new number between phone suppliers, if they are not able to port numbers you may have to sacrifice the number and have to choose a new number when you move to a new company. You should also see how long the company has been around and the features they provide, for example some suppliers require manual editing of the forwarding number or may not provide voice mail features. The process of buying a number is easy for most countries, all you have to do is signup as a member and choose a plan, there are however some destinations that require documentation, which includes having a local presence and identity proof.
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          <span class="glyphicon glyphicon-plus"></span>
          What are virtual phone numbers?
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
A virtual number is a number not directly linked to physical telephone line, which means you can have several numbers from different locations all going to the same telephone line. Virtual numbers allow you to purchase a number from another city or country outside of your business location, if you live in Australia but have clients from USA you can buy a virtual number and forward the call to your Australian landline number. The benefit is reducing your clients costs to call your company from overseas. More technically speaking a virtual number can link to a PSTN or SIP network. Note that virtual number usage is subject to that countries regulations.
      </div>
    </div>
  </div>
<div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
          <span class="glyphicon glyphicon-plus"></span>
          Why get a 1300 or 1800 phone number?
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse">
      <div class="panel-body">
1800 numbers are normally referred to as toll free numbers (free phone) for the caller, however the business who
operates the number pays higher cost. 1300 numbers are normally referred to as a landline number due to clients calling 1300 numbers pay a local rate. Choosing
between the 1300 & 1800 numbers depends on your business model, whether you want to absorb more of the clients calling costs.      </div>
    </div>
  </div>  
</div>
</div--><!-- /.container -->
    <!-- Main Content starts here Closed -->
      

	<?php include 'inc_free_uk_page.php' ;?>
    <?php include 'inc_footer_page.php' ;?>

       <form method="post" action="<?php echo SITE_URL?>index.php?choose_plan" name="frm-step1" id="frm-step1">
            <input type="hidden" value="" name="did_country_code" id="did_country_code">
            <input type="hidden" value="" name="did_city_code" id="did_city_code">
            <input type="hidden" value="" name="fwd_cntry" id="fwd_cntry">
            <input type="hidden" value="" name="forwarding_number" id="forwarding_number">
            <input type="hidden" value="" name="vnum_country" id="vnum_country">
            <input type="hidden" value="" name="vnum_city" id="vnum_city">
            <input type="hidden" value="choose_plan.php" name="next_page" id="next_page">
       </form>
	 </body>
	</html>