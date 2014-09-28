<?php




class Auth_Container_MDB2_Salt extends Auth_Container_MDB2
{
    function __construct($dsn)
    {
        parent::__construct($dsn);
    }
    
    /**
     * Crypt and verfiy the entered password
     *
     * @param  string Entered password
     * @param  string Password from the data container (usually this password
     *                is already encrypted.
     * @param  string Type of algorithm with which the password from
     *                the container has been crypted. (md5, crypt etc.)
     *                Defaults to "md5".
     * @return bool   True, if the passwords match
     */
    public function verifyPassword($password1, $password2, $cryptType = "sha1salt")
    {
        $this->log('Auth_Container::verifyPassword() called.', AUTH_LOG_DEBUG);
        switch ($cryptType) {
            case "crypt" :
                return ((string)crypt($password1, $password2) === (string)$password2);
                break;
            case "sha1salt" :
                return ((string)$this->sha1salt($password1, $password2) === (string)$password2);
                break;                
            case "none" :
            case "" :
                return ((string)$password1 === (string)$password2);
                break;
            case "md5" :
                return ((string)md5($password1) === (string)$password2);
                break;
            default :
                if (function_exists($cryptType)) {
                    return ((string)$cryptType($password1) === (string)$password2);
                } elseif (method_exists($this,$cryptType)) {
                    return ((string)$this->$cryptType($password1) === (string)$password2);
                } else {
                    return false;
                }
                break;
        }
    }
    
    function sha1salt($plainText, $salt = null) 
    {
        $SALT_LENGTH = 9;
        if ($salt === null) 
        {
            $salt = substr(md5(uniqid(rand() , true)) , 0, $SALT_LENGTH);
        }
        else
        {
            $salt = substr($salt, 0, $SALT_LENGTH);
        }
        
        return $salt . sha1($salt . $plainText);
    }        

}

/*
 * Due to a bug in the MDB2 Container, we need this function to be global and not
 * a method in the class like it should be.
 * We could modify the MDB2 container (around https://github.com/pear/Auth/blob/trunk/Auth/Container/MDB2.php#L572)
 * so that it also checked if the method exists like the verifyPassword
 * method, however the simple fix for now is to include it in the global
 * scope.
 */
function sha1salt($plainText, $salt = null)
{
    $SALT_LENGTH = 9;
    if ($salt === null)
    {
        $salt = substr(md5(uniqid(rand() , true)) , 0, $SALT_LENGTH);
    }
    else
    {
        $salt = substr($salt, 0, $SALT_LENGTH);
    }

    return $salt . sha1($salt . $plainText);
}