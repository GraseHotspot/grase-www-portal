<?php
/* Copyright 2008-2014 Timothy White */

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
namespace Grase\Database;

use Grase\ErrorHandling;

class Database
{
    protected $host = "";
    protected $user = "";
    protected $db = "";
    protected $pass = "";
    public $conn;

    public function __construct($settingsFile = '/etc/grase/radius.conf')
    {
        $this->loadSettingsFromFile($settingsFile);
        $this->conn = new \PDO(
            "mysql:host=" . $this->host . ";dbname=" . $this->db,
            $this->user,
            $this->pass
        );
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    private function loadSettingsFromFile($dbSettingsFile)
    {
        // Check that databaseSettingsFile is valid
        if (!is_readable($dbSettingsFile)) {
            ErrorHandling::fatalNoDatabaseError(
                T_("DB Config File isn't a valid file.") . "($dbSettingsFile)"
            );
        }

        $settings = file($dbSettingsFile);

        foreach ($settings as $setting) {
            list($key, $value) = explode(":", $setting);
            $value = trim($value);
            switch ($key) {
                case 'sql_username':
                    $this->user = $value;
                    break;
                case 'sql_password':
                    $this->pass = $value;
                    break;
                case 'sql_server':
                    $this->host = $value;
                    break;
                case 'sql_database':
                case 'sql_radmindatabase':
                    $this->db = $value;
                    break;
            }
        }
    }
}
