<?php

/* Copyright 2008 Timothy White */

require_once('/usr/share/grase/www/uam/includes/site.inc.php');
 
$path = substr($_SERVER['PATH_INFO'], 1); // this will get rid of the leading slash

$dir = "../public/$path";

if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".svn" && stristr($file, "php") === false && stristr($file, "htaccess") === false) {
            $files[] = array("name" => $file, "size" => filesize("$dir/$file"));
        }
    }
    closedir($handle);
}

$smarty->assign("path", "Public/".$path);
$smarty->assign("files", $files);
$smarty->display('files.tpl');
