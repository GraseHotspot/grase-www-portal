<?php

require_once('includes/site.inc.php');

load_templates(array('loginhelptext', 'belowloginhtml'));

/*$loginurl = parse_url($_GET['loginurl']);
$query = $loginurl['query'];
parse_str($query, $uamopts);*/

if(isset($_GET['disablejs']))
{
    // Set cookie
    setcookie('grasenojs','javascriptdisabled', time()+60*60*24*30);
    // Redirect via header to reload page?
    header("Location: http://$lanip:3990/prelogin");
}

if(isset($_GET['enablejs']))
{
    // Set cookie
    setcookie('grasenojs','', time()-60*60*24*30);
    // Redirect via header to reload page?
    header("Location: http://$lanip:3990/prelogin");
}

$res = @$_GET['res'];
$userurl = @$_GET['userurl'];
$challenge = @$_GET['challenge'];

if($userurl == 'http://logout/') $userurl = '';
if($userurl == 'http://1.0.0.0/') $userurl = '';

if($Settings->getSetting('disablejavascript') == 'TRUE')
{
    $nojs = true;
    $smarty->assign("nojs" , true);
    $smarty->assign("js" , false);    
    $smarty->assign("jsdisabled" , true);        
}elseif( isset($_COOKIE['grasenojs']) && $_COOKIE['grasenojs'] == 'javascriptdisabled')
{
    $nojs = true;
    $smarty->assign("nojs" , true);
    $smarty->assign("js" , false);    
}else
{
    $nojs = false;
    $smarty->assign("nojs" , false);
    $smarty->assign("js" , true);        
}

$smarty->assign("user_url", $userurl);
$smarty->assign("challenge", $challenge);
$smarty->assign("RealHostname", trim(file_get_contents('/etc/hostname')));

if($Settings->getSetting('autocreategroup'))
{
    $smarty->assign('automac', true);
}

/* Important parts of uamopts
    * challenge
    * userurl
    * res
    
*/    

if(!isset($_GET['res']))
{
    // Redirect to prelogin
        header("Location: http://$lanip:3990/prelogin");
}

// Already been through prelogin
/*$jsloginlink = "http://$lanip/grase/uam/mini?$query";
$nojsloginlink = $_GET['loginurl'];*/
    require_once '../radmin/automacusers.php';
if(@$_GET['automac'])
{
    // TODO only if this is enabled? (Although the function will do that 
    // anyway) so maybe only show the link if this is enabled?
    //
    // TODO need to ensure we have a challenge otherwise we need a fresh one, 
    // maybe if we AJAX the call so we always have a challenge?
    automacuser();
}

switch($res)
{
    case 'already':
        //if ($userurl) header("Location: $userurl");
        // Fall through to welcome page?
        if($nojs)
        {
            $smarty->display('loggedin.tpl');
            exit;
        }
        break;
    
    case 'failed':
        // Login failed? Show error and display login again
        $reply = array("Login Failed");
        if($_GET['reply'] != '') $reply = array($_GET['reply']);
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
        if($_GET['uid'] == mactoautousername($_GET['mac']))
        {
            break;
        }
        //
        load_templates(array('loggedinnojshtml'));
        $smarty->display('loggedin.tpl');
        exit;
        break;        
        
}




function setup_login_form()
{
	function check_mobi($useragent) {
		if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) { return true; }
	}
	if (check_mobi($_SERVER['HTTP_USER_AGENT'])) { 
		header("Location: ./mobile");
	}   
    	global $smarty;
    	$smarty->display('portal.tpl');
	exit;
}

$smarty->display('portal.tpl');


?>

