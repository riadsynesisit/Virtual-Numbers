#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/7day_voicemail_delete.php > /var/log/stickynumber/7day_voicemail_delete_logs/tasks.7day_voicemail_delete_"${DOW}".log