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

$PAGE = 'createtickets';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

function validate_form()
{
	//global $expirydate;
	$error = array();
	//if(! checkDBUniqueUsername($_POST['Username'])) $error.= "Username already taken<br/>";
	//if ( ! $_POST['Username'] || !$_POST['Password'] ) $error.="Username and Password are both Required<br/>";
	
	$NumberTickets = clean_int($_POST['numberoftickets'] );
	
	$MaxMb = clean_number($_POST['MaxMb'] );
	$Max_Mb = clean_number( $_POST['Max_Mb'] );	
	$MaxTime = clean_int( $_POST['MaxTime'] );
	$Max_Time = clean_int( $_POST['Max_Time'] );	
	
    
    $error[] = validate_int($NumberTickets);
	$error[] = validate_datalimit($MaxMb);
	$error[] = validate_datalimit($Max_Mb);
	$error[] = validate_timelimit($MaxTime);
	$error[] = validate_timelimit($Max_Time);		
	if((is_numeric($Max_Mb) || $_POST['Max_Mb'] == 'inherit') && is_numeric($MaxMb)) $error[] = T_("Only set one Data limit field");
	if((is_numeric($Max_Time) || $_POST['Max_Time'] == 'inherit') && is_numeric($MaxTime)) $error[] = T_("Only set one Time limit field");
	
	/*
	 * TODO: Remove this limit as we now store batches in a new table
	 * (Remove once we have a nice easy way to manage batches, accidently
	 * creating 1000 users and not being able to delete them is a bad thing)
	 */
	if($NumberTickets > 50) $error[] = T_("Max of 50 tickets per batch"); // Limit due to limit in settings length which stores batch for printing

	//list($error2, $expirydate) = validate_post_expirydate();
	//$error = array_merge($error, $error2);
	$error[] = validate_group("", $_POST['Group']);
	return array_filter($error);
}


/* ** Process batches actions (delete,print, export) **   */

if(isset($_POST['batchesprint']))
{
        foreach($_POST['selectedbatches'] as $batch)
        {
                $selectedbatches[] = clean_number($batch);
        }
        $selectedbatches = implode(',', $selectedbatches);
        if(sizeof($selectedbatches) == 0){
                $error[] = T_("Please select a batch to print");
                $smarty->assign("error", $error);
        }else{
                header ("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/printnewtickets?batch=$selectedbatches");
        }

}

if(isset($_POST['batchesexport']))
{
        foreach($_POST['selectedbatches'] as $batch)
        {
                $selectedbatches[] = clean_number($batch);
        }
        $selectedbatches = implode(',', $selectedbatches);
        if(sizeof($selectedbatches) == 0){
                $error[] = T_("Please select a batch to export");
                $smarty->assign("error", $error);
        }else{
                header ("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/exporttickets?batch=$selectedbatches");
        }

}

// TODO Delete batches

/*  **  Process creation of batches **   */

if(isset($_POST['createticketssubmit']))
{
	$error = validate_form();
	if($error ){
		//$user['Username'] = clean_text($_POST['Username']);
		//$user['Password'] = clean_text($_POST['Password']);
        $user['numberoftickets'] = clean_int($_POST['numberoftickets'] );    		
		$user['MaxMb'] = displayLocales(clean_number($_POST['MaxMb']));
		$user['Max_Mb'] = displayLocales(clean_number($_POST['Max_Mb']));
		if($_POST['Max_Mb'] == 'inherit' ) $user['Max_Mb'] = 'inherit';
				
		$user['MaxTime'] = displayLocales(clean_int($_POST['MaxTime']));
		$user['Max_Time'] = displayLocales(clean_int($_POST['Max_Time']));	
		if($_POST['Max_Time'] == 'inherit' ) $user['Max_Time'] = 'inherit';
		
		$user['Group'] = clean_text($_POST['Group']);
		$user['Expiration'] = expiry_for_group(clean_text($_POST['Group'])); //"${_POST['Expirydate_Year']}-${_POST['Expirydate_Month']}-${_POST['Expirydate_Day']}";
		$user['Comment'] = clean_text($_POST['Comment']);
		$smarty->assign("user", $user);
		$smarty->assign("error", $error);
		display_page('newtickets.tpl'); //TODO: What happens if this returns?
	}else
	{
	    $group = clean_text($_POST['Group']);
	    // Load group settings so we can use Expiry, MaxMb and MaxTime
	    $groupsettings = $Settings->getGroup($group);
	
	    $user['numberoftickets'] = clean_int($_POST['numberoftickets'] );    
	    
	    // TODO: Create function to make these the same across all locations
		if(is_numeric(clean_number($_POST['Max_Mb'])))
		    $MaxMb = clean_number($_POST['Max_Mb']);
		if(is_numeric(clean_number($_POST['MaxMb'])))
		    $MaxMb = clean_number($_POST['MaxMb']);
		if($_POST['Max_Mb'] == 'inherit')
		    $MaxMb = @ $groupsettings[$group]['MaxMb'];
		    
		if(is_numeric(clean_int($_POST['Max_Time'])))
		    $MaxTime =  clean_int($_POST['Max_Time']);
		if(is_numeric(clean_number($_POST['MaxTime'])))
		    $MaxTime = clean_int($_POST['MaxTime']);
		if($_POST['Max_Time'] == 'inherit')
		    $MaxTime = @ $groupsettings[$group]['MaxTime'];

        // We create the batch first, then add users to it (prevents us having unattached users if the batch dies for some reason)
		$batchID = $Settings->nextBatchID();
		$Settings->saveBatch($batchID, array(), $Auth->getUsername(), clean_text($_POST['Comment']));
		$Settings->setSetting('lastbatch', $batchID);

		$failedusers= 0;
		for($i = 0; $i < $user['numberoftickets']; $i++)
		{
		    $username =  \Grase\Util::randomUsername(5); // DONE: Username uniqness is checked as user creation time in Database
		    $password =  \Grase\Util::randomPassword(6);
		    
		    // Attempt to create user. Will error if it's not a unique username
		    if(database_create_new_user(
			    $username,
			    $password,
			    $MaxMb,
			    $MaxTime,
			    expiry_for_group($group, $groupsettings),
			    clean_text($_POST['Group']),
			    clean_text($_POST['Comment'])
		    ))
		    {
		        AdminLog::getInstance()->log("Created new user $username");
		        $Settings->addUserToBatch($batchID, $username);
    		    $createdusernames[] = $username;
	        }else{
	            // Failed to create. Most likely not a unique username.
	            // Try again but only for so long (i.e. all usernames are in use)
	            $i--;
	            
	            // This really chokes up the logs, maybe don't log this? TODO
	            AdminLog::getInstance()->log("Failed to created new user $username. Probably duplicate username");
	            $failedusers++;
	            
	            if($failedusers > 20)
	            {
	                AdminLog::getInstance()->log("Too many failed usersnames, stopping batch creation");
	                $error[] = sprintf(T_("Too many users failed to create. Batch creation stopped. %s users have been successfully created"), $i);
	                break;
	            }
	        }
		}

        // Load up user details of created users for displaying
		$createdusers = database_get_users($createdusernames);
		$smarty->assign("createdusers", $createdusers);

        // Check if we managed to create all users or if batch failed
		if($failedusers <= 20)
		{
		    $success[] = T_("Tickets Successfully Created");
		    $success[] = "<a target='_tickets' href='printnewtickets'>".T_("Print Tickets")."</a>";
	    }
	    $smarty->assign("success", $success);
	    $smarty->assign("error", $error);
		display_adduser_form();
	}
}else
{
	display_adduser_form();
}

function display_adduser_form()
{
	global $smarty, $Settings;
//    $user['Username'] = \Grase\Util::RandomUsername(5);
	$user['Password'] = \Grase\Util::randomPassword(6);
	
		// TODO: make default settings customisable
	$user['Max_Mb'] = 'inherit';
	$user['Max_Time'] = 'inherit';
	//$user['Max_Mb'] = 50;
	
	$user['Expiration'] = "--";//date('Y-m-d', strtotime('+3 month'));
	$smarty->assign("user", $user);
	
    $smarty->assign("last_batch", $Settings->getSetting('lastbatch'));
    $smarty->assign("listbatches", $Settings->listBatches());
    
	display_page('newtickets.tpl');
}

?>


