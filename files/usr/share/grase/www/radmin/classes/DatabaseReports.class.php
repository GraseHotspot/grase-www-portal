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
            $data[] = intval($result['TotalOctets']/1024/1024);
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
    
    private function processAssociativeResults($sql)
    {
        $res =& $this->db->query($sql);
        
        //print_r($res);
        // Always check that result is not an error
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);
        $data = array();
        foreach($results as $result)
        {
            $data[] = array($result['Label'], intval($result['Data']));
        }
        return $data;
    }
    
    public function getMonthGroupUsage()
    {
        $sql = "SELECT
                    SUM(AcctInputOctets + AcctOutputOctets)/1024/1024 AS Data,
                    radusergroup.GroupName AS Label
                FROM
                    radacct, 
                    radusergroup
                WHERE
                    radacct.UserName = radusergroup.UserName
                GROUP BY radusergroup.GroupName";
        return $this->processAssociativeResults($sql);
        
    }    
    
    public function getThisMonthDownUsage()
    {
           
        $sql = "SELECT SUM(TotalOctets) AS TotalOctets, Label FROM (
        SELECT
            SUM(AcctInputOctets) AS TotalOctets,
            DATE(AcctStartTime) AS Label
            FROM radacct
            WHERE ServiceType != 'Administrative-User'
            GROUP BY DAYOFMONTH(AcctStartTime)
            UNION
            SELECT '0' AS TotalOctets, CONCAT(dt.d, '-', days.d) as Label
        FROM
            (
                SELECT CONCAT(a1,b1) as d
                FROM
                    (
                    SELECT '0' as a1 UNION ALL SELECT '1' UNION ALL SELECT '2' UNION ALL SELECT '3'
                    ) a
                    JOIN
                    (
                    SELECT '0' as b1 UNION ALL SELECT '1' UNION ALL SELECT '2' UNION ALL SELECT '3' UNION ALL SELECT '4' UNION ALL SELECT '5' UNION ALL SELECT '6' UNION ALL SELECT '7' UNION ALL SELECT '8' UNION ALL SELECT '9'
                    ) b
                WHERE CONVERT(CONCAT(a1, b1), UNSIGNED ) <=
                ( select DAY(NOW()) ) AND CONCAT(a1,b1)<>'00') days JOIN (SELECT DATE_FORMAT(NOW(),'%Y-%m') as d) dt ORDER BY Label) dailyusage
        WHERE Label LIKE (SELECT DATE_FORMAT(NOW(),'%Y-%m-%%') as d)
        GROUP BY Label
        ";
        
        return $this->processDataResults($sql);
    }
    
    public function getThisMonthUpUsage()
    {
           
        $sql = "SELECT SUM(TotalOctets) AS TotalOctets, Label FROM (
        SELECT
            SUM(AcctOutputOctets) AS TotalOctets,
            DATE(AcctStartTime) AS Label
            FROM radacct
            WHERE ServiceType != 'Administrative-User'
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
                ( select DAY(NOW()) ) AND CONCAT(a1,b1)<>'00') days JOIN (SELECT DATE_FORMAT(NOW(),'%Y-%m') as d) dt ORDER BY Label) dailyusage
        WHERE Label LIKE (SELECT DATE_FORMAT(NOW(),'%Y-%m-%%') as d)                
        GROUP BY Label
        ";
        
        return $this->processDataResults($sql);
    }    
    
    public function getThisMonthUsage()
    {
        /*$sql = "SELECT
            SUM(AcctInputOctets) + SUM(AcctOutputOctets) AS TotalOctets,
            DATE(AcctStartTime) AS Label,
            DATE(AcctStartTime) AS Date
            FROM radacct
            GROUP BY DAYOFMONTH(AcctStartTime)
            ORDER BY AcctStartTime";*/
            
        $sql = "SELECT SUM(TotalOctets) AS TotalOctets, Label FROM (
        SELECT
            SUM(AcctInputOctets) + SUM(AcctOutputOctets) AS TotalOctets,
            DATE(AcctStartTime) AS Label
            FROM radacct
            WHERE ServiceType != 'Administrative-User'
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
        $sql = "SELECT
                    SUM(InputOctets) + SUM(OutputOctets) AS TotalOctets,
                    DATE_FORMAT(AcctDate, '%b %Y') AS Label,
                    AcctDate
                FROM
                    mtotacct
                WHERE UserName != ".$this->db->quote(RADIUS_CONFIG_USER)."
                GROUP BY Label
                UNION SELECT
                    SUM(AcctInputOctets) + SUM(AcctOutputOctets) AS TotalOctets,
                    DATE_FORMAT(AcctStartTime, '%b %Y') AS Label,
                    AcctStartTime
                FROM
                    radacct
                WHERE ServiceType != 'Administrative-User'
                GROUP BY Label
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
            WHERE UserName != ".$this->db->quote(RADIUS_CONFIG_USER)."
            GROUP BY AcctDate
            UNION
            SELECT
            SUM(radacct.AcctInputOctets) + SUM(radacct.AcctOutputOctets) AS TotalOctets,
            DATE_FORMAT(radacct.AcctStartTime, '%b %Y') AS Date
            FROM radacct
            WHERE ServiceType != 'Administrative-User'
            GROUP BY MONTH(AcctStartTime)
            ORDER BY Date 
            ) AS AggregatedTableUsage
            GROUP BY Date";
        return $this->processDataResults($sql);           
    }
    
    public function getThisMonthUsersUsage()
    {
        /*$sql = "SELECT
                SUM(radacct.AcctInputOctets) + SUM(radacct.AcctOutputOctets) AS TotalOctets,
                radcheck.Max-Total-Octets - SUM(radacct.AcctInputOctets) - SUM(radacct.AcctOutputOctets) AS TotalQuota
                UserName AS Label
                FROM radacct, radcheck
                WHERE radcheck.UserName = radacct.UserName
                GROUP BY UserName";
            
        $sql = "SELECT
                    SUM(radacct.AcctInputOctets) + SUM(radacct.AcctOutputOctets) AS TotalOctets,
                    radcheck.Value  AS TotalQuota,
                    radacct.UserName AS Label
                FROM
                    radacct, radcheck
                WHERE
                    radcheck.UserName = radacct.UserName
                    AND radcheck.Attribute='Max-Octets'
                GROUP BY radacct.UserName";*/
                
        $sql = "SELECT
                    SUM(TotalOctets) AS TotalOctets,
                    SUM(TotalQuota) AS TotalQuota,
                    Label
                FROM
                    (SELECT
                        0 AS TotalOctets,
                        radcheck.Value AS TotalQuota, 
                        radcheck.UserName AS Label
                    FROM
                        radcheck
                    WHERE
                        radcheck.attribute = 'Max-Octets'
                    UNION ALL
                    SELECT
                        SUM(radacct.AcctInputOctets) + SUM(radacct.AcctOutputOctets) AS TotalOctets, 
                        0 AS TotalQuota, 
                        radacct.UserName AS Label
                    FROM
                        radacct
                    WHERE UserName != ".$this->db->quote(RADIUS_CONFIG_USER)."
                    GROUP BY Label) AS T
                GROUP BY Label";

        $res =& $this->db->query($sql);
        
        //print_r($res);
        // Always check that result is not an error
        if (PEAR::isError($res)) {
            die($res->getMessage());
        }
        
        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);
        foreach($results as $result)
        {
            $data1[] = intval($result['TotalOctets']/1024/1024);
            $data2[] = intval($result['TotalQuota']/1024/1024);
            $label[] = $result['Label'];
        }

        return array($data1, $data2, $label);                    
                
        //return $this->processDataResults($sql);
    }        
    
    public function getDailyUsers()
    {
        $sql = "SELECT
            COUNT(DISTINCT UserName) AS Total,
            DATE(AcctStartTime) AS Date
            FROM radacct
            WHERE UserName != ".$this->db->quote(RADIUS_CONFIG_USER)."
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
            WHERE UserName != ".$this->db->quote(RADIUS_CONFIG_USER)."
            GROUP BY DAYOFMONTH(AcctStartTime)
            ORDER BY AcctStartTime";
        
        return $this->processCountResults($sql);

    }       
    
}
