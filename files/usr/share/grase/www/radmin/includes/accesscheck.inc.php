<?php

require_once 'includes/pageaccess.inc.php';

function check_level($ACCESS_LEVEL)
{
    $format = '%0' . (PHP_INT_SIZE * 8) . "b\n";

    global $Auth;
    // Require admin level unless defined. This prevents us from accidently forgetting to set an access level and users getting access to things they aren't allowed
    $ACCESS_LEVEL = isset($ACCESS_LEVEL) ? $ACCESS_LEVEL : ADMINLEVEL;
    // Check if level of access required is in the memberOf array

    $user_details = $Auth->getAuthData();
    
    if($ACCESS_LEVEL == ALLLEVEL) return TRUE;

    /*printf('  level=' . $format, $ACCESS_LEVEL);
    printf('  user=' . $format, $user_details['accesslevel']);
    printf('  res=' . $format, $ACCESS_LEVEL & $user_details['accesslevel']);                
    */
    // Bitwise check that the user can access it
    if ($ACCESS_LEVEL & $user_details['accesslevel'])
        return TRUE;
    
    return FALSE;
}

function check_page_access(){
    global $ACCESS_LEVEL;
    if(! check_level($ACCESS_LEVEL))
    {

        $templateEngine->displayPage('accessdenied.tpl');
        exit;
    }
}
?>
