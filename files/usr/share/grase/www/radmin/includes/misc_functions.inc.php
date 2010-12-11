<?

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


/* NOTE: This function is based on http://snipplr.com/view/5444/random-pronounceable-passwords-generator/ */
function rand_password($len)
{
	$c = "bcdfghjklmnprstvwz";
	$v = "aeiou";
	$password = "";

	#change 4 to how many sylabols
	for($i=0;$i < ($len/3); $i++){
	    if(!rand(0,1))
	    {
            	
		    $password.= $c[rand(0, strlen($c)-1)];
		    $password.= $v[rand(0, strlen($v)-1)];
		    $password.= $c[rand(0, strlen($c)-1)];
		    if($i+1 < ($len/3)) $password.=rand(1,9);
		    if($i+1 < ($len/3)) $password.=rand(1,9);		
		}else{
//		    if($i+1 < ($len/3)) $password.=rand(1,9);
//		    if($i+1 < ($len/3)) $password.=rand(1,9);				
		    $password.= $c[rand(0, strlen($c)-1)];
		    $password.= $v[rand(0, strlen($v)-1)];
		    $password.= $c[rand(0, strlen($c)-1)];
		}
	}
    if(strlen($password) < $len + 2) $password.=rand(1,9);
    if(strlen($password) < $len + 2) $password.=rand(1,9);				
	
	return $password;
}

/* This function is a modified version of the above function */
function rand_username($len)
{
	$c = "bcdfghjklmnprstvwz";
	$v = "aeiou";
	$password = "";

	#change 4 to how many sylabols
	for($i=0;$i < ($len/2); $i++){
	    if(rand(0,1))
	    {
		    if($i+1 < ($len/2)) $password.=rand(1,9);	    
		    $password.= $c[rand(0, strlen($c)-1)];
		    $password.= $v[rand(0, strlen($v)-1)];	    
	    }else
	    {	    
    //		$password.= $c[rand(0, strlen($c)-1)];
		    $password.= $v[rand(0, strlen($v)-1)];
		    $password.= $c[rand(0, strlen($c)-1)];
		    if($i+1 < ($len/2)) $password.=rand(1,9);
        }		
	}
	return $password;
}

/*function expiration_date_format($date)
{
	list($year, $month, $day) = split("-", $date);
	if($year && $month && $day) 	return date("F d Y H:i:s", makeTimeStamp($year, $month, $day));
	if(!$year && !$month && !$day) return "";
	die("Problem With expiration Date Format");
	//	return date("F d Y H:i:s", makeTimeStamp($year, $month, $day));
}*/

function expiration_to_timestamp($date)
{
	return strtotime($date);
/*	list($year, $month, $day) = split("-", $date);
	return l($year, $month, $day);*/
}

/* NOTE: This function is from Smarty Docs http://www.smarty.net/docs/en/tips.dates.tpl */
function makeTimeStamp($year='', $month='', $day='')
{
   if(empty($year))
   {
       $year = strftime('%Y');
   }
   if(empty($month))
   {
       $month = strftime('%m');
   }
   if(empty($day)) 
   {
       $day = strftime('%d');
   }

   return mktime(0, 0, 0, $month, $day, $year);
}




// Validation functions
function validate_post_expirydate()
{
	$error='';
	$expirydate ="${_POST['Expirydate_Year']}-${_POST['Expirydate_Month']}-${_POST['Expirydate_Day']}";
	if ( ! $_POST['Expirydate_Day'] &&
		 ! $_POST['Expirydate_Month'] &&
		 ! $_POST['Expirydate_Year'])
	{
		$expirydate='';/* No Expiry */ 
	}
	
	if ($expirydate &&
	 	! (	$_POST['Expirydate_Day'] &&
	 		$_POST['Expirydate_Month'] &&
	 		$_POST['Expirydate_Year'])
	 	)
	{
	 	/* Invalid date */
	 	$error.="Invalid Expiry Date<br/>";
	}
	
	if( $expirydate &&
		makeTimeStamp(
			$_POST['Expirydate_Year'],
			$_POST['Expirydate_Month'],
			$_POST['Expirydate_Day'] ) < time()
		)
	{
		$error.="Expiry Date in the past<br/>";
	}
	return array($error,$expirydate);
}

function validate_datalimit($limit)
{
	if ($limit && ! is_numeric($limit) ) return "Invalid value '$limit' for Data Limit<br/>";
	// TODO: Return what?
}

function validate_timelimit($limit)
{
	if ($limit && ! is_numeric($limit) ) return "Invalid value '$limit' for Time Limit<br/>";
	// TODO: Return what?
}

function validate_int($number)
{
	if ($number && is_numeric($number) && trim($number) != "") return "";
    return "Invalid number '$number' (Must be whole number)<br/>";
	// TODO: Return what?
}

function validate_group($username, $group)
{
	global $Usergroups;
	if(isset($Usergroups[$group]))
	{
		if($group == MACHINE_GROUP_NAME && strpos($username, "-dev") === false) // TODO: This no longer works for newer coovachilli, check for mac address format 00-00-00-00-00-00
			return _("Only Machines can be in the Machine group<br/>"); // TODO: Internationalsation of all strings
		return "";
	}else
	{
		return "Invalid Group<br/>";
	}
}

function expiry_for_group($group)
{
	global $Expiry;
	if(isset($Expiry[$group]) && $Expiry[$group] != '--') return date('Y-m-d', strtotime($Expiry[$group]));
	if(isset($Expiry[$group]) && $Expiry[$group] == '--') return "--";
	return date('Y-m-d', strtotime($Expiry[DEFAULT_GROUP_NAME]));
}

/*function user_account_status($Userdata)
{
	if(isset($Userdata['ExpirationTimestamp']) && $Userdata['ExpirationTimestamp'] < time())
	{
	    $status = EXPIRED_ACCOUNT;
	}
	elseif(isset($Userdata['Max-Octets']) && ($Userdata['Max-Octets'] - $Userdata['AcctTotalOctets']) <= 0 )
	{
	    $status = LOCKED_ACCOUNT;
	}
	elseif(isset($Userdata['Max-Octets']) && ($Userdata['Max-Octets'] - $Userdata['AcctTotalOctets']) <= 1024*1024*2 )
	{
	    $status = LOWDATA_ACCOUNT;
	}
	elseif($Userdata['Group'] == MACHINE_GROUP_NAME)
	{
	    $status = MACHINE_ACCOUNT; 
	}
	elseif($Userdata['Group'] != "")
	{
	    $status = NORMAL_ACCOUNT;
	}
	else
	{
	    $status = NOGROUP_ACCOUNT;
	}
	return $status;
}*/

function sort_users_into_groups($users)
{
	$users_group = array();
	foreach($users as $user)
	{
		if(isset($user['Group']) && $user['Group'] != '')
		{
			$users_group[$user['Group']][] = $user;
		}else
		{
			$users_group['Nogroup'][] = $user;
		}
	}
	return $users_group;
}

function clean_text($text)
{

	$text = strip_tags($text);
	$text = str_replace("<", "", $text);
	$text = str_replace(">", "", $text);

#	$text = htmlspecialchars($text, ENT_NOQUOTES);
#	$text = mysql_real_escape_string($text);

	return $text;
}


/* TODO: check where this code came from */
function file_upload_error_message($error_code)
{
    switch ($error_code)
    {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
		case 2:
            return 'Uploaded Image was too big';
            
        case UPLOAD_ERR_PARTIAL:
            return 'Error In Uploading';
            
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
            
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
            
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
            
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
            
        default:
            return 'Unknown upload error';
    }
}

/* TODO: check where this code came from */
function sha1salt($plainText, $salt = null) 
    {
        $SALT_LENGTH = 9;
        if ($salt === null) 
        {
            $salt = substr(md5(uniqid(rand() , true)) , 0, $SALT_LENGTH);
        }
        else
        {
            $salt = substr($salt, 0, $SALT_LENGTH);
        }
        
        return $salt . sha1($salt . $plainText);
    }

?>
