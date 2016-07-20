#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/refresh_did_set.php > /var/log/stickynumber/tasks.refresh_did_set_"${DOW}".log
