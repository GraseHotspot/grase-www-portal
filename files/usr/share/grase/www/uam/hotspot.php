<?php

require_once('includes/site.inc.php');

load_templates(array('loginhelptext', 'belowloginhtml', 'termsandconditions', 'aboveloginhtml'));

if (isset($_GET['disablejs'])) {
    // Set cookie
    setcookie('grasenojs', 'javascriptdisabled', time() + 60 * 60 * 24 * 30);
    // Redirect via header to reload page?
    header("Location: http://$lanIP:3990/prelogin");
}

if (isset($_GET['enablejs'])) {
    // Set cookie
    setcookie('grasenojs', '', time() - 60 * 60 * 24 * 30);
    // Redirect via header to reload page?
    header("Location: http://$lanIP:3990/prelogin");
}

$res = @$_GET['res'];
$userurl = @$_GET['userurl'];
$challenge = @$_GET['challenge'];

if ($userurl == 'http://logout/') {
    $userurl = '';
}
if ($userurl == 'http://1.0.0.0/') {
    $userurl = '';
}

if ($Settings->getSetting('disablejavascript') == 'TRUE') {
    $nojs = true;
    $smarty->assign("nojs", true);
    $smarty->assign("js", false);
    $smarty->assign("jsdisabled", true);
} elseif (isset($_COOKIE['grasenojs']) && $_COOKIE['grasenojs'] == 'javascriptdisabled') {
    $nojs = true;
    $smarty->assign("nojs", true);
    $smarty->assign("js", false);
} else {
    $nojs = false;
    $smarty->assign("nojs", false);
    $smarty->assign("js", true);
}

$smarty->assign("user_url", $userurl);
$smarty->assign("challenge", $challenge);
$smarty->assign("RealHostname", trim(file_get_contents('/etc/hostname')));
if ($Settings->getSetting('autocreategroup')) {
    $smarty->assign('automac', true);
}

/*
 * We need uamip and uamport to pass to jqchilli.js
 */
$uamIP = (empty($_GET['uamip'])) ? $lanIP : $_GET['uamip'];
$uamPort = (empty($_GET['uamport'])) ? 3990 : $_GET['uamport'];
$smarty->assign(
    'uamquery',
    http_build_query([
        'uamip' => $uamIP,
        'uamport' => $uamPort,
    ])
);

/* Important parts of uamopts
    * challenge
    * userurl
    * res
    
*/


// NB: This won't work when the local server isn't the coova-chilli device
if (!isset($_GET['res'])) {
    // Redirect to prelogin
    header("Location: http://$lanIP:3990/prelogin");
}

// Already been through prelogin
require_once '../radmin/automacusers.php';
if (@$_GET['automac']) {
    /* TODO only if this is enabled? (Although the function will do that
     * anyway) so maybe only show the link if this is enabled?
     * TODO need to ensure we have a challenge otherwise we need a fresh one,
     *  maybe if we AJAX the call so we always have a challenge?
     */
    automacuser();
    exit;
}

switch ($res) {
    case 'already':
        //if ($userurl) header("Location: $userurl");
        // Fall through to welcome page?
        if ($nojs) {
            $smarty->display('loggedin.tpl');
            exit;
        }
        break;

    case 'failed':
        // Login failed? Show error and display login again
        $reply = array("Login Failed");
        if ($_GET['reply'] != '') {
            $reply = array($_GET['reply']);
        }
        $smarty->assign("error", $reply);
        //break; // Fall through?

    case 'notyet':
    case 'logoff':
        // Display login
        setup_login_form();
        break;

    case 'success':
        //Logged in. Try popup and redirect to userurl
        // If this is an automac login (check UID vs MAC) then we skip the
        // normal success and go back to portal which should work better as
        // it's not a nojs login
        if ($_GET['uid'] == mactoautousername($_GET['mac'])) {
            break;
        }

        load_templates(array('loggedinnojshtml'));
        $smarty->display('loggedin.tpl');
        exit;
        break;

}


function setup_login_form()
{
    global $smarty;
    $smarty->display('portal.tpl');
    exit;
}

$smarty->display('portal.tpl');
