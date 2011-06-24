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
	$C = "BCDFGHJKLMNPRSTVWZ";
	$c = "bcdfghjklmnprstvwz";
	$v = "aeiou";
	$V = "AEIOU";

	$password = "";
	$syllables = 3; 

	for($i=0;$i < ($len/$syllables); $i++){
	    if(!rand(0,1))
	    {

		    $password.= $c[rand(0, strlen($c)-1)];
		    $password.= $v[rand(0, strlen($v)-1)];
		    $password.= $c[rand(0, strlen($c)-1)];
		    if($i+1 < ($len/$syllables)) $password.=rand(1,9);
		    if($i+1 < ($len/$syllables)) $password.=rand(1,9);
		    if($i+1 < ($len/$syllables)) $password.=rand(1,9);		    
		}else{
//		    if($i+1 < ($len/3)) $password.=rand(1,9);
//		    if($i+1 < ($len/3)) $password.=rand(1,9);
		    $password.= $c[rand(0, strlen($c)-1)];
		    $password.= $v[rand(0, strlen($v)-1)];
		    $password.= $c[rand(0, strlen($c)-1)];
		}
	}
    if(strlen($password) < $len + 3) $password.=rand(1,9);
    if(strlen($password) < $len + 3) $password.=rand(1,9);
    if(strlen($password) < $len + 3) $password.=rand(1,9);    

	return $password;
}

/* This function is a modified version of the above function */
function rand_username($len)
{
	$c = "bcdfghjklmnprstvwz";
	$v = "aeiou";
	$password = "";
	$syllables = 2; // Short due to username

	for($i=0;$i < ($len/$syllables); $i++){
	    if(rand(0,1))
	    {
		    if($i+1 < ($len/$syllables)) $password.=rand(1,9);
		    $password.= $c[rand(0, strlen($c)-1)];
		    $password.= $v[rand(0, strlen($v)-1)];
	    }else
	    {
    //		$password.= $c[rand(0, strlen($c)-1)];
		    $password.= $v[rand(0, strlen($v)-1)];
		    $password.= $c[rand(0, strlen($c)-1)];
		    if($i+1 < ($len/$syllables)) $password.=rand(1,9);
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
function validate_post_expirydate() // OBSOLETE ?
{
	$error = array();
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
	 	$error[] = T_("Invalid Expiry Date");
	}

	if( $expirydate &&
		makeTimeStamp(
			$_POST['Expirydate_Year'],
			$_POST['Expirydate_Month'],
			$_POST['Expirydate_Day'] ) < time()
		)
	{
		$error[] = T_("Expiry Date in the past");
	}
	return array($error,$expirydate);
}

function validate_datalimit($limit)
{
	if ($limit && ! is_numeric($limit) ) return sprintf(T_("Invalid value '%s' for Data Limit"),$limit);
	// TODO: Return what?
}

function validate_recur($recurrance)
{
    global $Recurtimes;
    if(!isset($Recurtimes[$recurrance])) return sprintf(T_("Invalid recurrance interval '%s'"), $recurrance);
	// TODO: Return what?    
}

function validate_recurtime($recurrance, $time)
{
    // $time is in minutes not seconds
    $Recurtimevales = array(
        'hour' => 60,
        'day' => 60 * 24,
        'week' => 60 * 24 * 7,
        'month' => 60 * 24 * 30);
    if($Recurtimevales[$recurrance] <= $time) return T_("Recurring time limit must be less than interval");
    print_r(array($Recurtimevales[$recurrance], $time, $recurrance));
	// TODO: Return what?    
}

function validate_timelimit($limit)
{
	if ($limit && ! is_numeric($limit) ) return sprintf(T_("Invalid value '%s' for Time Limit"), $limit);
	// TODO: Return what?
}

function validate_mac($macaddress)
{
    // Check string is in format XX-XX-XX-XX-XX-XX (and upper case);
    if(! preg_match('/([0-9A-F]{2}-){5}[0-9A-F]{2}/', $macaddress)) return T_("MAC Address not in correct format");
    // TODO: Check that each XX pair is a valid hex number
}

function validate_int($number)
{
	if ($number && is_numeric($number) && trim($number) != "") return "";
    return sprintf(T_("Invalid number '%s' (Must be whole number)"), $number);
	// TODO: Return what?
}

function validate_group($username, $group)
{
	global $Usergroups;
	if(isset($Usergroups[$group]))
	{
		if($group == MACHINE_GROUP_NAME && strpos($username, "-dev") === false) // TODO: This no longer works for newer coovachilli, check for mac address format 00-00-00-00-00-00
			return T_("Only Machines can be in the Machine group"); // TODO: Internationalsation of all strings
		return "";
	}else
	{
		return T_("Invalid Group");
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
    
    // Sort array alphabetically
	ksort($users_group);
	
	// Remove machines from spot alphapbetically
	$machines = $users_group[MACHINE_GROUP_NAME];
	unset($users_group[MACHINE_GROUP_NAME]);
	
	// Insert machines at end of list (will appear before "All")
	if(sizeof($machines) > 0)
    	$users_group[T_("Computers")] = $machines;
	
	return $users_group;
}

function stripspaces($text)
{
    return str_replace(' ', '', $text);
}

function underscorespaces($text)
{
    return str_replace(' ', '_', $text);
}


function clean_text($text)
{

	$text = strip_tags($text);
	$text = str_replace("<", "", $text);
	$text = str_replace(">", "", $text);

#	$text = htmlspecialchars($text, ENT_NOQUOTES);
#	$text = mysql_real_escape_string($text);

	return trim($text);
}

function clean_number($number)
{
    global $locale;
    $fmt = new NumberFormatter( $locale, NumberFormatter::DECIMAL );
    return $fmt->parse(ereg_replace("[^\.,0-9]", "", clean_text($number)));
}


function clean_int($number)
{
    return bigintval(clean_number($number));
    //ereg_replace("[^0-9]", "", clean_text($number));
}


// bigintval taken from http://stackoverflow.com/questions/990406/php-intval-equivalent-for-numbers-2147483647
function bigintval($value) {
  $value = trim($value);
  if (ctype_digit($value)) {
    return $value;
  }
  $value = preg_replace("/[^0-9](.*)$/", '', $value);
  if (ctype_digit($value)) {
    return $value;
  }
  return 0;
}



/* TODO: check where this code came from */
function file_upload_error_message($error_code)
{
    switch ($error_code)
    {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
		case 2:
            return T_('Uploaded Image was too big');

        case UPLOAD_ERR_PARTIAL:
            return T_('Error In Uploading');

        case UPLOAD_ERR_NO_FILE:
            return T_('No file was uploaded');

        case UPLOAD_ERR_NO_TMP_DIR:
            return T_('Missing a temporary folder');

        case UPLOAD_ERR_CANT_WRITE:
            return T_('Failed to write file to disk');

        case UPLOAD_ERR_EXTENSION:
            return T_('File upload stopped by extension');

        default:
            return T_('Unknown upload error');
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

function displayLocales($number, $isMoney=FALSE, $lg='') {
    global $locale;
    if ( $lg == '') $lg = $locale;

    if($isMoney)
    {
        $fmt = new NumberFormatter( $lg, NumberFormatter::CURRENCY );
        return $fmt->format($number);    
    }else
    {
        $fmt = new NumberFormatter( $lg, NumberFormatter::DECIMAL );
        return $fmt->format($number);    
    }
}

/* // This method uses locales complicated function. See above for Intl method 
function displayLocales_old($number, $isMoney, $lg='') {
    global $locale;
    if ( $lg == '') $lg = $locale;
    $ret = setLocale(LC_ALL, $lg);
    setLocale(LC_TIME, 'Europe/Paris');
    if ($ret===FALSE) {
        echo "Language '$lg' is not supported by this system.\n";
        return;
    }
    $LocaleConfig = localeConv();
    forEach($LocaleConfig as $key => $val) $$key = $val;

    // Sign specifications:
    if ($number>0) {
        $sign = $positive_sign;
        $sign_posn = $p_sign_posn;
        $sep_by_space = $p_sep_by_space;
        $cs_precedes = $p_cs_precedes;
    } else {
        $sign = $negative_sign;
        $sign_posn = $n_sign_posn;
        $sep_by_space = $n_sep_by_space;
        $cs_precedes = $n_cs_precedes;
    }

    // Number format:
    $n = number_format(abs($number), $frac_digits,
        $decimal_point, $thousands_sep);
    $n = str_replace(' ', '&nbsp;', $n);
    switch($sign_posn) {
        case 0: $n = "($n)"; break;
        case 1: $n = "$sign$n"; break;
        case 2: $n = "$n$sign"; break;
        case 3: $n = "$sign$n"; break;
        case 4: $n = "$n$sign"; break;
        default: $n = "$n [error sign_posn=$sign_posn&nbsp;!]";
    }

    // Currency format:
    $m = number_format(abs($number), $frac_digits,
        $mon_decimal_point, $mon_thousands_sep);
    if ($sep_by_space) $space = ' '; else $space = '';
    if ($cs_precedes) $m = "$currency_symbol$space$m";
    else $m = "$m$space$currency_symbol";
    $m = str_replace(' ', '&nbsp;', $m);
    switch($sign_posn) {
        case 0: $m = "($m)"; break;
        case 1: $m = "$sign$m"; break;
        case 2: $m = "$m$sign"; break;
        case 3: $m = "$sign$m"; break;
        case 4: $m = "$m$sign"; break;
        default: $m = "$m [error sign_posn=$sign_posn&nbsp;!]";
    }
    if ($isMoney) return $m; else return $n;
}*/
?>
