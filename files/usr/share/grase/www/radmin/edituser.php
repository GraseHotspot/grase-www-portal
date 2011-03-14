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

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';


if(isset($_GET['username']) && !checkDBUniqueUsername($_GET['username']))#Display single user, in detail
{
	$error = array();
	$msgbox = array();
	$username = mysql_real_escape_string($_GET['username']);
	if(isset($_POST['changepasswordsubmit'])) // Change Password
	{
	    if(!$_POST['Password'])
	    {
	        $error[] = _('A password is required');
	    }else
	    {	           
	        database_change_password($username, $_POST['Password']);
	        // TODO: Check return for success		
	        $msgbox[] = _("Password Updated");
	        AdminLog::getInstance()->log("Password changed for $username");
        }
	}

	if(isset($_POST['changegroupsubmit'])) // Change Group
	{
		$error2 = validate_group($username, clean_text($_POST['Group']));
		if($error2)
		{
			$error[] = $error2;
		}else
		{
			database_change_group($username, clean_text($_POST['Group']));
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			// TODO: Check return for success
			$msgbox[] = _("Group Changed");
			AdminLog::getInstance()->log("Group changed for $username");
		}
	}
	
	if(isset($_POST['changecommentsubmit'])) // Change Group
	{
	    $error2 = '';
		//$error2 = validate_comment($username, clean_text($_POST['Comment']));
		if($error2)
		{
			$error[] = $error2;
		}else
		{
			database_change_comment($username, clean_text($_POST['Comment']));
			// TODO: Check return for success			
			$msgbox[] = _("Comment Changed");
			AdminLog::getInstance()->log("Comment changed for $username");			
		}
	}	

	if(isset($_POST['changedatalimitsubmit']))  // Change Max Data Limit
	{
	    $error2 = array();
		$error2[] = validate_datalimit(clean_text($_POST['MaxMb']));
		$error2[] = validate_datalimit(clean_text($_POST['MaxMb_']));
		if($_POST['MaxMb'] && $_POST['MaxMb_']) $error2[] = _('Select an option OR type in a value');
		if(array_filter($error2))
		{
			$error = array_merge($error, $error2);
		}else
		{
			if(isset($_POST['MaxMb']) && $_POST['MaxMb'] != '') database_change_datalimit($username, clean_text($_POST['MaxMb']));
			if(isset($_POST['MaxMb_']) && $_POST['MaxMb_'] != '') database_change_datalimit($username, clean_text($_POST['MaxMb_']));			
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			// TODO: Check return for success			
			$msgbox[] = _("Max Data Limit Updated");	
			AdminLog::getInstance()->log("Max Data Limit changed for $username");			
		}

	}
	if(isset($_POST['adddatasubmit'])) // Change Max Data Limit (And expiry increase to be the groups expiry from today)
	{
	    $error2 = array();
		$error2[] = validate_datalimit(clean_text($_POST['AddMb']));
		$error2[] = validate_datalimit(clean_text($_POST['AddMb_']));		
		if($_POST['AddMb'] && $_POST['AddMb_']) $error2[] = _('Select an option OR type in a value');	
		if(array_filter($error2))
		{
			$error = array_merge($error, $error2);
		}else
		{
			if($_POST['AddMb']) database_increase_datalimit($username, clean_text($_POST['AddMb']));
			if($_POST['AddMb_']) database_increase_datalimit($username, clean_text($_POST['AddMb_']));
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			$msgbox[] = _("Data Limit Increased");	
			AdminLog::getInstance()->log("Data Limit increased for $username");			
		}
		
	}

	// Change Time Limit
	if(isset($_POST['changetimelimitsubmit'])) // Change Max Time Limit
	{
	    $error2 = array();
		$error2[] = validate_timelimit(clean_text($_POST['MaxTime']));
		$error2[] = validate_timelimit(clean_text($_POST['MaxTime_']));
		if($_POST['MaxTime'] && $_POST['MaxTime_']) $error2[] = _('Select an option OR type in a value');
		if(array_filter($error2))
		{
			$error = array_merge($error, $error2);
		}else
		{
			if(isset($_POST['MaxTime']) && $_POST['MaxTime'] != '') database_change_timelimit($username, clean_text($_POST['MaxTime']));
			if(isset($_POST['MaxTime_']) && $_POST['MaxTime_'] != '') database_change_timelimit($username, clean_text($_POST['MaxTime_']));			
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			$msgbox[] = _("Max Time Limit Updated");	
			AdminLog::getInstance()->log("Max Time Limit changed for $username");			
		}

	}
	if(isset($_POST['addtimesubmit'])) // Add Time to Limit
	{
	    $error2 = array();
		$error2[] = validate_datalimit(clean_text($_POST['AddTime']));
		$error2[] = validate_datalimit(clean_text($_POST['AddTime_']));		
		if($_POST['AddTime'] && $_POST['AddTime_']) $error2[] = _('Select an option OR type in a value');	
		if(array_filter($error2))
		{
			$error = array_merge($error, $error2);
		}else
		{
			if($_POST['AddTime']) database_increase_timelimit($username, clean_text($_POST['AddTime']));
			if($_POST['AddTime_']) database_increase_timelimit($username, clean_text($_POST['AddTime_']));
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			$msgbox[] = _("Time Limit Increased");	
			AdminLog::getInstance()->log("Time Limit increased for $username");			
		}
	}

	// Change Expiry (old code, this isn't permitted manually anymore)
	if(isset($_POST['changeexpirysubmit'])) // Change Expiry
	{
		$error[] = _("Changing Expiry Not Permitted. Please add data to update expiry date");
	}

	if(isset($_POST['deleteusersubmit'])) // Delete User
	{
		if($_POST['DeleteUser'] == "Yes, I want to delete this user") //Really delete user (TODO: DEFINE CONSTANTS)
		{
			database_delete_user($username);
			$msgbox = printf(_("User '%s' Deleted"),$username);
			AdminLog::getInstance()->log("User $username deleted");			
			//$users = database_get_user_names();
			$smarty->assign("error", $error);
			$smarty->assign("messagebox", $msgbox);
			//$smarty->assign("users", $users);
			//$smarty->display('listusers.tpl');
			require('display.php');
			die;
		}else
		{
			$error[] = _('Please type "Yes, I want to delete this user" (without the quotes) into the box before clicking delete user');
		}
		
	}

	$smarty->assign("error", $error);
	$smarty->assign("messagebox", $msgbox);	
	$smarty->assign("user", getDBUserDetails($_GET['username']));
	$smarty->display('edituser.tpl');

}else
{	# Display all users //TODO: Redirect?
	require('display.php');	
}

?>


