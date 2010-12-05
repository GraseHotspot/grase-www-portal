<?php

/* Copyright 2010 Timothy White */

function __autoload($class_name) {
    require_once './classes/' . $class_name . '.class.php';
}

AdminLog::getInstance()->log_cron("CRON");

echo CronFunctions::getInstance()->clearStaleSessions();
echo CronFunctions::getInstance()->deleteExpiredUsers();

?>
