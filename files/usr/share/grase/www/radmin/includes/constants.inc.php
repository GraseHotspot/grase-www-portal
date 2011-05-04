<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://hotspot.purewhite.id.au/

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
require_once('php-gettext/gettext.inc');

/* Define all Constants here */

define("APPLICATION_NAME", "GRASE");
define("USERMIN_APPLICATION_NAME", T_("My Account"));

// Account Status Constants (used in CSS) TODO: Obsolete these?

define("EXPIRED_ACCOUNT", "expired");
define("LOCKED_ACCOUNT", "locked");
define("LOWDATA_ACCOUNT", "lowdata");
define("MACHINE_ACCOUNT", "machine");
define("NORMAL_ACCOUNT", "normal");
define("NOGROUP_ACCOUNT", "nogroup");

// Group Constants TODO: Obsolete these?
define("MACHINE_GROUP_NAME", "Machine");
define("DEFAULT_GROUP_NAME", "Default");

// RADIUS ChilliSpot Config Constants
define("RADIUS_CONFIG_USER", "CoovaChilli");
define("RADIUS_CONFIG_PASSWORD", "radmin");
define("RADIUS_CONFIG_ATTRIBUTE", "ChilliSpot-Config");

?>
