<?php
function sip_url_to_phone($sip_url){
	$sip_url_to_phone = "";
	
	$temp_sip = explode("/", $sip_url);
	$sip_removed = $temp_sip[1];
	$temp_sip = explode("@",$sip_removed);
	$url_to_phone = $temp_sip[0];
	
	return $url_to_phone;
	
}


function seconds_humanized($seconds_to_humanize){
	$seconds_humanized = "0m 0s";
	
	$a_minute = 60;
	
	$seconds = intval($seconds_to_humanize) % $a_minute;
	$minutes = (intval($seconds_to_humanize) - $seconds) / $a_minute;
	
	$seconds_humanized = $minutes."m ".$seconds."s";
	
	return $seconds_humanized;
}

function generatePassword($length=9, $strength=0) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

?>