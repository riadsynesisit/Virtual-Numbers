<?php

session_start();//Use sessions
ini_set("display_errors",0);

$getID  =   $_GET['id'];
$type   =   $_GET['type'];

//list of required files
require("./includes/config.inc.php");
require("./includes/db.inc.php");
require("./includes/define_admin_menu.inc.php");
require("./includes/define_common_header.inc.php");

//list of required libraries
require("./libs/ast_realtime_dialplan.lib.php");
require("./libs/jkl5_common.php");
require("./libs/system_status.lib.php");
require("./libs/user_maintenance.lib.php");
require("./libs/didww.lib.php");
require("./libs/worldpay.lib.php");
require("./libs/utilities.lib.php");
require("./Net/SSH2.php");

//list of required classes
require("./classes/Assigned_DID.class.php");
require("./classes/AST_RT_Extensions.class.php");
require("./classes/AsteriskSQLiteCDR.class.php");
require("./classes/VoiceMail.class.php");
require("./classes/DCMOriginationConfig.class.php");
require("./classes/SystemConfigOptions.class.php");
require("./classes/SystemMessages.class.php");
require("./classes/Unassigned_DID.class.php");
require("./classes/Unassigned_087DID.class.php");
require("./classes/User.class.php");
require("./classes/UserMessages.class.php");
require("./classes/WebPage.class.php");
require("./classes/Member.class.php");
require("./classes/Access.class.php");
require("./classes/FraudDetection.class.php");
require("./classes/Block_Destination.class.php");
require("./classes/In_Block_Destination.class.php");
require("./classes/Daily_Stats.class.php");
require("./classes/Country.class.php");
require("./classes/Member_credits.class.php");
require("./classes/Payments_received.class.php");
require("./classes/DidwwAPI.class.php");
require("./classes/CC_Details.class.php");
require("./classes/Did_Countries.class.php");
require("./classes/Did_Cities.class.php");
require("./classes/Did_Plans.class.php");
require("./classes/DCMDestinationCosts.class.php");
require("./classes/Shopping_cart.class.php");
require("./classes/Worldpay_preapprovals.class.php");
require("./classes/Paypal_preapprovals.class.php");
require("./classes/SimplePay_preapprovals.class.php");
require("./classes/Risk_score.class.php");
require("./classes/Blacklist_ip.class.php");
require("./classes/Deleted_DID.class.php");
require("./classes/Client_notifications.class.php");
require("./classes/Auto_Renewal_Failures.class.php");

//list of required pages
require("./pages/account_details.inc.php");
require("./pages/call_records.inc.php");
require("./pages/voice_mails.inc.php");
require("./pages/view_records.inc.php");
require("./pages/change_password_done.inc.php");
require("./pages/change_password.inc.php");
require("./pages/dcm_csv_upload.inc.php");
require("./pages/dest_costs_thresholds.inc.php");
require("./pages/did_numbers.inc.php");
require("./pages/footer.inc.php");
require("./pages/index.inc.php");
require("./pages/login.inc.php");
require("./pages/logout.pending_text.inc.php");
require("./pages/setup.inc.php");
require("./pages/standard.php");
require("./pages/system_notices_new.inc.php");
require("./pages/system_notices_show.inc.php");
require("./pages/system_status.inc.php");
require("./pages/update_account_details.inc.php");
require("./pages/update_route.inc.php");
require("./pages/search.inc.php");
require("./pages/fraud_detection.inc.php");
require("./pages/fraud-detection-notices.inc.php");
require("./pages/asterisk_errors.inc.php");
require("./pages/asterisk_archieve_errors.inc.php");
require("./pages/fraud-detection-archieve-notices.inc.php");
require("./pages/block_destinations.inc.php");
require("./pages/inblock_destinations.inc.php");
require("./pages/add-number-1.inc.php");
require("./pages/payment_methods.inc.php");
require("./pages/add_card_number.inc.php");
require("./pages/update_card.inc.php");
require("./pages/invoices.inc.php");
?>