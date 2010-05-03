<?php

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
