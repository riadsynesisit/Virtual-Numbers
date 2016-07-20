#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/add_ten_087did.php > /var/log/stickynumber/did_087_logs/tasks.add_ten_087did_"${DOW}".log