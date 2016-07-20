<?php
	class Payments_received{
		
		function addPayment($user_id, 
								$chosen_plan, 
								$plan_period, 
								$amount, 
								$pymt_type, 
								$cart_id, 
								$card_logo,
								$card_number, 
								$ip_address, 
								$sip_price_per_min, 
								$did_price_per_month, 
								$did_set_up_price, 
								$minutes_credited,
								$is_new_did,
								$payment_reversed=0,
								$is_auto_renewal=0,
								$gbp_to_aud_rate,
								$pymt_gateway, 
								$wp_transId,
								$wp_transStatus,
								$transTime,
								$pp_sender_trans_id,
								$pp_sender_trans_status,
								$pp_receiver_trans_id,
								$pp_receiver_trans_status,
								$pp_sender_email,
								$auto_renewal=false){
			//Check if we already have a record in payments_received table for this transaction ID
			if($pymt_gateway=="PayPal"){
				$query = "select * from payments_received where pp_receiver_trans_id='$pp_receiver_trans_id'";
			}elseif($pymt_gateway=="WorldPay" || $pymt_gateway=="Credit_Card"){
				$query = "select * from payments_received where wp_transId='$wp_transId'";
			}elseif($pymt_gateway=="AccountCredit"){
				//No need to check for existing record in case payment is from Account Credit
			}else{
				return "Error: Unknown payment gateway : $pymt_gateway for cart ID: $cart_id";
			}
			if($pymt_gateway!="AccountCredit"){
				$result = mysql_query($query);
		        
		        if(mysql_error()==""){
		        	if(mysql_num_rows($result)!=0){
		        		//A record already exists in payments_received for cart_id $cart_id.
		        		//Simply return the id of this record from here
		        		$row = mysql_fetch_array($result);
			        	return $row["id"];
		        	}
		        }else{
		        	return "Error: An error occurred while checking if a payments_recieved record exists for cart ID: $cart_id. Error is: ".mysql_error()." Query is: ".$query;
		        }
			}
	        
			if(trim($pymt_type)=="AccountCredit"){
				$did_price_per_month_local = 0;
				$did_set_up_price_local = 0;
			}else{
				$did_price_per_month_local = $did_price_per_month;
				$did_set_up_price_local = $did_set_up_price;
			}
			if($chosen_plan == ""){
				$chosen_plan_local = 0;
			}else{
				$chosen_plan_local = $chosen_plan;
			}
			if($plan_period == ""){
				$plan_period_local = "M";
			}else{
				$plan_period_local = $plan_period;
			}
			if($card_logo == ""){
				$card_logo_local = "NULL";
			}else{
				$card_logo_local = "'$card_logo'";
			}
			if($card_number==""){
				$card_number_local = "NULL";
			}else{
				$card_number_local = $card_number;
			}
			if(trim($wp_transId)==""){
				$wp_transId_local = "NULL";
			}else{
				$wp_transId_local = "'$wp_transId'";
			}
			if(trim($wp_transStatus)==""){
				$wp_transStatus_local = "NULL";
			}else{
				$wp_transStatus_local = "'$wp_transStatus'";
			}
			if(trim($transTime)=="" || $transTime == "0000-00-00 00:00:00"){
				$transTime_local = "NOW()";
			}else{
				$transTime_local = "'$transTime'";
			}
			if(trim($pp_sender_trans_id)==""){
				$pp_sender_trans_id_local = "NULL";
			}else{
				$pp_sender_trans_id_local = "'$pp_sender_trans_id'";
			}
			if(trim($pp_sender_trans_status)==""){
				$pp_sender_trans_status_local = "NULL";
			}else{
				$pp_sender_trans_status_local = "'$pp_sender_trans_status'";
			}
			if(trim($pp_receiver_trans_id)==""){
				$pp_receiver_trans_id_local = "NULL";
			}else{
				$pp_receiver_trans_id_local = "'$pp_receiver_trans_id'";
			}
			if(trim($pp_receiver_trans_status)==""){
				$pp_receiver_trans_status_local = "NULL";
			}else{
				$pp_receiver_trans_status_local = "'$pp_receiver_trans_status'";
			}
			if(trim($pp_sender_email)==""){
				$pp_sender_email_local = "NULL";
			}else{
				$pp_sender_email_local = "'$pp_sender_email'";
			}
			if($auto_renewal == true){
				$sp_captured = 'Y';
				$sp_details = 'auto renew captured';
			}else{
				$sp_captured = 'N';
				$sp_details = 'not yet';
			}		
			$query = "insert into payments_received (
							user_id, 
							chosen_plan, 
							plan_period,
							amount,
							pymt_type, 
							cart_id, 
							card_logo, 
							card_number, 
							ip_address,
							sip_price_per_min, 
							did_price_per_month, 
							did_set_up_price, 
							minutes_credited,
							is_new_did,
							payment_reversed,
							is_auto_renewal,
							gbp_to_aud_rate, 
							pymt_gateway, 
							wp_transId, 
							wp_transStatus, 
							transTime, 
							pp_sender_trans_id, 
							pp_sender_trans_status, 
							pp_receiver_trans_id, 
							pp_receiver_trans_status, 
							pp_sender_mail,
							sp_captured,
							sp_details
							) values (
							$user_id, 
							$chosen_plan_local, 
							'$plan_period_local',
							$amount,
							'$pymt_type', 
							$cart_id, 
							$card_logo_local,
							$card_number_local, 
							'$ip_address',
							$sip_price_per_min, 
							$did_price_per_month_local, 
							$did_set_up_price_local, 
							$minutes_credited,
							$is_new_did,
							$payment_reversed,
							$is_auto_renewal,
							$gbp_to_aud_rate, 
							'$pymt_gateway', 
							$wp_transId_local, 
							$wp_transStatus_local, 
							$transTime_local, 
							$pp_sender_trans_id_local, 
							$pp_sender_trans_status_local, 
							$pp_receiver_trans_id_local, 
							$pp_receiver_trans_status_local,
							$pp_sender_email_local,
							'$sp_captured',
							'$sp_details'
							)";
			mysql_query($query);
			if(mysql_error() == ""){
				return mysql_insert_id();
			}else{
				return "Error: An error occurred while adding payments_recieved record. Error is: ".mysql_error()." Query is: ".$query;
			}
			
		}
		
		function get_payments_count($user_id){
			
			$query = "SELECT count(*) as payments_count
            				FROM payments_received pr 
            				WHERE pr.user_id = '$user_id' and 
            				pr.payment_reversed=0 and ( 
							(
								`pymt_gateway` =  'Credit_Card'
								and sp_captured =  'Y'
							)OR (
								pymt_gateway <>  'Credit_Card'
							)
						)";
			
			$result = mysql_query($query);
	        
	        if(mysql_error()==""){
		        if(mysql_num_rows($result)!=0){
		        	$row = mysql_fetch_array($result);
		        	return $row["payments_count"];
		        }else{
		        	return 0;
		        }
	        }else{
	        	die("Error: An error occured while getting number of payments received records from pbx_system_db.payments_received table: ".mysql_error());
	        }
			
		}
		
		function for_page_range($user_id, $limit, $offset){
		
			$retArr =   array();
            $query  =   "SELECT pr.id as id, 
            					date_format(pr.date_paid,'%d %b %Y %H:%i:%s') as date_paid, 
            					pr.chosen_plan as chosen_plan, 
            					format(pr.amount,2) as amount, 
            					pr.pymt_gateway as pymt_gateway, 
            					pr.wp_transId as transId, 
            					pr.pp_sender_trans_id as pp_sender_trans_id,
            					pr.pp_receiver_trans_id as pp_receiver_trans_id 
            				FROM payments_received pr
            				WHERE pr.user_id = '$user_id' and 
            				pr.payment_reversed=0 and ( 
								(
									pr.`pymt_gateway` =  'Credit_Card'
									and pr.sp_captured =  'Y'
								)OR (
									pr.pymt_gateway <>  'Credit_Card'
								)
							)
            				ORDER BY pr.id DESC 
            				LIMIT $limit OFFSET $offset;";
            $result =   mysql_query($query);
            if(mysql_num_rows($result)!=0)
            {
                while($row  =   mysql_fetch_assoc($result))
                {
                    $retArr[]   =   $row;
                }
                return $retArr;
            }else{
                return 0;
            }
		
		}
		
		/*function getInvoiceDetails($id,$user_id){
			
			$query = "select ul.name as name, 
								date_format(pr.date_paid,'%d %b %Y %H:%i:%s') as date_paid, 
								date_format(pr.date_paid,'%d %b %Y') as date_paid_dt, 
								dc.country_name,
								pr.id as inv_num, 
								pr.chosen_plan as chosen_plan, 
								pr.did_number as did_number, 
								pr.amount as amount, 
								pr.pymt_gateway as pymt_gateway, 
								if(pymt_gateway = 'Paypal', concat(concat(pymt_gateway,'-'), pr.pp_sender_trans_id), concat(concat(card_logo,'-'), pr.card_number) )  as pp_id,
								 
            					pr.wp_transId as transId, 
            					pr.pp_sender_trans_id as pp_sender_trans_id,
            					pr.pp_receiver_trans_id as pp_receiver_trans_id
							from user_logins ul, 
								payments_received pr,
								did_countries dc ,
                                assigned_dids ad 
							where ul.id=pr.user_id and 
								pr.id=$id and 
								ul.id=$user_id and 
								ad.id = pr.assigned_dids_id and 
								dc.country_prefix = ad.did_country_code and 
								pr.payment_reversed=0";
			$result =   mysql_query($query);
			if(mysql_error()==""){
				if(mysql_num_rows($result)!=0){
		        	$row = mysql_fetch_array($result);
		        	return $row;
		        }else{
		        	die("Error: No invoice for id=$id found in the database.");
		        }
			}else{
				die("Error: Failed to get invoice details from database. Error is: ".mysql_error());
			}
		}
		*/
		function getInvoiceDetails($id,$user_id){
			
			$query = "select ul.name as name, 
								date_format(pr.date_paid,'%d %b %Y %H:%i:%s') as date_paid, 
								date_format(pr.date_paid,'%d %b %Y') as date_paid_dt, 
								dc.country_name,
								pr.id as inv_num, 
								pr.chosen_plan as chosen_plan, 
								pr.did_number as did_number, 
								pr.amount as amount, 
								pr.pymt_gateway as pymt_gateway, 
								if(pymt_gateway = 'Paypal', concat(concat(pymt_gateway,'-'), pr.pp_sender_trans_id), concat(concat(card_logo,'-'), pr.card_number) )  as pp_id,
								 
            					pr.wp_transId as transId, 
            					pr.pp_sender_trans_id as pp_sender_trans_id,
            					pr.pp_receiver_trans_id as pp_receiver_trans_id
							from user_logins ul left join 
							payments_received pr on ul.id=pr.user_id left join
							assigned_dids ad on ad.id = pr.assigned_dids_id left join 
							did_countries dc on dc.country_prefix = ad.did_country_code
                            
							where  pr.id=$id and ul.id=$user_id and pr.payment_reversed=0";
			$result =   mysql_query($query);
			if(mysql_error()==""){
				if(mysql_num_rows($result)!=0){
		        	$row = mysql_fetch_array($result);
		        	return $row;
		        }else{
		        	die("Error: No invoice for id=$id found in the database.");
		        }
			}else{
				die("Error: Failed to get invoice details from database. Error is: ".mysql_error());
			}
		}
		
		function getLastCCPaymentDetails($user_id){
			
			$query = "select date_format(date_paid,'%d %b %Y %H:%i:%s') as date_paid, 
								round(amount,2) as amount,
								card_logo as card_logo,
								card_number as card_number
							from payments_received 
							where pymt_type='CreditCard' and
								user_id=$user_id and
								payment_reversed=0 
								order by date_paid desc limit 1";
			
			$result = mysql_query($query);
	        
	        if(mysql_error()==""){
		        if(mysql_num_rows($result)!=0){
		        	$row = mysql_fetch_array($result);
		        	return $row;
		        }else{
		        	return 0;
		        }
	        }else{
	        	die("Error: An error occured while getting number of payments received records from pbx_system_db.payments_received table: ".mysql_error());
	        }
			
		}
		
		function updateAssigned_DIDs_id($id, $assigned_dids_id, $didww_order_id, $didww_order_status, $didww_did_status, $didww_did_setup, $didww_did_monthly, $did_expiry){
			
			//Get the DID_Number and insert it into the payments_received table
			$query = "select did_number from assigned_dids where id=$assigned_dids_id";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				die("Error: An error occurred while getting the did number from id for did_id $assigned_dids_id. Error is: ".mysql_error());
			}
			if(mysql_num_rows($result)==0){
				die("Error: No records found in assinged_dids for did_id $assigned_dids_id. In Payments_received.updateAssigned_DIDs_id.");
			}
			$row = mysql_fetch_array($result);
			$did_number = $row["did_number"];
			
			$query = "update payments_received set 
								assigned_dids_id = $assigned_dids_id, 
								did_number = $did_number, 
								didww_order_id = '$didww_order_id', 
								didww_order_status = '$didww_order_status', 
								didww_did_status = '$didww_did_status', 
								didww_did_setup = '$didww_did_setup', 
								didww_did_monthly = '$didww_did_monthly', 
								didww_expiry_date_gmt = '$did_expiry' 
							where id=$id";
			mysql_query($query);
			if(mysql_error()==""){
				return true;
			}else{
				die("Error: An error occured while updating payments_received.assigned_dids_id. ".mysql_error()." Query is: ".$query);
			}
			
		}
		
		function reversePayment($id, $reversal_reason){
			
			$query = "update payments_received set payment_reversed = 1, reversal_reason='".mysql_real_escape_string(substr($reversal_reason,0,255))."' where id=$id";
			mysql_query($query);
			if(mysql_error()==""){
				return true;
			}else{
				die("Error: An error occured while updating payments_received for payment reversal. ".mysql_error());
			}
			
		}
		
		function getPaymentDetailsById($id){
			
			$query = "select * from payments_received where id=$id";
			$result = mysql_query($query);
	        if(mysql_error()==""){
		        if(mysql_num_rows($result)!=0){
		        	$row = mysql_fetch_array($result);
		        	return $row;
		        }else{
		        	die("Error: No record found in payments_received for id=$id");
		        }
	        }else{
	        	die("Error: An error occured while getting number of payments received record by id from pbx_system_db.payments_received table: ".mysql_error());
	        }
			
		}
		
		function getPayPalPayKey($id){
			
			$query = "select wp_transId from payments_received where id=$id";
			$result = mysql_query($query);
			if(mysql_error()==""){
				if(mysql_num_rows($result)!=0){
					$row = mysql_fetch_assoc($result);
					return $row["wp_transId"];//Though the field name is wp_transId it contains the PayPal payKey.
				}else{
					return "Error: No rows found while getting PayPal payKey in payments_received for id $id.";
				}
			}else{
				return "Error: An error occurred while getting PayPal payKey from payments_received for id $id. ".mysql_error();
			}
			
		}
		
		function getMonthlySummary(){
			
			$query = "select date_format(pr.date_paid,'%b') as month_name, 
								month(pr.date_paid) as month, 
								year(pr.date_paid) as year, 
								(select count(*) 
									from payments_received pr_new 
									where pr_new.is_new_did=1 and 
										month(pr_new.date_paid)=month(pr.date_paid) and 
										year(pr_new.date_paid)=year(pr.date_paid)
								) as new_did_orders,
								(select count(*) 
									from payments_received pr_renew 
									where pr_renew.is_new_did!=1 and 
										pr_renew.pymt_type!='AccountCredit' and 
										month(pr_renew.date_paid)=month(pr.date_paid) and 
										year(pr_renew.date_paid)=year(pr.date_paid)
								) as renew_did_orders, ";
						$query .= "(select count(*) 
									from auto_renewal_failures arf, 
										shopping_cart sc, 
										assigned_dids ad
									where arf.cart_id=sc.id and 
										sc.did_id=ad.id and 
										SUBSTRING(ad.did_number,1,3)!='087' and 
										SUBSTRING(ad.did_number,1,3)!='070' and 
										month(arf.date_failed)=month(pr.date_paid) and 
										year(arf.date_failed)=year(pr.date_paid)
								) as auto_renewal_failures, ";
						/*$query .= "(select count(*) 
									from deleted_dids dd
									where SUBSTRING(dd.did_number,1,3)!='087' and 
										SUBSTRING(dd.did_number,1,3)!='070' and 
										month(dd.did_deleted_on)=month(pr.date_paid) and 
										year(dd.did_deleted_on)=year(pr.date_paid)
								) as deleted_dids, ";*/
						$query .= "(select ifnull(round(sum(pr_gbp.amount),2),0) 
									from payments_received pr_gbp, 
										user_logins ul 
									where pr_gbp.user_id=ul.id and 
										ul.site_country='UK' and 
										month(pr_gbp.date_paid)=month(pr.date_paid) and 
										year(pr_gbp.date_paid)=year(pr.date_paid)
								) as gbp_amount, 
								(select ifnull(round(sum(pr_aud.amount),2),0) 
									from payments_received pr_aud, 
										user_logins ul 
									where pr_aud.user_id=ul.id and 
										ul.site_country!='UK' and 
										month(pr_aud.date_paid)=month(pr.date_paid) and 
										year(pr_aud.date_paid)=year(pr.date_paid)
								) as aud_amount 
							from payments_received pr 
							where pr.payment_reversed=0 
							group by year, month 
							order by year desc, month desc";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting monthly summary. Error is: ".mysql_error();
			}
			if(mysql_num_rows($result)==0){
				return 0;
			}
			$retArr =   array();
			while($row = mysql_fetch_assoc($result)){
                $retArr[]   =   $row;
            }
            return $retArr;
			
		}
		
		function getMonthlyDetails($month_num, $year, $did_order_type){
			
			if($did_order_type==1){
				$is_new_did=1;
			}else{
				$is_new_did=0;
			}
			
			$query = "select concat('M',pr.user_id+10000) as member_id, 
								round(pr.amount,2) as amount, 
								if(ul.site_country='UK','GBP','AUD') as currency, 
								date_format(pr.date_paid,'%d %b %Y %H:%i:%s') as date_paid, 
								pr.did_number as did_number, 
								pr.pymt_gateway as pymt_gateway, 
								if(pr.pymt_gateway='WorldPay',pr.wp_transId,if(pr.pymt_gateway='PayPal',pr.pp_receiver_trans_id,'N/A')) as trans_id, 
								pr.pymt_type as pymt_type 
							from payments_received pr, 
								user_logins ul
							where pr.user_id=ul.id and 
								month(pr.date_paid)=".mysql_real_escape_string($month_num)." and 
								year(pr.date_paid)=".mysql_real_escape_string($year)." and 
								is_new_did =".mysql_real_escape_string($is_new_did)." 
							order by pr.date_paid asc";
			$result = mysql_query($query);
			if(mysql_error()!=""){
				return "Error: An error occurred while getting monthly details of month $month_num/$year for DID Order Type $did_order_type. Error is: ".mysql_error();
			}
			if(mysql_num_rows($result)==0){
				return 0;
			}
			$retArr =   array();
			while($row = mysql_fetch_assoc($result)){
                $retArr[]   =   $row;
            }
            return $retArr;
			
		}
		
		function updateSPcaptured($cart_id, $capture_id, $user_id=""){
			$query = "update payments_received set sp_captured = 'Y', sp_details = '".$capture_id."' where cart_id =".$cart_id;
			if($user_id){
				$query .= " and user_id = " . $user_id. " ";
			}
			
			mysql_query($query);
			if(mysql_error()==""){
				return true;
			}else{
				return "Error: An error occured while updating payments_received.spcaptured. ".mysql_error()." Query is: ".$query;
			}
		}
		
		function isCaptured($lastPayID){
			$query = "select sp_captured from payments_received where id=$lastPayID";
			$result = mysql_query($query);
	        if(mysql_error()==""){
				$row = mysql_fetch_assoc($result);
				if($row['sp_captured'] == 'Y'){
					return true;
				}
		    }
			
			return false;
		}
		
		function deletePayment($id){
			$query = "delete from payments_received where id =".$id;
			
			mysql_query($query);
			if(mysql_error()==""){
				return true;
			}else{
				return "Error: An error occured while updating payments_received.spcaptured. ".mysql_error()." Query is: ".$query;
			}
		}
	}