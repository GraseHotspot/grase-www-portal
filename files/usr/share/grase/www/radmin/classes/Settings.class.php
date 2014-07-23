<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://grasehotspot.org/

    GRASE Hotspot is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GRASE Hotspot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GRASE Hotspot.  If not, see <http://www.gnu.org/licenses/>.
*/

/* TODO: Update this

database_config_file /etc/grase/radmin.conf and /etc/grase/radius.conf

/etc/grase/radmin.conf and /etc/grase/radius.conf format
sql_type: mysql
sql_server: localhost
sql_username: root
sql_password: 
sql_database: radius
sql_command: /usr/bin/mysql

These settings are used by other applications and scripts (non-php ones)
They are also the ones required to connect to the databases


*/


abstract class Settings
{
    abstract protected function setSetting($setting, $value);
    abstract protected function getSetting($setting);
}
?>
