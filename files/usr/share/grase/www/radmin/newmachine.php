<?php

/* Copyright 2010 Timothy White */

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

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

function validate_form()
{
	$error = array();
	if(! checkDBUniqueUsername($_POST['mac'])) $error[] = _("Username already taken");
	
	$MaxMb = ereg_replace("[^\.0-9]", "", $_POST['MaxMb'] );
	$Max_Mb = ereg_replace("[^\.0-9]", "", $_POST['Max_Mb'] );	
	$MaxTime = ereg_replace("[^\.0-9]", "", $_POST['MaxTime'] );
	$Max_Time = ereg_replace("[^\.0-9]", "", $_POST['Max_Time'] );	
	

    $error[] = validate_mac($_POST['mac']);
	$error[] = validate_datalimit($MaxMb);
	$error[] = validate_datalimit($Max_Mb);
	$error[] = validate_timelimit($MaxTime);
	$error[] = validate_timelimit($Max_Time);		
	if($Max_Mb && $MaxMb) $error[] = _("Only set one Data limit field");
	if($Max_Time && $MaxTime) $error[] = _("Only set one Time limit field");

	return array_filter($error);
}



if(isset($_POST['newmachinesubmit']))
{
	$error = validate_form();
	if($error ){
		$user['mac'] = clean_text($_POST['mac']);
		$user['MaxMb'] = ereg_replace("[^\.0-9]", "", clean_text($_POST['MaxMb']));
		$user['Max_Mb'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Mb']));		
		$user['MaxTime'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['MaxTime']));
		$user['Max_Time'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Time']));	
		$user['Comment'] = clean_text($_POST['Comment']);
		$smarty->assign("machine", $user);
		$smarty->assign("error", $error);
		display_page('addmachine.tpl');
	}else
	{
		if(ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Mb'])))
		    $MaxMb = ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Mb']));
		if(ereg_replace("[^\.0-9]", "", clean_text($_POST['MaxMb'])))
		    $MaxMb = ereg_replace("[^\.0-9]", "", clean_text($_POST['MaxMb']));
		if(ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Time'])))
		    $MaxTime =  ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Time']));
		if(ereg_replace("[^\.0-9]", "",clean_text($_POST['MaxTime'])))
		    $MaxTime = ereg_replace("[^\.0-9]", "",clean_text($_POST['MaxTime']));
		$mac = clean_text($_POST['mac']);
		$success = database_create_new_user(
			$mac,
			'password', // TODO: This needs to come from settings
			$MaxMb,
			$MaxTime,
			'--',
			'Machine', // TODO: This needs to be linked to settings
			clean_text($_POST['Comment'])
		);
		AdminLog::getInstance()->log("Created new machine $mac");
		$smarty->assign("messagebox", "$message<br/>User Successfully Created");
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


