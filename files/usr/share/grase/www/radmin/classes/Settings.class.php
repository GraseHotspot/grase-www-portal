<?php

/*
locationName

admin_users_passwd_file (If using flat file for admin interface)

database_config_file /etc/radmin.conf

/etc/radmin.conf contains
sql_type: mysql
sql_server: localhost
sql_username: root
sql_password: 
sql_database: radius
sql_command: /usr/bin/mysql
These settings are used by other applications and scripts (non-php ones)
They are also the ones required to connect to the database

currency
location
price
sellabledata
useabledata



*/


abstract class Settings
{
    abstract protected function setSetting($setting, $value);
    abstract protected function getSetting($setting);
}
?>
