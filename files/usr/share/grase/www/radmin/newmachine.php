<?php

/* Copyright 2010 Timothy White */

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
require_once('newuser.php');

$PAGE = 'createmachine';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

function validate_form()
{
    $error = array();
    if (!DatabaseFunctions::getInstance()->checkUniqueUsername($_POST['mac'])) {
        $error[] = T_("MAC Address already has an account");
    }

    $MaxMb = clean_number($_POST['MaxMb']);
    $Max_Mb = clean_number($_POST['Max_Mb']);
    $MaxTime = clean_int($_POST['MaxTime']);
    $Max_Time = clean_int($_POST['Max_Time']);

    if(!\Grase\Validate::MACAddress($_POST['mac'])) {
        $error[] =T_("MAC Address not in correct format");
    }
    if(!\Grase\Validate::numericLimit($MaxMb)) {
        $error[] = sprintf(T_("Invalid value '%s' for Data Limit"), $MaxMb);
    }
    if(!\Grase\Validate::numericLimit($Max_Mb)) {
        $error[] = sprintf(T_("Invalid value '%s' for Data Limit"), $Max_Mb);
    }
    if(!\Grase\Validate::numericLimit($MaxTime)) {
        $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $MaxTime);
    }
    if(!\Grase\Validate::numericLimit($Max_Time)) {
        $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $Max_Time);
    }
    if ($Max_Mb && $MaxMb) {
        $error[] = T_("Only set one Data limit field");
    }
    if ($Max_Time && $MaxTime) {
        $error[] = T_("Only set one Time limit field");
    }
    return array_filter($error);
}


if (isset($_POST['newmachinesubmit'])) {
    $error = validate_form();
    if ($error) {
        $user['mac'] = \Grase\Clean::text($_POST['mac']);
        $user['MaxMb'] = \Grase\Locale::localeNumberFormat(clean_number($_POST['MaxMb']));
        $user['Max_Mb'] = \Grase\Locale::localeNumberFormat(clean_number($_POST['Max_Mb']));
        $user['MaxTime'] = \Grase\Locale::localeNumberFormat(clean_int($_POST['MaxTime']));
        $user['Max_Time'] = \Grase\Locale::localeNumberFormat(clean_int($_POST['Max_Time']));
        $user['Comment'] = \Grase\Clean::text($_POST['Comment']);
        $templateEngine->assign("machine", $user);
        $templateEngine->assign("error", $error);
    } else {
        if (clean_number($_POST['Max_Mb'])) {
            $MaxMb = clean_number($_POST['Max_Mb']);
        }
        if (clean_number($_POST['MaxMb'])) {
            $MaxMb = clean_number($_POST['MaxMb']);
        }
        if (clean_int($_POST['Max_Time'])) {
            $MaxTime = clean_int($_POST['Max_Time']);
        }
        if (clean_int($_POST['MaxTime'])) {
            $MaxTime = clean_int($_POST['MaxTime']);
        }
        $mac = \Grase\Clean::text($_POST['mac']);

        // TODO: Check if successful
        DatabaseFunctions::getInstance()->createUser(
            $mac,
            DatabaseFunctions::getInstance()->getChilliConfigSingle('macpasswd'),
            $MaxMb,
            $MaxTime,
            '--', // No expiry for machine accounts
            false, // No ExpireAfter for machine accounts
            MACHINE_GROUP_NAME, // TODO: This needs to be linked to settings
            \Grase\Clean::text($_POST['Comment'])
        );
        $success[] = T_("Computer Account Successfully Created");
        AdminLog::getInstance()->log("Created new computer $mac");
        $templateEngine->assign("success", $success);
    }
}

$templateEngine->displayPage('addmachine.tpl');
