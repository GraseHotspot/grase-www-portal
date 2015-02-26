<?php
/* Copyright 2008-2014 Timothy White */

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
namespace Grase\Database;

use Grase\Util;

class Upgrade
{
    protected $radius;
    protected $radmin;
    protected $DBF;
    protected $Settings;

    protected $rowsUpdated = 0;

    public function __construct(Database $radius, Database $radmin, Radmin $Settings, $databasefunctions)
    {
        $this->radius = $radius->conn;
        $this->radmin = $radmin->conn;
        $this->Settings = $Settings;
        $this->DBF = $databasefunctions;
    }

    public function upgradeDatabase()
    {
        $oldDBVersion = $this->Settings->getSetting("DBVersion");

        try {
            // Somethings we can run anytime
            $this->defaultTemplates();

            // The rest we can only run if the Database hasn't been updated
            if ($oldDBVersion < 1.1) {
                $this->cleartextAttribute();
                $this->Settings->setSetting("DBVersion", 1.1);
            }

            if ($oldDBVersion < 1.2) {
                $this->onePointTwo();
                $this->Settings->setSetting("DBVersion", 1.2);
            }

            if ($oldDBVersion < 1.3) {
                $this->groupSimultaneousDefaults();
                $this->Settings->setSetting("DBVersion", 1.3);
            }

            if ($oldDBVersion < 1.4) {
                //$this->defaultTemplates($this->Settings);
                //$this->Settings->setSetting("DBVersion", 1.4);
            }

            if ($oldDBVersion < 1.5) {
                $this->defaultNetworkSettings();
                $this->Settings->setSetting("DBVersion", 1.5);
            }

            if ($oldDBVersion < 1.6) {
                $this->fixGroupAttributes();
                $this->Settings->setSetting("DBVersion", 1.6);
            }

            if ($oldDBVersion < 1.7) {
                $this->addAccessLevelColumn();
                $this->Settings->setSetting("DBVersion", 1.7);
            }

            if ($oldDBVersion < 1.8) {
                $this->defaultNetworkInterfaces();
                $this->walledGardenData();
                $this->Settings->setSetting("DBVersion", 1.8);
            }

            if ($oldDBVersion < 1.9) {
                $this->migrateLastBatch();
                $this->Settings->setSetting("DBVersion", 1.9);
            }

            if ($oldDBVersion < 2.0) {
                $this->migrateGroups();
                $this->Settings->setSetting("DBVersion", 2.0);
            }

            if ($oldDBVersion < 2.1) {
                $this->fixGroupNameIndex();
                $this->Settings->setSetting("DBVersion", 2.1);
            }

            if ($oldDBVersion < 2.2) {
                $this->fixPostAuthTable();
                $this->Settings->setSetting("DBVersion", 2.2);
            }

            if ($oldDBVersion < 2.3) {
                $this->fixServiceTypeOP();
                $this->Settings->setSetting("DBVersion", 2.3);
            }

            if ($oldDBVersion < 2.4) {
                $this->createAutocreatePassword();
                $this->Settings->setSetting("DBVersion", 2.4);
            }

            if ($oldDBVersion < 2.5) {
                $this->truncatePostAuth();
                $this->Settings->setSetting("DBVersion", 2.5);
            }

            if ($oldDBVersion < 2.6) {
                $this->decreaseChilliAdminInterval();
                $this->Settings->setSetting("DBVersion", 2.6);
            }

            if ($oldDBVersion < 2.7) {
                $this->moveTicketPrintSettings();
                $this->Settings->setSetting("DBVersion", 2.7);
            }

            if ($oldDBVersion < 2.8) {
                $this->createComputerGroup();
                $this->Settings->setSetting("DBVersion", 2.8);
            };

            if ($oldDBVersion < 2.9) {
                $this->defaultUsernamePasswordComplexity();
                $this->Settings->setSetting("DBVersion", 2.9);
            }

        } catch (\PDOException $Exception) {
            return T_('Upgrading DB failed: ') . $Exception->getMessage() . ': ' . $Exception->getCode();
        }

        if ($this->rowsUpdated > 0) {
            return T_('Database upgraded') . ' ' . $this->rowsUpdated;
        }

        return false;
    }

    // < 1.1
    private function cleartextAttribute()
    {
        $count = $this->radius->exec(
            "UPDATE radcheck
                                SET Attribute='Cleartext-Password'
                                WHERE Attribute='Password'"
        );
        $this->rowsUpdated += $count;
    }

    // < 1.2
    private function onePointTwo()
    {
        try {
            // remove unique key from radreply
            $this->rowsUpdated += $this->radius->exec("DROP INDEX userattribute ON radreply");
        } catch (\PDOException $Exception) {
// We want to ignore this exception as we don't care if the index exists
        }


        // Add Radius Config user for Coova Chilli Radconfig
        $this->rowsUpdated += $this->DBF->setUserPassword(RADIUS_CONFIG_USER, RADIUS_CONFIG_PASSWORD);

        // Set Radius Config user Service-Type to filter it out of normal users
        $result = $this->DBF->replace_radcheck_query(
            RADIUS_CONFIG_USER,
            'Service-Type',
            '==',
            'Administrative-User'
        );

        if (\PEAR::isError($result)) {
            return T_('Upgrading DB failed: ') . $result->toString();
        }

        $this->rowsUpdated += $result;

        // Add default macpasswd string
        $this->rowsUpdated += $this->DBF->setChilliConfigSingle('macpasswd', 'password');
        // Add default defidelsession
        $this->rowsUpdated += $this->DBF->setChilliConfigSingle('defidletimeout', '600');

        // Set last change time
        $this->Settings->setSetting('lastchangechilliconf', time());
        $this->rowsUpdated += 1;

        //Install default groups
        $dgroup["Staff"] = "+6 months";
        $dgroup["Ministry"] = "+6 months";
        $dgroup["Students"] = "+3 months";
        $dgroup["Visitors"] = "+1 months";
        $this->Settings->setSetting("groups", serialize($dgroup));
        $this->rowsUpdated += 1;
    }

    // < 1.3
    private function groupSimultaneousDefaults()
    {
        // Set default groups to not allow simultaneous use
        $this->rowsUpdated += $this->DBF->setGroupSimultaneousUse("Staff", 1);
        $this->rowsUpdated += $this->DBF->setGroupSimultaneousUse("Ministry", 1);
        $this->rowsUpdated += $this->DBF->setGroupSimultaneousUse("Students", 1);
        $this->rowsUpdated += $this->DBF->setGroupSimultaneousUse("Visitors", 1);
    }

    // < 1.4
    private function defaultTemplates()
    {
        if ($this->Settings->getTemplate('loginhelptext') === null) {
            // loginhelptext: displayed above login form in main portal page
            $this->Settings->setTemplate(
                'loginhelptext',
                <<<'EOT'
<p>By logging in, you are agreeing to the following:</p>
<ul>
    <li><strong>All network activity will be monitored, this includes: websites, bandwidth usage, protocols</strong></li>
    <li><strong>You will not access sites containing explicit or inappropriate material</strong></li>
    <li><strong>You will not attempt to access any system on this network</strong></li>
</ul>
EOT
            );
            $this->rowsUpdated++;
        }


        // helptext: page contents of info & help file
        if ($this->Settings->getTemplate('helptext') === null) {
            $this->Settings->setTemplate(
                'helptext',
                <<< 'EOT'
<p>For payment and an account, please contact the Office during office hours.</p>
<p>For a quick logout, bookmark <a href="http://10.1.0.1:3990/logoff">LOGOUT</a>, this link will instantly log you out, and return you to the Welcome page.<br/>
To get back to the status page, bookmark ether the Non javascript version (<a href="./nojsstatus" target="grasestatus">Hotspot Status nojs</a>), or the preferred javascript version (<a href="javascript: loginwindow = window.open('http://10.1.0.1/grase/uam/mini', 'grasestatus', 'width=300,height=400,location=no,directories=no,status=yes,menubar=no,toolbar=no'); loginwindow.focus();">Hotspot Status</a>). You can just drag ether link to your bookmark bar to easily bookmark them.</p>

<p>Your Internet usage is limit by the amount of data that flows to and from your computer, or the amount of time spent online (depending on what you account type is). To maximise your account, you may wish to do the following:</p>
<ul>
    <li>Browse with images turned off</li>
    <li>Resize all photos before uploading (800x600 is a good size for uploading to the internet, or emailing)</li>
    <li>Ensure antivirus programs do not attempt to update the program (you probably still want them to update the virus definition files).</li>
    <li>Use a client program for email instead of using webmail.</li>
    <li>Ensure when you finish using the Internet, you click logout so that other users won't be able to use your account</li>
</ul>
EOT
            );
            $this->rowsUpdated++;
        }

        if ($this->Settings->getTemplate('maincss') === null) {
            // maincss: main css override for login portal
            $this->Settings->setTemplate('maincss', '');
            $this->rowsUpdated++;
        }

        // loggedinnojshtml: html to show on successful login
        if ($this->Settings->getTemplate('loggedinnojshtml') === null) {
            $this->Settings->setTemplate(
                'loggedinnojshtml',
                <<<'EOT'
<p>Your login was successful. Please click <a href="nojsstatus" target="grasestatus">HERE</a> to open a status window<br/>If you don't open a status window, then bookmark the link <a href="http://logout/">http://logout/</a> so you can logout when finished.</p>
EOT
            );
            $this->rowsUpdated++;
        }

        //ticketPrintCSS
        if ($this->Settings->getTemplate('ticketPrintCSS') === null) {
            $this->Settings->setTemplate(
                'ticketPrintCSS',
                <<<'EOT'
body {
    line-height: 1.5;
    color: black;
    background-color : white;
    font-family: "Helvetica Neue", Arial, Helvetica, sans-serif;
    padding: 0;
    margin: 0;
}

.cutout_ticket {
    outline: solid 1px black;
    margin: 0.1cm;
    width: 5.5cm;
    float: left;
    text-align: left;
    font-size: 10pt;
    page-break-inside: avoid;
}

.ticket_item_label {
    padding-left: 0.3em;
    width: 5.5em;
    display: inline-block;
}

.info_username, .info_password {
    font-weight: bold;
}

#generated {
    display: none;
}
EOT
            );
            $this->rowsUpdated++;
        }

    }

    // < 1.5
    private function defaultNetworkSettings()
    {
        // Load default network settings (match old chilli config)
        $net['lanipaddress'] = '10.1.0.1';
        $net['networkmask'] = '255.255.255.0';
        $net['opendnsbogusnxdomain'] = true;
        $net['dnsservers'] = array('208.67.222.123', '208.67.220.123'); // OpenDNS Family Shield
        $net['bogusnx'] = array();

        $this->Settings->setSetting('networkoptions', serialize($net));
        $this->Settings->setSetting('lastnetworkconf', time());

        $this->rowsUpdated += 2;
    }

    // < 1.6
    private function fixGroupAttributes()
    {
        // Move groupAttributes to the correct table
        foreach ($this->DBF->getGroupAttributes() as $name => $group) {
            $this->DBF->setGroupAttributes($name, $group);
            $this->rowsUpdated++;
        }
    }

    // < 1.7
    private function addAccessLevelColumn()
    {
        try {
            $this->radmin->exec("ALTER TABLE auth DROP COLUMN accesslevel");
        } catch (\PDOException $e) {
        }

        $this->rowsUpdated += $this->radmin->exec("ALTER TABLE auth ADD COLUMN accesslevel INT NOT NULL DEFAULT 1");
    }

    // < 1.8
    private function defaultNetworkInterfaces()
    {
        $interfaces = Util::getDefaultNetworkIFS();
        $networkoptions = unserialize($this->Settings->getSetting('networkoptions'));
        $networkoptions['lanif'] = $interfaces['lanif'];
        $networkoptions['wanif'] = $interfaces['wanif'];

        $this->Settings->setSetting('networkoptions', serialize($networkoptions));

        $this->Settings->setSetting('lastnetworkconf', time());
        $this->rowsUpdated += 2;
    }

    private function walledGardenData()
    {
        // New chilli settings for garden
        $this->rowsUpdated += $this->DBF->setChilliConfigSingle('nousergardendata', true);
    }

    // < 1.9
    private function migrateLastBatch()
    {
        // Get last batch and migrate it to new batch system
        $lastbatch = $this->Settings->getSetting('lastbatch');
        // Check if lastbatch is an array, if so then we migrate
        if (is_array(unserialize($lastbatch))) {
            $lastbatchusers = unserialize($lastbatch);
            $nextBatchID = $this->Settings->nextBatchID();
            $this->Settings->saveBatch($nextBatchID, $lastbatchusers);
            // Lastbatch becomes an ID
            $this->Settings->setSetting('lastbatch', $nextBatchID);
        } else {
            $this->Settings->setSetting('lastbatch', 0);
        }
    }

    // < 2.0
    private function migrateGroups()
    {
        // Migrate groups to new system
        $groups = unserialize($this->Settings->getSetting('groups'));

        $groupattributes = $this->DBF->getGroupAttributes();

        foreach ($groups as $group => $expiry) {
            $attributes = array();
            $attributes['GroupName'] = \Grase\Clean::groupName($group);
            $attributes['GroupLabel'] = $group;
            $attributes['Expiry'] = @ $expiry;
            $attributes['MaxOctets'] = @ $groupattributes[$group]['MaxOctets'];
            $attributes['MaxSeconds'] = @ $groupattributes[$group]['MaxSeconds'];
            // No comment stored, but oh well
            $attributes['Comment'] = @ $groupattributes[$group]['Comment'];

            $this->rowsUpdated += $this->Settings->setGroup($attributes);
        }

        $this->Settings->setSetting('groups', serialize(''));
    }

    // < 2.1
    private function fixGroupNameIndex()
    {
        // Remove uniq index on radgroupcheck
        try {
            $this->rowsUpdated += $this->radius->exec("DROP INDEX GroupName ON radgroupcheck");
        } catch (\PDOException $e) {
// We don't care if it doesn't exist causing the drop to fail
        }

        $this->rowsUpdated += $this->radius->exec("ALTER TABLE radgroupcheck ADD KEY `GroupName` (`GroupName`(32))");
    }

    // < 2.2
    private function fixPostAuthTable()
    {
        $this->truncatePostAuth();

        try {
            // Just drop columns we are fixing, easier than checking for existance

            $this->rowsUpdated += $this->radius->exec(
                "
                            ALTER TABLE radpostauth
                              DROP COLUMN ServiceType,
                              DROP COLUMN FramedIPAddress,
                              DROP COLUMN CallingStationId"
            );
        } catch (\PDOException $e) {
// We don't care if it doesn't exist causing the drop to fail
        }

        // Add columns back in correctly
        $this->rowsUpdated += $this->radius->exec(
            "ALTER TABLE radpostauth
                ADD COLUMN ServiceType VARCHAR(32) DEFAULT NULL,
                ADD COLUMN FramedIPAddress VARCHAR(15) DEFAULT NULL,
                ADD COLUMN CallingStationId VARCHAR(50) DEFAULT NULL"
        );
    }

    // < 2.3
    private function fixServiceTypeOP()
    {
        // Previously we incorrectly set Service-Type op to := instead of ==
        // Set Radius Config user Service-Type to filter it out of normal users
        $this->rowsUpdated += $this->DBF->replace_radcheck_query(
            RADIUS_CONFIG_USER,
            'Service-Type',
            '==',
            'Administrative-User'
        );
    }

    // < 2.4
    private function createAutocreatePassword()
    {
        // Create the autocreatepassword setting, with a random string if it
        // doesn't already exist
        // Check that setting doesn't already exist as changing an existing
        // password will lock users out
        if (!$this->Settings->getSetting("autocreatepassword")) {
            $this->Settings->setSetting("autocreatepassword", Util::randomPassword(20));

            $this->rowsUpdated++;
        }
    }

    // < 2.5
    private function truncatePostAuth()
    {
        // Assume we are doing an upgrade from before postauth was
        // truncated and so we'll just truncate postauth to save time
        $this->rowsUpdated += $this->radius->exec("TRUNCATE radpostauth");
    }

    // < 2.6
    private function decreaseChilliAdminInterval()
    {
        // Set Chilli Admin interval to be lower (10 minutes)
        $this->rowsUpdated += $this->DBF->setChilliConfigSingle('interval', '600');
    }

    // < 2.7
    private function moveTicketPrintSettings()
    {
        $networkSettings = unserialize($this->Settings->getSetting('networkoptions'));
        $this->Settings->setSetting('printSSID', $networkSettings['printSSID']);
        unset($networkSettings['printSSID']);
        $this->Settings->setSetting('printGroup', $networkSettings['printGroup']);
        unset($networkSettings['printGroup']);
        $this->Settings->setSetting('printExpiry', $networkSettings['printExpiry']);
        unset($networkSettings['printExpiry']);
        $this->Settings->setSetting('networkoptions', serialize($networkSettings));
        $this->rowsUpdated += 3;
    }

    // < 2.8
    private function createComputerGroup()
    {
        // The special computer group is becoming a normal group
        if (! $this->Settings->getGroup("Computer")) {
            $this->Settings->setGroup(
                array(
                    "GroupName" => "Computer",
                    "GroupLabel" => "Computer",
                    "Comment" => "Autocreated Computers group"
                )
            );
            $this->rowsUpdated ++;
        }
    }

    // < 2.9
    private function defaultUsernamePasswordComplexity()
    {
        // Set default complexity options for generating username/password
        if (! $this->Settings->getSetting('numericPassword')) {
            $this->Settings->setSetting('numericPassword', false);
            $this->rowsUpdated ++;
        }
        if (! $this->Settings->getSetting('simpleUsername')) {
            $this->Settings->setSetting('simpleUsername', false);
            $this->rowsUpdated ++;
        }
    }
}
