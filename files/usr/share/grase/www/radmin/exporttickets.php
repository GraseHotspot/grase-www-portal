<?php

/* Copyright 2012 Timothy White */

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
$PAGE = 'users';
require_once 'includes/pageaccess.inc.php';


require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
if(isset($_GET['batch']))
{
    $batches = explode(',', $_GET['batch']);

    $users = array();
    foreach($batches as $batch)
    {
        $batch = clean_number($batch);
	    $fetchusers = DatabaseFunctions::getInstance()->getMultipleUsersDetails($Settings->getBatch($batch) );
	    if(!is_array($fetchusers)) $fetchusers = array();
	    $users = array_merge($users, $fetchusers);
	}
	
	// TODO: replace , with _ in below
    $title = sprintf(T_('Batch_%s_details'), implode('-', $batches));	
}else //TODO remove the lastbatch part?
{
        $batch = $Settings->getSetting('lastbatch');
	$users = DatabaseFunctions::getInstance()->getMultipleUsersDetails($Settings->getBatch($batch) );
	if(!is_array($users)) $users = array();
	$title = sprintf(T_('Batch_%s_details'), $batch);
}	

	generate_csv($users, $title);
	
function generate_csv($users, $title)
{
    global $Settings;
    
    $groupsettings = grouplist();
    
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=$title.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    $details = array();
    $details[] = array(
        T_("Username"),
        T_("Password"),
        T_("Expiry"),
        T_("Voucher Type"));
    
    foreach($users as $user)
    {
        $expiry = $user['FormatExpiration'];
        if($user['FormatExpiration'] == '--') $expiry = '';
        $details[] = array(
                $user['Username'],
                $user['Password'],
                $expiry,
                $groupsettings[$user['Group']]);
    }
    
    // Following based off http://www.php.net/manual/en/function.fputcsv.php#100033
    $outstream = fopen("php://output", 'w');

    function __outputCSV(&$vals, $key, $filehandler) {
        fputcsv($filehandler, $vals);
    }
    array_walk($details, '__outputCSV', $outstream);

    fclose($outstream);


}	
?>
