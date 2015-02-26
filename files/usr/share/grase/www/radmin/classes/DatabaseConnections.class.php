<?php

/* Copyright 2009 Timothy White */

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

require_once "MDB2.php";

class DatabaseConnections
{
    private $radiusDatabaseSettingsFile;
    private $radminDatabaseSettingsFile;
    private $radminDatabaseSettings;
    private $radiusDatabaseSettings;
    private $radminDB;
    private $radiusDB;
    private $radminDSN;
    private $radiusDSN;
    private $radminOptions;
    private $radiusOptions;

    /* To prevent multiple instances of the DB, but also allowing us to use the DB
     * from multiple locations without global vars, we get the instance with
     * $DBs =& DatabaseConnections::getInstance();
     * Initial call is
     * $DBs =& DatabaseConnections::getInstance('database_config_file');
     */

    public function &getInstance($radiusDatabaseSettingsFile = '/etc/grase/radius.conf', $radminDatabaseSettingsFile = '/etc/grase/radmin.conf')
    {
        // Static reference of this class's instance.
        static $instance;
        if (!isset($instance)) {
            $instance = new DatabaseConnections($radiusDatabaseSettingsFile, $radminDatabaseSettingsFile);
        }
        return $instance;
    }

    public function __construct($radiusDatabaseSettingsFile = '/etc/grase/radius.conf', $radminDatabaseSettingsFile = '/etc/grase/radmin.conf')
    {
        $this->radiusDatabaseSettingsFile = $radiusDatabaseSettingsFile;
        $this->radminDatabaseSettingsFile = $radminDatabaseSettingsFile;
        $this->connectDatabase();
    }

    private function loadSettingsFromFile($dbSettingsFile)
    {
        // Check that databaseSettingsFile is valid
        if (!is_readable($dbSettingsFile)) {
            \Grase\ErrorHandling::fatalNoDatabaseError(
                T_("DB Config File isn't a valid file.") . "($dbSettingsFile)"
            );
        }

        $settings = file($dbSettingsFile);

        foreach ($settings as $setting) {
            list($key, $value) = explode(":", $setting);
            $db_settings[$key] = trim($value);
        }
        return $db_settings;
    }

    private function connectDatabase()
    {
        $this->radminDatabaseSettings = $this->loadSettingsFromFile($this->radminDatabaseSettingsFile);
        $this->radiusDatabaseSettings = $this->loadSettingsFromFile($this->radiusDatabaseSettingsFile);

        // Set options and DSN
        $db_settings = $this->radiusDatabaseSettings;
        $this->radiusDSN = array(
            "phptype" => "mysql",
            "username" => $db_settings['sql_username'],
            "password" => $db_settings['sql_password'],
            "hostspec" => $db_settings['sql_server'],
            "database" => $db_settings['sql_database'],
            "new_link" => true
        );

        $this->radiusOptions = array(
            'portability' => MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE,
            'emulate_prepared' => true
        );

        $db_settings = $this->radminDatabaseSettings;
        $this->radminDSN = array(
            "phptype" => "mysql",
            "username" => $db_settings['sql_username'],
            "password" => $db_settings['sql_password'],
            "hostspec" => $db_settings['sql_server'],
            "database" => $db_settings['sql_radmindatabase'],
            'portability' => MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE,
            "new_link" => true
        );

        $this->radminOptions = array(
            'portability' => MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE,
            'emulate_prepared' => true
        );


        // Connect
        $this->radiusDB =& MDB2::connect($this->radiusDSN, $this->radiusOptions);
        if (PEAR::isError($this->radiusDB)) {
            //TODO Send more of error handler to error handling (i.e. userinfo in database errors, for debugging (stderr?))
            \Grase\ErrorHandling::fatalNoDatabaseError(
                $this->radiusDB->getMessage() . " RADIUS<br/>The RADIUS database does not exist"
            );
        }

        // Set mode for Radius DB
        $this->radiusDB->setFetchMode(MDB2_FETCHMODE_ASSOC);


        $this->radminDB =& MDB2::connect($this->radminDSN, $this->radminOptions);
        if (PEAR::isError($this->radminDB)) {
            // Attempt to create the radminDB? TODO: Make nicer?
            $this->radiusDB->loadModule('Manager');
            $this->radiusDB->createDatabase($db_settings['sql_radmindatabase']);

            $this->radminDB =& MDB2::connect($this->radminDSN, $this->radminOptions);
            if (PEAR::isError($this->radminDB)) {
                \Grase\ErrorHandling::fatalNoDatabaseError($this->radminDB->getMessage() . " RADMIN");
            }
        }

        // Set mode for Radmin DB
        $this->radminDB->setFetchMode(MDB2_FETCHMODE_ASSOC);

        // Enable DEBUG with GET request (see Explain_Queries below)
        // DEBUG is disabled in production environments, this should never be uncommented in a package we are building. TODO CHECK!
        //if(isset($_GET['debug'])) $this->debugDB();
    }

    private function debugDB()
    {
        // Select which db we are debugging (Settings will need it's own maybe)
        $mdb2 = $this->radiusDB;
        $mdb2 = $this->radminDB;

        // instance of the custom debug handler
        $my_debug_handler = new Explain_Queries($mdb2);
        // set debug option
        $mdb2->setOption('debug', 1);
        // set debug handler to the method that
        // collects all queries
        $mdb2->setOption(
            'debug_handler',
            array($my_debug_handler, 'collectInfo')
        );
        // register functions to be executed on shut down
        // after the script has finished execution.
        // Now that the show's over, it's the time to
        // report what happened in this script db-access-wise
        // First shutdown function executes the
        // SELECTs again, the other one prints the results
        register_shutdown_function(
            array($my_debug_handler, 'executeAndExplain')
        );
        register_shutdown_function(
            array($my_debug_handler, 'dumpInfo')
        );
    }

    public function getRadminDB()
    {
        return $this->radminDB;
    }

    public function getRadiusDB()
    {
        return $this->radiusDB;
    }

    public function getRadminDSN()
    {
        return $this->radminDSN;
    }

    public function getRadiusDSN()
    {
        return $this->radiusDSN;
    }
}

// Explain_Queries code from http://www.phpied.com/performance-tuning-with-mdb2/
// The custom error handler
//
// It will collect all the queries being executed
// in the script, the collection is done by the
// collectInfo() method.
// Once the script finishes executing, we'll call
// the method executeAndExplain() which will
// execute all unique SELECTs once again
// in order to give us an info of how much time
// each query takes.
// Then executeAndExplain() will execute again
// all SELECTs, this time prepending an EXPLAIN
// so that we can get valuable
// optimization-related information
// Not only that but instead of simple EXPLAIN,
// we can use EXPLAIN EXTENDED and after that
// we can call SHOW WARNINGS -
// this will give us even more optimization hints
//
// http://dev.mysql.com/doc/refman/5.1/en/explain.html
// http://dev.mysql.com/doc/refman/5.1/en/show-warnings.html
//
class Explain_Queries
{
    // how many queries were executed
    var $query_count = 0;
    // which queries and their count
    var $queries = array();
    // BT to each query
    var $qbt = array();

    // results of EXPLAIN-ed SELECTs
    var $explains = array();
    // the MDB2 instance
    var $db = false;

    // constructor that accepts MDB2 reference
    function Explain_Queries(&$db)
    {
        $this->db = $db;
    }

    // this method is called on every query
    function collectInfo(
        &$db,
        $scope,
        $message,
        $is_manip = null
    )
    {
        // increment the total number of queries
        $this->query_count++;
        // the SQL is a key in the queries array
        // the value will be the count of how
        // many times each query was executed
        @$this->queries[$message]++;

        // Add trace info if needed
        if (isset($_GET['bt'])) {
            $trace = array();
            $trace = debug_backtrace();
            //unset($trace[0]);
            foreach ($trace as &$t) {
                unset($t['args']);
                unset($t['object']);
            }

            $this->qbt[$message][] = $trace;
        }
    }

    // print the debug information
    function dumpInfo()
    {
        echo '<h3>Queries on this page</h3>';
        echo 'Number: ' . $this->query_count;
        echo '<pre>';
        print_r($this->queries);
        echo '</pre>';
        echo '<pre>';
        print_r(@$this->qbt);
        echo '</pre>';
        echo '<h3>EXPLAIN-ed SELECTs</h3>';
        echo '<pre>';
        print_r($this->explains);
        echo '</pre>';
    }

    // the method that will execute all SELECTs
    // with and without an EXPLAIN and will
    // create $this->explains array of debug
    // information
    // SHOW WARNINGS will be called after each
    // EXPLAIN for more information
    function executeAndExplain()
    {

        // at this point, stop debugging
        $this->db->setOption('debug', 0);
        $this->db->loadModule('Extended');

        // take the SQL for all the unique queries
        $queries = array_keys($this->queries);
        foreach ($queries as $sql) {
            // for all SELECTs…
            $sql = trim($sql);
            if (stristr($sql, "SELECT") !== false) {
                // note the start time
                $start_time = array_sum(
                    explode(" ", microtime())
                );
                // execute query
                $this->db->query($sql);
                // note the end time
                $end_time = array_sum(
                    explode(" ", microtime())
                );
                // the time the query took
                $total_time = $end_time - $start_time;

                // now execute the same query with
                // EXPLAIN EXTENDED prepended
                $explain = $this->db->getAll(
                    'EXPLAIN EXTENDED ' . $sql
                );

                $this->explains[$sql] = array();
                // update the debug array with the
                // new data from
                // EXPLAIN and SHOW WARNINGS
                if (!PEAR::isError($explain)) {
                    $this->explains[$sql]['explain'] = $explain;
                    $this->explains[$sql]['warnings'] =
                        $this->db->getAll('SHOW WARNINGS');
                }

                // update the debug array with the
                // count and time
                $this->explains[$sql]['time'] = $total_time;
            }
        }
    }
}
