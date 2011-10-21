<?php

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

class ErrorHandling
{
    // ErrorHandling::fatal_error
    public function fatal_error($error)
    {
        $AdminLog =& AdminLog::getInstance();
        $AdminLog->log_error($error);
        
        if(file_exists('/usr/share/php/smarty/libs/') && ! is_link('/usr/share/php/smarty/libs/'))
        {
            // Debian bug http://bugs.debian.org/cgi-bin/bugreport.cgi?bug=514305
            // Remove this code once fixed?
            require_once('smarty/libs/Smarty.class.php');
        }else
        {
            require_once('smarty/Smarty.class.php');
        }
                //require_once 'libs/Smarty.class.php';

        $smarty = new Smarty;

        $smarty->compile_check = true;
        $smarty->register_block('t', 'smarty_block_t'); // Needed even though message will be in English
        $smarty->assign("Application", APPLICATION_NAME);
        $smarty->assign("error", $error);
        
        $smarty->display("error.tpl");
        die();

    }
    
    // ErrorHandling::fatal_db_error
    public function fatal_db_error($error, $pear_error_obj)
    {
        $AdminLog =& AdminLog::getInstance();
        $AdminLog->log_error($error . $pear_error_obj->toString());
        
        if(file_exists('/usr/share/php/smarty/libs/') && ! is_link('/usr/share/php/smarty/libs/'))
        {
            // Debian bug http://bugs.debian.org/cgi-bin/bugreport.cgi?bug=514305
            // Remove this code once fixed?
            require_once('smarty/libs/Smarty.class.php');
        }else
        {
            require_once('smarty/Smarty.class.php');
        }
        //require_once 'libs/Smarty.class.php';

        $smarty = new Smarty;

        $smarty->compile_check = true;
        $smarty->register_block('t', 'smarty_block_t'); // Needed even though message will be in English
        $smarty->assign("Application", APPLICATION_NAME);
        $smarty->assign("error", $error . $pear_error_obj->getMessage());
        
        $smarty->display("error.tpl");
        var_dump($pear_error_obj);
        die();

    }    
    
    
    // ErrorHandling::fatal_nodb_error
    public function fatal_nodb_error($error)
    {
        //$AdminLog =& AdminLog::getInstance();
        //$AdminLog->log_error($error);
        if(file_exists('/usr/share/php/smarty/libs/') && ! is_link('/usr/share/php/smarty/libs/'))
        {
            // Debian bug http://bugs.debian.org/cgi-bin/bugreport.cgi?bug=514305
            // Remove this code once fixed?
            require_once('smarty/libs/Smarty.class.php');
        }else
        {
            require_once('smarty/Smarty.class.php');
        }

        
//        require_once 'libs/Smarty.class.php';

        $smarty = new Smarty;

        $smarty->compile_check = true;
        $smarty->register_block('t', 'smarty_block_t'); // Needed even though message will be in English
        $smarty->assign("Application", APPLICATION_NAME);
        $smarty->assign("error", $error);
        $smarty->assign("memory_used", memory_get_usage());
        
        $smarty->display("error.tpl");
        die();

    }    

}

?>
