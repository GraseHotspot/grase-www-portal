<?php
require_once('includes/site.inc.php');

$automac = new \Grase\AutoCreateUser($Settings, $DatabaseFunctions);
echo $_GET['callback'] . '('. $automac->automacuser(true) .')';
