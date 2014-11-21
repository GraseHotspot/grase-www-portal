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
$PAGE = 'createuser';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

function validate_form($userDetails, $type = 'User')
{
    $error = array();
    if ($type = 'User') {
        if (!DatabaseFunctions::getInstance()->checkUniqueUsername($userDetails['Username'])) {
            $error[] = T_("Username already taken");
        }

        if (!$userDetails['Username'] || !$userDetails['Password']) {
            $error[] = T_("Username and Password are both Required");
        }

    }

    if ($type = 'Computer') {
        if (!DatabaseFunctions::getInstance()->checkUniqueUsername($userDetails['mac'])) {
            $error[] = T_("MAC Address already has an account");
        }

        if(!\Grase\Validate::MACAddress($userDetails['mac'])) {
            $error[] =T_("MAC Address not in correct format");
        }
    }

    if (!\Grase\Validate::numericLimit($userDetails['MaxMb']) && $userDetails['MaxMb'] != '') {
        $error[] = sprintf(T_("Invalid value '%s' for 1 Data Limit"), $userDetails['MaxMb']);
    }
    if (!\Grase\Validate::numericLimit($userDetails['Max_Mb']) && $userDetails['Max_Mb'] != 'inherit') {
        $error[] = sprintf(T_("Invalid value '%s' for Data Limit"), $userDetails['Max_Mb']);
    }
    if (!\Grase\Validate::numericLimit($userDetails['MaxTime']) && $userDetails['MaxTime'] != '') {
        $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $userDetails['MaxTime']);
    }
    if (!\Grase\Validate::numericLimit($userDetails['Max_Time']) && $userDetails['Max_Time'] != 'inherit') {
        $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $userDetails['Max_Time']);
    }
    if ((is_numeric($userDetails['Max_Mb']) || $userDetails['Max_Mb'] == 'inherit') && is_numeric(
        $userDetails['MaxMb']
    )
    ) {
        $error[] = T_("Only set one Data limit field");
    }
    if ((is_numeric($userDetails['Max_Time']) || $userDetails['Max_Time'] == 'inherit') && is_numeric(
        $userDetails['MaxTime']
    )
    ) {
        $error[] = T_("Only set one Time limit field");
    }

    $error[] = validate_group($userDetails['Username'], $userDetails['Group']);
    return array_filter($error);
}

if (isset($_POST['newusersubmit'])) {
    // Fill details from form
    $user['Username'] = \Grase\Clean::username($_POST['Username']);
    $user['Password'] = \Grase\Clean::text($_POST['Password']);

    $user['MaxMb'] = $_POST['MaxMb'];
    $user['Max_Mb'] = clean_number($_POST['Max_Mb']);
    if ($_POST['Max_Mb'] == 'inherit') {
        $user['Max_Mb'] = 'inherit';
    }

    $user['MaxTime'] = $_POST['MaxTime'];
    $user['Max_Time'] = clean_int($_POST['Max_Time']);
    if ($_POST['Max_Time'] == 'inherit') {
        $user['Max_Time'] = 'inherit';
    }

    $user['Group'] = \Grase\Clean::text($_POST['Group']);
    $user['Expiration'] = expiry_for_group(\Grase\Clean::text($_POST['Group']));
    $user['Comment'] = \Grase\Clean::text($_POST['Comment']);

    // Validate details
    $error = validate_form($user);
    if ($error) {
        $templateEngine->assign("user", $user);
        $templateEngine->assign("error", $error);
        $templateEngine->displayPage('adduser.tpl');
        exit();
    } else {

        // Load group settings so we can use Expiry, MaxMb and MaxTime
        $groupSettings = $Settings->getGroup($user['Group']);

        // TODO: Create function to make these the same across all locations
        // Check if we are using the dropdown, or inherit to override the input field
        if (is_numeric($user['Max_Mb'])) {
            $user['MaxMb'] = $user['Max_Mb'];
        } elseif ($user['Max_Mb'] == 'inherit') {
            $user['MaxMb'] = $groupSettings[$user['Group']]['MaxMb'];
        }

        // Check if we are using the dropdown, or inherit to override the input field
        if (is_numeric($user['Max_Time'])) {
            $user['MaxTime'] = $user['Max_Time'];
        } elseif ($user['Max_Time'] == 'inherit') {
            $user['MaxTime'] = $groupSettings[$user['Group']]['MaxTime'];
        }

        // TODO: Check if valid
        DatabaseFunctions::getInstance()->createUser(
            $user['Username'],
            $user['Password'],
            $user['MaxMb'],
            $user['MaxTime'],
            expiry_for_group($user['Group'], $groupSettings),
            $groupSettings[$user['Group']]['ExpireAfter'],
            $user['Group'],
            $user['Comment']
        );
        $success[] = sprintf(T_("User %s Successfully Created"), $user['Username']);
        $success[] = "<a target='_tickets' href='export.php?format=html&user=${user['Username']}'>" .
            sprintf(T_("Print Ticket for %s"), $user['Username']) . "</a>";
        AdminLog::getInstance()->log(sprintf(T_("Created new user %s"), $user['Username']));
        $templateEngine->assign("success", $success);

        // We are now loading the form afresh, ensure we clear the $user array
        $user = array();
    }
}

$user['Password'] = \Grase\Util::randomPassword($Settings->getSetting('passwordLength'));

// TODO: make default settings customisable
$user['Max_Mb'] = 'inherit';
$user['Max_Time'] = 'inherit';
$user['Expiration'] = "--";
$templateEngine->assign("user", $user);
$templateEngine->displayPage('adduser.tpl');
