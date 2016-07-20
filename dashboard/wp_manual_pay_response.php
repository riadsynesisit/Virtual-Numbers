<html>
<title>Manually Process a WP payment notification message</title>
<head>
<script src="../js/jquery-1.10.1.min.js" type="text/javascript"></script>
</head>
<script type="text/javascript">

if(confirm("Are you sure? Do you want to send the payment notification?")){
	send_pay_notification();
}

function send_pay_notification(){
	
	$.ajax({
        type: "POST",
        //url: "https://www.stickynumber.com.au/dashboard/wp_pay_response.php",
		//url: "http://dev.stickynumber.com/dashboard/wp_pay_response.php",
		url: "http://localhost/sticky/dashboard/wp_pay_response.php",
        /*data: "debug=true&region=&AAV=00000&authAmountString=A$9.25&_SP.charEnc=UTF-8&desc=Payment+3+of+FuturePay+agreement+ID+50652333&tel=&address1=&countryMatch=S&cartId=519&address2=&address3=&callbackPW=gaC5UswU&rawAuthCode=A&lang=en&amountString=A$9.25&transStatus=Y&authCost=9.25&currency=AUD&installation=305745&amount=9.25&countryString=Australia&displayAddress=1%2F195+glebe+point+road%0A%0A%0ASydney&transTime=1412731329813&testMode=0&name=Hisham+Najm&routeKey=VISA_CREDIT-SSL&ipAddress=&fax=&rawAuthMessage=cardbe.msg.authorised&instId=305745&AVS=0111&compName=Hawking+Software+Pte+Ltd+t%2Fa+StickyNumber.com.au+Online+Number&futurePayId=50652333&authAmount=9.25&postcode=2037&cardType=Visa&cost=9.25&authCurrency=AUD&country=AU&charenc=UTF-8&email=306733%40stickynumber.net&address=1%2F195+glebe+point+road%0A%0A%0ASydney&transId=1005074919&msgType=authResult&town=&authMode=A",*/ 
		data: "debug=true&region=&AAV=00000&authAmountString=A$9.90&_SP.charEnc=UTF-8&desc=Virtual+Number+of+AU+130&tel=&address1=26+Columbia+Avenue&countryMatch=Y&address2=&cartId=10951&address3=&lang=en&rawAuthCode=A&callbackPW=gaC5UswU&transStatus=Y&amountString=A$9.90&authCost=9.90&currency=AUD&installation=305745&amount=9.90&countryString=Australia&displayAddress=26+Columbia+Avenue%0A%0A%0AClapham&transTime=1445227289986&name=Scott+Heidrich&testMode=0&routeKey=VISA_COMMERCIAL_DEBIT-SSL&ipAddress=219.90.192.149&fax=&rawAuthMessage=cardbe.msg.authorised&instId=305745&AVS=2112&compName=Hawking+Software+Pte+Ltd+t%2Fa+StickyNumber.com.au+Online+Number&futurePayId=52157758&authAmount=9.90&postcode=5062&cardType=Visa+Commercial+Debit&cost=9.90&authCurrency=AUD&country=AU&charenc=UTF-8&email=425895%40stickynumber.net&address=26+Columbia+Avenue%0A%0A%0AClapham&transId=4707033707&authentication=ARespH.card.authentication.0&msgType=authResult&town=Clapham&authMode=A",
        async: false,
        success: function (msg) {
            //alert(msg);
			document.write(msg);
            $("#manual_response").html(msg);
        },
        error: function (xhr, option, err) {
            //alert("XHR Status: " + xhr.statusText + ", Error - " + err);
			document.write(err);
        }
    });
}
</script>
<body>
<div id="manual_response"></div>
</body>
</html>

