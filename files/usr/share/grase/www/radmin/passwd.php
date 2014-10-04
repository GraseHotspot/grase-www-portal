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
$PAGE = 'passwd';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

function getAdminUsers()
{
    global $Auth;
    $users = $Auth->listUsers();
    $adminUsers = array();
    foreach ($users as $user) {
        $adminUsers[$user['username']] = $user['password'];

    }
    return $adminUsers;
}

$adminUsers = getAdminUsers();

$errors = array();
$success = array();

if (isset($_POST['changepasswordsubmit'])) {
    // Change Password
    if (isset($_POST['OldPassword']) &&
        $_POST['OldPassword'] &&
        isset($_POST['NewPassword']) &&
        $_POST['NewPassword'] &&
        isset($_POST['ConfirmPassword']) &&
        $_POST['ConfirmPassword']
    ) {
        if (!$Auth->storage->verifyPassword($_POST['OldPassword'], $adminUsers[$Auth->getUsername()])) {
            $errors[] = T_("Old Password incorrect");
        } elseif ($_POST['NewPassword'] != $_POST['ConfirmPassword']) {
            $errors[] = T_("New passwords don't match");
        } else {
            $Auth->changePassword(
                $Auth->getUsername(),
                $_POST['NewPassword']
            ) or $error_passwd = "Error Changing Password"; // TODO: Check successful
            $success[] = T_("Password Changed");
            AdminLog::getInstance()->log(T_("Password Changed"));
        }
    } else {
        $errors[] = T_("Please fill in all password fields");
    }
}

// Add new admin user
if (isset($_POST['addadminusersubmit'])) {
    if (isset($_POST['newAccessLevel'])) {
        switch ($_POST['newAccessLevel']) {
            case 'admin':
                $newAccessLevel = ADMINUSER;
                break;
            case 'power':
                $newAccessLevel = POWERUSER;
                break;
            case 'normal':
                $newAccessLevel = NORMALUSER;
                break;
            default:
                $errors[] = T_("Invalid Access level");
        }
    } else {
        $errors[] = T_("Need an access level to create admin user");
    }
    if (isset($adminUsers[$_POST['newUsername']])) {
        $errors[] = sprintf(T_("User %s already exists"), $_POST['newUsername']);
    }
    if (trim($_POST['newPassword']) == "" || trim($_POST['newUsername']) == "") {
        $errors[] = T_("Need both username and password");
    }

    if (sizeof($errors) == 0) {
        // Access level is set at creation and can't be changed via the Auth class
        if ($Auth->addUser($_POST['newUsername'], $_POST['newPassword'], array('accesslevel' => $newAccessLevel))) {
            $success[] = T_("User Created");
            AdminLog::getInstance()->log("New Admin User Created, ${_POST['newUsername']}");
        } else {
            $errors[] = T_("Error Creating Admin User");
        }
    }
}

// Delete admin user
if (isset($_POST['deleteadminusersubmit'])) {
    if ($_POST['deleteusername']) {
        $success[] = sprintf(T_("User %s Deleted"), $_POST['deleteusername']);
        $Auth->removeUser($_POST['deleteusername']) or $error_delete = "Error Deleting User";
        AdminLog::getInstance()->log("Admin User Deleted, ${_POST['deleteusername']}");
    } else {
        $errors[] = T_("Invalid Delete Request");
    }
}

$templateEngine->assign("error", $errors);
$templateEngine->assign("success", $success);

$adminUsersDetails = array();
foreach ($Auth->listUsers() as $adminUserDetail) {
    unset($adminUserDetail['password']);
    switch ($adminUserDetail['accesslevel']) {
        case 1:
            $adminUserDetail['accesslevellabel'] = T_("Admin User");
            break;
        case 2:
            $adminUserDetail['accesslevellabel'] = T_("Power User");
            break;
        case 4:
            $adminUserDetail['accesslevellabel'] = T_("Limited User");
            break;
        default:
            $adminUserDetail['accesslevellabel'] = T_("Unknown Access Level");
            break;
    }
    $adminUsersDetails[] = $adminUserDetail;
}


$templateEngine->assign("adminusers", $adminUsersDetails);
$templateEngine->displayPage('changepasswd.tpl');
