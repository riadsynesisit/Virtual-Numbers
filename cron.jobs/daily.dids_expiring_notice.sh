#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/dids_expiring_notice.php > /var/log/stickynumber/did_expiring_notice_logs/tasks.dids_expiring_notice_"${DOW}".log