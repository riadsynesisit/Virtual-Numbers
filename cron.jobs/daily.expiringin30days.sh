#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/expiringin30days.php > /var/log/stickynumber/expiring_30_days_logs/tasks.expiringin30days_"${DOW}".log