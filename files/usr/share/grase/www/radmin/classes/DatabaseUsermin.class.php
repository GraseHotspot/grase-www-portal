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

class DatabaseUsermin
{
    private $db;

    private $users = array();

    public function __construct($db)
    {
        $this->db =& $db;
        $this->loadusers();
    }

    private function loadusers()
    {
        $sql = "SELECT UserName, Value FROM radcheck WHERE Attribute = 'Cleartext-Password'";

        $res =& $this->db->query($sql);

        // Always check that result is not an error
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }

        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);

        foreach ($results as $user) {
            $users[$user['UserName']] = $user['Value'];
        }

        $this->users = $users;
    }

    public function getUsers()
    {

        return $this->users;
    }
}
