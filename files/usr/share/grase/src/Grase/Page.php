<?php

namespace Grase;

    /* Copyright 2008-2014 Timothy White */

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

require_once("smarty3/SmartyBC.class.php");

class Page
{
    private $te;
    private $errorMessages = array();
    private $successMessages = array();
    private $warningMessages = array();

    public function __construct($template_engine = null)
    {
        if ($template_engine == null) {
            $this->te = new \SmartyBC();
            $this->setupSmarty();
        } else {
            $this->te = $template_engine;
        }
    }

    private function setupSmarty()
    {
        //$this->te->error_reporting = E_ALL & ~E_NOTICE;
        $this->te->compile_check = true;
        //$this->te->register_outputfilter('smarty_outputfilter_strip');
        //$this->te->registerPlugin('modifier', 'bytes', array("Formatting", "formatBytes"));
        $this->te->register_modifier('bytes', array("Formatting", "formatBytes"));
        $this->te->register_modifier('seconds', array("Formatting", "formatSec"));
        $this->te->register_modifier('displayLocales', 'displayLocales');
        $this->te->register_modifier('displayMoneyLocales', 'displayMoneyLocales');
        $this->te->register_function('inputtype', 'input_type');
        $this->te->register_modifier("sortby", "smarty_modifier_sortby");

        // i18n
        //$locale = (!isset($_GET["l"]))?"en_GB":$_GET["l"];
        $this->te->register_block('t', 'smarty_block_t');

    }

    public function errorMessage($message)
    {
        $this->errorMessages[] = $message;
    }

    public function successMessage($message)
    {
        $this->successMessages[] = $message;
    }

    public function warningMessage($message)
    {
        $this->warningMessages[] = $message;
    }

    public function clearAssign($template_var)
    {
        return $this->te->clearAssign($template_var);
    }

    public function displayPage($template)
    {
        \assign_vars($this->te);
        $this->setupTemplateVariables();
        return $this->te->display($template);
    }

    public function setupTemplateVariables()
    {
        if (sizeof($this->warningMessages) != 0) {
            $this->assign("warningmessages", $this->warningMessages);
        }
        if (sizeof($this->errorMessages) != 0) {
            $this->assign("error", $this->errorMessages);
        }
        if (sizeof($this->successMessages) != 0) {
            $this->assign("success", $this->successMessages);
        }
    }

    public function assign($template_var, $value)
    {
        return $this->te->assign($template_var, $value);
    }
}