<?php

require_once('includes/site.inc.php');
load_templates(array('helptext'));
$smarty->display('help.tpl');