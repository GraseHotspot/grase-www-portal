<?php

/* Copyright 2012 Timothy White */

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
$PAGE = 'users';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

$DBF = DatabaseFunctions::getInstance();

if (isset($_GET['user'])) {
    $users = $DBF->getMultipleUsersDetails(array(\Grase\Clean::text($_GET['user'])));
    if (!is_array($users)) {
        $users = array();
    }
    $title = \Grase\Clean::text($_GET['user']) . ' Voucher';
} elseif (isset($_GET['batch'])) {
    $batches = explode(',', $_GET['batch']);
    $users = array();

    foreach ($batches as $batch) {
        $batch = clean_number($batch);
        $usersInBatch = $DBF->getMultipleUsersDetails($Settings->getBatch($batch));
        if (is_array($usersInBatch)) {
            $users = array_merge($users, $usersInBatch);
        }
    }
    // TODO: replace , with _ in below
    $title = sprintf(T_('Batch_%s_details'), implode('-', $batches));

} elseif (isset($_GET['group'])) {
    $groups = explode(',', $_GET['group']);
    $users = array();

    foreach ($groups as $group) {
        $group = clean_groupname($group);
        $usersInGroup = $DBF->getMultipleUsersDetails($DBF->getUsersByGroup($group));
        if (is_array($usersInGroup)) {
            $users = array_merge($users, $usersInGroup);
        }
    }
    $title = sprintf(T_('Group_%s_details'), implode('-', $groups));

} else {
    echo T_("Need a group or batch");
    // TODO Use error template
    exit;
}

if ($_GET['format'] == 'csv') {
    generate_csv($users, $title);
} elseif ($_GET['format'] == 'html') {
    printTickets($users, $title);
} else {
    // TODO error page
    echo T_("Need valid format");
    exit;
}

function generate_csv($users, $title)
{
    $groupSettings = grouplist();

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=$title.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $details = array();
    $details[] = array(
        T_("Username"),
        T_("Password"),
        T_("Expiry"),
        T_("Voucher Type")
    );

    foreach ($users as $user) {
        $expiry = $user['FormatExpiration'];
        if ($user['FormatExpiration'] == '--') {
            $expiry = '';
        }
        // TODO? If the group is deleted, we can still export users in it, but groupSettings is NULL
        $details[] = array(
            $user['Username'],
            $user['Password'],
            $expiry,
            $groupSettings[$user['Group']]
        );
    }

    // Following based off http://www.php.net/manual/en/function.fputcsv.php#100033
    $outstream = fopen("php://output", 'w');

    function __outputCSV(&$vals, $key, $filehandler)
    {
        fputcsv($filehandler, $vals);
    }

    array_walk($details, '__outputCSV', $outstream);
    fclose($outstream);
}

function printTickets($users, $title)
{
    global $templateEngine, $Settings;
    $users_groups = sort_users_into_groups($users);
    $templateEngine->assign("batchTitle", $title);
    $templateEngine->assign("users", $users);
    $templateEngine->assign("users_groups", $users_groups);
    $templateEngine->assign("groupsettings", grouplist());
    $templateEngine->assign("networksettings", unserialize($Settings->getSetting('networkoptions')));
    $templateEngine->displayPage('printnewtickets.tpl');
}
