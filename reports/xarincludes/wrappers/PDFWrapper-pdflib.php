<?php

class PDFWrapper_PDFlib extends PDFWrapper 
{
  var $type='PDFlib';
  var $p;  
	var $debug=0;
	
	function debug($msg) 
    {
		if ($debug) echo "$msg<br>";
	}

  function PDFWrapper_PDFlib() 
  {
    static $a=0; $a++;	$this->debug( "PDFLib instantiated !<br>");
    $this->p=PDF_new();
  }

  // Instantiation functions                  
  function Open($file) 
  {
		static $a=0; $a++;	$this->debug( "Open($file)<br>");
		PDF_open_file($this->p,$file);
		PDF_set_info($this->p,"Creator","PDFReports");
		PDF_set_info($this->p,"Author","InterAKT");
		PDF_set_info($this->p,"Title","PDFReports");
  }                      
  function Close() 
  {
		pdf_close($this->p);
  }
  function CloseWeb($redir="") 
  {
		static $a=0; $a++;	$this->debug("CloseWeb($redir): $a<br>");
		$this->Close();
		$data = pdf_get_buffer($this->p);
		header("Content-type: application/pdf");
		header("Content-disposition: inline; filename=test.pdf");
		header("Content-length: " . strlen($data));
		echo $data;
  }
  
  // Page functions
  function BeginPage ($width, $height) 
  {
		static $a=0; $a++; $this->debug( "BeginPage($width,$height): $a <br>");
		PDF_begin_page($this->p,$width,$height);
		$this->SetFont("Helvetica", 10);
  }                                   
  function EndPage() 
  {
		static $a=0; $a++; $this->debug( "EndPage(): $a<br>");
		PDF_end_page($this->p);
  }
  
  // Image functions
  function PlaceImage($type, $src, $x, $y) 
  {
		static $a=0; $a++; $this->debug( "PlaceImage($type, $src, $x, $y): $a <br>");
		$img=pdf_open_image_file($this->p,strtolower($type),$src,"",0);
		PDF_place_image($this->p,$img,$x,$y,1);
  }
  function PlaceScaledImage($type, $src, $x, $y, $width, $height) 
  {
		static $a=0; $a++;	$this->debug( "PlaceScaledImage($type, $src, $x, $y, $width, $height) : $a<br>");

		$img=PDF_open_image_file($this->p,strtolower($type),$src,"",0);
		$dpi_x =PDF_get_value($this->p,"resx",$img);
		$dpi_y =PDF_get_value($this->p,"resy",$img);
		/*calculate scaling factors from the dpi values,see description of resx/resy */
		if ($dpi_x >0 && $dpi_y >0) { //resx and resy are specified in the file 
			$scale_x =72.0/$dpi_x;
			$scale_y =72.0/$dpi_y;
		}else if ($dpi_x <0 && $dpi_y <0){ //only the ratio of resx and resy is known*/
			$scale_x =1.0;
			$scale_y =$dpi_y / $dpi_x;
		}else { //no information about resx and resy av.
			$scale_x =1.0;
			$scale_y =1.0;
		}
		PDF_save($this->p);
		PDF_scale($this->p,$scale_x, $scale_y);
		PDF_place_image($this->p,$img,$x,$y,1.0);
		PDF_restore($this->p);
		PDF_close_image($this->p,$img);
  }

  // Graphic functions
  function SetColor($red, $green, $blue) 
  {
		static $a=0;	$a++;	$this->debug( "SetColor: $a<br>");
		PDF_setcolor($this->p,"stroke","rgb",$red,$green,$blue);
  } 
  function SetBGColor($red, $green, $blue) 
  {
		static $a=0;	$a++;	$this->debug( "SetBGColor: $a<br>");
		PDF_setcolor($this->p,"fill","rgb",$red,$green,$blue);
  }
  function SetAllColor($red, $green, $blue) 
  {
		static $a=0;	$a++;	$this->debug( "SetAllColor: $a<br>");
		$this->SetColor($red,$green,$blue);
		$this->SetBGcolor($red,$green,$blue);
  }
  function SetLineStyle($width, $black, $white) 
  {
		static $a=0; $a++;	$this->debug( "SetLineStyle: $a<br>");
		PDF_setlinewidth($this->p,$width);
		PDF_setdash($this->p,$black,$white);
  }
  function Line($x1, $y1, $x2, $y2) 
  {
		static $a;	$a =0; $a++;	$this->debug( "Line: $a<br>");
		$this->MoveTo($x1,$y1);
		PDF_lineto($this->p,$x2,$y2);
		PDF_stroke($this->p);
  }
  function MoveTo($x, $y) 
  {
		static $a;	$a =0; $a++;	$this->debug( "MoveTo: $a<br>");
		PDF_moveto($this->p,$x,$y);
  }
  function LineTo($x, $y) 
  {
		static $a;	$a =0; $a++;	$this->debug( "LineTo: $a<br>");
		PDF_lineto($this->p,$x,$y);
		PDF_stroke($this->p);
  }
  function Rectangle($x, $y, $width, $height) 
  {
		static $a;	$a =0; $a++;	$this->debug( "Rectangle: $a<br>");
		PDF_rect($this->p,$x,$y,$width,$height);
		PDF_stroke($this->p);
  } 
  function Bar($x, $y, $width, $height) 
  {
		static $a=0;	$a++;	$this->debug( "Bar: $a<br>");
		PDF_rect($this->p,$x,$y,$width,$height);
		PDF_fill_stroke($this->p);
  }
  function Circle($x, $y, $radius) 
  {
		static $a;	$a =0; $a++;	$this->debug( "Circle: $a<br>");
		pdf_circle($this->p,$x,$y,$radius);
		PDF_stroke($this->p);
  }
  function Disc($x, $y, $radius) 
  {
		static $a;	$a =0; $a++;	$this->debug( "Disc: $a<br>");
		PDF_circle($this->p,$x,$y,$radius);
		PDF_fill_stroke($this->p);
  }
  function Arc($x, $y, $radius, $startangle, $endangle) 
  {
		PDF_arc($this->p,$x,$y,$radius,$statangle,$endangle);
		PDF_stroke($this->p);
  }  
  function Pie($x, $y, $radius, $startangle, $endangle) 
  {
		static $a;	$a =0; $a++;	$this->debug( "Pie: $a<br>");
		// Is a pie a filled arc?
		PDF_arc($this->p,$x,$y,$radius,$statangle,$endangle);
		PDF_stroke($this->p);
  }    
  function FullPie($x, $y, $radius, $startangle, $endangle) 
  {
		static $a;	$a =0; $a++;	$this->debug( "FullPie: $a<br>");
		PDF_arc($this->p,$x,$y,$radius,$statangle,$endangle);
		PDF_stroke($this->p);
  }
  // Font functions
  function SetFont($fontname, $size, $bold=false, $italic=false, $underline=false) 
  {
		static $a;	$a =0; $a++;	$this->debug( "SetFont: $a<br>");
		$font = PDF_findfont($this->p,$fontname,"host",0);
		PDF_setfont($this->p,$font,$size);
  }
  // Text functions
  function ShowText($text, $x, $y, $width, $height, $align)
  {
		static $a=0; $a++;	$this->debug( "ShowText: $a<br>");
		// FIXME: take width height and align into account
		PDF_set_text_pos($this->p,$x,$y);
		PDF_show($this->p,$text);
  }

  function GetTextWidth($text,$size) 
  {
      $font = PDF_get_value($this->p,"font");
      return ceil(PDF_stringwidth($this->p, $text,$font , $size));
  }
}
?>