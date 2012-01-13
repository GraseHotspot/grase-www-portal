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
}else
{
	$users = database_get_users(unserialize($Settings->getSetting('lastbatch')));
	if(!is_array($users)) $users = array();
}	
	$users_groups = sort_users_into_groups($users); // TODO: Reports and then no longer sort user list by downloads??
	$smarty->assign("users", $users);
	$smarty->assign("users_groups", $users_groups);
	$smarty->register_modifier( "sortby", "smarty_modifier_sortby" );   
	//display_page('printnewtickets.tpl');
	
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
	
	
	
	
	generate_pdf($users);
	
function generate_pdf($users){
/*    require_once('/usr/share/tcpdf/config/lang/eng.php');
    require_once('/usr/share/tcpdf/tcpdf.php');

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('GRASE Hotspot');
    $pdf->SetTitle('Voucher Batch X');
    $pdf->SetSubject('Hotspot Login Vouchers');

    $pdf->AddPage();
    
// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);    

// set some text for example
$txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

// Multicell test
$pdf->MultiCell(55, 5, '[LEFT] '.$txt, 1, 'L', 0, 0, 0, 0, true);
$pdf->MultiCell(55, 5, '[RIGHT] '.$txt, 1, 'R', 0, 0, 60, 0, true);
$pdf->MultiCell(55, 5, '[CENTER] '.$txt, 1, 'C', 0, 0, 30, 10, true);
$pdf->MultiCell(55, 5, '[JUSTIFY] '.$txt."\n", 1, 'J', 0, 0, '' ,'', true);
$pdf->MultiCell(55, 5, '[DEFAULT] '.$txt, 1, '', 0, 0, '', '', true);
    
    
    
    
    
    
    
    $pdf->Output('batchX.pdf', 'I');*/
    
$txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';    
    
    $labels = new PDFLabels('Avery 5160');
    foreach($users as $user)
    {
        $label = T_("Username").": ".$user['Username'];
        $label .= "\n".T_("Password").": ".$user['Password'];
        $label .= "\n".T_("Voucher Type").": ".$user['Group'];        
        $labels->Add_PDF_Label($label);
    }
    $labels->Output_Doc();

}	
?>
