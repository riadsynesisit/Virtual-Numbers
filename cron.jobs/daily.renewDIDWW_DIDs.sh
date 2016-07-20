#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/renewDIDWW_DIDs.php > /var/log/stickynumber/tasks.renewDIDWW_DIDs_"${DOW}".log