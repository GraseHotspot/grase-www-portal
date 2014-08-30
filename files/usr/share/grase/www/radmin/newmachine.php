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
$PAGE = 'createmachine';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

function validate_form()
{
	$error = array();
	if(! DatabaseFunctions::getInstance()->checkUniqueUsername($_POST['mac'])) $error[] = T_("MAC Address already has an account");
	
	$MaxMb = clean_number( $_POST['MaxMb'] );
	$Max_Mb = clean_number( $_POST['Max_Mb'] );	
	$MaxTime = clean_int( $_POST['MaxTime'] );
	$Max_Time = clean_int( $_POST['Max_Time'] );	
	

    $error[] = validate_mac($_POST['mac']);
	$error[] = validate_datalimit($MaxMb);
	$error[] = validate_datalimit($Max_Mb);
	$error[] = validate_timelimit($MaxTime);
	$error[] = validate_timelimit($Max_Time);		
	if($Max_Mb && $MaxMb) $error[] = T_("Only set one Data limit field");
	if($Max_Time && $MaxTime) $error[] = T_("Only set one Time limit field");

	return array_filter($error);
}



if(isset($_POST['newmachinesubmit']))
{
	$error = validate_form();
	if($error ){
		$user['mac'] = clean_text($_POST['mac']);
		$user['MaxMb'] = displayLocales(clean_number($_POST['MaxMb']));
		$user['Max_Mb'] = displayLocales(clean_number($_POST['Max_Mb']));		
		$user['MaxTime'] = displayLocales(clean_int($_POST['MaxTime']));
		$user['Max_Time'] = displayLocales(clean_int($_POST['Max_Time']));	
		$user['Comment'] = clean_text($_POST['Comment']);
		$smarty->assign("machine", $user);
		$smarty->assign("error", $error);
		display_page('addmachine.tpl');
	}else
	{
		if(clean_number($_POST['Max_Mb']))
		    $MaxMb = clean_number($_POST['Max_Mb']);
		if(clean_number($_POST['MaxMb']))
		    $MaxMb = clean_number($_POST['MaxMb']);
		if(clean_int($_POST['Max_Time']))
		    $MaxTime =  clean_int($_POST['Max_Time']);
		if(clean_int($_POST['MaxTime']))
		    $MaxTime = clean_int($_POST['MaxTime']);
		$mac = clean_text($_POST['mac']);
		database_create_new_user( // TODO: Check if successful
			$mac,
			DatabaseFunctions::getInstance()->getChilliConfigSingle('macpasswd'), // DONE: macpasswd comes from DB
			$MaxMb,
			$MaxTime,
			'--', // No expiry for machine accounts
			MACHINE_GROUP_NAME, // TODO: This needs to be linked to settings
			clean_text($_POST['Comment'])
		);
		$success[] = T_("Computer Account Successfully Created");
		AdminLog::getInstance()->log("Created new computer $mac");
		$smarty->assign("success", $success);
		display_addmachine_form();
	}
}else
{
	display_addmachine_form();
}

function display_addmachine_form()
{
	global $smarty;
	display_page('addmachine.tpl');
}

?>


