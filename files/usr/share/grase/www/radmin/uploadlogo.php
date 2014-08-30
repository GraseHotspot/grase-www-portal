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
$PAGE = 'uploadlogo';
require_once 'includes/pageaccess.inc.php';


require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

// Logo

    $error = FALSE;
    $success = FALSE;
	if(isset($_POST['newlogosubmit'])) // Upload new logo
	{

        //$error = true;
        //$success = false;


		//$error_logo = "Logo Image not valid";
		//$error_logo = file_upload_error_message($_FILES['newlogo']['error']);
		if ($_FILES['newlogo']['error'] === UPLOAD_ERR_OK)
		{
			//print "Uploading image...";
			if(!file_exists($_FILES['newlogo']['tmp_name']))
			{
				$error = "Logo Failed to upload";
			}elseif($_FILES['newlogo']['size'] > 50960)
			{
				$error = "Logo too big";
			}else
			{
			    // TODO: test if jpg or png
			    // TODO: test if jpeg/jpg/png extension otherwise browser doesn't know type
				//print "Attempting to test if png";
				if(exif_imagetype($_FILES['newlogo']['tmp_name']) != IMAGETYPE_PNG)
				{
					$error = "Logo is not a png";
				}else
				{
				    // TODO: don't overwrite logo.X, upload to logo dir and remember name to add to css/html
					//print "Attempting to move file";
					if(move_uploaded_file($_FILES['newlogo']['tmp_name'], '/usr/share/grase/www/images/logo.png'))
					{
					    $error = false;
						$success = "Logo Updated (you may need to refresh your browser to see the change)";
						AdminLog::getInstance()->log("New Logo Uploaded");
					}else
					{			
						$error = "Unable to save new logo to server";
					}
				}
			}
		}
		else
		{
		    $error = \Grase\Util::fileUploadErrorCodeToMessage($_FILES['newlogo']['error']);
		}
	}
	if($error)
	    $templateEngine->assign("error", array($error));
	    
    if($success)
        $templateEngine->assign("success", array($success));

	display_page('uploadlogo.tpl');

?>


