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
$PAGE = 'groups';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

$error = array();
$success = array();

if (isset($_POST['submit'])) {
    /* Filter out blanks. Key index is maintained with array_filter so name->expiry association is maintained */
    $groupNames = $_POST['groupname'];
    $groupComment = array_filter($_POST['groupcomment']);
    $groupExpiry = array_filter($_POST['groupexpiry']);
    $groupExpireAfter = array_filter($_POST['groupexpireafter']);
    $groupDataLimit = array_filter($_POST['Group_Max_Mb']);
    $groupTimeLimit = array_filter($_POST['Group_Max_Time']);
    $groupBandwidthDownLimit = array_filter($_POST['Bandwidth_Down_Limit']);
    $groupBandwidthUpLimit = array_filter($_POST['Bandwidth_Up_Limit']);
    $groupRecurDataLimit = array_filter($_POST['Recur_Data_Limit']);
    $groupRecurData = array_filter($_POST['Recur_Data']);
    $groupRecurTimeLimit = array_filter($_POST['Recur_Time_Limit']);
    $groupRecurTime = array_filter($_POST['Recur_Time']);
    $groupSimultaneousUse = array_filter($_POST['SimultaneousUse']);
    $groupLoginTime = array_filter($_POST['LoginTime']);

    if (sizeof($groupNames) == 0) {
        $error[] = T_("A minimum of one group is required");
    }
    if (sizeof($groupExpiry) < sizeof($groupNames) - 1) {
        $success[] = T_("It is not recommended having groups without expiries.");
    }

    foreach ($groupNames as $key => $name) {
        // There are attributes set but no group name
        if (\Grase\Clean::text($name) == '') {
            if (
                isset($groupComment[$key]) ||
                isset($groupExpiry[$key]) ||
                isset($groupExpireAfter[$key]) ||
                isset($groupDataLimit[$key]) ||
                isset($groupTimeLimit[$key]) ||
                isset($groupBandwidthDownLimit[$key]) ||
                isset($groupBandwidthUpLimit[$key]) ||
                isset($groupRecurDataLimit[$key]) ||
                isset($groupRecurData[$key]) ||
                isset($groupRecurTimeLimit[$key]) ||
                isset($groupRecurTime[$key]) ||
                isset($groupLoginTime[$key])
            ) {
                $error[] = T_("Invalid group name or group name missing");
            }
            // Just loop as trying to process a group without a name is hard so they just have to reenter those details
            continue;
        }

        // Process expiry's
        $groupExpiries[clean_groupname($name)] = $groupExpiry[\Grase\Clean::text($key)];

        // Validate expiries
        if (isset($groupExpiry[$key])) {
            if (strtotime($groupExpiry[$key]) == false) {
                $error[] = sprintf(T_("%s: Invalid expiry format"), $name);
            } elseif (strtotime($groupExpiry[$key]) < time()) {
                $error[] = sprintf(T_("%s: Expiry can not be in the past"), $name);
            }
        }

        // Validate expire afters
        if (isset($groupExpireAfter[$key])) {
            if (strtotime($groupExpireAfter[$key]) == false) {
                $error[] = sprintf(T_("%s: Invalid Expire After format"), $name);
            } elseif (strtotime($groupExpireAfter[$key]) < time()) {
                $error[] = sprintf(T_("%s: Expire after can not be in the past"), $name);
            }
        }

        if(!\Grase\Validate::numericLimit($groupTimeLimit[$key])) {
            $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $groupTimeLimit[$key]);
        }
        if(!\Grase\Validate::numericLimit($groupRecurLimit[$key])) {
            $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $groupRecurLimit[$key]);
        }
        if(!\Grase\Validate::numericLimit($groupRecurDataLimit[$key])) {
            $error[] = sprintf(T_("Invalid value '%s' for Data Limit"), $groupRecurDataLimit[$key]);
        }
        if(!\Grase\Validate::recurrenceInterval($groupRecurTime[$key], recurtimes())) {
            $error[] = sprintf(T_("Invalid recurrence interval '%s'"), $groupRecurTime[$key]);
        }
        if(!\Grase\Validate::recurrenceInterval($groupRecurData[$key], recurtimes())) {
            $error[] = sprintf(T_("Invalid recurrence interval '%s'"), $groupRecurData[$key]);
        }
        if(!\Grase\Validate::recurrenceTime($groupRecurTime[$key], $groupRecurTimeLimit[$key])) {
            $error[] = T_("Recurring time limit must be less than interval");
        }
        if(!\Grase\Validate::bandwidthOptions($groupBandwidthDownLimit[$key], bandwidth_options())) {
            $error[] = sprintf(T_("Invalid Bandwidth Limit '%s'"), $groupBandwidthDownLimit[$key]);
        }
        if(!\Grase\Validate::bandwidthOptions($groupBandwidthUpLimit[$key], bandwidth_options())) {
            $error[] = sprintf(T_("Invalid Bandwidth Limit '%s'"), $groupBandwidthUpLimit[$key]);
        }
        //TODO we don't validate that it's not 0, relying on HTML5 to do that
        $error[] = @ validate_int($groupSimultaneousUse[$key], true);
        // TODO: Validate Login-Time
        $error[] = @ validate_uucptimerange($groupLoginTime[$key]);
        $error = array_filter($error);

        if (isset($groupRecurTime[$key]) xor isset($groupRecurTimeLimit[$key])) {
            $error[] = sprintf(T_("Need both a time limit and recurrance for '%s'"), \Grase\Clean::text($name));
        }


        $groups[clean_groupname($name)] = array_filter(
            array(
                'DataRecurTime' => \Grase\Clean::text($groupRecurData[$key]),
                'DataRecurLimit' => clean_number($groupRecurDataLimit[$key]),
                'TimeRecurTime' => @ \Grase\Clean::text($groupRecurTime[$key]),
                'TimeRecurLimit' => @ clean_int($groupRecurTimeLimit[$key]),
                'BandwidthDownLimit' => @ clean_int($groupBandwidthDownLimit[$key]),
                'BandwidthUpLimit' => @ clean_int($groupBandwidthUpLimit[$key]),
                'SimultaneousUse' => @ clean_int($groupSimultaneousUse[$key]),
                'LoginTime' => @ $groupLoginTime[$key]
            )
        );
        $groupSettings[clean_groupname($name)] = array_filter(
            array(
                'GroupName' => clean_groupname($name),
                'Comment' => \Grase\Clean::text($groupComment[$key]),
                'GroupLabel' => \Grase\Clean::text($name),
                'Expiry' => @ $groupExpiry[$key],
                'ExpireAfter' => @ $groupExpireAfter[$key],
                'MaxMb' => @ clean_number($groupDataLimit[$key]),
                'MaxTime' => @ clean_int($groupTimeLimit[$key]),
            )
        );
    }

    if (sizeof($error) == 0) {
        // No errors. Save groups
        foreach ($groupSettings as $attributes) {
            $Settings->setGroup($attributes);
        }

        // Delete groups no longer referenced
        foreach ($Settings->getGroup() as $oldgroup => $oldgroupsettings) {
            if (!isset($groupSettings[$oldgroup])) {
                $Settings->deleteGroup($oldgroup);
            }
        }

        // Delete groups from radgroupreply not in groupExpiries...
        // Deleting groups out of radgroupreply will modify current users
        // Need to do check for any users still using group, if no user then delete
        // TODO: check for groups that have not changed so don't run this on them
        // TODO: cron function that removes groups no longer referenced anywhere

        foreach ($groups as $name => $group) {
            DatabaseFunctions::getInstance()->setGroupAttributes($name, $group);
        }

        $success[] = T_("Groups updated");
    }

    if (sizeof($error) > 0) {
        $templateEngine->assign("error", $error);
    }
    if (sizeof($success) > 0) {
        $templateEngine->assign("success", $success);
    }

    // TODO set this initially
    $templateEngine->assign("groupcurrentdata", $groups);
    $templateEngine->assign("groupsettings", $Settings->getGroup());
    $templateEngine->displayPage('groups.tpl');

} else {

    $templateEngine->assign("groupcurrentdata", DatabaseFunctions::getInstance()->getGroupAttributes());
    $templateEngine->assign("groupsettings", $Settings->getGroup());
    $templateEngine->displayPage('groups.tpl');
}
