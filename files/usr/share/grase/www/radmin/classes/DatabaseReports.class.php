<?php

class DatabaseReports
{
    private $db;

    public function __construct($db)
    {
        $this->db =& $db;
        //print_r($db);
    }
    
    private function processDataResults($sql)
    {
        $res =& $this->db->query($sql);
        
        //print_r($res);
        // Always check that result is not an error
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);
        foreach($results as $result)
        {
            $data[] = $result['TotalOctets']/1024/1024;
            $label[] = $result['Label'];
        }
        return array($data, $label);    
    }
    
    
    private function processCountResults($sql)
    {
        $res =& $this->db->query($sql);
        
        //print_r($res);
        // Always check that result is not an error
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);
        foreach($results as $result)
        {
            $data[] = intval($result['Total']);
            $label[] = $result['Date'];
        }
        return array($data, $label);    
    }    
    
    public function getThisMonthUsage()
    {
        $sql = "SELECT
            SUM(AcctInputOctets) + SUM(AcctOutputOctets) AS TotalOctets,
            DATE(AcctStartTime) AS Label,
            DATE(AcctStartTime) AS Date
            FROM radacct
            GROUP BY DAYOFMONTH(AcctStartTime)
            ORDER BY AcctStartTime";
            
        $sql = "SELECT SUM(TotalOctets) AS TotalOctets, Label FROM (
        SELECT
            SUM(AcctInputOctets) + SUM(AcctOutputOctets) AS TotalOctets,
            DATE(AcctStartTime) AS Label
            FROM radacct
            GROUP BY DAYOFMONTH(AcctStartTime)
            UNION
            SELECT '0' AS TotalOctets, CONCAT(dt.d, '-', days.d) as Label
        FROM
            (SELECT CONCAT(a1,b1) as d
            FROM
                (SELECT '0' as a1 UNION ALL SELECT '1' UNION ALL SELECT '2' UNION ALL SELECT '3') a
                JOIN
                (SELECT '0' as b1 UNION ALL SELECT '1' UNION ALL SELECT '2' UNION ALL SELECT '3' UNION ALL SELECT '4' UNION ALL SELECT '5' UNION ALL SELECT '6' UNION ALL SELECT '7' UNION ALL SELECT '8' UNION ALL SELECT '9') b
                WHERE CONVERT(CONCAT(a1, b1), UNSIGNED ) <=
                ( select DAY(NOW()) ) AND CONCAT(a1,b1)<>'00') days JOIN (SELECT DATE_FORMAT(NOW(),'%Y-%m') as d) dt ORDER BY Label) dailyusage GROUP BY Label
        ";
        
        return $this->processDataResults($sql);
/*/ SELECT '0' AS TotalOctets, CONCAT(dt.d, '-', days.d) as Label
        FROM
            (SELECT CONCAT(a1,b1) as d
            FROM
                (SELECT '0' as a1 UNION ALL SELECT '1' UNION ALL SELECT '2' UNION ALL SELECT '3') a
                JOIN
                (SELECT '0' as b1 UNION ALL SELECT '1' UNION ALL SELECT '2' UNION ALL SELECT '3' UNION ALL SELECT '4' UNION ALL SELECT '5' UNION ALL SELECT '6' UNION ALL SELECT '7' UNION ALL SELECT '8' UNION ALL SELECT '9') b
                WHERE CONVERT(CONCAT(a1, b1), UNSIGNED ) <=
                ( select DAY(NOW()) ) AND CONCAT(a1,b1)<>'00') days JOIN (SELECT DATE_FORMAT(NOW(),'%Y-%m') as d) dt ORDER BY Label; 
*/
/*
        SELECT SUM(TotalOctets), Label FROM (
        SELECT
            SUM(AcctInputOctets) + SUM(AcctOutputOctets) AS TotalOctets,
            DATE(AcctStartTime) AS Label
            FROM radacct
            GROUP BY DAYOFMONTH(AcctStartTime)
            UNION
            SELECT '0' AS TotalOctets, CONCAT(dt.d, '-', days.d) as Label
        FROM
            (SELECT CONCAT(a1,b1) as d
            FROM
                (SELECT '0' as a1 UNION ALL SELECT '1' UNION ALL SELECT '2' UNION ALL SELECT '3') a
                JOIN
                (SELECT '0' as b1 UNION ALL SELECT '1' UNION ALL SELECT '2' UNION ALL SELECT '3' UNION ALL SELECT '4' UNION ALL SELECT '5' UNION ALL SELECT '6' UNION ALL SELECT '7' UNION ALL SELECT '8' UNION ALL SELECT '9') b
                WHERE CONVERT(CONCAT(a1, b1), UNSIGNED ) <=
                ( select DAY(NOW()) ) AND CONCAT(a1,b1)<>'00') days JOIN (SELECT DATE_FORMAT(NOW(),'%Y-%m') as d) dt ORDER BY Label) dailyusage GROUP BY Label;

*/
    }
    
    public function getPreviousMonthsUsage()
    {
        $sql = "SELECT
            SUM(InputOctets) + SUM(OutputOctets) AS TotalOctets,
            DATE_FORMAT(AcctDate, '%b %Y') AS Label
            FROM mtotacct
            GROUP BY AcctDate
            ORDER BY AcctDate";
        
        return $this->processDataResults($sql);

    }
    
    public function getMonthsUsage()
    {
        $sql = "
            SELECT SUM(TotalOctets) AS TotalOctets, Date AS Label FROM (
            SELECT
            SUM(mtotacct.InputOctets) + SUM(mtotacct.OutputOctets) AS TotalOctets,
            DATE_FORMAT(mtotacct.AcctDate, '%b %Y') AS Date
            FROM mtotacct
            GROUP BY AcctDate
            UNION
            SELECT
            SUM(radacct.AcctInputOctets) + SUM(radacct.AcctOutputOctets) AS TotalOctets,
            DATE_FORMAT(radacct.AcctStartTime, '%b %Y') AS Date
            FROM radacct
            GROUP BY MONTH(AcctStartTime)
            ORDER BY Date 
            ) AS AggregatedTableUsage
            GROUP BY Date";
        return $this->processDataResults($sql);           
    }
    
    public function getThisMonthUsersUsage()
    {
        $sql = "SELECT
                SUM(radacct.AcctInputOctets) + SUM(radacct.AcctOutputOctets) AS TotalOctets,
                UserName AS Label
                FROM radacct
                GROUP BY UserName";
        return $this->processDataResults($sql);
    }        
    
    public function getDailyUsers()
    {
        $sql = "SELECT
            COUNT(DISTINCT UserName) AS Total,
            DATE(AcctStartTime) AS Date
            FROM radacct
            GROUP BY DAYOFMONTH(AcctStartTime)
            ORDER BY AcctStartTime";
        
        return $this->processCountResults($sql);

    }    
    
    public function getDailySessions()
    {
        $sql = "SELECT
            COUNT(RadAcctId) AS Total,
            DATE(AcctStartTime) AS Date
            FROM radacct
            GROUP BY DAYOFMONTH(AcctStartTime)
            ORDER BY AcctStartTime";
        
        return $this->processCountResults($sql);

    }       
    
}
