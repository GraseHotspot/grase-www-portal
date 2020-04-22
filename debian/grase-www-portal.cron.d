# /etc/cron.d/grase-www-poral: crontab fragment for grase-www-portal
# This clears stale sessions and at the end of the month, moves user accounting
# details into the monthly tables clearing the current months table.
# m h	dom mon dow	command

PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
#MAILTO=
## NEEDS USERNAME
@daily		    root    /usr/share/grase/scripts/mysql_backup

# Cron scripts are handled by Symfony4 console command and our runner
@hourly         www-data /usr/share/grase/symfony4/bin/console grase:cron:runner
@reboot         www-data /usr/share/grase/symfony4/bin/console grase:cron:runner
