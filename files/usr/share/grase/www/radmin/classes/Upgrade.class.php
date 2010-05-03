<?php

class Upgrade
{
    function upgradeAdminUsers($userfile, $Auth)
    {
        AdminLog::getInstance()->log("Performing Upgrade on Auth for Admin Users");        
        $AdminAuth = new AdminAuthFlatFile($userfile);
        $userlogins = $AdminAuth->getUserLogins();
        foreach($userlogins as $user => $password){
            $sql = "INSERT INTO auth (
                    username, 
                    password
                    )
                    VALUES
                    (
                    '$user', 
                    '$password'
                    )";
            $Auth->query($sql);
        }
        
    }
    
    function upgradeSettings($Settings)
    {
    
    
    }
}
?>
