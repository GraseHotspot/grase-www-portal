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

class Reports
{
    private $DatabaseConnections;
    private $DatabaseReports;

    public function __construct($db)
    {
        $this->DatabaseConnections =& $db;
        $this->DatabaseReports = new DatabaseReports($this->DatabaseConnections->getRadiusDB());
    }
    
/*    private function constructChart($elements, $title, $labels, $tooltip = '')
    {
        $chart = new OFC_Chart();
        $chart->set_title( new OFC_Elements_Title( $title ) );


        $x = new OFC_Elements_Axis_X();
        $x->set_colour( '#428C3E' );
        $x->set_grid_colour( '#86BF83' );
        $x->set_labels_from_array($labels);

        $y = new OFC_Elements_Axis_Y();

        foreach($elements as $element)
        {
            $max[] = $this->getMaxYAxis($element['data']);
        }
        $y->set_range( 0, max($max), max($max)/5);

        $chart->set_y_axis( $y );
        //
        // Add the X Axis object to the chart:
        //
        $chart->set_x_axis( $x );
        //
        // here we add our data sets to the chart:
        //
        foreach($elements as $element)
        {
            $chart->add_element( $this->constructBar($element, $tooltip) );
        }
        
        return $chart;    
    }
    
    private function constructBar($data, $tooltip)
    {
        $colour = '#DFC329';
        if(isset($data['colour'])) $colour = $data['colour'];
        $data_bar = new OFC_Charts_Bar_Filled();
        $data_bar->set_colour( $colour );
        $data_bar->set_values( $data['data'] );
        
        if($tooltip) $data_bar->set_tooltip($tooltip);
        return $data_bar;
    }
    
    private function constructBarChart($data, $labels, $title, $tooltip = '')
    {
        $data_bar = new OFC_Charts_Bar_Filled();
        $data_bar->set_colour( '#DFC329' );
        $data_bar->set_values( $data );
        
        if($tooltip) $data_bar->set_tooltip($tooltip);  

        $x = new OFC_Elements_Axis_X();
        //$x->set_rotate ('45');
        $x->set_colour( '#428C3E' );
        $x->set_grid_colour( '#86BF83' );
        //$x->set_steps('2');
        $x->set_labels_from_array($labels);
        $x->labels->set_rotate ('-30');

        $y = new OFC_Elements_Axis_Y();
        $y->set_range( 0, $this->getMaxYAxis($data), $this->getMaxYAxis($data)/5);

        $chart = new OFC_Chart();
        $chart->set_title( new OFC_Elements_Title( $title ) );
        $chart->set_y_axis( $y );
        //
        // Add the X Axis object to the chart:
        //
        $chart->set_x_axis( $x );
        //
        // here we add our data sets to the chart:
        //
        $chart->add_element( $data_bar );
        
        return $chart;
  
    }
    
    private function constructStackBarChart($data, $labels, $title)
    {
        $data_bar = new OFC_Charts_Bar_Stack();
        $data_bar->set_colour( '#DFC329' );
        
        foreach($data as $dataset)
        {
            $data_bar->append_stack($dataset);
        }
        //$data_bar->set_values( $data );

        $x = new OFC_Elements_Axis_X();
        $x->set_colour( '#428C3E' );
        $x->set_grid_colour( '#86BF83' );
        $x->set_labels_from_array($labels);

        $y = new OFC_Elements_Axis_Y();
        $y->set_range( 0, $this->getMaxYAxis($data), $this->getMaxYAxis($data)/5);

        $chart = new OFC_Chart();
        $chart->set_title( new OFC_Elements_Title( $title ) );
        $chart->set_y_axis( $y );
        //
        // Add the X Axis object to the chart:
        //
        $chart->set_x_axis( $x );
        //
        // here we add our data sets to the chart:
        //
        $chart->add_element( $data_bar );
        
        return $chart;
  
    }    */
    
    private function getMaxYAxis($data)
    {
        return ceil(intval(max($data))/50) * 50;
    }
    
    public function getThisMonthUsageReport()
    {
    
        list($data, $labels) = $this->DatabaseReports->getThisMonthUsage();        
        $chart = $this->constructBarChart($data, $labels, 'Current Months Daily Usage (Mb)');
        return $chart->toPrettyString();

    }
    
    public function getThisMonthUpUsageReport()
    {
        return $this->DatabaseReports->getThisMonthUpUsage();
    }    
    
    public function getThisMonthDownUsageReport()
    {
        return $this->DatabaseReports->getThisMonthDownUsage();
        /*list($data, $labels) = $this->DatabaseReports->getThisMonthUsage();        
        $results = "[";
        
        $last_key = end(array_keys($data));

        foreach($data as $key=> $value)
        {
            $date = strtotime($labels[$key]);
            $date = $labels[$key];
            $results .= "['$date ', $value]";
            if($key != $last_key) $results .=",";
        }
        $results .= "]";
        return $results;*/

    }
    
    public function getMonthGroupUsage()
    {
        return $this->DatabaseReports->getMonthGroupUsage();
    }    
    
    public function getThisMonthUsersUsageReport()
    {
    
        list($data, $labels) = $this->DatabaseReports->getThisMonthUsersUsage();        
        $chart = $this->constructBarChart($data, $labels, 'Current Months Users Usage (Mb)', ' #x_label# #val# Mb');
        return $chart->toPrettyString();

    }    
    
    public function getPreviousMonthsUsageReport()
    {
        return $this->DatabaseReports->getPreviousMonthsUsage();      
/*        list($data, $labels) = $this->DatabaseReports->getPreviousMonthsUsage();      
        $chart = $this->constructBarChart($data, $labels, 'Previous Months Usage (Mb)');
        return $chart->toPrettyString();*/

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
        $elements[] = array('data' => $data, 'colour' =>'#DCF000');
        list($data, $labels) = $this->DatabaseReports->getDailySessions();
        $elements[] = array('data' => $data);        

//        $chart = $this->constructBarChart($data, $labels, 'Daily Users');
        $chart = $this->constructChart($elements, 'Daily Stats', $labels);
        return $chart->toPrettyString();

    }

    public function getDailySessionsReport()
    {
    
        list($data, $labels) = $this->DatabaseReports->getDailySessions();        
        /*
        list($data2, $labels) = $this->DatabaseReports->getDailySessions();       
        foreach($data as $key => $value)
        {
            $combined_data[$key] = array($data[$key], $data2[$key]);
        }
        */        
        $chart = $this->constructBarChart($data, $labels, 'Daily Sessions');
        return $chart->toPrettyString();

    }                      
}
