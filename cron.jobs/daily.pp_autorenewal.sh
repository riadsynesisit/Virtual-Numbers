#!/bin/sh
#DOW="$(date +'%a')"
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/daily_paypal_future_pay_did_renewals.php > /var/log/stickynumber/paypal_daily_renewals_logs/tasks.daily_paypal_future_pay_did_renewals_"${DOW}".log