<?php

include "class.ezpdf.php";

class PDFWrapper_ezPDF extends PDFWrapper 
{
    var $type='ezPDF';
    var $p;  
	var $curFontSize;

    function PDFWrapper_ezPDF() 
    {
        $this->p= false; // We haven't actually started yet, to prevent blank page
		$this->curFontSize=10;
    }

    // Instantiation functions                  
    function Open($file) 
    {
		// FIXME: use file for something?
    }                      
    function Close() 
    {
		//pdf_close($this->p);
    }
    function CloseWeb($redir="") 
    {
		// FIXME: What is redir?
		//$this->p->ezStream();
        return $this->p->ezOutput();
    }
    
    // Page functions
    function BeginPage ($width, $height) 
    {
		if ($this->p) {
			$this->p->ezNewPage();
		} else {
			$this->p=new Cezpdf(array(0,0,$width,$height),'portrait');
			$this->p->addInfo("Creator","PDFReports");
			$this->p->addInfo("Author","InterAKT");
			$this->p->addInfo("Title","PDFReports");
		}
    }                                   
    function EndPage() 
    {
        // Why is this commented out?
		//pdf_end_page($this->p);
    }
    
    // Image functions
    function PlaceImage($type, $src, $x, $y) 
    {
		if ($type='JPEG') {
			$this->p->addJpegFromFile($src,$x,$y);
		}
    }
    function PlaceScaledImage($type, $src, $x, $y, $width, $height) 
    {
		if ($type='JPEG') {
			$this->p->addJpegFromFile($src,$x,$y,$width,$height);
		}
    }
    
    // Graphic functions
    function SetColor($red, $green, $blue) 
    {
		$this->p->SetStrokeColor($red,$green,$blue);
    } 
    function SetBGColor($red, $green, $blue) 
    {
		$this->p->SetColor($red,$green,$blue);
    }
    function SetAllColor($red, $green, $blue) 
    {
		$this->p->SetStrokeColor($red,$green,$blue);
		$this->p->SetColor($red,$green,$blue);
    }
    function SetLineStyle($width, $black, $white) 
    {
		$this->p->SetLineStyle($width,'','',array($black,$white));
    }
    function Line($x1, $y1, $x2, $y2) 
    {
		$this->p->Line($x1-10, $y1, $x2-10, $y2);
    }
    function MoveTo($x, $y) 
    {
		$this->p->addText($x,$y,'',0,0);
    }
    function LineTo($x, $y) 
    {
		//pdf_lineto($this->p,$x,$y);
    }
    function Rectangle($x, $y, $width, $height) 
    {
		$this->p->rectangle($x-10, $y, $width, $height);
    } 
    function Bar($x, $y, $width, $height) 
    {
		$this->p->filledrectangle($x-10, $y, $width, $height);
    }
    function Circle($x, $y, $radius) 
    {
		$this->p->ellipse($x-10, $y, $radius);
    }
    function Disc($x, $y, $radius) 
    {
		// Is a disc a filled circle?
		// $this->p->Disc($x, $y, $radius);
    }
    function Arc($x, $y, $radius, $startangle, $endangle) 
    {
		//pdf_arc($this->p,$x,$y,$radius,$statangle,$endangle);
    }  
    function Pie($x, $y, $radius, $startangle, $endangle) 
    {
		// Is a pie a filled arc?
        //$this->p->Pie($x, $y, $radius, $startangle, $endangle);
    }    
    function FullPie($x, $y, $radius, $startangle, $endangle) 
    {
		// What is a full pie?
        //$this->p->FullPie($x, $y, $radius, $startangle, $endangle);
    }
    
    // Font functions
    function SetFont($fontname, $size, $bold, $italic, $underline) 
    {
		$this->curFontSize=$size;
        $this->p->SelectFont($fontname);
    }
    
    // Text functions
    function ShowText($text, $x, $y, $width, $height, $align)
    {
		// Divide the strings into the lines
		$text = str_replace("\\n","\n",$text);
		$lines = explode("\n",$text);
		$pos = $y + ((count($lines)-1) * 10);
		// Add each line
		foreach ($lines as $line) {
			$this->p->addTextWrap($x-10,$pos,$width, $this->curFontSize,$line,strtolower($align));
			$pos-=10;
		}
	}
    
	function GetTextWidth($text,$size) 
    {
		// FIXME: the 4 is experimentally, is this a bug in getTextWidth?
		return $this->p->getTextWidth($size,$text)+4;
	}
}
?>