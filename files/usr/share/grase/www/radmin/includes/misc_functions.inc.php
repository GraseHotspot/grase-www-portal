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

/* Smarty functions */

function input_type($params, &$smarty)
{
    $val = $params['value'];
    $checked = " ";
    switch($params['type'])
    {
        case "ip":
            return 'type="text" pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}" title="IP Address" value="'.$val.'"';
            break;
        case "bool":
            if($val) $checked = "checked";
            return 'type="checkbox" '.$checked;
        default:
            return 'type="text" value="'.$val.'"';
    }
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
		\Grase\Util::makeTimeStamp(
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

function validate_yesno($yesno)
{
    if($yesno != 'yes' && $yesno != 'no') return sprintf(T_("Invalid response to YesNo Question '%s'"), $yesno);
}

function validate_bandwidth($kbits)
{
    $options = bandwidth_options();
    if(!isset($options[$kbits]) ) return sprintf(T_("Invalid Bandwidth Limit '%s'"), $kbits);
}

function validate_recurtime($recurrance, $time)
{
    // $time is in minutes not seconds
    $Recurtimevales = array(
        'hour' => 60,
        'day' => 60 * 24,
        'week' => 60 * 24 * 7,
        'month' => 60 * 24 * 30);
    //print_r(array($Recurtimevales[$recurrance], $time, $recurrance));
    if($Recurtimevales[$recurrance] < $time) return T_("Recurring time limit must be less than interval");

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

function validate_num($number, $error='')
{
	if ($number && is_numeric($number) && trim($number) != "") return "";
	if ($number + 0 === 0) return "";
	if($error != '') return $error; // Return the error message sent to us if defined
        return sprintf(T_("Invalid number %s"), $number);
	// TODO: Return what?
}

function validate_int($number, $option = false) //TODO make this actually validate int?
{
    if ($number && is_numeric($number) && trim($number) != "") return "";
    if($option && trim($number) == "") return "";
    return sprintf(T_("Invalid number '%s' (Must be whole number)"), $number);
	// TODO: Return what?
}

function validate_uucptimerange($timeranges)
{
    // We can have multiple time ranges, so split on comma (and |)
    if(trim($timeranges))
    {
        $timerange = str_replace('|', ',', $timeranges);
        
        $timerange = explode(',', $timerange);
        
        // For each range, check we start with valid start, followed by range
        foreach($timerange as $range)
        {
            $result = preg_match('/^(Su|Mo|Tu|We|Th|Fr|Sa|Sun|Mon|Tue|Wed|Thur|Fri|Sat|Wk|Any|Al|Never)(\d{4}-\d{4})?$/', $range);
            //var_dump(array($range, $result));        
            if($result == 0)
                return T_('Invalid Time Range ' . $timeranges);
        }
    }
}

function validate_group($username, $group)
{
	global $Settings;
	$groups = $Settings->getGroup();
	if(isset($groups[$group]))
	{
		if($group == MACHINE_GROUP_NAME && strpos($username, "-dev") === false) // TODO: This no longer works for newer coovachilli, check for mac address format 00-00-00-00-00-00
			return T_("Only Machines can be in the Machine group"); // TODO: Internationalsation of all strings
		return "";
	}else
	{
		return T_("Invalid Group");
	}
}

function expiry_for_group($group, $groups = '')
{
	global $Settings;
	if($groups == '')
    	$groups = $Settings->getGroup($group);
	if(isset($groups[$group]['Expiry']) && $groups[$group]['Expiry'] != '--') return date('Y-m-d H:i:s', strtotime($groups[$group]['Expiry']));
	//if(isset($Expiry[$group]) && ( $Expiry[$group] == '--' || $Expiry[$group] == '')) return "--";
	//return date('Y-m-d', strtotime($Expiry[DEFAULT_GROUP_NAME]));
	return "--";
}

/* Functions to check the group settings to ensure all currently used values are present in the dropdown boxes */

function checkGroupsDataDropdowns($datavals)
{
        global $Settings;
        $mb = explode(' ', $datavals);
        $group_settings = $Settings->getGroup();
        $group_attribs = DatabaseFunctions::getInstance()->getGroupAttributes();        

        foreach($group_settings as $name => $group)
        {       
                if(
                        isset($group['MaxMb']) &&
                        !in_array($group['MaxMb'], $mb) )
                                $mb[] = $group['MaxMb'];

                if(
                        isset($group_attribs[$name]['DataRecurLimit']) &&
                        !in_array($group_attribs[$name]['DataRecurLimit'], $mb))
                                $mb[] = $group_attribs[$name]['DataRecurLimit'];
        }
        asort($mb);
        $mb = trim(implode(" ", $mb));
        return $mb;
}

function checkGroupsTimeDropdowns($datavals)
{
        global $Settings;
        $time = explode(' ', $datavals);
        $group_settings = $Settings->getGroup();
        $group_attribs = DatabaseFunctions::getInstance()->getGroupAttributes();

        foreach($group_settings as $name => $group)
        {       
                if(
                        isset($group['MaxTime']) &&
                        !in_array($group['MaxTime'], $time))
                                $time[] = $group['MaxTime'];

                if(
                        isset($group_attribs[$name]['TimeRecurLimit']) &&
                        !in_array($group_attribs[$name]['TimeRecurLimit'], $time))
                                $time[] = $group_attribs[$name]['TimeRecurLimit'];
        }
        asort($time);
        $time = trim(implode(" ", $time));
        return $time;
}

function checkGroupsBandwidthDropdowns($datavals)
{
        global $Settings;
        $bw = explode(' ', $datavals);
        $group_settings = $Settings->getGroup();
        $group_attribs = DatabaseFunctions::getInstance()->getGroupAttributes();

        foreach($group_settings as $name => $group)
        {       

                if(
                        isset($group_attribs[$name]['BandwidthUpLimit']) &&
                        !in_array($group_attribs[$name]['BandwidthUpLimit'], $bw))
                                $bw[] = $group_attribs[$name]['BandwidthUpLimit'];
                if(
                        isset($group_attribs[$name]['BandwidthDownLimit']) &&
                        !in_array($group_attribs[$name]['BandwidthDownLimit'], $bw))
                                $bw[] = $group_attribs[$name]['BandwidthDownLimit'];
        }
        asort($bw);
        $bw = trim(implode(" ", $bw));
        return $bw;
}

/* */

function sort_users_into_groups($users)
{
	$users_group = array();
	$expiredusers = array();
	$lockedusers = array();
	$lowusers = array();
	
	foreach($users as $user)
	{
		if(isset($user['Group']) && $user['Group'] != '')
		{
			$users_group[$user['Group']][] = $user;
		}else
		{
			$users_group['Nogroup'][] = $user;
		}
		
		
		if($user['account_status'] == EXPIRED_ACCOUNT)
		{
		    $expiredusers[] = $user;
		}
		
		if($user['account_status'] == LOCKED_ACCOUNT)
		{
		    $lockedusers[] = $user;
		}
		

		if($user['account_status'] == LOWDATA_ACCOUNT || $user['account_status'] == LOWTIME_ACCOUNT)
		{
		    $lowusers[] = $user;
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
	
	// Built in sort groups (can't have spaces in name)
	if(sizeof($expiredusers) > 0)
	    $users_group[T_("Expired")] = $expiredusers;
	    
	if(sizeof($lockedusers) > 0)
	    $users_group[T_("Out Of Quota")] = $lockedusers;
	    
	if(sizeof($lowusers) > 0)
	    $users_group[T_("Low Quota")] = $lowusers;	    	    
	    
	return $users_group;
}

function underscorespaces($text)
{
    // This function is used to cleanup things like ids, so replace all chars that shouldn't be in id's and such
    return str_replace(array(' ', '$', '(', ')'), '_', $text);
}

function clean_groupname($text)
{
  // Get the group name in a suitable format
  return underscorespaces(clean_text($text));
}

function clean_username($text)
{
    // Usernames should be stricter than other strings, ' and " just cause problems
    $text = clean_text($text);
    $text = str_replace("'", "", $text);
    $text = str_replace('"', "", $text);    
    // Maybe should also strip spaces?
    return $text;
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
    $cleannum = $fmt->parse(ereg_replace("[^\.,0-9]", "", clean_text($number)));
    return $cleannum;
}

function clean_numberarray($numberarray)
{
        //Explode it into it's array using " " (as this can't appear in numbers anywhere in the world)
        $numarray = explode(' ', $numberarray);
        foreach($numarray as $num)
        {
                $numericarray[] = clean_number($num);
        }
        
        return implode(" ", $numericarray);
}


function clean_int($number)
{
    if(!is_numeric(clean_number($number))) return clean_number($number);
    return bigintval(clean_number($number));
    //ereg_replace("[^0-9]", "", clean_text($number));
}


if(!function_exists('bigintval')) {
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


function displayMoneyLocales($number)
{
        return displayLocales($number, TRUE);
}

function displayLocales($number, $isMoney=FALSE, $lg='') {
    global $locale;
    if ( $lg == '') $lg = $locale;

    if ( $number == '' ) return $number;

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

function get_available_languages()
{
    $langs = array();
    $lang_codes = glob('/usr/share/grase/locale/??', GLOB_ONLYDIR);
    foreach($lang_codes as $code)
    {
        $lang['display'] = Locale::getDisplayLanguage(basename($code), 'en');
        $lang['code'] = basename($code);
        $langs[] = $lang;
    }
    
    return $langs;
}

?>
