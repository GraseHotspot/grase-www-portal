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

/* PDFLabel Class is a class to print labels based on code by 
   Laurent PASSEBECQ lpasseb@numericable.fr
   and Steve Dillon  steved@mad.scientist.com
   found in the FPDF downloads at http://www.fpdf.de/downloads/addons/29/
   written to take advantage of the TCPDF class
*/

    require_once('/usr/share/grase/tcpdf/config/lang/eng.php');
    require_once('/usr/share/grase/tcpdf/tcpdf.php');
    
class PDFLabels {

    // Private vars
    private $Label_Layout_Name = '';
    // Label margins (page)
    private $Margin_Left = 0;
    private $Margin_Top = 0;
    // Horiz/Vert space between 2 labels
    private $X_Space = 0;
    private $Y_Space = 0;
    // Number of labels across and down on a page
    private $Y_Number = 0;
    private $X_Number = 0;
    // Label width and height
    private $Width = 0;
    private $Height = 0;
    // Line and Character size/height
    private $FontSize = 8;
    private $Char_Size = 10;
    private $Line_Height = 10;
    // Type of metric for labels (in or mm)
    private $Metric = 'mm';
    private $MetricDoc = 'mm';
    private $PaperSize = 'a4';
    private $PaperOrientation = 'P';
    // Font
    private $Font_Name = 'Helvetica';
    
    private $COUNTX = 1;
    private $COUNTY = 1;
    
    private $pdf = NULL;
    private $firstpage = true;
    
    private $title = 'BatchX';
    
    // Some settings we can change
    
    public $print_border = 0;
    
    // Preset Labels
	static $preset_labels = array(
	    'Avery 5160' => array(
            'name'=>'Avery 5160',
            'paper-size'=>'letter',     
            'metric'=>'mm',     
            'marginLeft'=>1.762,     
            'marginTop'=>10.7,         
            'NX'=>3,     
            'NY'=>10,     
            'SpaceX'=>3.175,     
            'SpaceY'=>0,     
            'width'=>66.675,     
            'height'=>25.4,         
            'font-size'=>8),
        'Overflow' => array(
            'name' => 'Overflow Cafe Custom',
            'paper-size' => 'a4',
            'paper-orientation' => 'L',
            'metric' => 'mm',
            'marginLeft'=> 15,     
            'marginTop'=>1,         
            'NX'=>3,     
            'NY'=>7,     
            'SpaceX'=>1,     
            'SpaceY'=>1,     
            'width'=>85,     
            'height'=>28,         
            'font-size'=>10,
            'print_border' => 1),
            
    );
    
    private function setFormat($format){
        $this->Metric                = $format['metric'];
        $this->Label_Layout_Name     = $format['name'];
        $this->Margin_Left           = $format['marginLeft'];
        $this->Margin_Top            = $format['marginTop'];
        $this->X_Space               = $format['SpaceX'];
        $this->Y_Space               = $format['SpaceY'];
        $this->X_Number              = $format['NX'];
        $this->Y_Number              = $format['NY'];
        $this->Width                 = $format['width'];
        $this->Height                = $format['height'];
        $this->FontSize              = $format['font-size'];   
        $this->PaperSize             = isset($format['paper-size']) ? $format['paper-size'] : $this->PaperSize;
        $this->PaperOrientation      = isset($format['paper-orientation']) ? $format['paper-orientation'] : $this->PaperOrientation;
        $this->print_border      = isset($format['print_border']) ? $format['print_border'] : $this->print_border;        
        
        
        // set font
        //$pdf->SetFont('helvetica', '', 8);     
    }
    
    // Constructor
    function __construct ($format, $title, $posX=1, $posY=1) {
        if (is_array($format)) {
            // Custom format passed from app
            $labelformat = $format;
        } else {
            // Predefiend format
            $labelformat = self::$preset_labels[$format];
        }
        
        $this->setFormat($labelformat);
        
        // create new PDF document
        $this->pdf = new TCPDF($this->PaperOrientation, $this->MetricDoc, $this->PaperSize, true, 'UTF-8', false);
        
        $this->pdf->SetFont($this->Font_Name, '', $this->FontSize); 
        $this->pdf->SetMargins(0,0); 
        $this->pdf->SetAutoPageBreak(false);
        
        $this->pdf->setPrintFooter(false);
        $this->pdf->setPrintHeader(false);
        
        // set cell padding
        //$pdf->setCellPaddings(1, 1, 1, 1);

        // set cell margins
        //$pdf->setCellMargins(1, 1, 1, 1);
        
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('GRASE Hotspot');
        $this->pdf->SetTitle($title);
        $this->title = $title;
        $this->pdf->SetSubject('Hotspot Login Vouchers');        
        
        
        // Start at the given label position
        if ($posX > 1) $posX--; else $posX=0;
        if ($posY > 1) $posY--; else $posY=0;
        if ($posX >=  $this->X_Number) $posX =  $this->X_Number-1;
        if ($posY >=  $this->Y_Number) $posY =  $this->Y_Number-1;
        $this->COUNTX = $posX;
        $this->COUNTY = $posY;
    
    }
    
    // Print a label
    function Add_PDF_Label($texte) {
        // We are in a new page, then we must add a page
        if ($this->firstpage || (($this->COUNTX ==0) and ($this->COUNTY==0))) {
            $this->pdf->AddPage();
            $this->firstpage = false;
        }

        $_PosX = $this->Margin_Left+($this->COUNTX*($this->Width+$this->X_Space));
        $_PosY = $this->Margin_Top+($this->COUNTY*($this->Height+$this->Y_Space));
        
        $this->pdf->MultiCell($this->Width, $this->Height, $texte, $this->print_border, 'C', 0, 0, $_PosX, $_PosY, true, 0, false, true, $this->Height, 'M', true);

        $this->COUNTX++;

        if ($this->COUNTX == $this->X_Number) {
            // End of row reached, we start a new one
            $this->COUNTY++;
            $this->COUNTX=0;
        }

        if ($this->COUNTY == $this->Y_Number) {
            // Page full, we start a new one
            $this->COUNTX=0;
            $this->COUNTY=0;
        }
    }
    
    function Output_Doc($filename = '')
    {
        if($filename == '') $filename = $this->title .'.pdf';
        $this->pdf->Output($filename, 'I');
    }
    
}
?>
