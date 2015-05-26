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
$PAGE = 'vouchers';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';


function array_filter_num($var)
{
    if (!is_numeric($var)) {
        return false;
    }
        return($var != '');
}

$error = array();
$warning = array();
$success = array();

if (isset($_POST['submit'])) {
/* Filter out blanks. Key index is maintained with array_filter so
     * name->expiry association is maintained */
    //$groupnames = array_filter($_POST['groupname']);
    $vouchernames = $_POST['vouchername'];
    $voucherprice = array_filter($_POST['voucherprice'], "array_filter_num");
    $vouchergroup = array_filter($_POST['vouchergroup']);
    $vouchermaxmb = array_filter($_POST['voucherMax_Mb']);
    $vouchermaxtime = array_filter($_POST['voucherMax_Time']);
    $voucherinit = array_filter($_POST['initialvoucher']);
    $vouchertopup = array_filter($_POST['topupvoucher']);
    $voucherdesc = array_filter($_POST['voucherdescription']);

    if (sizeof($voucherinit) == 0) {
        $success[] = T_("No Initial vouchers defined, users will be unable to purchase a new account");
    }

    //$Expiry = array();
    foreach ($vouchernames as $key => $name) {
    // There are attributes set but no group name
        if (\Grase\Clean::text($name) == '') {
            if (isset($voucherprice[$key]) ||
                isset($vouchermaxmb[$key]) ||
                isset($vouchermaxtime[$key]) ||
                isset($voucherinit[$key]) ||
                isset($vouchertopup[$key]) ||
                isset($voucherdesc[$key])
            ) {
                $warning[] = T_("Invalid voucher name or voucher name missing");
            }
            // Just loop as trying to process a group without a name is hard so they will just have to reenter those details
            continue;
            
        }
        
        if (!isset($voucherprice[$key])) {
            $error[] = T_("Vouchers need a price");

        } else {
            // Don't want to show both errors
            if(!\Grase\Validate::validateNumber($voucherprice[$key])) {
                $error[] = T_('Invalid price');
            }
        }
        
        
        if (!(isset($vouchermaxmb[$key]) || isset($vouchermaxtime[$key]))) {
            $warning[] = T_("It is not recommended having vouchers without a data or time limit");
        }
        
        
        // validate limits
        //$error[] = validate_datalimit($groupdatalimit[$key]);
    
        // Silence warnings (@) as we don't care if they are set or not'
        if (!\Grase\Validate::numericLimit($vouchermaxtime[$key])) {
            $error[] = sprintf(T_("Invalid value '%s' for Time Limit"), $vouchermaxtime[$key]);
        }
        if (!\Grase\Validate::numericLimit($vouchermaxmb[$key])) {
            $error[] = sprintf(T_("Invalid value '%s' for Data Limit"), $vouchermaxmb[$key]);
        }
        
        // TODO validate groupname, it already comes in in the correct format though
        
        $error = array_filter($error);
    

        $vouchersettings[\Grase\Clean::groupName($name)] = array_filter(array(
            'VoucherName' => \Grase\Clean::groupName($name),
            'VoucherLabel' => \Grase\Clean::text($name),
            'VoucherPrice' => @ clean_number($voucherprice[$key]),
            'VoucherGroup' => $vouchergroup[$key],
            'MaxMb'     => @ clean_number($vouchermaxmb[$key]),
            'MaxTime'   => @ clean_int($vouchermaxtime[$key]),
            'Description' => @ \Grase\Clean::text($voucherdesc[$key]),
            'TopupVoucher' => $vouchertopup[$key] ? true : false,
            'InitVoucher' => $voucherinit[$key] ? true : false,
            
        ));

    }
    
    if (sizeof($error) == 0) {
    // No errors. Save groups
        //$Settings->setSetting("groups", serialize($groupexpiries));
        foreach ($vouchersettings as $attributes) {
        //$Settings->setGroup($attributes);
            $Settings->setVoucher($attributes);
        }
        
        // Delete vouchers no longer referenced
        foreach ($Settings->getVoucher() as $oldvoucher => $oldvouchersettings) {
            if (!isset($vouchersettings[$oldvoucher])) {
                $Settings->deleteVoucher($oldvoucher);
            }
        }
        
        $success[] = T_("Vouchers updated");
    }
    
    $error = array_unique(array_merge($error, $warning));
    if (sizeof($error) > 0) {
        $templateEngine->assign("error", $error);
    }
    if (sizeof($success) > 0) {
        $templateEngine->assign("success", $success);
    }

}


$templateEngine->assign("vouchersettings", $Settings->getVoucher());
$templateEngine->displayPage('vouchers.tpl');
