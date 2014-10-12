<?php

require_once 'includes/pageaccess.inc.php';

function check_level($ACCESS_LEVEL)
{
    global $Auth;
    /* Require admin level unless defined. This prevents us from accidently
     * forgetting to set an access level and users getting access to things
     * they aren't allowed
     * */
    $ACCESS_LEVEL = isset($ACCESS_LEVEL) ? $ACCESS_LEVEL : ADMINLEVEL;
    // Check if level of access required is in the memberOf array

    $user_details = $Auth->getAuthData();

    if ($ACCESS_LEVEL == ALLLEVEL) {
        return true;
    }

    // Bitwise check that the user can access it
    if ($ACCESS_LEVEL & $user_details['accesslevel']) {
        return true;
    }

    return false;
}

function check_page_access()
{
    global $ACCESS_LEVEL, $templateEngine;
    if (!check_level($ACCESS_LEVEL)) {

        $templateEngine->displayPage('accessdenied.tpl');
        exit;
    }
}
