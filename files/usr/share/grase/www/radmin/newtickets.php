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
	global $expirydate;
	$error = array();
	//if(! checkDBUniqueUsername($_POST['Username'])) $error.= "Username already taken<br/>";
	//if ( ! $_POST['Username'] || !$_POST['Password'] ) $error.="Username and Password are both Required<br/>";
	
	$NumberTickets = ereg_replace("[^0-9]", "", $_POST['numberoftickets'] );
	
	$MaxMb = ereg_replace("[^\.0-9]", "", $_POST['MaxMb'] );
	$Max_Mb = ereg_replace("[^\.0-9]", "", $_POST['Max_Mb'] );	
	$MaxTime = ereg_replace("[^\.0-9]", "", $_POST['MaxTime'] );
	$Max_Time = ereg_replace("[^\.0-9]", "", $_POST['Max_Time'] );	
	
    
    $error[] = validate_int($NumberTickets);
	$error[] = validate_datalimit($MaxMb);
	$error[] = validate_datalimit($Max_Mb);
	$error[] = validate_timelimit($MaxTime);
	$error[] = validate_timelimit($Max_Time);		
	if($Max_Mb && $MaxMb) $error[] = _("Only set one Data limit field");
	if($Max_Time && $MaxTime) $error[] = _("Only set one Time limit field");
	
	if($NumberTickets > 50) $error[] = _("Max of 50 tickets per batch"); // Limit due to limit in settings length which stores batch for printing

	list($error2, $expirydate) = validate_post_expirydate();
	$error = array_merge($error, $error2);
	$error[] = validate_group("", $_POST['Group']);
	return array_filter($error);
}



if(isset($_POST['createticketssubmit']))
{
	$error = validate_form();
	if($error ){
		//$user['Username'] = clean_text($_POST['Username']);
		//$user['Password'] = clean_text($_POST['Password']);
        $user['numberoftickets'] = ereg_replace("[^0-9]", "", $_POST['numberoftickets'] );    		
		$user['MaxMb'] = ereg_replace("[^\.0-9]", "", clean_text($_POST['MaxMb']));
		$user['Max_Mb'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Mb']));		
		$user['MaxTime'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['MaxTime']));
		$user['Max_Time'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Time']));	
		$user['Group'] = clean_text($_POST['Group']);
		$user['Expiration'] = expiry_for_group(clean_text($_POST['Group'])); //"${_POST['Expirydate_Year']}-${_POST['Expirydate_Month']}-${_POST['Expirydate_Day']}";
		$user['Comment'] = clean_text($_POST['Comment']);
		$smarty->assign("user", $user);
		$smarty->assign("error", $error);
		display_page('newtickets.tpl'); //TODO: What happens if this returns?
	}else
	{
	    $user['numberoftickets'] = ereg_replace("[^0-9]", "", $_POST['numberoftickets'] );    
		if(ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Mb'])))
		    $MaxMb = ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Mb']));
		if(ereg_replace("[^\.0-9]", "", clean_text($_POST['MaxMb'])))
		    $MaxMb = ereg_replace("[^\.0-9]", "", clean_text($_POST['MaxMb']));
		if(ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Time'])))
		    $MaxTime =  ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Time']));
		if(ereg_replace("[^\.0-9]", "",clean_text($_POST['MaxTime'])))
		    $MaxTime = ereg_replace("[^\.0-9]", "",clean_text($_POST['MaxTime']));
		for($i = 0; $i < $user['numberoftickets']; $i++)
		{
		    $username =  rand_username(5);	
		    $password =  rand_password(6);
		    database_create_new_user( // TODO: Check if successful
			    $username,
			    $password,
			    $MaxMb,
			    $MaxTime,
			    expiry_for_group(clean_text($_POST['Group'])),
			    clean_text($_POST['Group']),
			    "AC: ". $Auth->getUsername() . "@".date("YmdHi")." ". clean_text($_POST['Comment'])
		    );
		    AdminLog::getInstance()->log("Created new user $username");
		    //$createdusers[] = array("UserName" => $username, "password" => $password);
		    //$createdusernames[] = array("UserName" => $username);
		    $createdusernames[] = $username;		    		    
		}
		print strlen(serialize($createdusernames));
		$Settings->setSetting('lastbatch', serialize($createdusernames));
		$createdusers = database_get_users($createdusernames);
		$smarty->assign("createdusers", $createdusers);
		$success[] = _("Tickets Successfully Created");
		$success[] = "<a target='_tickets' href='printnewtickets'>"._("Print Tickets")."</a>";				
	    $smarty->assign("success", $success);
		display_adduser_form();
	}
}else
{
	display_adduser_form();
}

function display_adduser_form()
{
	global $smarty, $Settings;
//    $user['Username'] = rand_username(5);	
	$user['Password'] = rand_password(6);
	$user['Max_Mb'] = 50;
	$user['Expiration'] = "--";//date('Y-m-d', strtotime('+3 month'));
	$smarty->assign("user", $user);
	
	$valid_last_batch = false;
    if(is_array(unserialize($Settings->getSetting('lastbatch')))) $valid_last_batch = true;
    $smarty->assign("valid_last_batch", $valid_last_batch);
    
	display_page('newtickets.tpl');
}

?>


