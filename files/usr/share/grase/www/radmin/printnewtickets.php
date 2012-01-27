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
$PAGE = 'users';
require_once 'includes/pageaccess.inc.php';


require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';
if(isset($_GET['user']))
{
    $users = database_get_users(array(clean_text($_GET['user'])));
	if(!is_array($users)) $users = array();    
	$title = clean_text($_GET['user']) . ' Voucher';
}elseif(isset($_GET['batch']))
{
    $batch = clean_number($_GET['batch']);
	$users = database_get_users($Settings->getBatch($batch) );
	if(!is_array($users)) $users = array();
	$title = sprintf(T_('Batch %s Vouchers'), $batch);
}else
{
    $batch = $Settings->getSetting('lastbatch');
	$users = database_get_users($Settings->getBatch($batch) );
	if(!is_array($users)) $users = array();
	$title = sprintf(T_('Batch %s Vouchers'), $batch);
}	

    /* Don't need any smarty stuff here as we now do pdf
	$users_groups = sort_users_into_groups($users); // TODO: Reports and then no longer sort user list by downloads??
	$smarty->assign("users", $users);
	$smarty->assign("users_groups", $users_groups);
	$smarty->register_modifier( "sortby", "smarty_modifier_sortby" );   
	//display_page('printnewtickets.tpl');
	*/
	
	$preset_labels['Avery 5160'] = array(
	    'name'=>'5160',
	    'paper-size'=>'letter',     
	    'metric'=>'mm',     
	    'marginLeft'=>1.762,     
	    'marginTop'=>10.7,         
	    'NX'=>3,     
	    'NY'=>10,     
	    'SpaceX'=>3.175,     
	    'SpaceY'=>0,     
	    'width'=>66.675,     
	    'height'=>25.4,         
	    'font-size'=>8
	    );
	
	generate_pdf($users, $title);
	
function generate_pdf($users, $title)
{
    global $Settings;
    /* The following will work in the future */
    //$ssid = $Settings->getSetting('printSSID');
    //$print_group = $Settings->getSetting('printGroup');
    
    // These settings are temporarily in network settings
    $networksettings = unserialize($Settings->getSetting('networkoptions'));
    $ssid = $networksettings['printSSID'];
    $print_group = $networksettings['printGroup'];
    
    $labels = new PDFLabels('Overflow', $title);
    foreach($users as $user)
    {
        $label = '';
        if($ssid)
            $label .= sprintf(T_("Wireless Network: %s"), $ssid) . "\n";        
            
        $label .= sprintf(T_("Username: %s"), $user['Username']) . "\n";
        $label .= sprintf(T_("Password: %s"), $user['Password']) . "\n";
        
        if($print_group)
            $label .= sprintf(T_("Voucher Type: "), $user['Group']) . "\n";
            
        $labels->Add_PDF_Label($label);
    }
    $labels->Output_Doc();

}	
?>
