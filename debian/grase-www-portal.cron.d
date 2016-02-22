# /etc/cron.d/grase-www-poral: crontab fragment for grase-www-portal
# This clears stale sessions and at the end of the month, moves user accounting
# details into the monthly tables clearing the current months table.
# m h	dom mon dow	command

PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
#MAILTO=
## NEEDS USERNAME
@daily		    root    /usr/share/grase/scripts/mysql_backup

# Most cron scripts have moved to PHP classes actived by cron.php
@hourly         nobody  REMOTE_ADDR='' php /usr/share/grase/www/radmin/cron.php
@reboot         nobody  REMOTE_ADDR='' php /usr/share/grase/www/radmin/cron.php
