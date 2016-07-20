#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/7day_did_expiry.php > /var/log/stickynumber/7day_did_expiry_logs/tasks.7day_did_expiry_"${DOW}".log
