<?php

/*
 * This file is for bootstrapping the bare minimum needed for GRASE, and only what can't be done via the vendor
 * autoloader. All parts of the GRASE system should rely on the vendor/autoload.php and not this file
 */

// Needed for T_() function for translation of strings
require_once('php-gettext/gettext.inc');
