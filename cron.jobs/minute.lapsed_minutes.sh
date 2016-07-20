#!/bin/sh
DOW="$(date +'%F-%H-%M')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/lapsed_minutes.php > /var/log/stickynumber/minutes_lapsed_logs/tasks.minutes_lapsed_"${DOW}".log