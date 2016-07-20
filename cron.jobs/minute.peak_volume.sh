#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/peak_volume.php > /var/log/stickynumber/peak_volume_logs/tasks.peak_volume_"${DOW}".log