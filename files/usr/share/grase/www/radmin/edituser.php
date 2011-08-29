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
	$success = array();
	$username = mysql_real_escape_string($_GET['username']);
	$user = getDBUserDetails($_GET['username']);
	
	if(isset($_POST['updateusersubmit']))
	{   // Process form for changed items and do updates

        // Update password
	    if(clean_text($_POST['Password']) && clean_text($_POST['Password']) != $user['Password'])
	    {
	        database_change_password($username, clean_text($_POST['Password']));
	        // TODO: Check return for success		
	        $success[] = T_("Password Changed");
	        AdminLog::getInstance()->log("Password changed for $username");	    
        }
        
        // Update group if changed
        if(clean_text($_POST['Group']) != $user['Group'])
        {
            $temperror =  validate_group($username, $_POST['Group']);
            if(array_filter($temperror))
            {
                $error = array_merge($error, $temperror);
            }
            else
            {
			    database_change_group($username, clean_text($_POST['Group']));
			    database_update_expirydate($username,
			        expiry_for_group(getDBUserGroup($username)));
			    // TODO: Check return for success
			    $success[] = T_("Group Changed");
			    AdminLog::getInstance()->log("Group changed for $username");                
            }        
        }
        
        // Update comment if changed
        if(clean_text($_POST['Comment']) != $user['Comment'])
        {
			database_change_comment($username, clean_text($_POST['Comment']));
			// TODO: Check return for success			
			$success[] = T_("Comment Changed");
			AdminLog::getInstance()->log("Comment changed for $username");        
        }
        
        // Increase Data Limit
        if(clean_number($_POST['Add_Mb']))
        {
            $temperror[] = validate_datalimit(clean_number($_POST['Add_Mb']));
            if(array_filter($temperror))
            {
                $error = array_merge($error, $temperror);
            }
            else            
            {
			    database_increase_datalimit($username, clean_number($_POST['Add_Mb']));
    			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
    			// TODO: Check return for success			
    			$success[] = T_("Data Limit Increased");	
			AdminLog::getInstance()->log(sprintf(T_("Data Limit increased for %s"), $username));            
            }
        }
        
        // If Data Limit is changed and Not added too, Change Data Limit
        if(clean_number($_POST['MaxMb']) !== ''
           && ! clean_number($_POST['Add_Mb'])
           && clean_number($_POST['MaxMb']) != clean_number($user['MaxMb']))
        {
            $temperror[] = validate_datalimit(clean_number($_POST['MaxMb']));
            if(array_filter($temperror))
            {
                $error = array_merge($error, $temperror);
            }
            else
    		{

			    database_change_datalimit($username, clean_number($_POST['MaxMb']));
			    database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			    // TODO: Check return for success			
			    $success[] = T_("Max Data Limit Updated");	
			    AdminLog::getInstance()->log(sprintf(T_("Max Data Limit changed for %s"), $username));			
		    }        
        }
        
        // Increase Time Limit
        if(clean_number($_POST['Add_Time']))
        {
            $temperror[] = validate_timelimit(clean_number($_POST['Add_Time']));
            if(array_filter($temperror))
            {
                $error = array_merge($error, $temperror);
            }
            else            
            {
			    database_increase_timelimit($username, clean_number($_POST['Add_Time']));
    			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
    			// TODO: Check return for success			
    			$success[] = T_("Time Limit Increased");	
			AdminLog::getInstance()->log(sprintf(T_("Time Limit increased for %s"), $username));            
            }
        }        
        
        // If Time Limit is changed and Not added too, Change Time Limit        
        if(clean_number($_POST['MaxTime']) !== ''
           && ! clean_number($_POST['Add_Time'])
           && clean_number($_POST['MaxTime']) != $user['MaxTime'])
        {
            $temperror[] = validate_timelimit(clean_number($_POST['MaxTime']));
            if(array_filter($temperror))
            {
                $error = array_merge($error, $temperror);
            }
            else
    		{
			    database_change_timelimit($username, clean_number($_POST['MaxTime']));
			    database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			    // TODO: Check return for success			
			    $success[] = T_("Max Time Limit Updated");	
			    AdminLog::getInstance()->log(sprintf(T_("Max Time Limit changed for %s"), $username));			
		    }        
        }        
        
	}


	if(isset($_POST['deleteusersubmit'])) // Delete User
	{
		//if($_POST['DeleteUser'] == "Yes, I want to delete this user") //Really delete user (TODO: DEFINE CONSTANTS)
		//{
			database_delete_user($username); // TODO: Check for success
			$success[] = sprintf(T_("User '%s' Deleted"),$username);
			AdminLog::getInstance()->log("User $username deleted");			
			//$users = database_get_user_names();
			$smarty->assign("error", $error);
			$smarty->assign("success", $success);
			//$smarty->assign("users", $users);
			//$smarty->display('listusers.tpl');
			require('display.php');
			die; // TODO: Recode so don't need die (too many nests?)
		//}else
		//{
		//	$error[] = T('Please type "Yes, I want to delete this user" (without the quotes) into the box before clicking delete user');
		//}
		
	}

	$smarty->assign("error", $error);
	$smarty->assign("success", $success);	
	$smarty->assign("user", getDBUserDetails($_GET['username']));
	
	$smarty->assign("groupdata", DatabaseFunctions::getInstance()->getGroupAttributes());
    $smarty->assign("groups", unserialize($Settings->getSetting("groups")));	
	
	display_page('edituser.tpl');

}else
{	# Display all users //TODO: Redirect?
	//require('display.php');	
	header("Location: display");
}

?>


