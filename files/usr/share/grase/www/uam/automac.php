<?php
require_once('includes/site.inc.php');
require_once('../radmin/automacusers.php');
echo $_GET['callback'] . '('. automacuser(true) .')';
?>
