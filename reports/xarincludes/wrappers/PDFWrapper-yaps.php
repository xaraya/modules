<?php
require_once(PDF_WRAPPER."/Yaps.inc.php");

class PDFWrapper_Yaps extends PDFWrapper 
{
  var $type='Yaps';
  var $p;  
  function PDFWrapper_Yaps() 
  {
    //echo "Yaps instantiated !<br>";
    $this->p=new Yapser();
  }

  // Instantiation functions                  
  function Open($file) 
  {
    $this->p->Open($file);
    $this->p->SetInfo("Creator","PDFReports");
    $this->p->SetInfo("Author","InterAKT");
    $this->p->SetInfo("Title","PDFReports");
  }                      
  function Close() 
  {
    $this->p->Close();
  }
  function CloseWeb($redir="") 
  {
    $this->p->Close();
    $this->p->CloseWeb("");
  }
  
  // Page functions
  function BeginPage ($width, $height) 
  {
    $this->p->BeginPage($width, $height);
  }                                   
  function EndPage() 
  {
    $this->p->EndPage();
  }
  
  // Image functions
  function PlaceImage($type, $src, $x, $y) 
  {
    $this->p->ShowImage($src, $x, $y);
  }
  function PlaceScaledImage($type, $src, $x, $y, $width, $height) 
  {
    $this->p->ShowImage($src, $x, $y + $height, $width, $height);
  }

  // Graphic functions
  function SetColor($red, $green, $blue) 
  {
    $this->p->SetColor($red, $green, $blue);
  } 
  function SetBGColor($red, $green, $blue) 
  {
    $this->p->SetColor($red, $green, $blue);
  }
  function SetAllColor($red, $green, $blue) 
  {
    $this->p->SetColor($red, $green, $blue);
  }
  function SetLineStyle($width, $black, $white) 
  {
    $this->p->SetLineWidth($width);
    $this->p->SetDash($black." ".$white,0);
  }
  function Line($x1, $y1, $x2, $y2) 
  {
    $this->p->Line($x1, $y1, $x2, $y2);
  }
  function MoveTo($x, $y) 
  {
    $this->p->MoveTo($x, $y);
  }
  function LineTo($x, $y) 
  {
    $this->p->LineTo($x, $y);
  }
  function Rectangle($x, $y, $width, $height) 
  {
    $this->p->Rectangle($x, $y, $width, $height);
  } 
  function Bar($x, $y, $width, $height) 
  {
    $this->p->Bar($x, $y, $width, $height);
  }
  function Circle($x, $y, $radius) 
  {
    $this->p->Circle($x, $y, $radius);
  }
  function Disc($x, $y, $radius) 
  {
    $this->p->Disc($x, $y, $radius);
  }
  function Arc($x, $y, $radius, $startangle, $endangle) 
  {
    $this->p->Arc($x, $y, $radius);
  }  
  function Pie($x, $y, $radius, $startangle, $endangle) 
  {
    $this->p->Pie($x, $y, $radius, $startangle, $endangle);
  }    
  function FullPie($x, $y, $radius, $startangle, $endangle) 
  {
    $this->p->FullPie($x, $y, $radius, $startangle, $endangle);
  }

  // Font functions
  function SetFont($fontname, $size, $bold, $italic, $underline) 
  {
    $this->p->SetUnderline($underline);
    $this->p->SetFont($fontname, $size);
  }
  
  // Text functions
  function ShowText($text, $x, $y, $width, $height, $align)
  {
    $this->p->SetAlign($align);
    $this->p->ShowBoxed($text,$x,$y+$height, $width, $height);
  }
}
?>