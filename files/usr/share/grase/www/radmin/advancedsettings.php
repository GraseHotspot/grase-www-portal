<?php

/* Copyright 2014 Timothy White */

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
$PAGE = 'advancedsettings';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

$success = array();

if (isset($_POST['submitAdvancedSettingsForm'])) {
    if (trim($_POST['name'])) {
        $settingName = $_POST['name'];
        $settingValue = $_POST['value'];
        $Settings->setSetting($settingName, $settingValue);
        $success[] = sprintf(T_("Updated %s setting with value %s"), $settingName, $settingValue);
    }
}

$templateEngine->assign('success', $success);
$templateEngine->assign('allSettings', $Settings->getSettingsCache());
$templateEngine->displayPage('advancedsettings.tpl');
