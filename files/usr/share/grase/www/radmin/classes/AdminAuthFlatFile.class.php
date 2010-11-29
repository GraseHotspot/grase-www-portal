<?php

/* Copyright 2008 Timothy White */


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
		    $this->userlogins['admin'] = $this->generateHash("radmin"); // TODO check this default password
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
