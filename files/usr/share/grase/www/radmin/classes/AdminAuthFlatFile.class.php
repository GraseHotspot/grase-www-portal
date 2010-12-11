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


/*function __autoload($class_name) {
    require_once $class_name . '.class.php';
}*/

class AdminAuthFlatFile extends AdminAuth
{
    private $userlogins;
    private $userfile;
    
    function __construct($userfile = '/etc/radmin/admin_users_passwd')
    {
        $this->userfile = $userfile;
        $this->load_admin_users();
    }
    
    public function getUserLogins()
    {
        return $this->userlogins;
    }

    private function load_admin_users()
    {
	    $this->userlogins = unserialize(file_get_contents($this->userfile, FILE_SKIP_EMPTY_LINES));
	    if(!is_array($this->userlogins))
	    { // No users in admin file (TODO)
		    $this->userlogins['admin'] = $this->generateHash("radmin"); // TODO: check this default password
	    }
    //	Setup default username/password as $userlogins['user'] = "password";
    //	TODO
    //	Default username password in admin_interface_file of format a:1:{s:5:"admin";s:8:"password";}

    }

    function save_admin_users()
    {
	    return file_put_contents($this->userfile, serialize($this->userlogins));
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
?>
