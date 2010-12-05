<?php

/* Copyright 2008 Timothy White */

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';


if(isset($_GET['username']) && !checkDBUniqueUsername($_GET['username']))#Display single user, in detail
{
	$error = "";
	$username = mysql_real_escape_string($_GET['username']);
	if(isset($_POST['changepasswordsubmit'])) // Change Password
	{
		database_change_password($username, $_POST['Password']);
		$error = "Password Updated";
		AdminLog::getInstance()->log("Password changed for $username");
	}

	if(isset($_POST['changegroupsubmit'])) // Change Group
	{
		$error2 = validate_group($username, clean_text($_POST['Group']));
		if($error2)
		{
			$error = $error2;
		}else
		{
			database_change_group($username, clean_text($_POST['Group']));
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			$error = "Group Changed";
			AdminLog::getInstance()->log("Group changed for $username");
		}
	}
	
	if(isset($_POST['changecommentsubmit'])) // Change Group
	{
	    $error2 = '';
		//$error2 = validate_comment($username, clean_text($_POST['Comment']));
		if($error2)
		{
			$error = $error2;
		}else
		{
			database_change_comment($username, clean_text($_POST['Comment']));
			$error = "Comment Changed";
			AdminLog::getInstance()->log("Comment changed for $username");			
		}
	}	

	if(isset($_POST['changedatalimitsubmit']))  // Change Max Data Limit
	{
		$error2 = validate_datalimit(clean_text($_POST['MaxMb']));
		$error2 .= validate_datalimit(clean_text($_POST['MaxMb_']));
		if($_POST['MaxMb'] && $_POST['MaxMb_']) $error2 = 'Select an option OR type in a value';
		if($error2)
		{
			$error = $error2;
		}else
		{
			if(isset($_POST['MaxMb']) && $_POST['MaxMb'] != '') database_change_datalimit($username, clean_text($_POST['MaxMb']));
			if(isset($_POST['MaxMb_']) && $_POST['MaxMb_'] != '') database_change_datalimit($username, clean_text($_POST['MaxMb_']));			
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			$error = "Max Data Limit Updated";	
			AdminLog::getInstance()->log("Max Data Limit changed for $username");			
		}

	}
	if(isset($_POST['adddatasubmit'])) // Change Max Data Limit (And expiry increase to be the groups expiry from today)
	{
		$error2 = validate_datalimit(clean_text($_POST['AddMb']));
		$error2 .= validate_datalimit(clean_text($_POST['AddMb_']));		
		if($_POST['AddMb'] && $_POST['AddMb_']) $error2 = 'Select an option OR type in a value';	
		if($error2)
		{
			$error = $error2;
		}else
		{
			if($_POST['AddMb']) database_increase_datalimit($username, clean_text($_POST['AddMb']));
			if($_POST['AddMb_']) database_increase_datalimit($username, clean_text($_POST['AddMb_']));
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			$error = "Data Limit Increased";	
			AdminLog::getInstance()->log("Data Limit increased for $username");			
		}
		
	}

	// Change Time Limit
	if(isset($_POST['changetimelimitsubmit'])) // Change Max Time Limit
	{
		$error2 = validate_timelimit(clean_text($_POST['MaxTime']));
		$error2 .= validate_timelimit(clean_text($_POST['MaxTime_']));
		if($_POST['MaxTime'] && $_POST['MaxTime_']) $error2 = 'Select an option OR type in a value';
		if($error2)
		{
			$error = $error2;
		}else
		{
			if(isset($_POST['MaxTime']) && $_POST['MaxTime'] != '') database_change_timelimit($username, clean_text($_POST['MaxTime']));
			if(isset($_POST['MaxTime_']) && $_POST['MaxTime_'] != '') database_change_timelimit($username, clean_text($_POST['MaxTime_']));			
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			$error = "Max Time Limit Updated";	
			AdminLog::getInstance()->log("Max Time Limit changed for $username");			
		}

	}
	if(isset($_POST['addtimesubmit'])) // Add Time to Limit
	{
		$error2 = validate_datalimit(clean_text($_POST['AddTime']));
		$error2 .= validate_datalimit(clean_text($_POST['AddTime_']));		
		if($_POST['AddTime'] && $_POST['AddTime_']) $error2 = 'Select an option OR type in a value';	
		if($error2)
		{
			$error = $error2;
		}else
		{
			if($_POST['AddTime']) database_increase_timelimit($username, clean_text($_POST['AddTime']));
			if($_POST['AddTime_']) database_increase_timelimit($username, clean_text($_POST['AddTime_']));
			database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
			$error = "Time Limit Increased";	
			AdminLog::getInstance()->log("Time Limit increased for $username");			
		}
	}

	// Change Expiry (old code, this isn't permitted manually anymore)
	if(isset($_POST['changeexpirysubmit'])) // Change Expiry
	{
		$error = "Changing Expiry Not Permitted. Please add data to update expiry date";
	}

	if(isset($_POST['deleteusersubmit'])) // Delete User
	{
		if($_POST['DeleteUser'] == "Yes, I want to delete this user") //Really delete user (TODO: DEFINE CONSTANTS)
		{
			database_delete_user($username);
			$error = "User '$username' Deleted";
			AdminLog::getInstance()->log("User $username deleted");			
			//$users = database_get_user_names();
			$smarty->assign("error", $error);
			//$smarty->assign("users", $users);
			//$smarty->display('listusers.tpl');
			require('display.php');
			die;
		}else
		{
			$error = 'Please type "Yes, I want to delete this user" (without the quotes) into the box before clicking delete user';
		}
		
	}

	$smarty->assign("error", $error);
	$smarty->assign("user", getDBUserDetails($_GET['username']));
	$smarty->display('edituser.tpl');

}else
{	# Display all users //TODO: Redirect?
	require('display.php');	
}

?>


