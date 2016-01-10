<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://grasehotspot.org/

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
/* Define all Constants here */

define("APPLICATION_NAME", "GRASE");
define("APPLICATION_VERSION", "3.8.0");

// Account Status Constants (used in CSS) TODO: Obsolete these?

define("EXPIRED_ACCOUNT", "expired");
define("LOCKED_ACCOUNT", "locked");
define("LOWDATA_ACCOUNT", "lowdata");
define("LOWTIME_ACCOUNT", "lowtime");
define("NORMAL_ACCOUNT", "normal");
define("NOGROUP_ACCOUNT", "nogroup");

// RADIUS ChilliSpot Config Constants
define("RADIUS_CONFIG_USER", "CoovaChilli");
define("RADIUS_CONFIG_PASSWORD", "radmin");
define("RADIUS_CONFIG_ATTRIBUTE", "ChilliSpot-Config");

// Access level bitmasks

define("ADMINLEVEL", 1); // 2^0
define("POWERLEVEL", 2 | ADMINLEVEL); //3
define("NORMALLEVEL", 4 | POWERLEVEL); //7
define("REPORTLEVEL", 8);
define("CREATEUSERLEVEL", 16);
define("ALLLEVEL", 0); // 0

// User bitmasks
//define("ROOTUSER", -1); //(~0)
define("ADMINUSER", 1); // 2^0
define("POWERUSER", 2); //2^1
define("NORMALUSER", 4); //2^2
