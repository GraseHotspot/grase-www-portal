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

require_once 'includes/page_functions.inc.php';

function usermin_createmenuitems()
{
	//	$menubar['id'] = array("href" => , "label" => );
	$menubar['user'] = array("href" => "?user", "label" => "My Details");
	$menubar['history'] = array("href" => "?history", "label" => "My History");
	$menubar['logout'] = array("href" => "?logoff", "label" => "Logoff" );
	return $menubar;
}

function usermin_assign_vars()
{
	global $smarty, $location;
	$smarty->assign("Application", USERMIN_APPLICATION_NAME);

	$smarty->assign("Title", $location . " - " . USERMIN_APPLICATION_NAME);

	// Setup Menus
	$smarty->assign("MenuItems", usermin_createmenuitems());
	isset($_SESSION['username']) && $smarty->assign("LoggedInUsername", $_SESSION['username']);

}


?>
