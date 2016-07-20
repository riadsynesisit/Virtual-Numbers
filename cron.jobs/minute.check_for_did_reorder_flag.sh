#!/bin/sh
DOW="$(date +'%F-%H-%M')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/add_ten_did.php > /var/log/stickynumber/did_070_logs/tasks.add_ten_did_"${DOW}".log