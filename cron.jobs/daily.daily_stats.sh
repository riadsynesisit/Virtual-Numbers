#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/daily_stats.php > /var/log/stickynumber/daily_stat_logs/tasks.daily_stats_"${DOW}".log