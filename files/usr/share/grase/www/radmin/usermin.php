<?php
require_once 'includes/usermin_session.inc.php';
require_once 'includes/database_functions.inc.php';


if(isset($_GET['history']))
{
    $smarty->assign("sessions", getDBSessionsAccounting($Auth->getUsername()));
    $smarty->display('usermin_history.tpl');
}
else
{

    if(isset($_POST['changepasswordsubmit']))
    {
        $newpass1 = trim($_POST['NewPassword']);
        $newpass2 = trim($_POST['PasswordVerify']);        
        // Work on changing password
        if($newpass1 != $newpass2)
        {
            $error = "New Passwords must match";
        }elseif($newpass1 == '')
        {
            $error = "Password must not be blank";
        }else
        {
            $error = "Password not updated";
            if(database_change_password($Auth->getUsername(), $newpass1))
            {
                $error = "";
                $notice = "Password Changed";
            }
        }

    }
    
    $smarty->assign("error", $error);
    $smarty->assign("notice", $notice);
    $smarty->assign("user", getDBUserDetails($Auth->getUsername()));
    $smarty->display('usermin_userdetails.tpl');

}
?>

