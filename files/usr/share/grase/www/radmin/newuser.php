<?php

/* Copyright 2008 Timothy White */

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

function validate_form()
{
	global $expirydate;
	$error="";
	if(! checkDBUniqueUsername($_POST['Username'])) $error.= "Username already taken<br/>";
	if ( ! $_POST['Username'] || !$_POST['Password'] ) $error.="Username and Password are both Required<br/>";
	
	$MaxMb = ereg_replace("[^\.0-9]", "", $_POST['MaxMb'] );
	$Max_Mb = ereg_replace("[^\.0-9]", "", $_POST['Max_Mb'] );	
	$MaxTime = ereg_replace("[^\.0-9]", "", $_POST['MaxTime'] );
	$Max_Time = ereg_replace("[^\.0-9]", "", $_POST['Max_Time'] );	
	

	$error.= validate_datalimit($MaxMb);
	$error.= validate_datalimit($Max_Mb);
	$error.= validate_timelimit($MaxTime);
	$error.= validate_timelimit($Max_Time);		
	if($Max_Mb && $MaxMb) $error.="Only set one Data limit field";
	if($Max_Time && $MaxTime) $error.="Only set one Time limit field";

	list($error2, $expirydate) = validate_post_expirydate();
	$error.=$error2;
	$error.= validate_group($_POST['Username'], $_POST['Group']);
	return $error;
}



if(isset($_POST['newusersubmit']))
{
	$error=validate_form();
	if($error ){
		$user['Username'] = clean_text($_POST['Username']);
		$user['Password'] = clean_text($_POST['Password']);
		$user['MaxMb'] = ereg_replace("[^\.0-9]", "", clean_text($_POST['MaxMb']));
		$user['Max_Mb'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Mb']));		
		$user['MaxTime'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['MaxTime']));
		$user['Max_Time'] = ereg_replace("[^\.0-9]", "",clean_text($_POST['Max_Time']));	
		$user['Group'] = clean_text($_POST['Group']);
		$user['Expiration'] = expiry_for_group(clean_text($_POST['Group'])); //"${_POST['Expirydate_Year']}-${_POST['Expirydate_Month']}-${_POST['Expirydate_Day']}";
		$user['Comment'] = clean_text($_POST['Comment']);
		$smarty->assign("user", $user);
		$smarty->assign("error", "Error in data, please correct and try again<br/>$error");
		display_page('adduser.tpl');
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
		$success = database_create_new_user(
			clean_text($_POST['Username']),
			clean_text($_POST['Password']),
			$MaxMb,
			$MaxTime,
			expiry_for_group(clean_text($_POST['Group'])),
			clean_text($_POST['Group']),
			clean_text($_POST['Comment'])
		);
		AdminLog::getInstance()->log("Created new user ${_POST['Username']}");
		$smarty->assign("messagebox", "$message<br/>User Successfully Created");
		display_adduser_form();
	}
}else
{
	display_adduser_form();
}

function display_adduser_form()
{
	global $smarty;
//    $user['Username'] = rand_username(5);	
	$user['Password'] = rand_password(6);
	$user['Max_Mb'] = 50;
	$user['Expiration'] = "--";//date('Y-m-d', strtotime('+3 month'));
	$smarty->assign("user", $user);
	display_page('adduser.tpl');
}

?>


