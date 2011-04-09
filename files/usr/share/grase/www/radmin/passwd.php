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

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

    function getAdminUsers()
    {
        global $Auth;
        $users = $Auth->listUsers();
        foreach($users as $user)
        {
            $admin_users[$user['username']] = $user['password'];

        }
        return $admin_users;
    }
    
    $admin_users = getAdminUsers();

	$errors = array();
	
	if(isset($_POST['changepasswordsubmit'])){
	    if(	isset($_POST['OldPassword']) &&
		    $_POST['OldPassword'] &&
		    isset($_POST['NewPassword']) &&
		    $_POST['NewPassword'] &&
		    isset($_POST['ConfirmPassword']) &&
		    $_POST['ConfirmPassword']) // Change Password
	    {
		    if(! $Auth->storage->verifyPassword($_POST['OldPassword'],$admin_users[$Auth->getUsername()]))
		    {
			    $errors[] = _("Old Password incorrect");
		    }elseif($_POST['NewPassword'] != $_POST['ConfirmPassword'])
		    {
			    $errors[] = _("New passwords don't match");
		    }else
		    {

			    $Auth->changePassword($Auth->getUsername(), $_POST['NewPassword']) or $error_passwd = "Error Changing Password"; // TODO: Check successful
			    $success[] = _("Password Changed");			    
                AdminLog::getInstance()->log(_("Password Changed"));
		    }
	    }else $errors[] = _("Please fill in all password fields");
	}

	if(isset($_POST['addadminusersubmit'])) // Add new admin user
	{
		if(isset($admin_users[$_POST['newUsername']]))
		{
			$errors[] = sprintf(_("User %s already exists"), $_POST['Username']);
		}elseif($_POST['newPassword'] && $_POST['newUsername'])
		{
			$success[] = _("User Created");
			$Auth->addUser($_POST['newUsername'], $_POST['newPassword']) or $error_user = "Error Creating User";
			AdminLog::getInstance()->log("New Admin User Created, ${_POST['newUsername']}");
		}else $errors[] = _("Need both username and password");
	}

	if(isset($_POST['deleteadminusersubmit'])) // Delete admin user
	{
		if($_POST['deleteusername']){
			$success[] = sprintf(_("User %s Deleted"), $_POST['deleteusername']);
			$Auth->removeUser($_POST['deleteusername']) or $error_delete = "Error Deleting User";
			AdminLog::getInstance()->log("Admin User Deleted, ${_POST['deleteusername']}");
		}else $errors[] = _("Invalid Delete Request");
	}
	
	
	$smarty->assign("error", $errors);
	$smarty->assign("success", $success);	


    $admin_users = getAdminUsers();
	$smarty->assign("adminusers", array_keys($admin_users));
	display_page('changepasswd.tpl');

?>


