<?php

/* Copyright 2010 Timothy White */

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

/* CronFunctions is mostly an upgrade and maintenace class, makes it easy to
 * upgrade database features and do regular cleaning. Cron is called after a
 * package update as well allowing for upgrades at each install
 */

class CronFunctions extends DatabaseFunctions
{
    /* Inherited from DatabaseFunctions
     *
     * $db is Radius DB handle
     */

    public function &getInstance()
    {
        // Static reference of this class's instance.
        static $instance;
        if (!isset($instance)) {
            $instance = new CronFunctions();
        }
        return $instance;
    }

    public function clearOldBatches()
    {
        $rowsaffected = 0;
        // Delete user names from batch that are no longer in radcheck table (gone)

        /*
         * Not the fastest way to do this, but due to it being in 2 different databases that we wish to keep user perms
         * separate for, we need to execute extra queries and do some php processing
         */
        $sql = "SELECT UserName FROM radcheck";

        $result = $this->db->queryAll($sql);

        if (PEAR::isError($result)) {
            return T_('Unable to select user from radcheck') . $result->toString();
        }

        foreach ($result as $user) {
            $users[] = $this->db->quote($user['UserName']);
        }
        $users = implode(', ', $users);

        // $users has already been escaped above
        $sql = "DELETE FROM batch WHERE UserName NOT IN ($users)";

        $sql2 = "DELETE FROM batches WHERE batchID NOT IN (SELECT batchID FROM batch)";

        $result = $this->radminDB->exec($sql);

        if (PEAR::isError($result)) {
            return T_('Unable to cleanup old users from batch ') . $result->toString();
        }

        $rowsaffected += $result;

        $result = $this->radminDB->exec($sql2);

        if (PEAR::isError($result)) {
            return T_('Unable to cleanup old batches ') . $result->toString();
        }

        $rowsaffected += $result;

        if ($rowsaffected) {
            return "($rowsaffected) " . T_('Old Batches Cleaned');
        }

        return false;
    }

    public function clearPostAuthMacRejects()
    {
        $sql = "
                SELECT id
                FROM radpostauth R
                  JOIN (
                         SELECT
                           username,
                           max(AuthDate) AS maxauthdate
                         FROM radpostauth
                         WHERE username LIKE '__-__-__-__-__-__'
                               AND reply = 'Access-Reject'
                         GROUP BY username
                       ) A ON (R.username = A.username)
                WHERE reply = 'Access-Reject'
                      AND authdate <> maxauthdate";

        $result =& $this->db->query($sql);

        if (PEAR::isError($result)) {
            return T_('Unable to get PostAuth MAC Reject IDs: ') . $result->toString();
        }


        $rows = 0;
        $time_start = microtime(true);
        while (($row = $result->fetchRow())) {
            set_time_limit(30);
            $sql = sprintf(
                "DELETE FROM radpostauth WHERE id = %s",
                $this->db->quote($row['id'])
            );

            $rowresult = $this->db->exec($sql);


            if (PEAR::isError($rowresult)) {
                return T_('Unable to delete PostAuth MAC Reject entry: ' . $rowresult->toString());
            }

            $rows += $rowresult;

        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        if ($rows) {
            return "Deleted $rows in $time seconds: " . T_('PostAuth MAC Reject rows cleared');
        }

        return false;
    }
}

/* Post auth needs some cleaning up.

DELETE all access-rejects for mac addresses except last 1
DELETE R from radpostauth R JOIN (select username, max(AuthDate) AS maxauthdate from radpostauth
WHERE username LIKE '__-__-__-__-__-__' AND reply = 'Access-Reject' GROUP BY username) A ON (R.username = A.username)
WHERE reply= 'Access-Reject' AND authdate <> maxauthdate;

^^^ SQL is SOOOO slow

INSTEAD we do following select which might take a minute

SELECT id from radpostauth R JOIN (select username, max(AuthDate) AS maxauthdate from radpostauth
WHERE username LIKE '__-__-__-__-__-__' AND reply = 'Access-Reject' GROUP BY username) A ON (R.username = A.username)
WHERE reply= 'Access-Reject' AND authdate <> maxauthdate;

THEN WE DELETE 1 by 1
This will cleanup all mac address auths and coovachilli auths, leaving only the last attempt
DELETE t1 from radpostauth t1, radpostauth t2 WHERE t1.username=t2.username AND t1.reply = t2.reply AND t1.id < t2.id
AND (t1.username  REGEXP '^([[:xdigit:]]{2}-){5}[[:xdigit:]]{2}' OR t1.username = "CoovaChilli")

Probably only need to clear rejects for mac address, and clear accepts for coovachilli admin user
*/
