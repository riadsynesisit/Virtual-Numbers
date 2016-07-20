#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/populate_did_cities.php > /var/log/stickynumber/populate_did_city_logs/tasks.populate_did_cities_"${DOW}".log