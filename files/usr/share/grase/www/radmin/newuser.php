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
$PAGE = 'createuser';
require_once 'includes/pageaccess.inc.php';


require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

function validate_form()
{
	global $expirydate;
	$error = array();
	if(! checkDBUniqueUsername($_POST['Username'])) $error[] = T_("Username already taken");
	if ( ! $_POST['Username'] || !$_POST['Password'] ) $error[] = T_("Username and Password are both Required");
	
   	$MaxMb = clean_number($_POST['MaxMb'] );
	$Max_Mb = clean_number($_POST['Max_Mb'] );	
	$MaxTime = clean_int($_POST['MaxTime'] );
	$Max_Time = clean_int($_POST['Max_Time'] );	
	
	$error[] = validate_datalimit($MaxMb);
	$error[] = validate_datalimit($Max_Mb);
	$error[] = validate_timelimit($MaxTime);
	$error[] = validate_timelimit($Max_Time);		
	if(is_numeric($Max_Mb) && is_numeric($MaxMb)) $error[] = T_("Only set one Data limit field");
	if(is_numeric($Max_Time) && is_numeric($MaxTime)) $error[] = T_("Only set one Time limit field");

    /* // Expiry is not submitted anymore
	list($error2, $expirydate) = validate_post_expirydate();
	$error = array_merge($error, $error2); // validate_post_expirydate can return multiple errors*/
	$error[] = validate_group($_POST['Username'], $_POST['Group']);
	return array_filter($error);
}



if(isset($_POST['newusersubmit']))
{
	$error=validate_form();
	if($error ){
		$user['Username'] = clean_text($_POST['Username']);
		$user['Password'] = clean_text($_POST['Password']);
		$user['MaxMb'] = displayLocales(clean_number($_POST['MaxMb']));
		$user['Max_Mb'] = displayLocales(clean_number($_POST['Max_Mb']));		
		$user['MaxTime'] = displayLocales(clean_int($_POST['MaxTime']));
		$user['Max_Time'] = displayLocales(clean_int($_POST['Max_Time']));	
		$user['Group'] = clean_text($_POST['Group']);
		$user['Expiration'] = expiry_for_group(clean_text($_POST['Group'])); //"${_POST['Expirydate_Year']}-${_POST['Expirydate_Month']}-${_POST['Expirydate_Day']}";
		$user['Comment'] = clean_text($_POST['Comment']);
		$smarty->assign("user", $user);
		$smarty->assign("error", $error);
		display_page('adduser.tpl');
	}else
	{
		if(is_numeric(clean_number($_POST['Max_Mb'])))
		    $MaxMb = clean_number($_POST['Max_Mb']);
		if(is_numeric(clean_number($_POST['MaxMb'])))
		    $MaxMb = clean_number($_POST['MaxMb']);
		if(is_numeric(clean_int($_POST['Max_Time'])))
		    $MaxTime =  clean_int($_POST['Max_Time']);
		if(is_numeric(clean_number($_POST['MaxTime'])))
		    $MaxTime = clean_int($_POST['MaxTime']);
		database_create_new_user( // TODO: Check if valid
			clean_text($_POST['Username']),
			clean_text($_POST['Password']),
			$MaxMb,
			$MaxTime,
			expiry_for_group(clean_text($_POST['Group'])),
			clean_text($_POST['Group']),
			clean_text($_POST['Comment'])
		);
		$success[] = sprintf(T_("User %s Successfully Created"),clean_text($_POST['Username']));
		$success[] = "<a target='_tickets' href='printnewtickets?user=". clean_text($_POST['Username']) ."'>".sprintf(T_("Print Ticket for %s"), clean_text($_POST['Username']))."</a>";		
		AdminLog::getInstance()->log(sprintf(T_("Created new user %s"),clean_text($_POST['Username'])));
		$smarty->assign("success", $success);
		display_adduser_form();
	}
}else
{
	display_adduser_form();
}

function display_adduser_form()
{
	global $smarty, $pricemb;
//    $user['Username'] = rand_username(5);	
	$user['Password'] = rand_password(6);
	$user['Max_Mb'] = round(10/$pricemb, 2); // TODO: Make a default setting for data and time and put in settings page
	$user['Expiration'] = "--";//date('Y-m-d', strtotime('+3 month'));
	$smarty->assign("user", $user);
	display_page('adduser.tpl');
}

?>


