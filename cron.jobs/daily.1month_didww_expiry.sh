#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/1month_didww_expiry.php > /var/log/stickynumber/1month_didww_expiry_logs/tasks.1month_didww_expiry_"${DOW}".log