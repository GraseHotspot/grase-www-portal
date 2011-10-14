<?php

/* PHP No longer correctly gets the timezone from the system. Try to set it */

$tzfile = file_get_contents('/etc/timezone');

if($tzfile)
    date_default_timezone_set($tzfile);
else
    date_default_timezone_set(@date_default_timezone_get());
    
?>
