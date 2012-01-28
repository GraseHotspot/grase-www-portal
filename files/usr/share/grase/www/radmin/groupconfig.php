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
$PAGE = 'groups';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

$error = array();
$success = array();

if(isset($_POST['submit']))
{
    /* Filter out blanks. Key index is maintained with array_filter so
     * name->expiry association is maintained */
    //$groupnames = array_filter($_POST['groupname']);
    $groupnames = $_POST['groupname'];
    $groupexpiry = array_filter($_POST['groupexpiry']);
    $groupdatalimit = array_filter($_POST['Group_Max_Mb']);
    $grouptimelimit = array_filter($_POST['Group_Max_Time']);    
    $groupdownlimit = array_filter($_POST['Bandwidth_Down_Limit']);
    $groupuplimit = array_filter($_POST['Bandwidth_Up_Limit']);    
    //$grouprecurdatalimit = array_filter($_POST['Recur_Data_Limit']);
    //$grouprecurdata = array_filter($_POST['Recur_Data']);    
    $grouprecurtimelimit = array_filter($_POST['Recur_Time_Limit']);
    $grouprecurtime = array_filter($_POST['Recur_Time']);
    $simultaneoususe = array_filter($_POST['SimultaneousUse']);    
    

    

    if(sizeof($groupnames) == 0)
    {
        $error[] = T_("A minimum of one group is required");
    }
    if(sizeof($groupexpiry) < sizeof($groupnames) - 1)
    {
        $success[] = T_("It is not recommended having groups without expiries.");
    }
    

    //$Expiry = array();
    foreach($groupnames as $key => $name)
    {
        // There are attributes set but no group name
        if(clean_text($name) == '')
        {
            if(
                isset($groupexpiry[$key]) ||
                isset($groupdatalimit[$key]) ||
                isset($grouptimelimit[$key]) ||
                isset($groupdownlimit[$key]) ||
                isset($groupuplimit[$key]) ||
                //isset($grouprecurdatalimit[$key]) ||
                //isset($grouprecurdata[$key]) ||
                isset($grouprecurtimelimit[$key]) ||
                isset($grouprecurtime[$key])
            )
            {
                $error[] = T_("Invalid group name or group name missing");
            }
            /*else
            {
                continue;
            }*/
            // Just loop as trying to process a group without a name is hard so they will just have to reenter those details
            continue;
            
        }
        // Process expiry's    
        $groupexpiries[clean_text($name)] = $groupexpiry[clean_text($key)];
        
        // Validate expiries
        if(isset($groupexpiry[$key]))
        {
            if(strtotime($groupexpiry[$key]) == FALSE)
            {
                $error[] = sprintf(T_("%s: Invalid expiry format"), $name);
            }
            elseif(strtotime($groupexpiry[$key]) < time())
            {
                $error[] = sprintf(T_("%s: Expiry can not be in the past"), $name);
            }
        }
        
        // Process radgroupreply options
        
        // validate limits
	//$error[] = validate_datalimit($groupdatalimit[$key]);
	
	// Silence warnings (@) as we don't care if they are set or not'
	$error[] = @ validate_timelimit($grouptimelimit[$key]);
	$error[] = @ validate_timelimit($grouprecurtimelimit[$key]);   
	//$error[] = validate_datalimit($grouprecurdatalimit[$key]);
	$error[] = @ validate_recur($grouprecurtime[$key]);
	$error[] = @ validate_recur($grouprecurdata[$key]);
	$error[] = @ validate_recurtime($grouprecurtime[$key], $grouprecurtimelimit[$key]);	    
	$error[] = @ validate_bandwidth($groupdownlimit[$key]);
	$error[] = @ validate_bandwidth($groupuplimit[$key]);
	$error[] = @ validate_yesno($simultaneoususe[$key]);	    
	$error = array_filter($error);
	
	if(isset($grouprecurtime[$key]) xor isset($grouprecurtimelimit[$key]))
	{
	    $error[] = sprintf(T_("Need both a time limit and recurrance for '%s'"), clean_text($name));
	}
	
	/*if(isset($grouprecurdata[$key]) xor isset($grouprecurdatalimit[$key]))
	{
	    $error[] = sprintf(T_("Need both a data limit and recurrance for '%s'"), clean_text($name));
	}*/	    
	
        $groups[clean_text($name)] = array_filter(array(
            //'MaxMb' => clean_number($groupdatalimit[$key]),
            //'MaxTime' => clean_int($grouptimelimit[$key]),
            //'DataRecurTime' => clean_text($grouprecurdata[$key]),
            //'DataRecurLimit' => clean_number($grouprecurdatalimit[$key]),
            'TimeRecurTime' => @ clean_text($grouprecurtime[$key]),
            'TimeRecurLimit' => @ clean_int($grouprecurtimelimit[$key]),
            'BandwidthDownLimit' => @ clean_int($groupdownlimit[$key]),
            'BandwidthUpLimit' => @ clean_int($groupuplimit[$key]),
            'SimultaneousUse' => @ $simultaneoususe[$key],
        ));
        $groupsettings[clean_text($name)] = array_filter(array(
            'GroupName' => clean_text($name),
            'Expiry'    => @ $groupexpiry[$key],
            'MaxMb'     => @ clean_number($groupdatalimit[$key]),
            'MaxTime'   => @ clean_int($grouptimelimit[$key]),
        ));        

    }
    

    
    if(sizeof($error) == 0)
    {

        // No errors. Save groups
        //$Settings->setSetting("groups", serialize($groupexpiries));
        foreach($groupsettings as $attributes)
        {
            $Settings->setGroup($attributes);
        }
        
        // Delete groups no longer referenced
        foreach($Settings->getGroup() as $oldgroup => $oldgroupsettings)
        {
            if(!isset($groupsettings[$oldgroup]))
                $Settings->deleteGroup($oldgroup);
        }
        
        // Delete groups from radgroupreply not in groupexpiries...
        // Deleting groups out of radgroupreply will modify current users
        // Need to do check for any users still using group, if no user then delete
        // TODO: check for groups that have not changed so don't run this on them
        // TODO: cron function that removes groups no longer referenced anywhere
        foreach($groups as $name => $group)
        {
     
            DatabaseFunctions::getInstance()->setGroupAttributes($name, $group);
        }
        
        $success[] = T_("Groups updated");
    } 
    
    if(sizeof($error) > 0) $smarty->assign("error", $error);	
    if(sizeof($success) > 0) $smarty->assign("success", $success);
    
    // TODO set this initially
    $smarty->assign("groupdata", $groups);
    $smarty->assign("groupsettings", $Settings->getGroup());
    //$smarty->assign("groups", $Expiry);

	display_page('groups.tpl');
      
}
else{

	$smarty->assign("groupdata", DatabaseFunctions::getInstance()->getGroupAttributes());
    $smarty->assign("groupsettings", $Settings->getGroup());

    //$smarty->assign("groups", $Expiry);

	display_page('groups.tpl');
	
}

?>


