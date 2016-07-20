<?php

		// STEP 1: read POST data
		
		// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
		// Instead, read raw POST data from the input stream. 
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		$sender_txn_id = "";
		$receiver_id = "";
		$receiver_email = "";
		$payment_amount = 0;
		$payment_status = "";
		$keyvalues = '';
		foreach ($raw_post_array as $keyval) {
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2){
				$myPost[$keyval[0]] = urldecode($keyval[1]);
				$keyvalues .= trim(urldecode($keyval[0]))."=".trim(urldecode($keyval[1])).", ";
				switch(trim(urldecode($keyval[0]))){
					case "transaction[0].id_for_sender_txn";
						$sender_txn_id = trim(urldecode($keyval[1]));
						break;
					case "transaction[0].id":
						$txn_id = urldecode($keyval[1]);
						break;
					case "transaction[0].status":
						$payment_status = urldecode($keyval[1]);
						break;
					case "transaction[0].receiver":
						$receiver_email = urldecode($keyval[1]);
						break;
					case "transaction[0].amount":
						$payment_amount = urldecode($keyval[1]);
						break;
				}
			}
		}
		
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exists = true;
		}
		
		foreach ($myPost as $key => $value) {
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}
		
		// Step 2: POST IPN data back to PayPal to validate
		
		$ch = curl_init("https://www.paypal.com/cgi-bin/webscr");
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		
		if( !($res = curl_exec($ch)) ) {
			// error_log("Got " . curl_error($ch) . " when processing IPN data");
			curl_close($ch);
			exit;
		}
		curl_close($ch);