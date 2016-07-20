#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/monthly_renew_of_annual_plans.php > /var/log/stickynumber/monthly_renew_annual_logs/tasks.monthly_renew_of_annual_plans_"${DOW}".log
