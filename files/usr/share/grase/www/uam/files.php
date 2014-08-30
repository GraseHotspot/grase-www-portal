<?php

/* Copyright 2008 Timothy White */

require_once('/usr/share/grase/www/uam/includes/site.inc.php');
 
$path = substr($_SERVER['PATH_INFO'], 1); // this will get rid of the leading slash

$dir = "../public/$path";

if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".svn" && stristr($file, "php") === FALSE && stristr($file, "htaccess") === FALSE ) {
            $files[] = array("name" => $file, "size" => filesize("$dir/$file"));            
        }
    }
    closedir($handle);
}

$templateEngine->assign("path", "Public/".$path);
$templateEngine->assign("files", $files);
$templateEngine->display('files.tpl');
?>

