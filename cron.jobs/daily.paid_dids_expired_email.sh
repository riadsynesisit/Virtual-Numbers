#!/bin/sh
DOW="$(date +'%F')"
/usr/bin/php /var/www/html/stickynumber/dashboard/tasks/paid_dids_expired_email_notices.php > /var/log/stickynumber/paid_dids_expired_emails_logs/tasks.paid_dids_expired_email_notices_"${DOW}".log