<?php

function NTS_API_Connect($debug_messages=true){

	if($debug_messages){
        echo "Open a socket connection to ".MAGRATHEA_API_HOST."\n";
	}
	
	if( isset($_SESSION["NTSAPI_Connected"]) && $_SESSION["NTSAPI_Connected"]=="true"){
		return false;
	}

	$_SESSION["NTSAPI_Connected"] = "true";
        //Open a socket connection
        $fp = fsockopen (MAGRATHEA_API_HOST, MAGRATHEA_PORT, $errno, $errstr, 30);

        $res = "";
        while(!feof($fp)){
                $res = fgets($fp, 1024);
                if(trim($res)!=""){
                	if($debug_messages){
                        echo "Response to socket connection is: ".$res;
                	}
                    break;
                }
        }


        if (substr($res,0,1)!="0") {
                // Socket Connection Error
                echo "Socket connection error\n";
        } else {
        	if($debug_messages){
                echo "Sending AUTH command to NTS API\n";
        	}
            $answer_back = NTS_API_send_command($fp, "AUTH ".MAGRATHEA_AUTH_NAME." ".MAGRATHEA_AUTH_WORD);
            if($debug_messages){
                echo "Answer from NTS_API is:".$answer_back;
            }
            if($answer_back===false){
            	return false;
            }
                if(substr($answer_back,0,1)!="0"){
                        return false;
                }else{
                        return $fp;
                }
        }

}

function NTS_API_send_command($fp, $command_to_send){

        fputs($fp, $command_to_send);
        stream_set_timeout($fp, 5);
        $info = stream_get_meta_data($fp);
		if ($info['timed_out']) {
        	echo 'Connection timed out!';
        	return;
    	}
        while(!feof($fp)){
                $res = fgets($fp, 1024);
                if(trim($res)!=""){
                        return $res;
                }
        }
        echo "\nNo response from NTS API\n";
        return false;

}

function NTS_API_disconnect($fp){

		$_SESSION["NTSAPI_Connected"] = "false";
        return NTS_API_send_command($fp, "QUIT");

}

function NTS_API_request_block($fp, $did_start = ''){

        echo "Requesting a block of ten numbers...\n"; 
        //$check_string = MAGRATHEA_DID_START."_____";//Six leading digits followed by underscores
        $numbers_block = "";

        $i = 0;
        $api_result = "1";
        while(substr(trim($api_result),0,1)=="1"){
        	if($i>9){//All ranges checked. Dead lock. Can't proceed any further
        		echo "NO NUMBERS in any of the ranges were returned.\n ";
        		return false;
        	}
			if($did_start){
				$check_string = $did_start.$i." 10";//Six leading digits followed by size of the block
			}else{
				$check_string = MAGRATHEA_DID_START.$i." 10";//Six leading digits followed by size of the block
			}
        	echo "Command being sent is: BLKACTI ".$check_string." \n";
        	$api_result = NTS_API_send_command($fp, "BLKACTI ".$check_string);
        	echo "Response to BLKACTI is: ".$api_result." \n";
        	$i = $i + 1;
        }
        
        if(substr($api_result,0,1)=="0"){
                //Get the number range except the last 11th digit (The last digit is each one of 0 to 9
                $num_range = substr($api_result,2,10);
                echo "Number range obtained is: ".$num_range."x\n";
                for($i=0; $i<=9; $i++){
                	echo "Sending SET for: ".$num_range.$i." 1 S:".$num_range.$i."@".SERVER_SIP_NAME."\n";
                    $set_result = NTS_API_send_command($fp, "SET ".$num_range.$i." 1 S:".$num_range.$i."@".SERVER_SIP_NAME);
                    echo "Response to SET is: ".$set_result;
                    if(substr($set_result,0,1)=="0"){
                    	//Number successfully set
                        $numbers_block .= $num_range.$i."|";
                        echo "SUCCESSfully set the number ".$num_range.$i."\n";
                    }else{
                        //Failed to set the number
                        echo "FAILED to SET the number\n";
                    }
                }//End of for loop
                echo "Sending disconnect request\n";
                $disc_response = NTS_API_disconnect($fp);
                echo "Response to disconnect is: ".$disc_response;
                echo "Returning numbers to save is: ".$numbers_block."\n";
                return $numbers_block;
        }else{
                //Failed to return the block of numbers
                echo "FAILED to get block of ten numbers - Response is:".$api_result."\n";
                return false;
        }

}

function NTS_API_request_did($fp, $did_start = ''){

		if($did_start){
			$check_string = $did_start."_____";//Six leading digits followed by five underscores
		}else{
			$check_string = MAGRATHEA_DID_START."_____";//Six leading digits followed by five underscores
		}
		$numbers_block = "";
		
		$loop_count = 0;
		
		for($i=0;$i<=9;){//Run until we have 10 successfully set numbers
			$loop_count++;
			if($loop_count>=15){//Maximum 15 attempts 
				break;
			}
			echo "\n$loop_count. Requesting a single DID number...\n"; 
       		$api_result = NTS_API_send_command($fp, "ALLO ".$check_string);
			echo "Response to ALLO ".$check_string." is:\n".$api_result;
        
        	if(substr($api_result,0,1)=="0"){//success
                //Get the number
                $did_number = substr($api_result,2,11);
                echo "DID Number obtained is: ".$did_number."\n";
                echo "Sending ACTI for: ".$did_number." 1 S:".$did_number."@".SERVER_SIP_NAME."\n";
                $set_result = NTS_API_send_command($fp, "ACTI ".$did_number);
                echo "Response to ACTI is: ".$set_result;
                if(substr($set_result,0,1)=="0"){//Number successfully activated
                    //Now need to set the number
                    echo "Sending SET for: ".$did_number." 1 S:".$did_number."@".SERVER_SIP_NAME."\n";
                    $set_result = NTS_API_send_command($fp, "SET ".$did_number." 1 S:".$did_number."@".SERVER_SIP_NAME);
                    if(substr($set_result,0,1)=="0"){//Number successfully set
                    	$numbers_block .= $did_number."|";
                    	echo "SUCCESSfully set the number ".$did_number."\n";
                    	$i++; //increment the counter
                    }else{//Failed to set the number
                    	echo "FAILED to SET the number $did_number\n";
                    	//Deactivate the number (If can't be set MUST be DEactivated to prevent fraud)
                    	echo "Deactivating this number\n";
                    	$set_result = NTS_API_send_command($fp, "DEAC ".$did_number);
                		echo "Response to DEAC is: ".$set_result;
                    }	
                }else{//Failed to ACTI the number
                    echo "FAILED to ACTI the number $did_number\n";
                }
        	}else{//Failed to get the number
                echo "FAILED to get single DID number - Response is:".$api_result."\n";
                break;//No continuous looping - just get out of here
                //continue; //Dont worry - just try again!
       	 	}//end of if ALLO success
		}//end of for loop
		echo "Sending disconnect request\n";
        $disc_response = NTS_API_disconnect($fp);
        echo "Response to disconnect is: ".$disc_response."\n";
		echo "Returning numbers to save is: ".$numbers_block."\n";
        return $numbers_block;
}

function NTS_API_request_specific_did($fp,$did,$debug_messages=true){
		 
       	$api_result = NTS_API_send_command($fp, "ALLO ".$did);
       	if($api_result===false){
       		return "ERROR: No response from NTS API for ALLO $did command.";
       	}
        $error_msg = "";
		
        if(substr($api_result,0,1)=="0"){//success
            $set_result = NTS_API_send_command($fp, "ACTI ".$did);
	        if($set_result===false){
	       		return "ERROR: No response from NTS API for ACTI $did command.";
	       	}
            if(substr($set_result,0,1)=="0"){//Number successfully activated
                //Now need to set the number
            	if($debug_messages){
                	echo "Sending SET for: ".$did." 1 S:".$did."@".SERVER_SIP_NAME."\n";
            	}
                $set_result = NTS_API_send_command($fp, "SET ".$did." 1 S:".$did."@".SERVER_SIP_NAME);
	            if($set_result===false){
		       		return "ERROR: No response from NTS API for SET ".$did." 1 S:".$did."@".SERVER_SIP_NAME." command.";
		       	}
                if(substr($set_result,0,1)=="0"){//Number successfully set
                	if($debug_messages){
                    	echo "SUCCESSfully set the number ".$did."\n";
                	}
                    return "SUCCESS";
                }else{//Failed to set the number
                	if($debug_messages){
                    	echo "FAILED to SET the number $did\n";
                	}
                    $error_msg = "Failed to reactive DID Number $did (SET Failed). Response is : ".$set_result;
                    //Deactivate the number (If can't be set MUST be DEactivated to prevent fraud)
                    if($debug_messages){
                    	echo "Deactivating this number\n";
                    }
                    $set_result = NTS_API_send_command($fp, "DEAC ".$did);
                    if($debug_messages){
                		echo "Response to DEAC is: ".$set_result;
                    }
	                if($set_result===false){
		       			return "ERROR: No response from NTS API for DEAC $did command.";
		       		}
                }	
            }else{//Failed to ACTI the number
            	if($debug_messages){
                	echo "FAILED to ACTI the number $did\n";
            	}
                $error_msg = "Failed to reactive DID Number $did (ACTI Failed). Response is : ".$set_result;
            }
        }else{//Failed to get the number
        	if($debug_messages){
            	echo "FAILED to get specific DID number - Response is:".$api_result."\n";
        	}
            $error_msg = "Failed to reactive DID Number $did. Response is : ".$api_result;
       	}//end of if ALLO success

       	if($debug_messages){
			echo "Sending disconnect request\n";
       	}
        $disc_response = NTS_API_disconnect($fp);
        if($debug_messages){
        	echo "Response to disconnect is: ".$disc_response."\n";
        }
        return "ERROR: $error_msg";
}

function NTS_API_request_087did($fp){

		$check_string = MAGRATHEA_087DID_START."_____";//six leading digits followed by five underscores
		$numbers_block = "";
		
		$loop_count = 0;
		
		for($i=0;$i<=9;){//Run until we have 10 successfully set numbers
			$loop_count++;
			if($loop_count>=30){//Maximum 30 attempts 
				break;
			}
			echo "\n$loop_count. Requesting a single DID number...\n"; 
       		$api_result = NTS_API_send_command($fp, "ALLO ".$check_string);
			echo "Response to ALLO ".$check_string." is:\n".$api_result;
        
        	if(substr($api_result,0,1)=="0"){//success
                //Get the number
                $did_number = substr($api_result,2,11);
                echo "DID Number obtained is: ".$did_number."\n";
                echo "Sending ACTI for: ".$did_number." 1 S:".$did_number."@".SERVER_SIP_NAME."\n";
                $set_result = NTS_API_send_command($fp, "ACTI ".$did_number);
                echo "Response to ACTI is: ".$set_result;
                if(substr($set_result,0,1)=="0"){//Number successfully activated
                    //Now need to set the number
                    echo "Sending SET for: ".$did_number." 1 S:".$did_number."@".SERVER_SIP_NAME."\n";
                    $set_result = NTS_API_send_command($fp, "SET ".$did_number." 1 S:".$did_number."@".SERVER_SIP_NAME);
                    if(substr($set_result,0,1)=="0"){//Number successfully set
                    	$numbers_block .= $did_number."|";
                    	echo "SUCCESSfully set the number ".$did_number."\n";
                    	$i++; //increment the counter
                    }else{//Failed to set the number
                    	echo "FAILED to SET the number $did_number\n";
                    	//Deactivate the number (If can't be set MUST be DEactivated to prevent fraud)
                    	echo "Deactivating this number\n";
                    	$set_result = NTS_API_send_command($fp, "DEAC ".$did_number);
                		echo "Response to DEAC is: ".$set_result;
                    }	
                }else{//Failed to ACTI the number
                    echo "FAILED to ACTI the number $did_number\n";
                }
        	}else{//Failed to get the number
                echo "FAILED to get single DID number - Response is:".$api_result."\n";
                continue; //Dont worry - just try again!
       	 	}//end of if ALLO success
		}//end of for loop
		echo "Sending disconnect request\n";
        $disc_response = NTS_API_disconnect($fp);
        echo "Response to disconnect is: ".$disc_response."\n";
		echo "Returning numbers to save is: ".$numbers_block."\n";
        return $numbers_block;
}

function NTS_API_activate_did($nts_api_connection, $did){



}

function NTS_API_setup_did_sip($fp, $did){

	echo "Sending SET for: ".$did." 1 S:".$did."@".SERVER_SIP_NAME."\n";
    $set_result = NTS_API_send_command($fp, "SET ".$did." 1 S:".$did."@".SERVER_SIP_NAME);
    echo "Response to SET is: ".$set_result;

}