<?php
// Yaps v 1.0.1

// Temporary directory to write the PostScript file
if (!defined('YTMP_DIR')) define('YTMP_DIR',"/tmp");
// Complete GhostScript filename
if (!defined('YGS_DIR')) define('YGS_DIR',"/usr/bin/gs");

class Yapser 
{
  var $PSFile = "";
  var $PSFileName = "";
  var $PDFFileName = "";
  var $autoStroke = "TRUE";
  var $fontName = "";
  var $fontSize = "";
  var $isUnderline = "FALSE";
  var $align="LEFT";
  var $underline="";
  var $fonts = "";
  function Yapser() 
  {    
    $this->fonts=array();          
  }
  // File Handling functions
  function Open($filename="") 
  {
    if ($filename=="") {
      $this->PSFileName = tempnam(YTMP_DIR, "yaps");
    } else {
      $this->PSFileName=$filename;
    }
    $this->PSFile= fopen($this->PSFileName, "w+") or die("Unable to open PostScript output file ".$this->PSFileName);
    $this->out("%!PS-Adobe-3.0
    % Yaps Functions
    /Encoding ISOLatin1Encoding def
    
    /KTSetFont { findfont exch dup /KTfontHeight 2 -1 roll store scalefont setfont } bind def
    /KTShowAt { moveto show } bind def
    /KTShowAtUnderline {3 copy 3 -1 roll stringwidth pop 3 -1 roll dup 4 1 roll add 3 1 roll dup 1 sub 3 1 roll 1 sub moveto lineto moveto show} bind def
    /KTLine { 4 -2 roll moveto lineto} bind def
    /KTCurve { curveto } bind def
    /KTRect { 8 -2 roll 2 copy moveto 8 2 roll lineto lineto lineto lineto } bind def
    /KTCircle { 0 360 arc } bind def
    /KTArc { arc} bind def
    /KTPie { 3 copy pop 7 -2 roll 2 copy 2 copy 11 -3 roll arc lineto 4 -2 roll 2 copy 6 2 roll sin mul add 4 -2 roll cos mul 3 -1 roll add 2 -1 roll lineto closepath} bind def
    /KTSetColor { setrgbcolor } bind def
    /KTSetLineWidth { setlinewidth } bind def
    /KTSetDash { setdash } bind def
    /KTSetLineCap { setlinecap } bind def
    /KTSetLineJoin { setlinejoin } bind def
    /KTRotate { rotate } bind def                                                  
    /KTTranslate { translate } bind def    

    % Internal use Functions
    /KTForStr { 0 1 3 -1 roll dup 4 1 roll length 1 sub  { dup 3 -1 roll dup 4 -1 roll 1 getinterval 4 -1 roll dup 4 -1 roll 5 2 roll exec } for pop pop } bind def
    /KTStringWidth { stringwidth pop } bind def
    /KTFits { KTStringWidth gt } bind def
    /KTIn { search {pop pop pop true } {pop false} ifelse } bind def

    % Internal use Dictionary Variables
    /KTSeparators ( ) def
    /KTEolSeparators (\n) def
    /KTstartx 500 def
    /KTstarty 300 def
    /KTx 0 def
    /KTy 0 def
    /KTwidth 0 def 
    /KTheight 0 def
    /KTfontHeight 0 def
    /KTlastSeparator 0 def
    /KTIsN 1 def
    /KTString 1024 string def
    /KTSubString 1024 string def
    /KTIndex 0 def
  %%EndProlog
  initgraphics");
  }
  function Close() 
  {
    fclose($this->PSFile);
  }
  function CloseWeb() 
  {
    $this->PStoPDF("");
    $fp=fopen($this->PDFFileName,'r');
    $data = fread($fp,filesize($this->PDFFileName));
    fclose($fp);
    $this->DelPDF();
    $this->DelPS();
    header("Content-type: application/pdf");
		header("Content-Length: ".filesize($this->PDFFileName));
		header("Content-Disposition: inline; filename=Yaps.pdf");    
    echo $data;   
  }
  function CloseWebPS() 
  {
    $fp=fopen($this->PSFileName,'r');
    $data = fread($fp,filesize($this->PSFileName));
    fclose($fp);
    $this->DelPS();
    header("Content-type: application/postscript");
		header("Content-Length: ".filesize($this->PSFileName));
		header("Content-Disposition: inline; filename=Yaps.ps");    
    echo $data;   
  }
  function PStoPDF($pdffile="") 
  {
    if ($pdffile=="") {
      $this->PDFFileName = tempnam(YTMP_DIR, "yaps");
    } else {
      $this->PDFFileName=$pdffile;
    }
    $Command = sprintf("cat %s | ".YGS_DIR." -q -sDEVICE=pdfwrite -sOutputFile=%s - ", $this->PSFileName, $this->PDFFileName);
    system($Command);
    $this->DelPS();
  }
  function DelPDF() 
  {
    system(sprintf("rm %s", $this->PDFFileName));
  }
  function DelPS() 
  {
    system(sprintf("rm %s", $this->PSFileName));
  }
  function SetInfo($info, $value) 
  {
    $this->out("
  %%".$info.": ".$value);
  }

  // Page functions
  function BeginPage ($width, $height) 
  {
    $this->out("
  <<  /PageSize [".$width." ".$height."]
        /ImagingBBox null
  >> setpagedevice
  gsave");
  }
  function EndPage() 
  {
    $this->out("
  grestore
  stroke
  showpage");
  }  

  // Graphic functions
  // Function setting the autoStroke (automatically stokes the paths you create). Warning: filled objects are stroked independent of the autoStroke's value
  function SetAutoStroke($autoStroke) 
  {  // $autoStroke is "true" or "false"
    $this->autoStroke=$autoStroke;
  }   
  function SetColor($red, $green, $blue) 
  {
    $this->out("
  ".$red." ".$green." ".$blue." KTSetColor");
  } 
  function SetLineWidth($width) 
  {
    $this->out("
  ".$width." KTSetLineWidth");
  }
  function SetDash($array, $offset) 
  { // Sets the line dash. $array is a space-sepparated string containing non-zero integers
    $this->out("
  [".$array."] ".$offset." KTSetDash");
  }
  function SetLineCap($cap) 
  { // Set shape of line ends for stroke (0 = butt, 1 = round, 2 = square)
    $this->out("
  ".$cap." KTSetLineCap");
  }
  function SetLineJoin($join) 
  { //Set shape of corners for stroke (0 = miter, 1 = round, 2 = bevel)
    $this->out("
  ".$join." KTSetLineJoin");
  }
  function Line($x1, $y1, $x2, $y2) 
  {
    $this->out("
  ".$x1." ".$y1." ".$x2." ".$y2." KTLine");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  }
  function Curve($x1, $y1, $x2, $y2, $x3, $y3) 
  {
    $this->out("
  ".$x1." ".$y1." ".$x2." ".$y2." ".$x3." ".$y3." KTCurve");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  }
  function MoveTo($x, $y) 
  {
    $this->out("
  ".$x." ".$y." moveto");
  }
  function LineTo($x, $y) 
  {
    $this->out("
  ".$x." ".$y." lineto");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  }
  function Rectangle($x, $y, $width, $height) 
  {
    $this->out("
  ".$x." ".$y." ".($x+$width)." ".$y." ".($x+$width)." ".($y+$height)." ".$x." ".($y+$height)." KTRect");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  } 
  function Bar($x, $y, $width, $height) 
  {
    $this->out("
  newpath
  ".$x." ".$y." ".($x+$width)." ".$y." ".($x+$width)." ".($y+$height)." ".$x." ".($y+$height)." KTRect
  closepath
  fill");
  }
  function Circle($x, $y, $radius) 
  {
    $this->out("
  ".$x." ".$y." ".$radius." KTCircle");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  }
  function Disc($x, $y, $radius) 
  {
    $this->out("
  newpath
  ".$x." ".$y." ".$radius." KTCircle
  closepath
  fill");
  }
  function Arc($x, $y, $radius, $startangle, $endangle) 
  {
    $this->out("
  ".$x." ".$y." ".$radius." ".$startangle." ".$endangle." KTArc");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  }  
  function Pie($x, $y, $radius, $startangle, $endangle) 
  {
    $this->out("
  ".$x." ".$y." ".$radius." ".$startangle." ".$endangle." KTPie");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  }    
  function FullPie($x, $y, $radius, $startangle, $endangle) 
  {
    $this->out("
  newpath
  ".$x." ".$y." ".$radius." ".$startangle." ".$endangle." KTPie
  closepath
  fill");
  }

  // Path functions
  function Stroke() 
  {
    $this->out("
  stroke");
  }
  function NewPath() 
  {
    $this->out("
  newpath");
  }
  function ClosePath() 
  {
    $this->out("
  closepath");
  }

  // Clipping functions
  function BeginClip() 
  {
    $this->out("
  clipsave
  clip");
  }
  function BeginEOClip() 
  {
    $this->out("
  clipsave
  eoclip");
  }
  function EndClip() 
  {
    $this->out("
  cliprestore");
  }

  // Coordinate functions
  function Rotate($angle) 
  {
    $this->out("
  ".$angle." KTRotate");
  }
  function Translate($tx, $ty) 
  {
    $this->out("
  ".$tx." ".$ty." KTTranslate");
  }

  // Font functions
  function SetFont($fontname, $size) 
  {
    $this->fontSize=$size;
    $this->fontName=$fontname;
    if (!in_array($fontname,$this->fonts)) {
      array_push($this->fonts,$fontname);      
      $this->out("      
  /".$fontname." findfont
  dup length 
  dict begin 
    { 
      1 index /FID ne
        {def}
        {pop pop}
      ifelse
    } forall
    /Encoding ISOLatin1Encoding def
    currentdict
  end
  /".$fontname." exch definefont pop");
  }
    $this->out("
  ".$size." /".$fontname." KTSetFont");
  }
  function GetFontSize() 
  {
    return $this->fontSize;
  }
  function GetFontName() 
  {
    return $this->fontName;
  }  
  function SetUnderline($underline="FALSE") 
  {
    $this->isUnderline=strtoupper($underline);
    if ($this->isUnderline=="TRUE") {
      $this->underline="Underline";
    } else {
      $this->underline="";
      }
  }
  function GetUnderline() 
  {
    return $this->isUnderline;
  }
  function SetAlign($align="LEFT") 
  {
    $this->align=strtoupper($align);
  }
  function GetAlign() 
  {
    return $this->align;
  }
    
    
  // Text functions
  function ShowAt($text, $x, $y)
  {
    $this->out("
    (".$text.") ".$x." ".$y." KTShowAt".$this->underline."
");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  }

  function ShowBoxed($text, $x, $y, $width, $height) 
  {
    $this->out("
  % KTShowBoxed
  clipsave
  ".$x." ".$y." ".($width)." ".(-$height)." rectclip
  /KTstartx ".$x." store
  /KTstarty ".$y." store
  /KTwidth ".$width." store
  /KTheight ".$height." store
  /KTx KTstartx store
  /KTy KTstarty KTfontHeight sub store
  /KTString 1024 string store
  /KTSubString 1024 string store
  /KTIndex 0 store
  /KTlastSeparator -1 store
  /KTIsN 1 store
  {
    KTy KTstarty KTheight sub gt 
    {
      dup (\n) eq 
      {
        /KTx KTstartx store
        /KTy KTy KTfontHeight sub store
        /KTlastSeparator KTIndex store
        /KTIsN 1 store
      }
      {
        dup KTStringWidth KTx add /KTx 2 -1 roll store
        KTx KTstartx KTwidth add gt
        {
          KTString KTlastSeparator 1 add KTIndex KTlastSeparator sub 1 sub getinterval /KTSubString 2 -1 roll store
          /KTx KTstartx KTSubString KTStringWidth add store
          KTIsN 0 eq
          {
            KTString KTlastSeparator (\n) putinterval
            /KTlastSeparator KTlastSeparator store
            /KTIsN 1 store            
            /KTy KTy KTfontHeight sub store
          }
          if
          dup KTStringWidth /KTx 2 -1 roll KTstartx add store
          /KTx KTx KTSubString KTStringWidth add store
          KTx KTstartx KTwidth add gt
          {
            KTString KTIndex (\n) putinterval
            /KTlastSeparator KTIndex store
            /KTIsN 1 store
            /KTIndex KTIndex 1 add store
            dup KTStringWidth /KTx KTstartx 3 -1 roll add store
            /KTy KTy KTfontHeight sub store
          }
          if          
        }
        if
        dup KTSeparators 2 -1 roll KTIn
        {
          /KTlastSeparator KTIndex store
          /KTIsN 0 store
        }
        if
      }
      ifelse
      KTString KTIndex 3 -1 roll putinterval /KTIndex KTIndex 1 add store pop
    } 
    {
      pop pop
    }
    ifelse 
  } 
  (".$text.") KTForStr
  /KTString KTString 0 KTIndex 1 add getinterval store
  KTString KTIndex (\n) putinterval
  ");
    switch ($this->align) {
      default:
        $this->out("

  % KTRenderBoxed Left-Align
  /KTstartx ".$x." store
  /KTstarty ".$y." store
  /KTwidth ".$width." store
  /KTheight ".$height." store
  /KTx KTstartx store
  /KTy KTstarty KTfontHeight sub store
  KTx KTy moveto
  { 
    KTy KTstarty KTheight sub gt
    {
    dup KTx KTy KTShowAt".$this->underline."
    dup (\n) eq
    {
      /KTx KTstartx store
      /KTy KTy KTfontHeight sub store
    }
    {
      dup
      KTStringWidth KTx add /KTx 2 -1 roll store
    }
    ifelse
    }
    if
    pop    
    pop
  }    
  KTString KTForStr
  stroke
  cliprestore");
        break;
      case "RIGHT":
        $this->out("
        
  % KTRenderBoxed Right-Align
  /KTstartx ".$x." store
  /KTstarty ".$y." store
  /KTwidth ".$width." store
  /KTheight ".$height." store
  /KTx KTstartx store
  /KTy KTstarty KTfontHeight sub store
  /KTlastSeparator 0 store
  KTx KTy moveto
  { 
    KTy KTstarty KTheight sub gt
    {
    dup (\n) eq
    {
      2 copy pop KTString KTlastSeparator 3 -1 roll KTlastSeparator sub getinterval dup KTStringWidth KTstartx KTwidth add 2 -1 roll sub KTy KTShowAt".$this->underline."
      2 copy pop /KTlastSeparator 2 -1 roll store      
      /KTx KTstartx store
      /KTy KTy KTfontHeight sub store
      
    }
    if
    }
    if 
    pop    
    pop
  }    
  KTString KTForStr
  stroke
  cliprestore  
");
        break;
      case "CENTER":
        $this->out("
        
  % KTRenderBoxed Center-Align
  /KTstartx ".$x." store
  /KTstarty ".$y." store
  /KTwidth ".$width." store
  /KTheight ".$height." store
  /KTx KTstartx store
  /KTy KTstarty KTfontHeight sub store
  /KTlastSeparator 0 store
  KTx KTy moveto
  { 
    KTy KTstarty KTheight sub gt
    {
    dup (\n) eq
    {
      2 copy pop KTString KTlastSeparator 3 -1 roll KTlastSeparator sub getinterval dup KTStringWidth KTwidth 2 -1 roll sub 2 div KTstartx add KTy KTShowAt".$this->underline."
      2 copy pop /KTlastSeparator 2 -1 roll store      
      /KTx KTstartx store
      /KTy KTy KTfontHeight sub store
      
    }
    if
    }
    if 
    pop    
    pop
  }    
  KTString KTForStr
  stroke
  cliprestore  
");
        break;

    }  
  }

  // Image functions
  function ShowImage($src, $x, $y, $width="0", $height="0") 
  {
    $size=getimagesize($src);    
    if ($width=="0" || $height=="0") {
      $width=$size[0];
      $height=$size[1];
    }
    switch ($size[2]) {
      case "1":
        //gif
        break;
      case "2":
        //jpg
    $this->out("
  ".$x." ".$y." moveto
  /WIDTH ".$size[0]." def
  /HEIGHT ".$size[1]." def
  /DPI 75 def
  ".$x." ".($y-$height)." translate
  ".$width." ".$height." scale
  /DeviceRGB setcolorspace
  <<
      /ImageType         1
      /Width             WIDTH
      /Height            HEIGHT
      /BitsPerComponent  8
      /Decode            [0 1 0 1 0 1]
      /ImageMatrix       [WIDTH 0 0 HEIGHT neg 0 HEIGHT]
      /DataSource currentfile /DCTDecode filter
  >>
  image
");
    $fp=fopen($src,'r');
    $data = fread($fp,filesize($src));
    fclose($fp);
    $this->out($data);
    $this->out("
  ".(1/$width)." ".(1/$height)." scale    
  ".-$x." ".-($y-$height)." translate");
        break;
      case "3":
        // PNG
    $this->out("
  ".$x." ".$y." moveto
  /WIDTH ".$size[0]." def
  /HEIGHT ".$size[1]." def
  /DPI 75 def
  ".$x." ".($y-$height)." translate
  ".$width." ".$height." scale
  /DeviceRGB setcolorspace
  <<
      /ImageType         1
      /Width             WIDTH
      /Height            HEIGHT
      /BitsPerComponent  8
      /Decode            [0 1 0 1 0 1]
      /ImageMatrix       [WIDTH 0 0 HEIGHT neg 0 HEIGHT]
      /DataSource currentfile /DCTDecode filter
  >>
  image
");
    $img = imagecreatefrompng($src);
    $tmpname=tempnam(YTMP_DIR, "yaps");
    imagejpeg($img,$tmpname,99);
    imagedestroy($img);
    $fp=fopen($tmpname,'r');
    $data = fread($fp,filesize($tmpname));
    fclose($fp);
    system(sprintf("rm %s", $tmpname));    
    $this->out($data);    
    $this->out("
  ".(1/$width)." ".(1/$height)." scale    
  ".-$x." ".-($y-$height)." translate");
        break;
      case "4":
        // SWF
        break;        
      default:
        break;
    }
  }
  

  function ShowAtUnderline($text, $x, $y)
  {
    $this->out("
    (".$text.") ".$x." ".$y." KTShowAtUnderline");
    if ($this->autoStroke=="TRUE") {
      $this->out("
  stroke");
    }
  }   
  
  // Private functions. Do NOT call these functions from outside of object. Use this ONLY if you have strong knoledge of PostScript Language
  function out($outtext) 
  {
//    echo $outtext."<br>";
    fwrite($this->PSFile, $outtext);
  }  
}
?>