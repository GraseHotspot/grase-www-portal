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

require_once 'includes/usermin_session.inc.php';
require_once 'includes/database_functions.inc.php';


if(isset($_GET['history']))
{
    $smarty->assign("sessions", DatabaseFunctions::getInstance()->getRadiusUserSessionsDetails($Auth->getUsername()));
    display_page('usermin_history.tpl');
}
else
{
    $error = array();
    if(isset($_POST['changepasswordsubmit']))
    {
        $newpass1 = trim($_POST['NewPassword']);
        $newpass2 = trim($_POST['PasswordVerify']);        
        // Work on changing password
        if($newpass1 != $newpass2)
        {
            $error[] = T_("New Passwords must match");
        }elseif($newpass1 == '')
        {
            $error[] = T_("Password must not be blank");
        }else
        {
            if(database_change_password($Auth->getUsername(), $newpass1))
            {
                $success[] = T_("Password Changed");
            }else
            {
                $error[] = T_("Password not updated");            
            }
        }

    }
    
    $smarty->assign("error", array_filter($error));
    $smarty->assign("success", $success);
    $smarty->assign("user", DatabaseFunctions::getInstance()->getUserDetails($Auth->getUsername()));
    display_page('usermin_userdetails.tpl');

}
?>

