<!doctype html>
<?php 

if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"]!="localhost"){
	if(isset($_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_URI"]=="/index.php?page=terms" || $_SERVER["REQUEST_URI"]=="/terms")){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: forgot-password/");
		exit;
	}
	
}
?>
<?php include 'inc_header_page.php'; ?>

        
         <!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
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
		
		<link rel="stylesheet" media="all" href="<?php echo SITE_URL; ?>css/override_bootstrap.css"/>
		
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
                     <div class="heading">Terms of Service</div>
                     
                        <div class="row Mtop20 f14"> 
                        
                        <p class="Mtop20">
                        <strong>Introduction</strong><br/><br/>

BY SIGNING UP, ENROLLING IN, USING, ENTERING YOUR CREDIT/DEBIT CARD INFORMATION, AND/OR PAYING FOR THE SERVICE(S), YOU AGREE TO THE PRICES, CHARGES, AND THE TERMS OF THIS AGREEMENT. iF YOU DO NOT AGREE TO BE BOUND BY THESE TERMS, YOU MUST ENSURE YOU DO NOT INDICATE TO US YOUR ACKNOWLEDGEMENT AND AGREEMENT TO BE IRREVOCABLY BOUT BY TESE TERMS. YOUR ACKNOWLEDGEMENT AND AGREEMENT TO BE IRREVOCABLY BOUND BY THESE TERMS WILL BE INDICATED TO US UPON MAKING AN APPPLICATION TO US FOR THE USE OR PURHASE OF SERVICES, PURCHASING OR USING SERVICES PROVIDED BY US, OR BECOMING ONE OF OUR CUSTOMERS AT ANY TIME, WHICHEVER OCCURS FIRST.<br/><br/>

We may change the terms of this agreement from time to time, without prior notice to you. Notices of amendment(s) made to this agreement will be considered given and effective on the date that such amendment(s) are posted at <?php echo SITE_DOMAIN; ?>. Further, the agreement posted shall supersede all previously agreed to electronic and/or written Terms of Service.<br/><br/>


<strong>Refunds</strong><br/><br/>

0.1 &nbsp; &nbsp;You acknowledge that invoiced phone number registrations and/or renewals are non-refundable in whole or in part if the contract is terminated by you.<br/><br/>

0.2 &nbsp; &nbsp;Sticky Number will not refund to you the cost of registering a phone number after you have submitted a phone number application form, provided that the phone number has been registered. This is due to the bespoke nature of phone numbers. Phone numbers are specific to the consumer's specifications and service provision will begin from the moment the phone number registration is submitted to the registration authorities.<br/><br/>

0.3 &nbsp; &nbsp;Sticky Number makes no representations as to how long refunds will be processed.<br/><br/>

0.4 &nbsp; &nbsp;You acknowledge that Sticky Number is unable to change or edit the phone number after you have registered it and is not liable to refund any errors or omissions on your part. For clarity, you are responsible for the correct input of the phone number you request.<br/><br/>

0.5 &nbsp; &nbsp;In the event that phone numbers have been misspelled in error and You have been charged for them, you may be eligible for a credit to your account if you contact us within 3 days of registering the phone number but are not eligible for refunds. Late notifications will not qualify for any credit. You acknowledge that credit is granted at the sole discretion of Sticky Number.<br/><br/>

0.6 &nbsp; &nbsp;Further to clause 0.2 you acknowledge and understand that phone numbers are not entitled to cooling off periods.<br/><br/>



<strong>Service</strong><br/><br/>

1.3 &nbsp; &nbsp;You acknowledge and understand that the Service provided you by <?php echo SITE_DOMAIN; ?> is not a traditional telephone service. Accordingly, the Service is subject to different regulatory treatment, which may differ from the regulatory treatment of traditional telephone services; such difference(s) may limit or otherwise affect your rights before telecom regulating agencies and/or departments and organizations.<br/><br/>

1.4 &nbsp; &nbsp;The Company does not guarantee backup data for any voicemail message(s), fax data and/or transmission(s), voicemail greeting(s) or any other data/transmission(s) sent through its systems. It is possible to lose said data, and in such case, said data cannot be recovered; regardless of the circumstances surrounding such a loss, <?php echo SITE_DOMAIN; ?> shall not be held liable for any loss or harm resulting from your use of these services.<br/><br/>

1.5 &nbsp; &nbsp;It is strongly recommended that you test the Services upon initial setup and periodically thereafter to ensure the Services are functioning to your satisfaction. Should you fail to properly test the Services as recommended, you do so at your own risk and you shall be the sole party responsible for any loss or damages.<br/><br/>

1.6 &nbsp; &nbsp;The Service(s) provided to you by <?php echo SITE_DOMAIN; ?> stand independent of any third-party carrier(s) and/or other service provider(s) you may be using, or will use, in conjunction with our Service. This agreement applies only to those Services that are owned and managed by <?php echo SITE_DOMAIN; ?>. In no way does this agreement cancel or amend any existing agreement(s) you may already have in place, or will have, with any third-party carrier and/or service provider(s). Your agreement(s) with any and all third-party carrier(s) and/or other service provider(s) are between you and the third-party carrier(s) and/or other service provider(s). Such agreement(s) are NOT applicable to the Services provided to you by <?php echo SITE_DOMAIN; ?>. Further, <?php echo SITE_DOMAIN; ?> shall not be held liable for any action(s), or lack thereof, inadequacies and/or failures of any third-party carrier(s) and/or service provider(s) you use in conjunction with the Service. You agree that you are solely responsible for any and all fee(s) due to any and all third-party carrier(s) and/or other service provider(s) you use in conjunction with our Service.<br/><br/>

1.7 &nbsp; &nbsp;Sticky Number guarantee the delivery of international caller ID. If you are able to receive international caller IDs for some time and then are suddenly not able to then there is nothing we can do in this scenario.<br/>In general this ability to receive international caller IDs is usually because of equipment configuration/policies on the carrier side. The carrier may at anytime decide to upgrade their equipment removing these abilities, which are not guaranteed in any way, or may change their policies, to prevent delivery of this information.<br />In short there is nothing we can do to guarantee or provide the delivery of incoming international caller IDs.<br /><br />

<strong>Subscriber Access</strong><br/><br/>

2.2 &nbsp; &nbsp;It is your responsibility to maintain the security and confidentiality of your account username and password at all times and you are solely responsible for any liability or damages resulting from your failure to maintain that security and confidentiality and for all activities that occur under your username and password. You must notify us immediately if you believe that your online account with <?php echo SITE_DOMAIN; ?> has been compromised by unauthorized access so that we may assign you a new username and/or password. All username and password resets will be sent only via email.


<strong>Directory Listing / Publication of numbers</strong><br/><br/>

2.2 &nbsp; &nbsp;We do not publish directories of any of the phone numbers owned and managed by <?php echo SITE_DOMAIN; ?>. Further, we will not assist you in publishing any of the phone number(s), provided you by <?php echo SITE_DOMAIN; ?>, in any directory.<br/><br/>

4.2 &nbsp; &nbsp;It is possible for your phone number(s) to have been listed in a directory and/or on a website(s) or other publications at the request of the previous subscriber of your phone number(s); this factor is beyond our control and we shall not be held liable for any harm or loss resulting from such. If you receive phone calls from a previous subscriber's callers, which is not uncommon, you should contact us immediately to cancel that phone number(s) and select a replacement phone number(s). Please note that you will not receive credit(s) for any such calls so it is in your best interest to contact us immediately to cancel the phone number(s).<br/><br/>

4.3 &nbsp; &nbsp;<?php echo SITE_DOMAIN; ?> will not reimburse you, in any way, for any cost(s) associated with the publication of your <?php echo SITE_DOMAIN; ?> phone number(s). You are solely responsible for any costs, fees, damages and/or losses related to the publication of the phone number(s) we provide you.<br/><br/>


<strong>Restrictions On Use</strong><br/><br/>

6.1 &nbsp; &nbsp;You agree to use the Service(s) provided you by <?php echo SITE_DOMAIN; ?> for legal and legitimate purposes. Unlawful, improper and/or illegitimate use will be defined by <?php echo SITE_DOMAIN; ?> or, any official government police agency, which notifies <?php echo SITE_DOMAIN; ?> of your unlawful use of the Service it provides to you.<br/><br/>

6.2 &nbsp; &nbsp;You are liable for any and all content transmitted through the Service provide you by <?php echo SITE_DOMAIN; ?>. You are solely liable for the content of any and all transmissions sent through <?php echo SITE_DOMAIN; ?> systems as a result of your use of the Service, regardless of whether or not such content is solicited or unsolicited<br/><br/>

6.3 &nbsp; &nbsp;You shall not use the Service provide you by <?php echo SITE_DOMAIN; ?> for transmitting obscene, fraudulent, harassing, infringing, libelous, or otherwise unethical content. Further, you shall not use the Service for distributing junk mail, chain letters, "spamming," telephonic solicitations of any kind or nature, or other such communications or content, regardless of whether or not such content is solicited or unsolicited.<br/><br/>

6.4 &nbsp; &nbsp;<?php echo SITE_DOMAIN; ?> reserves the right to restrict termination to certain geographical regions and/or certain special services hotlines at its sole discretion. Additionally, <?php echo SITE_DOMAIN; ?> reserves the right to refuse service to certain geographical regions at its sole discretion<br/><br/>

6.5 &nbsp; &nbsp;<?php echo SITE_DOMAIN; ?> may immediately cancel your Service and repossess any and all phone number(s) associated with your account if/when your use of the Service(s) provided to you interfere in any way with <?php echo SITE_DOMAIN; ?> 's ability to provide Service(s) and products to its other customers.<br/><br/>

6.6 &nbsp; &nbsp;Your use of the Service(s) provided you by <?php echo SITE_DOMAIN; ?> subjects you to any and all local laws and all international laws and regulations.<br/><br/>

6.7 &nbsp; &nbsp;Should your use of the Service(s) provided you by <?php echo SITE_DOMAIN; ?> be deemed of a fraudulent, unethical, or otherwise prohibited nature, by <?php echo SITE_DOMAIN; ?>, <?php echo SITE_DOMAIN; ?> reserves the right to immediately close your account, terminate all Service to it, and repossess and re-assign any and all phone number(s) associated with said account, and deem forfeit any remaining balance on your <?php echo SITE_DOMAIN; ?> account. <?php echo SITE_DOMAIN; ?> shall not be held liable for any harm or loss you experience as a result of such actions.<br/><br/>

6.8 &nbsp; &nbsp;The Services may not be used to support a calling card platform of any kind.<br/><br/>

6.9 &nbsp; &nbsp;Foreign carriers and/or regulatory agencies may impose, upon the end-to-end international service they provide, limitations, restrictions and/or cease entirely your ability to use the Service <?php echo SITE_DOMAIN; ?> provides you at anytime, without prior notice. In such case, you must conform to said limitations, restrictions and/or entire cessation of service by the foreign carriers and/or regulatory agencies<br/><br/>

6.10 &nbsp; &nbsp;You are not authorized to charge services provided to you to the phone number(s) assigned to you by <?php echo SITE_DOMAIN; ?> and you may not request that any third-party service provider charge any such services to any number(s) provided you by <?php echo SITE_DOMAIN; ?>. Any such activity will constitute just cause for <?php echo SITE_DOMAIN; ?> to immediately cancel your Service and charge your credit/debit card for said charges. <?php echo SITE_DOMAIN; ?> shall not be held liable for any harm, loss or damages arising from such actions.<br/><br/>

6.11 &nbsp; &nbsp;We may temporarily suspend and/or cancel Service to your account if you change your ring-to destination to a geographical region in which we prohibit termination. In such case, your account and phone number(s) will be disabled until your account is manually reviewed by our staff and/or cancelled entirely.<br/><br/>

6.12 &nbsp; &nbsp;You agree to comply with all applicable foreign and domestic laws and rules and regulations regarding the transmission of technical data exported or imported from the United Kingdom to your ring-to destination country. Further, you agree to hold <?php echo SITE_DOMAIN; ?> harmless of any damages or liabilities, of any kind, related to your violation of UK and/or International laws, rules and/or regulations while you are a customer of <?php echo SITE_DOMAIN; ?>.<br/><br/>


<strong>Instant Activation</strong><br/><br/>

8.1 &nbsp; &nbsp;If you provide a free email address at sign-up (i.e., Yahoo!, Gmail, Hotmail, Live, etc.) your account may not be activated instantly. Additionally, we may require that you submit images of the front and back of the credit/debit card you've presented and images of your official identification (if your selected payment method is other than a credit/debit card, an image of your official identification may be required) before your account can be approved. Please allow at least 24 business hours for processing of this information.<br/><br/>

8.2 &nbsp; &nbsp;You may not begin using the phone number(s) you've ordered until such time as your account is approved and activated by us.<br/><br/>


<strong>Registration</strong><br/><br/>

9.1 &nbsp; &nbsp;When signing up for our Services you agree to furnish a true and accurate representation of your identity, contact information, and billing information; and at subsequent times at the request of <?php echo SITE_DOMAIN; ?>. If you provide false or misleading information, or if we have reason to believe that you have presented false or misleading information, we reserve the right to cancel your account, any Services related to your account, and refuse any and all of your current and future attempts to establish Service with us.<br/><br/>

9.2 &nbsp; &nbsp;If you attempt to sign up for an account that is affiliated, in any way, to an account that has been closed by our Legal Department, your request for Service will be denied and you will need to seek services elsewhere.<br/><br/>

<strong>Our Rights To Limit Or Terminate Service Or This Agreement</strong><br/><br/>

17.1 &nbsp; &nbsp;<?php echo SITE_DOMAIN; ?> can, without prior notice, limit or terminate the Service(s) it provides to you for this or any other good cause, including but not limited to: (I) if you or any user of your <?php echo SITE_DOMAIN; ?> account: (a) breach this agreement in any way; (b) provide false or misleading information about your identity; (c) use our service in any way that disrupts our ability to provide Services to our existing customers; (d) use our service in any way that adversely affects our relationship with our vendors and/or our ability to offer Services to our future customers; (e) provide false or misleading credit and/or financial information to us; (f) become insolvent or go bankrupt; (g) are involved, either directly or indirectly, in any official police investigation <?php echo SITE_DOMAIN; ?> receives notification of; (h) constantly express your dissatisfaction with our Service and hinder, in any way, our ability to remedy any issues you may have with your Service (which may include, but is not limited to: constant phone calls and/or emails to us about the issues you have reported); (i) steal from us; j) interfere with our operations and/or network quality in any way; (k) refuse to pay when billed for service; (l) refuse to furnish information requested by us or present false or misleading information which is essential for billing purposes or for establishing your creditworthiness; (m) act in a manner that is threatening, harassing, obscene, or otherwise inappropriate and/or abusive towards our representatives; (n) use our services in a fraudulent manner with the intent to deceive; (o) have been given written notice of an outstanding balance owed to us yet your balance remains unpaid for twenty-nine (29) days; (p) were previously served with notice of your breach of this agreement, were allowed to and took corrective action, but thereafter engaged in the same breach activity or a new breach of this agreement; (q) act in a manner that hinders or frustrates any investigation by us or others having legal authority to investigate our legal obligations.<br/><br/>

17.3 &nbsp; &nbsp;<?php echo SITE_DOMAIN; ?> may limit or terminate your Service as a result of any new governmental regulations and policies, whether domestic or international, which it must adhere to.<br/><br/>


<strong>Warranty Disclaimer</strong><br/><br/>

18.1 &nbsp; &nbsp;<?php echo SITE_DOMAIN; ?> WILL MAKE ALL REASONABLE EFFORTS, UNDER THE CIRCUMSTANCES, TO MAINTAIN ITS OVERALL NETWORK QUALITY. <?php echo SITE_DOMAIN; ?> MAKES NO WARRANTIES ABOUT THE SERVICE PROVIDED HEREUNDER, EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO ANY WARRANTY OF MERCHANTIBILITY, COMPLETENESS, QUALITY, OR FITNESS FOR A PARTICULAR PURPOSE, TITLE OR NON-INFRINGEMENT, OR ANY WARRANTY ARISING BY USAGE OF TRADE, COURSE OF DEALING, OR COURSE OF PERFORMANCE. ACCORDINGLY, ALL SERVICES OFFERED BY <?php echo SITE_DOMAIN; ?> ARE PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS. NO CREDIT ALLOWANCES ARE PROVIDED FOR INTERUPTION OF SERVICE OF ANY KIND. IN NO EVENT SHALL <?php echo SITE_DOMAIN; ?> BE HELD LIABLE TO YOU NOR ANY THIRD PARTY FOR ANY INDIRECT, SPECIAL, INCIDENTAL, CONSEQUENTIAL, ACTUAL, PUNITIVE, OR EXEMPLARY DAMAGES, INCLUDING, WITHOUT LIMITATION TO, DAMAGES FOR LOSS OF REVENUE, LOSS OF PROFITS, OR LOSS OF YOUR CLIENT(S) GOODWILL, ARISING IN ANY MANNER FROM THIS AGREEMENT AND OR THE PERFORMANCE OR NONPERFORMANCE HEREUNDER. NEITHER PARTY SHALL BE HELD LIABLE FOR ANY DELAY OR FAILURE IN PERFORMANCE OF ANY PARTY OF THIS AGREEMENT, OTHER THAN FOR ANY DELAY OR FAILURE IN AN OBLIGATION TO PAY MONEY, TO THE EXTENT SUCH DELAY OR FAILURE IS CAUSED BY FIRE, FLOOD, EXPLOSION, ACCIDENT, WAR STRIKE, EMBARGO, GOVERNMENTAL REQUIREMENT, CIVIL OR MILITARY AUTHORITY, ACT OF GOD, INABILITY TO SECURE MATERIALS OR LABOR, OR ANY OTHER CAUSES BEYOND THEIR REASONABLE CONTROL. ANY SUCH DELAY OR FAILURE SHALL SUSPEND THIS AGREEMENT UNTIL THE FORCE MAJEURE CEASES AND THE TERM SHALL BE EXTENDED BY THE LENGTH OF THE SUSPENSION. THIS AGREEMENT SUPERCEDES ANY AND ALL PRESENT AND FUTURE AGREEMENTS MADE BETWEEN YOU AND <?php echo SITE_DOMAIN; ?> WITH RESPECT TO QUALITY, UP-TIME, RELIABILTY, AND GENERAL PERFORMANCE. <?php echo SITE_DOMAIN; ?> DOES NOT WARRANTY THE RELIABILTY, UP-TIME, QUALITY, AND GENRAL PERFORMANCE OF THE SERIVCE IT PROVIDES TO YOU. FURTHER, <?php echo SITE_DOMAIN; ?> MAKES NO WARRANTY THAT THE SERVICE(S) IT PROVIDES TO YOU WILL BE UNINTERRUPTED.<br/><br/>


<strong>Proprietary</strong><br/><br/>

19.1 &nbsp; &nbsp;<?php echo SITE_DOMAIN; ?> maintains exclusive ownership of the service interest and title, including but not limited to all of its trademarks, copyrights and other intellectual property. Additionally, <?php echo SITE_DOMAIN; ?> maintains sole and exclusive ownership of the all telephone numbers and/or fax numbers managed by <?php echo SITE_DOMAIN; ?>'s system, voicemail services offered through <?php echo SITE_DOMAIN; ?>, fax services offered through <?php echo SITE_DOMAIN; ?> and all technologies and software it creates. Any and all rights not expressly stated herein are retained by <?php echo SITE_DOMAIN; ?>.<br/><br/>

19.2 &nbsp; &nbsp;We reserve the right not to disclose our carrier/supplier information to you.<br/><br/>


<strong>Communications</strong><br/><br/>

20.1 &nbsp; &nbsp;Notices are given by <?php echo SITE_DOMAIN; ?> via email, by a general posting on the <?php echo SITE_DOMAIN; ?> website and/or via telephone. <?php echo SITE_DOMAIN; ?> reserves the right to determine which method of communication it employs to communicate with you.<br/><br/>

20.2 &nbsp; &nbsp;<?php echo SITE_DOMAIN; ?> will make all reasonable efforts to deliver email notifications to you regarding the status of your account. It is beyond our control if you do not receive the communications we attempt to deliver to your email address. <?php echo SITE_DOMAIN; ?> shall not be liable for any harm or loss resulting from the suspension, repossession and/or re-assignment of your assigned phone number(s) and/or cancellation of your account because you did not receive our email communications. Further, we may change or cease entirely all notifications at anytime without prior notice.<br/><br/>

20.3 &nbsp; &nbsp;You agree to waive your rights under these Terms of Service to receive ten (10) days advance notice of the amount that we will debit from your credit/debit card(s) on file for your <?php echo SITE_DOMAIN; ?> account. While we may send you email notices from time to time regarding the billing matters of your account, we are not obligated to do so. We may change or cease entirely our notifications at anytime without prior notice.<br/><br/>

20.4 &nbsp; &nbsp;When contacting us via email, voicemail, fax and/or postal mail, we ask that you allow at least 24 business hours, from the time we receive your inquiry, to receive a response from us. Occasionally, response times may be greater, depending on the volume of communications we receive from our other customers. All communications we receive are handled in the order in which they were received.<br/><br/>

20.5 &nbsp; &nbsp;When communicating with us via email you may be asked to verify your account number, the name on file for your account, and the full billing address you provided; if you are unable to verify this very basic information our support staff will be unable to assist you.<br/><br/>


<strong>Information Disclosure</strong><br/><br/>

21.1 &nbsp; &nbsp;In cooperation with any official investigation, we may disclose any and all of the information we maintain on record for your account, to any police agency, legal entity, and/or any other third party, which issues us a REPS(s), subpoena(s), search warrant(s), court order(s), or any other official demand(s) for information we maintain on record for your account. In the event that we receive a REPA(s), subpoena(s), search warrant(s), court order(s), and/or any other official demand(s) for information we maintain on record for your account, we reserve the right to immediately cancel your Service, repossess and re-assign any phone number(s) associated with your account and block your access to our website. <?php echo SITE_DOMAIN; ?> shall not be held liable for any harm or loss arising from such.<br/><br/>

21.2 &nbsp; &nbsp;We may monitor your use of the Service we provide you for violations of this TOS agreement. We may take all necessary actions, we deem fit, if we suspect a breach of this agreement or if think it necessary to protect us from imminent harm or loss.<br/><br/>

21.3 &nbsp; &nbsp;During the term of this Agreement, Customer grants the Company a license to use the Customer's name and logo, if applicable, in the Company's promotional material to advertise that the Customer is a client of the Company. The Company agrees that it shall not share, sell, trade, barter, or offer for free the use of the Customer's name, logo, and affiliate data to any other individual, corporation, or entity that is not owned in whole or in part by the Company.<br/><br/>

21.4 &nbsp; &nbsp;In an effort to protect our subscribers from credit/debit card fraud, <?php echo SITE_DOMAIN; ?> actively analyzes all new accounts and existing accounts to identify and block individuals suspected of fraud from using our Service now and in the future. We may share this data (including, but not limited to, IP addresses, email addresses, the credit/debit card number(s) used, etc.) with third-party payment processors and/or Law Enforcement agencies in an effort to reduce fraud.<br/><br/>



<strong>Headings Of No Force Or Effect</strong><br/><br/>

23.1 &nbsp; &nbsp;The headings throughout this agreement are intended for reference only and have no effect or bearing on the meaning of any provision listed herein.<br/><br/>


<strong>Indemnification</strong><br/><br/>

24.1 &nbsp; &nbsp;You shall defend, indemnify, and hold harmless <?php echo SITE_DOMAIN; ?>, its officers, directors, employees, and agents from any breach of this Agreement, use of Customer's account or in connection with the placement or transmission of any message, information, software or other content using the Services. <?php echo SITE_DOMAIN; ?> shall be defended by attorneys of their choice at Customer's expense.<br/><br/>


<strong>Severability</strong><br/><br/>

25.1 &nbsp; &nbsp;Should any parts of this agreement be legally declared invalid or unenforceable, all other parts of this agreement will remain valid and enforceable. In such a case, said invalidity or non-enforceability will not invalidate or render unenforceable any other portion of this agreement.<br/><br/>


<strong>Additional Provisions</strong><br/><br/>

26.1 &nbsp; &nbsp;Should a problem/issue arise with the Service you must notify us immediately so that we may remedy, to the best of our ability, the problem/issue you are having with the Service; if we are unable to remedy the problem you are having with the Service you will be offered a replacement phone number(s), at an equal or greater monthly cost. If you opt not to take the replacement phone number(s), you may continue to use the problematic Service at your own risk. <br/><br/>

26.2 &nbsp; &nbsp;You are responsible for reviewing the TOS available on the <?php echo SITE_DOMAIN; ?> website for any amendments to this agreement and/or our rates and/or other amendments to our Service. Your continued use of the Service, after <?php echo SITE_DOMAIN; ?>'s posting of any amended version of its TOS, rates, and/or modifications to its Service, constitutes your acceptance and agreement with any and all amendments made and such modifications/amendments supersede any previous agreements between you and <?php echo SITE_DOMAIN; ?>. <br/><br/>

26.4 &nbsp; &nbsp;Should you violate any term(s) of this agreement, <?php echo SITE_DOMAIN; ?> reserves the right to immediately cancel all Service(s) it provides to you. <br/><br/>

26.5 &nbsp; &nbsp;You promise that you are of legal age to enter into this agreement and that you fully understand and fully agree with all of its terms and conditions.<br/><br/> 

26.6 &nbsp; &nbsp;ALL content on this site is copyright protected and may not be reproduced, adopted, or transmitted without the prior written consent of <?php echo SITE_DOMAIN; ?>. <br/><br/>

Important - Please Read Carefully <br/><br/>

Copyright, Authors' Rights and Database Rights<br/><br/> 

27.1 &nbsp; &nbsp;All content included on the website, such as text, logos, graphics, buttons, images, icons, downloads, data, and software, is the property of <?php echo SITE_DOMAIN; ?>, and is protected by United Kingdom and international copyright and authors' rights laws. All content on this website is the exclusive property of <?php echo SITE_DOMAIN; ?> and is protected by United Kingdom and international copyright laws. All software used on this website is the property of <?php echo SITE_DOMAIN; ?> and is protected by United Kingdom and international copyright and authors' rights laws. <br/><br/>

27.2 &nbsp; &nbsp;You may not extract and/or use and re-utilise parts of the contents of this website without <?php echo SITE_DOMAIN; ?> express written consent. Any unauthorised copying of this web site or parts of this web site will constitute an infringement of copyright. You may not utilise any robots, or similar data mining or data gathering and extraction tools to extract for use and/or re-utilisation of any parts of this website, without <?php echo SITE_DOMAIN; ?> express written consent. You may not create and/or publish your own web site or database that features parts of this website without <?php echo SITE_DOMAIN; ?> express written consent (e.g. our prices and/or product listings). <br/><br/>

27.3 &nbsp; &nbsp;In case of Copyright Infringement we will seek legal remedy and you may be liable for damages (including costs and solicitors fees). <br/><br/>

27.4 &nbsp; &nbsp;This constitutes the entire Terms of Service agreement between the parties named herein and this agreement may only be amended by <?php echo SITE_DOMAIN; ?>.<br/><br/>


                        </p>
                                          
                        </div>
                     
                   </div><!-- Left box end -->
                   
                   
    </div><!-- /.container -->
	
       
       <!-- Main Content starts here -->
    <!-- Main Content starts here Closed -->
      

	<?php include 'inc_free_uk_page.php' ;?>
    <?php include 'inc_footer_page.php' ;?>

       