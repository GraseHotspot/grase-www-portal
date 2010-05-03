<?php

class ErrorHandling
{
    // ErrorHandling::fatal_error
    public function fatal_error($error)
    {
        $AdminLog =& AdminLog::getInstance();
        $AdminLog->log_error($error);
        
        require('smarty/Smarty.class.php');
        //require_once 'libs/Smarty.class.php';

        $smarty = new Smarty;

        $smarty->compile_check = true;
        $smarty->assign("Application", APPLICATION_NAME);
        $smarty->assign("error", $error);
        
        $smarty->display("error.tpl");
        die();

    }
    
    
    // ErrorHandling::fatal_nodb_error
    public function fatal_nodb_error($error)
    {
        //$AdminLog =& AdminLog::getInstance();
        //$AdminLog->log_error($error);
        require('smarty/Smarty.class.php');

        
//        require_once 'libs/Smarty.class.php';

        $smarty = new Smarty;

        $smarty->compile_check = true;
        $smarty->assign("Application", APPLICATION_NAME);
        $smarty->assign("error", $error);
        $smarty->assign("memory_used", memory_get_usage());
        
        $smarty->display("error.tpl");
        die();

    }    

}

?>
