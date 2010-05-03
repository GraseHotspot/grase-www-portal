<?php

/*abstract class AdminAuth
{
    abstract protected function addUser($username, $password);
    abstract protected function deleteUser($username);
    abstract protected function changeUserPassword($username, $password);
    abstract protected function validateLogin($username, $password);
    abstract protected function checkAdminUsernameExists($username);
    abstract protected function getUserLogins();    */

class AdminAuth
{    
    const SALT_LENGTH =  9;
    
    function generateSessionAuthToken($username, $password) 
    {
        return crypt($username);
    }
    
    function validateSession() 
    {
    
        return (crypt($_SESSION['username'], $_SESSION['auth']) == $_SESSION['auth']);
    }
    
    function generateHash($plainText, $salt = null) 
    {
    
        if ($salt === null) 
        {
            $salt = substr(md5(uniqid(rand() , true)) , 0, self::SALT_LENGTH);
        }
        else
        {
            $salt = substr($salt, 0, self::SALT_LENGTH);
        }
        
        return $salt . sha1($salt . $plainText);
    }

}

?>
