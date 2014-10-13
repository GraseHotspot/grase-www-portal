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
$PAGE = 'edituser';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

// Check if _GET['username'] is set, and that the username exists (checkUniqueUsername returns true if it doesn't exist)
if (!isset($_GET['username']) || DatabaseFunctions::getInstance()->checkUniqueUsername($_GET['username'])) {
    // Redirect to display all users
    header("Location: display");
    exit();
}

// Display single user, in detail
$error = array();
$success = array();

$username = mysql_real_escape_string(
    $_GET['username']
); // TODO change this? i.e. make database class do it if it doesn't already
$user = DatabaseFunctions::getInstance()->getUserDetails($_GET['username']);

if (isset($_POST['updateusersubmit'])) {   // Process form for changed items and do updates
    $addMb = clean_number($_POST['Add_Mb']);
    $maxMb = clean_number($_POST['MaxMb']);
    $addTime = clean_number($_POST['Add_Time']);
    $maxTime = clean_number($_POST['MaxTime']);

    // Update password
    if (\Grase\Clean::text($_POST['Password']) && \Grase\Clean::text($_POST['Password']) != $user['Password']) {
        DatabaseFunctions::getInstance()->setUserPassword($username, \Grase\Clean::text($_POST['Password']));
        // TODO: Check return for success
        $success[] = T_("Password Changed");
        AdminLog::getInstance()->log("Password changed for $username");
    }

    // Update group if changed
    if (\Grase\Clean::text($_POST['Group']) && \Grase\Clean::text($_POST['Group']) != $user['Group']) {
        $temperror = validate_group($username, $_POST['Group']);
        if (array_filter($temperror)) {
            $error = array_merge($error, $temperror);
        } else {
            DatabaseFunctions::getInstance()->setUserGroup($username, \Grase\Clean::text($_POST['Group']));
            DatabaseFunctions::getInstance()->setUserExpiry(
                $username,
                expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username))
            );
            // TODO: Check return for success
            $success[] = T_("Group Changed");
            AdminLog::getInstance()->log("Group changed for $username");
        }
    }

    // Update comment if changed
    if (\Grase\Clean::text($_POST['Comment']) != $user['Comment']) {
        DatabaseFunctions::getInstance()->setUserComment($username, \Grase\Clean::text($_POST['Comment']));
        // TODO: Check return for success
        $success[] = T_("Comment Changed");
        AdminLog::getInstance()->log("Comment changed for $username");
    }

    // Lock/Unlock update
    if (\Grase\Clean::text($_POST['LockReason']) != $user['LockReason']) {
        if (\Grase\Clean::text($_POST['LockReason']) == '') {
            DatabaseFunctions::getInstance()->unlockUser($username);
            $success[] = T_("User Account Unlocked");
            AdminLog::getInstance()->log("Account $username unlocked");
        } else {
            // Using \Grase\Clean::username as the LockReason is processed by JSON from CoovaChilli from Radius and so ' and " don't carry well
            DatabaseFunctions::getInstance()->lockUser($username, \Grase\Clean::username($_POST['LockReason']));
            $success[] = T_("User Account Locked");
            AdminLog::getInstance()->log(
                "Account $username locked: " . \Grase\Clean::username($_POST['LockReason'])
            );
        }
    }

    // Increase Data Limit

    if ($addMb) {
        if (!\Grase\Validate::numericLimit($addMb)) {
            $error[] = sprintf(T_("Invalid value '%s' for Data Limit"),$addMb);
        } else {
            DatabaseFunctions::getInstance()->increaseUserDatalimit($username, $addMb);
            DatabaseFunctions::getInstance()->setUserExpiry(
                $username,
                expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username))
            );
            // TODO: Check return for success
            $success[] = T_("Data Limit Increased");
            AdminLog::getInstance()->log(sprintf(T_("Data Limit increased for %s"), $username));
        }
    }

    // If Data Limit is changed and Not added too, Change Data Limit
    if ($maxMb !== ''
        && !$addMb
        && $maxMb != clean_number($user['MaxMb'])
    ) {
        if (!\Grase\Validate::numericLimit($maxMb)) {
            $error[] = sprintf(T_("Invalid value '%s' for Data Limit"),$maxMb);
        } else {
            DatabaseFunctions::getInstance()->setUserDataLimit($username, clean_number($_POST['MaxMb']));
            DatabaseFunctions::getInstance()->setUserExpiry(
                $username,
                expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username))
            );
            // TODO: Check return for success
            $success[] = T_("Max Data Limit Updated");
            AdminLog::getInstance()->log(sprintf(T_("Max Data Limit changed for %s"), $username));
        }
    }

    // Increase Time Limit
    if ($addTime) {
        if (!\Grase\Validate::numericLimit($addTime)) {
            $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $addTime);
        } else {
            DatabaseFunctions::getInstance()->increaseUserTimelimit($username, $addTime);
            DatabaseFunctions::getInstance()->setUserExpiry(
                $username,
                expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username))
            );
            // TODO: Check return for success
            $success[] = T_("Time Limit Increased");
            AdminLog::getInstance()->log(sprintf(T_("Time Limit increased for %s"), $username));
        }
    }

    // If Time Limit is changed and Not added too, Change Time Limit
    if ($maxTime !== ''
        && !$addTime
        && $maxTime != $user['MaxTime']
    ) {
        if (!\Grase\Validate::numericLimit($maxTime)) {
            $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $maxTime);
        } else {
            DatabaseFunctions::getInstance()->setUserTimeLimit($username, $maxTime);
            DatabaseFunctions::getInstance()->setUserExpiry(
                $username,
                expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username))
            );
            // TODO: Check return for success
            $success[] = T_("Max Time Limit Updated");
            AdminLog::getInstance()->log(sprintf(T_("Max Time Limit changed for %s"), $username));
        }
    }

}

if (isset($_POST['unexpiresubmit'])) {
    DatabaseFunctions::getInstance()->setUserExpiry(
        $username,
        expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username))
    );
    $success[] = T_("Expiry updated");
}

// Delete User
if (isset($_POST['deleteusersubmit'])) {
    DatabaseFunctions::getInstance()->deleteUser($username); // TODO: Check for success
    $success[] = sprintf(T_("User '%s' Deleted"), $username);
    AdminLog::getInstance()->log("User $username deleted");
    $templateEngine->assign("error", $error);
    $templateEngine->assign("success", $success);
    require('display.php');
    die; // TODO: Recode so don't need die (too many nests?)

}

$templateEngine->assign("error", $error);
$templateEngine->assign("success", $success);

// if $success we need to reload the info
if (sizeof($success) > 0 || sizeof($error) > 0) {
    $user = DatabaseFunctions::getInstance()->getUserDetails($_GET['username']);
}

// After potential reload, we can assign it to smarty
$templateEngine->assign("user", $user);

// After all user details are loaded, we can load our warning
if ($user['AccountLock'] == true) {
    $templateEngine->warningMessage(T_('User account is locked and will not be able to login'));
}

$templateEngine->displayPage('edituser.tpl');
