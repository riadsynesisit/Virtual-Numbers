#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/activation_reminder.php > /var/log/stickynumber/activation_reminder_logs/tasks.activation_reminder_"${DOW}".log