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
$PAGE = 'users';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
if (isset($_GET['user'])) {
    $users = DatabaseFunctions::getInstance()->getMultipleUsersDetails(array(\Grase\Clean::text($_GET['user'])));
    if (!is_array($users)) {
        $users = array();
    }
    $title = \Grase\Clean::text($_GET['user']) . ' Voucher';
} elseif (isset($_GET['batch'])) {
    $batches = explode(',', $_GET['batch']);

    $users = array();
    foreach ($batches as $batch) {
        $batch = clean_number($batch);
        $fetchUsers = DatabaseFunctions::getInstance()->getMultipleUsersDetails($Settings->getBatch($batch));
        if (!is_array($fetchUsers)) {
            $fetchUsers = array();
        }
        $users = array_merge($users, $fetchUsers);
    }

    // TODO: replace , with _ in below
    $title = sprintf(T_('Batch %s Vouchers'), implode('-', $batches));
} else {
    $batch = $Settings->getSetting('lastbatch');
    $users = DatabaseFunctions::getInstance()->getMultipleUsersDetails($Settings->getBatch($batch));
    if (!is_array($users)) {
        $users = array();
    }
    $title = sprintf(T_('Batch %s Vouchers'), $batch);
}

$preset_labels['Avery 5160'] = array(
    'name' => '5160',
    'paper-size' => 'letter',
    'metric' => 'mm',
    'marginLeft' => 1.762,
    'marginTop' => 10.7,
    'NX' => 3,
    'NY' => 10,
    'SpaceX' => 3.175,
    'SpaceY' => 0,
    'width' => 66.675,
    'height' => 25.4,
    'font-size' => 8
);

generate_pdf($users, $title);

function generate_pdf($users, $title)
{
    global $Settings;
    /* The following will work in the future */
    //$ssid = $Settings->getSetting('printSSID');
    //$print_group = $Settings->getSetting('printGroup');

    $groupsettings = grouplist();

    // These settings are temporarily in network settings
    $networksettings = unserialize($Settings->getSetting('networkoptions'));
    $ssid = $networksettings['printSSID'];
    $print_group = $networksettings['printGroup'];
    $print_expiry = $networksettings['printExpiry'];

    $labels = new PDFLabels('Overflow', $title);
    foreach ($users as $user) {
        $label = '';
        if ($ssid) {
            $label .= sprintf(T_("Wireless Network: %s"), $ssid) . "\n";
        }

        $label .= sprintf(T_("Username: %s"), $user['Username']) . "\n";
        $label .= sprintf(T_("Password: %s"), $user['Password']) . "\n";

        if ($print_group) {
            $label .= sprintf(T_("Voucher Type: %s"), $groupsettings[$user['Group']]) . "\n";

        }

        if ($print_expiry
            && $user['FormatExpiration']
            && $user['FormatExpiration'] != '--'
        ) {
            $label .= sprintf(T_("Expiry: %s"), $user['FormatExpiration']) . "\n";
        }


        $labels->Add_PDF_Label($label);
    }
    $labels->Output_Doc();

}
