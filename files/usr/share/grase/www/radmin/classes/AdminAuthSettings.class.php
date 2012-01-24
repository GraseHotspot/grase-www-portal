<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://hotspot.purewhite.id.au/

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

// TODO: This appears to be obsolete, replaced by Auth Class, remove old code

class AdminAuthSettings extends AdminAuth
{
    private $userlogins;
    private $Settings;
    
    function __construct($Settings)
    {
        $this->Settings = $Settings;
        $this->load_admin_users();
    }
    
    public function getUserLogins()
    {
        return $this->userlogins;
    }
    
    public function importUserLogins($userlogins)
    {
        // Overwrites admin user database with $userlogins !! DANGER
        $this->userlogins = $userlogins;
        $this->save_admin_users();
    }

    private function load_admin_users()
    {
	    $this->userlogins = unserialize($this->Settings->getSetting('admin_users'));
	    if(!is_array($this->userlogins))
	    { // No users in admin file Attempt to import old users from flatfile
		    //$this->userlogins['admin'] = $this->generateHash("radmin");
		    $AdminAuthOld = new AdminAuthFlatFile();
		    $this->importUserLogins($AdminAuthOld->getUserLogins());
	    }
    //	Setup default username/password as $userlogins['user'] = "password";
    //	TODO

    }

    function save_admin_users()
    {
	    return $this->Settings->setSetting('admin_users', serialize($this->userlogins));
    }		

    public function changeUserPassword($username, $password)
    {
	    $this->userlogins[$username] = $this->generateHash($password);
	    return $this->save_admin_users();
    }
    
    public function addUser($username, $password)
    {
        return $this->changeUserPassword($username, $password);
    }

    public function deleteUser($username)
    {
	    unset($this->userlogins[$username]);
	    return $this->save_admin_users();
    }
    
    public function validateLogin($username, $password) 
    {
        return (isset($this->userlogins[$username]) && $this->userlogins[$username] == $this->generateHash($password, $this->userlogins[$username]) && $password != '');
    }
    
    function checkAdminUsernameExists($username) 
    {
        return (isset($this->userlogins[$username]));
    }

}
