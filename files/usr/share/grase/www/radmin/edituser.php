<?php

/* Copyright 2008 Timothy White */

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
$PAGE = 'edituser';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';


if(isset($_GET['username']) && !DatabaseFunctions::getInstance()->checkUniqueUsername($_GET['username']))#Display single user, in detail
{
	$error = array();
	$success = array();

	$username = mysql_real_escape_string($_GET['username']); // TODO change this? i.e. make database class do it if it doesn't already
	$user = DatabaseFunctions::getInstance()->getUserDetails($_GET['username']);
	
	if(isset($_POST['updateusersubmit']))
	{   // Process form for changed items and do updates

        // Update password
	    if(clean_text($_POST['Password']) && clean_text($_POST['Password']) != $user['Password'])
	    {
            DatabaseFunctions::getInstance()->setUserPassword($username, clean_text($_POST['Password']));
	        // TODO: Check return for success		
	        $success[] = T_("Password Changed");
	        AdminLog::getInstance()->log("Password changed for $username");	    
        }
        
        // Update group if changed
        if(clean_text($_POST['Group']) && clean_text($_POST['Group']) != $user['Group'])
        {
            $temperror =  validate_group($username, $_POST['Group']);
            if(array_filter($temperror))
            {
                $error = array_merge($error, $temperror);
            }
            else
            {
                DatabaseFunctions::getInstance()->setUserGroup($username, clean_text($_POST['Group']));
			    DatabaseFunctions::getInstance()->setUserExpiry($username,
			        expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username)));
			    // TODO: Check return for success
			    $success[] = T_("Group Changed");
			    AdminLog::getInstance()->log("Group changed for $username");                
            }        
        }
        
        // Update comment if changed
        if(clean_text($_POST['Comment']) != $user['Comment'])
        {
            DatabaseFunctions::getInstance()->setUserComment($username, clean_text($_POST['Comment']));
			// TODO: Check return for success			
			$success[] = T_("Comment Changed");
			AdminLog::getInstance()->log("Comment changed for $username");        
        }
        
        // Lock/Unlock update
        if(clean_text($_POST['LockReason']) != $user['LockReason'])
        {
            if(clean_text($_POST['LockReason']) == ''){
		        DatabaseFunctions::getInstance()->unlockUser($username);
		        $success[] = T_("User Account Unlocked");
    			AdminLog::getInstance()->log("Account $username unlocked");        
	        }else{
	            // Using clean_username as the LockReason is processed by JSON from CoovaChilli from Radius and so ' and " don't carry well
	            		        DatabaseFunctions::getInstance()->lockUser($username, clean_username($_POST['LockReason']));
		        $success[] = T_("User Account Locked");
    			AdminLog::getInstance()->log("Account $username locked: ".clean_username($_POST['LockReason']));        
	        }

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
                DatabaseFunctions::getInstance()->increaseUserDatalimit($username, clean_number($_POST['Add_Mb']));
    			DatabaseFunctions::getInstance()->setUserExpiry($username, expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username)));
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

                DatabaseFunctions::getInstance()->setUserDataLimit($username, clean_number($_POST['MaxMb']));
			    DatabaseFunctions::getInstance()->setUserExpiry($username, expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username)));
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
                DatabaseFunctions::getInstance()->increaseUserTimelimit($username, clean_number($_POST['Add_Time']));
    			DatabaseFunctions::getInstance()->setUserExpiry($username, expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username)));
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
                DatabaseFunctions::getInstance()->setUserTimeLimit($username, clean_number($_POST['MaxTime']));
			    DatabaseFunctions::getInstance()->setUserExpiry($username, expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username)));
			    // TODO: Check return for success			
			    $success[] = T_("Max Time Limit Updated");	
			    AdminLog::getInstance()->log(sprintf(T_("Max Time Limit changed for %s"), $username));			
		    }        
        }        
        
	}
	
	if(isset($_POST['unexpiresubmit']))
	{
	    DatabaseFunctions::getInstance()->setUserExpiry($username, expiry_for_group(DatabaseFunctions::getInstance()->getUserGroup($username)));
	    $success[] = T_("Expiry updated");
	}
	


	if(isset($_POST['deleteusersubmit'])) // Delete User
	{
        DatabaseFunctions::getInstance()->deleteUser($username); // TODO: Check for success
        $success[] = sprintf(T_("User '%s' Deleted"),$username);
        AdminLog::getInstance()->log("User $username deleted");
        $smarty->assign("error", $error);
        $smarty->assign("success", $success);
        require('display.php');
        die; // TODO: Recode so don't need die (too many nests?)

	}
	


	$smarty->assign("error", $error);
	$smarty->assign("success", $success);
	
	// if $success we need to reload the info
	if(sizeof($success) > 0 || sizeof($error) > 0)	
    	$user = DatabaseFunctions::getInstance()->getUserDetails($_GET['username']);

    // After potential reload, we can assign it to smarty
   	$smarty->assign("user", $user);	

    // After all user details are loaded, we can load our warning
    if($user['AccountLock'] == true) $warningmessages[] = T_('User account is locked and will not be able to login');
	
	display_page('edituser.tpl');

}else
{	# Display all users //TODO: Redirect?
	//require('display.php');	
	header("Location: display");
}

?>


