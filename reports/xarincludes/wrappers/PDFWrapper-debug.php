<?php

class PDFWrapper_Debug extends PDFWrapper 
{
  var $type='Debug';
  var $p;  

  function PDFWrapper_Debug() 
  {
    echo "Debug instantiated !<br>";
  }

  // Instantiation functions                  
  function Open($file) 
  {
		echo "Open<br>";
  }                      
  function Close() 
  {
		echo "Close<br>";
  }
  function CloseWeb($redir="") 
  {
		echo "CloseWeb<br>";
  }
  
  // Page functions
  function BeginPage ($width, $height) 
  {
		echo "BeginPage<br>";
  }                                   
  function EndPage() 
  {
		echo "EndPage<br>";
  }
  
  // Image functions
  function PlaceImage($type, $src, $x, $y) 
  {
		echo "PlaceImage<br>";
  }
  function PlaceScaledImage($type, $src, $x, $y, $width, $height) 
  {
		echo "PlaceScaledImage<br>";
  }

  // Graphic functions
  function SetColor($red, $green, $blue) 
  {
		echo "SetColor<br>";
  } 
  function SetBGColor($red, $green, $blue) 
  {
		echo "SeteBGColor<br>";
  }
  function SetAllColor($red, $green, $blue) 
  {
		echo "SetAllColor<br>";
  }
  function SetLineStyle($width, $black, $white) 
  {
		echo "SetLineStyle<br>";
  }
  function Line($x1, $y1, $x2, $y2) 
  {
		echo "Line<br>";
  }
  function MoveTo($x, $y) 
  {
		echo "MoveTo<br>";
  }
  function LineTo($x, $y) 
  {
		echo "LineTo<br>";
  }
  function Rectangle($x, $y, $width, $height) 
  {
		echo "Rectangle<br>";
  } 
  function Bar($x, $y, $width, $height) 
  {
		echo "Bar<br>";
  }
  function Circle($x, $y, $radius) 
  {
		echo "Circle<br>";
  }
  function Disc($x, $y, $radius) 
  {
		echo "Disc<br>";
  }
  function Arc($x, $y, $radius, $startangle, $endangle) 
  {
		echo "Arce<br>";
  }  
  function Pie($x, $y, $radius, $startangle, $endangle) 
  {
		echo "Pie<br>";
  }    
  function FullPie($x, $y, $radius, $startangle, $endangle) 
  {
		echo "FullPie<br>";
  }

  // Font functions
  function SetFont($fontname, $size, $bold, $italic, $underline) 
  {
		echo "Setfont<br>";
  }
  
  // Text functions
  function ShowText($text, $x, $y, $width, $height, $align)
  {
		echo "Showtext<br>";
  }
  
  function GetTextWidth($text,$size) 
  {
      echo "GetTextWidth<br>";
      return 0;
  }

}
?>