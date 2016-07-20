#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/3month_did_expiry.php > /var/log/stickynumber/3month_did_expiry_logs/tasks.3month_did_expiry_"${DOW}".log