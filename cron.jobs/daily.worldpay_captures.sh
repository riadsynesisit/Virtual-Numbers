#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/worldpay_captures.php > /var/log/stickynumber/worldpay_logs/worldpay_captures_"${DOW}".log