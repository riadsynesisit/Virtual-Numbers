#!/bin/sh
MAILTO=""
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/remove_unassigned_dids.php > /var/log/stickynumber/remove_unassigned_did_logs/tasks.remove_unassigned_dids_"${DOW}".log