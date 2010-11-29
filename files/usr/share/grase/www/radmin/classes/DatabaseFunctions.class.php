<?

/* Copyright 2008 Timothy White */

class DatabaseFunctions
{
    private $db;
    
    public function &getInstance()
    {
        // Static reference of this class's instance.
        static $instance;
        if(!isset($instance)) {
            $instance = new DatabaseFunctions();
        }
        return $instance;
    }    
    
    private function __construct()
    {
        $this->db =& DatabaseConnections::getInstance()->getRadiusDB();
    }
    
    
    
    
    /* Database Functions
     * getSoldData          -   Gets total amount of data sold for valid accounts
     * getMonthUsedData     -   Gets how much data was used in that month (historic)
     * getUsedData          -   Gets current month used data
     *
     *
     *
     */
    
    // Accounting Functions
    
    // NAME:     getSoldData
    // PURPOSE:  Retrieves from the Database the total Data Sold to users
    // IMPORTS:
    // EXPORTS:  The total value of all sold data in octets

    // DatabaseFunctions::getInstance()->getSoldData();
    public function getSoldData() 
    {
        // Only counts Max-Octets currently (Future proof with up and down) TODO
        $sql = "select SUM(Value)
			      FROM radcheck
			      WHERE Attribute='Max-Octets'
			      AND Username IN (
			      	select UserName
			      	FROM radcheck
			      	WHERE Attribute='Expiration'
			      	AND STR_TO_DATE(Value,'%M %d %Y %T') > now()
			      );"; // DONE Fix to only include non-expired users

        $result = $this->db->queryOne($sql);
        
        if (PEAR::isError($result)) {
            ErrorHandling::fatal_error('Retrieving Sold Usage failed: '. $result->getMessage());
        }
        $soldoctets = $result;
        return $soldoctets + 0;
    }
    
    /* NAME:     getMonthUsedData
    * PURPOSE:  Retrieves from the Database the total data used for the selected
    *			 month
    * IMPORTS:  Month (as a numeric value of 1-12), defaults to last month
    * EXPORTS:  The total value of all sold data in octets
    * Asserts:
    *			 Must be a month that is earlier than current month, otherwise the
    *			 data will not be in the mtotacct table
    */

    public function getMonthUsedData($month = "") 
    {

        // TODO Will only work for months in mtotacct        
        if ($month == "") $month = date("m") - 1; //last month
        $date = date("Y-m-d", mktime(0, 0, 0, $month, 1, date("Y"))); // last month
        
        $sql = sprintf("SELECT 
            SUM(InputOctets) + SUM(OutputOctets) AS TotalOctets
            FROM mtotacct
            WHERE AcctDate='%s'",
            mysql_real_escape_string($date));
        
        $usedoctets = $this->db->queryOne($sql);
        
        if (PEAR::isError($usedoctets)) {
            ErrorHandling::fatal_error('Retrieving Month Usage failed: '. $usedoctets->getMessage());
        }
        
        return $usedoctets;
    }
    
    public function getUsedData() 
    {
        $sql = "SELECT
            SUM(radacct.AcctInputOctets) + SUM(radacct.AcctOutputOctets)
            FROM radacct
            WHERE DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= AcctStartTime";
        
        $usedoctets = $this->db->queryOne($sql);
        
        if (PEAR::isError($usedoctets)) {
            ErrorHandling::fatal_error('Retrieving Current Month Usage failed: '. $usedoctets->getMessage());
        }
        
        return $usedoctets + 0;
    }
    
    
    
    public function getRadiusSessionDetails($radacctid) 
    {
        $sql = sprintf("SELECT RadAcctId,
            AcctStartTime,
            AcctStopTime,
            AcctSessionTime,
            FramedIPAddress,
            Username,
            AcctInputOctets,
            AcctOutputOctets,
            SUM(AcctInputOctets + AcctOutputOctets) AS AcctTotalOctets,
            CallingStationId
            FROM radacct
            WHERE RadAcctID = '%s'
            ORDER BY RadAcctId DESC",
            mysql_real_escape_string($radacctid));
        
        $session = $this->db->queryRow($sql);
        
         if (PEAR::isError($session)) {
            ErrorHandling::fatal_error('Retrieving Session by RadAcctID failed: '. $session->getMessage());
        }
        
        return $session;
    }
    
    public function getRadiusUserSessionsDetails($username = '') 
    {
        $where_clause = sprintf("WHERE Username = '%s'", mysql_real_escape_string($username));
        if($username == '') $where_clause = '';
        $sql = sprintf("SELECT RadAcctId,
            AcctStartTime,
            AcctStopTime,
            AcctSessionTime,
            FramedIPAddress,
            Username,
            AcctInputOctets,
            AcctOutputOctets,
            AcctInputOctets + AcctOutputOctets AS AcctTotalOctets,
            CallingStationId
            FROM radacct
            %s
            ORDER BY RadAcctId DESC",
            $where_clause);
        
        $sessions = $this->db->queryAll($sql);
        
        if (PEAR::isError($sessions)) {
            ErrorHandling::fatal_error('Retrieving Sessions by Username failed: '. $sessions->getMessage());
        }        
        return $sessions;
    }    

    public function checkUniqueUsername($username)
    {
        $sql = sprintf("SELECT Username
            FROM radcheck
            WHERE Username='%s'",
            mysql_real_escape_string($username));
            
        $results = $this->db->query($sql);
        
        if (PEAR::isError($results)) {
            ErrorHandling::fatal_error('Checking Uniq Username failed: '. $results->getMessage());
        }
        
        $unique = true;
        if($results->numRows() != 0)
        {
            $unique = false;
        }
        
        return $unique;
    }

}



?>
