#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/daily_simplepay_future_pay_did_renewals.php > /var/log/stickynumber/simplepay_daily_renewals_logs/tasks.daily_simplepay_future_pay_did_renewals_"${DOW}".log