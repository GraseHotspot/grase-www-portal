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

namespace Grase;

class Reports
{
    private $DatabaseConnections;
    private $DatabaseReports;

    public function __construct($db)
    {
        $this->DatabaseConnections =& $db;
        $this->DatabaseReports = new Database\Reports(
            $this->DatabaseConnections->getRadiusDB()
        );
    }


    private function getMaxYAxis($data)
    {
        return ceil(intval(max($data)) / 50) * 50;
    }

    public function getThisMonthUsageReport()
    {

        list($data, $labels) = $this->DatabaseReports->getThisMonthUsage();
        $chart = $this->constructBarChart(
            $data,
            $labels,
            'Current Months Daily Usage (Mb)'
        );
        return $chart->toPrettyString();

    }

    public function getThisMonthUpUsageReport()
    {
        return $this->DatabaseReports->getThisMonthUpUsage();
    }

    public function getThisMonthDownUsageReport()
    {
        return $this->DatabaseReports->getThisMonthDownUsage();
    }

    public function getMonthGroupUsage()
    {
        return $this->DatabaseReports->getMonthGroupUsage();
    }

    public function getUsersUsageMonthReport($month = '')
    {
        return $this->DatabaseReports->getUsersUsageForMonth($month);
    }

    public function getUsersUsageByMonth()
    {
        return $this->DatabaseReports->getUsersUsageByMonth();
    }

    public function getPreviousMonthsUsageReport()
    {
        return $this->DatabaseReports->getPreviousMonthsUsage();
    }

    public function getMonthsUsageReport()
    {

        list($data, $labels) = $this->DatabaseReports->getMonthsUsage();
        $chart = $this->constructBarChart($data, $labels, 'Months Usage (Mb)');
        return $chart->toPrettyString();

    }

    public function getDailyUsersReport()
    {

        list($data, $labels) = $this->DatabaseReports->getDailyUsers();
        $elements[] = array('data' => $data, 'colour' => '#DCF000');
        list($data, $labels) = $this->DatabaseReports->getDailySessions();
        $elements[] = array('data' => $data);

//        $chart = $this->constructBarChart($data, $labels, 'Daily Users');
        $chart = $this->constructChart($elements, 'Daily Stats', $labels);
        return $chart->toPrettyString();

    }

    public function getDailySessionsReport()
    {

        list($data, $labels) = $this->DatabaseReports->getDailySessions();
        $chart = $this->constructBarChart($data, $labels, 'Daily Sessions');
        return $chart->toPrettyString();

    }
}
