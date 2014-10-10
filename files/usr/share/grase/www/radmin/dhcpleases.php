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

$PAGE = 'dhcpleases';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

function getChilliLeases()
{
    exec('sudo /usr/sbin/chilli_query -json list', $output, $return);

    if ($return === 0) {
        // Command worked
        return json_decode($output[0], true);
    }
    return false;
}
$leases = getChilliLeases();

$templateEngine->assign("chilliSessions", $leases['sessions']);
$templateEngine->displayPage('dhcpleases.tpl');
