
<div class="getstarted-btn-box"><div class="getstarted-btn">Get Started</div></div>
<div class="getstarted-formbox">
	<!--div class="row">Please Select A Number :</div-->
	<div class="row">
		<label for="did_country">Please Select A Number :</label>
		<select title="Please Select Country" class="selectP5" name="did_country" id="did_country">
		   <option value="">Please select a country</option>
		   <?php foreach($did_countries as $did_country) { ?>
			<option value="<?php echo trim($did_country["country_iso"]); ?>" <?php if(trim($sel_did_country_code)!="" && trim($sel_did_country_code)==trim($did_country["country_iso"])){echo " selected "; } ?>><?php echo $did_country["country_name"] . ' ' . $did_country["country_prefix"] ?></option>
		   <?php } ?>
	  </select>
	</div>
	
	<div class="row">
		<label for="city">Select City</label>
		<select title="Select City" class="selectP5" name="city" id="city">
		   <option value="">Please select a city</option>
		</select>										  
	</div>
	
	<!--div class="row">Please Enter Your Forwarding Number :</div-->
	<div class="row">
		<label for="country">Please Enter Your Forwarding Number :</label>
		<select title="Select Country Number" class="selectP5" name="country" id="country">
		   <option value="">Please Select Forwarding Country</option>
		   <?php foreach($countries as $country) { ?>
				<option value="<?php echo trim($country['country_code']); ?>" <?php if(trim($sel_fwd_cntry) != "" && trim($sel_fwd_cntry)==trim($country["country_code"])){echo " selected "; } ?>><?php echo $country['printable_name'] . '+' . $country['country_code'] ?></option>
		   <?php } ?>
		   
		</select>										  
	</div>
	
	<div class="row">
		<label for="number">Enter Number</label>
		<input title="Enter Number" type="text" class="input" placeholder="+" name="number" id="number" value="<?php echo $forwarding_number; ?>" />
	</div>
	
	<div class="row"><a class="slidesjs-navigation continue-btn" id="btn-continue-step1">Continue</a></div>
</div><!-- End of getstarted-formbox -->
