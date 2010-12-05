<?php

/* Copyright 2008 Timothy White */

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

// TODO: Make a proper settings file?

	$smarty->assign("location", $location);
	$smarty->assign("pricemb", $pricemb);
	$smarty->assign("pricetime", $pricetime);
	$smarty->assign("currency", $currency);
	$smarty->assign("dispcurrency", $CurrencySymbols[$currency]);
	$smarty->assign("sellable_data", $sellable_data/1048576);
	$smarty->assign("useable_data", $useable_data/1048576);
	$smarty->assign("support_name", $support_name);
	$smarty->assign("support_link", $support_link);
	$smarty->assign("website_name", $website_name);
	$smarty->assign("website_link", $website_link);

//$old_error_level = error_reporting(1); // TODO: Don't have this catching stuff

// Location
    $error_locationname = false;
	if(isset($_POST['changelocationsubmit'])) // Change Location Name
	{
		$error_locationname = "Location Name not valid";
		$new_location = trim(clean_text($_POST['newlocationname']));
		if($new_location != "")
		{
			if($Settings->setSetting('locationName', $new_location))
			{
				$error_locationname = "Location Name Changed";
				AdminLog::getInstance()->log("Location Name changed to $new_location");
				$smarty->assign("location", $new_location);
				$smarty->assign("Title", $new_location . " - " . APPLICATION_NAME);
			}
			else
			{
			    $error_locationname = "Error Saving Location Name to Setting files";
			}
		}
	}
	$smarty->assign("error_locationname", $error_locationname);

// Logo
    $error_logo = false;
	if(isset($_POST['changelogosubmit'])) // Change Location Name
	{

		$error_logo = "Logo Image not valid";
		$error_logo = file_upload_error_message($_FILES['newlogo']['error']);
		if ($_FILES['newlogo']['error'] === UPLOAD_ERR_OK)
		{
			//print "Uploading image...";
			if(!file_exists($_FILES['newlogo']['tmp_name']))
			{
				$error_logo = "Logo Failed to upload";
			}elseif($_FILES['newlogo']['size'] > 20480)
			{
				$error_logo = "Logo too big";
			}else
			{
				//print "Attempting to test if png";
				if(exif_imagetype($_FILES['newlogo']['tmp_name']) != IMAGETYPE_PNG)
				{
					$error_logo = "Logo is not a png";
				}else
				{
					//print "Attempting to move file";
					if(move_uploaded_file($_FILES['newlogo']['tmp_name'], '/usr/share/grase/www/images/logo.png'))
					{
						$error_logo = "Logo Updated (you may need to refresh your browser to see the change)";
						AdminLog::getInstance()->log("New Logo Uploaded");
					}else
					{			
						$error_logo = "Unable to save new logo to server";
					}
				}
			}
		}
	}
	$smarty->assign("error_logo", $error_logo);

// Website

    $error_website = false;
	if(isset($_POST['changewebsitesubmit']))
	{ 
		$error_website = "Website Details not valid";
		$new_websitename = trim(clean_text($_POST['newwebsitename']));
		$new_websitelink = trim(clean_text($_POST['newwebsitelink']));
		if($new_websitename != "" && $new_websitelink != "" && strpos($new_websitelink, ' ') === false)
		{
		    $error_website = "Unabled to update website details";
			if(
			    $Settings->setSetting('websiteName', $new_websitename) > 0 &&
			    $Settings->setSetting('websiteLink', $new_websitelink) > 0			    
			)
			{
				$error_website = "Website Updated";
				AdminLog::getInstance()->log("Website settings updated");
				$smarty->assign("website_name", $new_websitename);
				$smarty->assign("website_link", $new_websitelink);
			}
		}
	}
	$smarty->assign("error_website", $error_website);

// Pricing
    $error_price = false;
	if(isset($_POST['changepricingsubmit']))
	{ 
		$error_price = "";
		$new_pricemb = trim(clean_text($_POST['newpricemb']));
		$new_pricetime = trim(clean_text($_POST['newpricetime']));
		$new_currency = trim(clean_text($_POST['newcurrency']));

		if($new_pricemb != "" && is_numeric($new_pricemb))
		{
			if($Settings->setSetting('priceMb', $new_pricemb))
			{
				$error_price .= "Price/Mb Changed<br/>";
				AdminLog::getInstance()->log("Price/Mb changed");
				$smarty->assign("pricemb", $new_pricemb);
			}else
			{
				$error_price .= "Unable to update Price/Mb<br/>";
			}
		}else
		{
			$error_price .= "Invalid Price/Mb<br/>";
		}

		if($new_pricetime != "" && is_numeric($new_pricetime))
		{
			if($Settings->setSetting('priceMinute', $new_pricetime))
			{
				$error_price .= "Price/Time Changed<br/>";
				AdminLog::getInstance()->log("Price/Time changed");
				$smarty->assign("pricetime", $new_pricetime);
			}else
			{
				$error_price .= "Unable to update Price/Minute<br/>";
			}
		}else
		{
			$error_price .= "Invalid Price/Minute<br/>";
		}

		if($new_currency != "" && strlen($new_currency) < 4)
		{
			if($Settings->setSetting('currency', $new_currency))
			{
				$error_price .= "Currency Changed";
				AdminLog::getInstance()->log("Currency changed to ${CurrencySymbols[$new_currency]}");
				$smarty->assign("currency", $new_currency);
				$smarty->assign("dispcurrency", $CurrencySymbols[$new_currency]);
			}else
			{
				$error_price .= "Unable to update Currency<br/>";
			}
		}else
		{
			$error_price .= "Invalid Currency<br/>";
		}


	}
	$smarty->assign("error_pricing", $error_price);

// Data limits

    $error_data = false;
	if(isset($_POST['changedatasubmit']))
	{ 
		$error_data = "Data Limits not valid";
		$new_selldata = trim(clean_text($_POST['newsellable_data']));
		$new_usedata = trim(clean_text($_POST['newuseable_data']));
		if($new_selldata != "" && is_numeric($new_selldata) && $new_usedata != "" && is_numeric($new_usedata) )
		{
			if($Settings->setSetting('sellableData', $new_selldata*1048576) && $Settings->setSetting('useableData', $new_usedata*1048576)) // TODO: Make this octets properly and combine octets functions with other areas
			{
				$error_data = "Data Limits changed";
				AdminLog::getInstance()->log("Graph Data Limits changed");
				$smarty->assign("sellable_data", $new_selldata);
				$smarty->assign("useable_data", $new_usedata);
			}
		}
	}
	$smarty->assign("error_data", $error_data);

// Support Contact

    $error_support = false;
	if(isset($_POST['changesupportsubmit']))
	{ 
		$error_support = "Support Contact Details not valid";
		$new_supportname = trim(clean_text($_POST['newsupportname']));
		$new_supportlink = trim(clean_text($_POST['newsupportlink']));
		if($new_supportname != "" && $new_supportlink != "" && strpos($new_supportlink, ' ') === false)
		{
			if($Settings->setSetting('supportContactLink', $new_supportlink) && $Settings->setSetting('supportContactName', $new_supportname))
			{
				$error_support = "Support Contact Details Updated";
				AdminLog::getInstance()->log("Support Contact Details changed");
				$smarty->assign("support_name", $new_supportname);
				$smarty->assign("support_link", $new_supportlink);
			}
		}
	}
	$smarty->assign("error_support", $error_support);

//error_reporting($old_error_level);
	require('includes/site_settings.inc.php'); // ReRead settings
	display_page('settings.tpl');

?>


