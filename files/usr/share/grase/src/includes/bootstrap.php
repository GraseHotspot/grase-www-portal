<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$DatabaseConnections = new DatabaseConnections();
$DatabaseFunctions = new DatabaseFunctions($DatabaseConnections);
