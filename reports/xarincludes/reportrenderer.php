<?php

// ***
// PDFReports-Lite Version 1.0.p0.1 // patched for Xaraya use and changed xml schema to allow more complex reports
// ***
//if (!defined('PDF_VERSION')) define('PDF_VERSION',"PDFReports-Lite 1.0 - HSD patch version 0.1");
define('PDF_VERSION',"PDFReports-Lite 1.0 - Xaraya patch version 0.2");

// Defines the absolute path to the PDFWrapper library
if (!defined('PDF_WRAPPER')) define('PDF_WRAPPER',dirname(__FILE__));
// Modify the following line for the desired PDF creation library.
// Build in a safeguard if not defined, use the Pure PDF library 
if (!defined('PDF_LIBRARY')) define('PDF_LIBRARY',"ezpdf");
//if (!defined('PDF_LIBRARY')) define('PDF_LIBRARY',"pdflib");
//if (!defined('PDF_LIBRARY')) define('PDF_LIBRARY',"yaps");

require(PDF_WRAPPER."/PDFWrapper.php");

// ***
// Parser classes & functions
// ***

/*
 * When something goes really wrong we die with a reason
 */
function PDF_die($location, $errmsg) 
{
  die("<font color='red' size='4' face='Arial, Helvetica, sans-serif'>"
			.PDF_VERSION."</font><br/><font color='blue' face='Arial, Helvetica, sans-serif'><b>"
			.($location)."</b></font> <font color='red' face='Arial, Helvetica, sans-serif'>".($errmsg)."</font>");
}

/*
 * Check wheter a value on a certain location is a value of arrays (this should be handled by xml-parser when it's validating)
 */
function PDF_in($location, $value, $inarray) 
{
  if ( !in_array($value,$inarray) ) PDF_die($location, "XML-ERROR: Value in node not in: <b>( ".implode(", ",$inarray)." )</b>.");
}

/*
 * Check whether a value is numeric
 */
function PDF_numeric($location, $value) 
{
  if ( !is_numeric($value) ) PDF_die($location, "XML-ERROR: Value in node not numeric.");
}
/*
 * Check wheter an item at a certain location contains the right stuff
 */
function PDF_contain($location, $item) 
{
  PDF_die($location,  "XML-ERROR: Element cannot contain type: [".strtoupper($item)."].");  
}

/*
 * check wheter an value is an value unit specification
 *
 */
function PDF_unit($location, $item) 
{
    $expr = '/^([-|+]*[0-9]*.*[0-9]+)(mm|pt)*$/';
    if (!preg_match($expr, $item, $matches)) {
        PDF_DIE($location, "XML-ERROR: Wrong unit specification: [".strtoupper($item)."].");
    }
    // match should have 3 entries:
    // 0 -> the whole
    // 1 -> the value
    // 2 -> the unit
    if(count($matches) == 2) {
        // No unit was specified, insert the default
        $matches[2] = 'pt';
    }
    return toPoints($matches[1],$matches[2]);
}

/*
 * Convert a measurement to points
 *
 */
function toPoints($value, $unit_from) 
{
    switch (strtolower($unit_from)) {
    case 'mm':
        return 2.8452756 * $value ;
    case 'pt':
        return $value;
    default:
        return $value;
    }
}

/*
 * For each tag encountered in the document an instance of this
 * class is made
 */
class XMLTag 
{
	var $name;
	var $nodes;
	var $attrs;

	var $n;
	var $cdata;
    
	function XMLTag($name) 
    {
		$this->name=$name;
		$this->nodes=array();
		$this->attrs=array();
		$this->n=0;
		$this->cdata="";
	}
	function getNode($name, $force=0) 
    {
		$ret=-1;
		for ($i=0;$i<$this->n && $ret==-1;$i++) {
			if ($this->nodes[$i]->name==$name) {
				$ret=$i;
			}
		}
		if ($ret<0) {
			if ($force==1) {
				PDF_die("[XML::PARSER::getNode()]","XMLNode '".$name."' not found in XMLNode '".$this->name."'");
			}
		}
		return $ret;
	}
}

/*
 * Entry point for parsing the xml report definition
 * The xml file is passed in and the root tag is created
 */
function getRootNode($file) 
{
	global $d;
	global $ROOT;
	$d=0;
	$ROOT=0;	
	$ROOT=new XMLTag("ROOT");
	$parser = xml_parser_create();
	xml_parser_set_option ($parser, XML_OPTION_CASE_FOLDING, 1);
	xml_set_element_handler($parser,"startElement","endElement");
	xml_set_character_data_handler($parser,"charData");
	if (!($fp = fopen($file, "r"))) {
		PDF_die("[XML::PARSER]","Could not open XML input !");
	}
	while ($data = fread($fp, 4096)) {
	    if (!xml_parse($parser, $data, feof($fp))) {
            //			echo $data."<br/>";
    		PDF_die("[XML::PARSER]", "Invalid XML file");
    	}
	}
	xml_parser_free($parser);
	return $ROOT->nodes[0];
}

/*
 * For each start element encountered a new instance
 * of xmltag is created with the appropriate properties
 */
function startElement($parser, $name, $attrs) 
{
    global $d;
    global $ROOT;
    $nn=new XMLTag($name);
    while (list($key, $value) = each($attrs)) {
     	$nn->attrs[$key]=$value;
   	}
   	$c=&$ROOT;
   	for ($i=0;$i<$d;$i++) {
   		$c=&$c->nodes[$c->n-1];
   	}
   	$c->nodes[$c->n]=$nn;
   	$c->n++;    
   	$d++;
}
/*
 * End element is found, 
 */
function endElement($parser, $name) 
{
    global $d;
    global $ROOT;    
   	$c=&$ROOT;
   	for ($i=0;$i<$d;$i++) {
   		$c=&$c->nodes[$c->n-1];
   	}
	$d--;
}
/*
 * Character data
 */
function charData($parser,$data) 
{
    global $d;
    global $ROOT;   	    
   	$c=&$ROOT;
   	for ($i=0;$i<$d;$i++) {
   		$c=&$c->nodes[$c->n-1];
   	}
   	$c->cdata.=trim($data);
}

/* 
 * Handle the report element
 */
class report 
{
	// Subelements of report element
	var $datasource;
	var $documentheader;
	var $documentfooter;
	var $pageheader;
	var $pagefooter;
	var $detail;
    
	// Constructor
	function report($node, &$db, $filter="") 
    {
		//		echo "report element <br/>";
		$t=&$node->nodes[$node->getNode("DATASOURCE",1)];
		if ($filter!="\"\"") {
			if ($t->nodes[$t->getNode("WHERE",1)]->cdata!="\"\"") {
				$t->nodes[$t->getNode("WHERE",1)]->cdata.=".\" AND \".".$filter;
			} else {
				$t->nodes[$t->getNode("WHERE",1)]->cdata=$filter;
			}
		}		
        
		// Report element contains datasource, documentheader, pageheader, detail, pagefooter, documentfooter
		$this->datasource=new datasource($t, $db);
		$this->documentheader=new documentheader($node->nodes[$node->getNode("DOCUMENTHEADER",1)]);
		$this->pageheader=new pageheader($node->nodes[$node->getNode("PAGEHEADER",1)]);
		$this->detail=new detail($node->nodes[$node->getNode("DETAIL",1)],$db);
		$this->pagefooter=new pagefooter($node->nodes[$node->getNode("PAGEFOOTER",1)]);
		$this->documentfooter=new documentfooter($node->nodes[$node->getNode("DOCUMENTFOOTER",1)]);
	}             
}

/* 
 * Handle the document tag
 */
class document 
{
	// Attributes of document element
	var $page;
	var $pagelist=array("A6"=>array("width"=>"297","height"=>"421"),
                        "A5"=>array("width"=>"421","height"=>"595"),
                        "A4"=>array("width"=>"595","height"=>"842"),
                        "A3"=>array("width"=>"842","height"=>"1190"),
                        "A2"=>array("width"=>"1190","height"=>"1684"),
                        "A1"=>array("width"=>"1684","height"=>"2380"),
                        "A0"=>array("width"=>"2380","height"=>"3368"));
	var $type;
	var $left;
	var $right;
	var $top;
	var $bottom;
	var $width;
	var $height;

	// Subelements of document
	var $report;
	
	function document($node, &$db, $filter="") 
    {
		//		echo "document element<br/>";
		// Default page is A4
		$this->page=(strlen($node->attrs["PAGE"])>0)?strtoupper($node->attrs["PAGE"]):"A4";
        PDF_in("[DOCUMENT][PAGE]", $this->page, array("A6", "A5", "A4", "A3", "A2", "A1", "A0") );
		// Default type is portrait
		$this->type=(strlen($node->attrs["TYPE"])>0)?strtoupper($node->attrs["TYPE"]):"PORTRAIT";
		PDF_in("[DOCUMENT][TYPE]", $this->type, array("PORTRAIT", "LANDSCAPE") );

		$this->left   = PDF_unit("[DOCUMENT][LEFT]" , $node->attrs["LEFT"]);
		$this->right  = PDF_unit("[DOCUMENT][RIGHT]", $node->attrs["RIGHT"]);
		$this->top    = PDF_unit("[DOCUMENT][TOP]"  , $node->attrs["TOP"]);
        $this->bottom = PDF_unit("[DOCUMENT][BOTTOM", $node->attrs["BOTTOM"]);

		if ($this->type=="LANDSCAPE") {
			$this->width=$this->pagelist[$this->page]["height"];
			$this->height=$this->pagelist[$this->page]["width"];
		} else {
			$this->width=$this->pagelist[$this->page]["width"];
			$this->height=$this->pagelist[$this->page]["height"];
		}	
		/* Document element contains report */
		$this->report = new report($node->nodes[$node->getNode("REPORT",1)],$db, $filter);

	}
}

class datasource 
{
	var $db;	
	var $fields;
	var $table;
	var $where;
	var $groupby;
	var $orderby;
	var $sql;
	var $rs;
	var $rs_lastpage;
	var $rs_total;
	var $rs_current;
	var $repeat;
	
	function datasource($node, &$db) 
    {
		//		echo "datasource element<br/>";
		$this->db=$db;
		$this->fields=$node->nodes[$node->getNode("FIELDS",1)]->cdata;
		$this->table=$node->nodes[$node->getNode("TABLE",1)]->cdata;
		$this->where=$node->nodes[$node->getNode("WHERE",1)]->cdata;
		$this->groupby=$node->nodes[$node->getNode("GROUPBY",1)]->cdata;
		$this->orderby=$node->nodes[$node->getNode("ORDERBY",1)]->cdata;
		$this->sql="\"SELECT \".".$this->fields.".\" FROM \".".$this->table;
		if ($this->where!="\"\"") {
			$this->sql.=".\" WHERE \".".$this->where;
		}
		if ($this->groupby!="\"\"") {
			$this->sql.=".\" GROUP BY \".".$this->groupby;
		}
		if ($this->orderby!="\"\"") {
			$this->sql.=".\" ORDER BY \".".$this->orderby;
		}
	}
	function getData(&$renderer, &$datasource) 
    {
//		echo $renderer->solveexp($this->sql,$datasource)."<br/>";
		$this->rs=$this->db->Execute($renderer->solveexp($this->sql,$datasource)) or PDF_die("[DOCUMENT]::[DATASOURCE]:getData()",$this->db->ErrorMsg());
		$this->rs_current=0;
		$this->rs_lastpage=0;
		$this->rs_total=$this->rs->RecordCount();
//		echo "<br/>".$this->rs_total."<br/>";
		$this->repeat=0;
	}
	
	function closeData() 
    {
		$this->ok=-2;
		$this->rs->Close();
	}
	function dbClose() 
    {
		$this->db->Close();
	}
}

class headerfooter 
{
	var $height;
	
	var $container;
	var $n;
	function headerfooter($node,$tagstring) 
    {
		$this->height = PDF_unit("$tagstring[HEIGHT]",$node->attrs["HEIGHT"]);
		$this->container=array();
		$this->n=0;
		for ($i=0;$i<$node->n;$i++) {
			$t=0;
			switch ($node->nodes[$i]->name) {
				case "TEXT":
					$this->container[$this->n]=new text($node->nodes[$i]);
					$this->n++;
					break;
				case "IMAGE":
					$this->container[$this->n]=new image($node->nodes[$i]);
					$this->n++;
					break;
				case "RECTANGLE":
					$this->container[$this->n]=new rectangle($node->nodes[$i]);
					$this->n++;
					break;
				case "LINE":
					$this->container[$this->n]=new line($node->nodes[$i]);
					$this->n++;
					break;					
				default:
					PDF_contain("$tagstring]",$node->nodes[$i]->name);
			}
		}
	}
}

class documentheader extends headerfooter 
{
	function documentheader($node) 
    {
		//		echo "docheader<br/>";
		parent::headerfooter($node,"[DOCUMENT]::[DOCUMENTHEADER]");
	}
}

class documentfooter extends headerfooter 
{
	function documentfooter($node) 
    {
		//		echo "docfooter<br/>";
		parent::headerfooter($node,"[DOCUMENT]::[DOCUMENTFOOTER]");
	}
}

class pageheader extends headerfooter 
{
	function pageheader($node) 
    {
		//		echo "pageheader<br/>";
		parent::headerfooter($node,"[DOCUMENT]::[PAGEHEADER]");
	}
}

class pagefooter extends headerfooter 
{
	function pagefooter($node) 
    {
		//		echo "pagefooter<br/>";
		parent::headerfooter($node,"[DOCUMENT]::[PAGEFOOTER]");
	}
}

class detailheader extends headerfooter 
{
	function detailheader($node) 
    {
		//		echo "detailheader<br/>";
		parent::headerfooter($node,"[DOCUMENT]::[DETAIL]::[HEADER]");
	}
}


class detail 
{
	var $width;
	var $height;
	var $repeat;
	var $flow;
	var $detailheader;
	
	var $container;
	var $n;
	var $hasSub;
	
	function detail($node, &$db) 
    {
		$this->width = PDF_unit("[DOCUMENT]::[DETAIL][WIDTH]" ,$node->attrs["WIDTH"]);
		$this->height= PDF_unit("[DOCUMENT]::[DETAIL][HEIGHT]",$node->attrs["HEIGHT"]);

		$this->repeat=$node->attrs["REPEAT"];
		PDF_numeric("[DOCUMENT]::[DETAIL][REPEAT]",$this->repeat);
		$this->flow=strtoupper($node->attrs["FLOW"]);
        PDF_in("[DOCUMENT]::[DETAIL][FLOW]", $this->flow, array("HORIZONTAL", "VERTICAL") );
		$this->detailheader=new detailheader($node->nodes[$node->getNode("DETAILHEADER",1)]);
		$this->hasSub=0;
		$this->container=array();
		$this->n=0;
		for ($i=0;$i<$node->n;$i++) {
			$t=0;
			switch ($node->nodes[$i]->name) {
            case "TEXT":
                $this->container[$this->n]=new text($node->nodes[$i]);
                $this->n++;
                break;
            case "IMAGE":
                $this->container[$this->n]=new image($node->nodes[$i]);
                $this->n++;
                break;
            case "RECTANGLE":
                $this->container[$this->n]=new rectangle($node->nodes[$i]);
                $this->n++;
                break;
            case "LINE":
                $this->container[$this->n]=new line($node->nodes[$i]);
                $this->n++;
                break;					
            case "BREAKPAGE":
                $this->container[$this->n]=new breakpage($node->nodes[$i]);
                $this->n++;
                break;
                break;				
            case "DETAILHEADER":
                break;
            default:
                PDF_contain("[DOCUMENT]::[DETAIL]",$node->nodes[$i]->name);
			}
		}
	}
}

class text 
{
	var $x;
	var $y;
	var $width;
	var $height;
	var $align;	
	var $font;
	var $size;
	var $bold;
	var $italic;
	var $underline;
	var $color;
	var $bgcolor;
	var $red;
	var $green;
	var $blue;
	var $bred;
	var $bgreen;
	var $bblue;
	var $text;
    var $position;
	
	function text($node) 
    {
        // If font tag omitted, default to Helvetica
        $this->font="Helvetica";
        if (array_key_exists("FONT",$node->attrs)) {
            $this->font=(strlen($node->attrs["FONT"])>0)?$node->attrs["FONT"]:"Helvetica";
        }

		// Default size is 10pt
		$this->size=(strlen($node->attrs["SIZE"])>0)?$node->attrs["SIZE"]:'10pt';
		$this->size= PDF_unit("[DOCUMENT]::[TEXT][SIZE]",$this->size);

		// Default not bold
        $this->bold="FALSE";
        if (array_key_exists("BOLD",$node->attrs)) {
            $this->bold=(strtoupper($node->attrs["BOLD"]=="BOLD"))?"TRUE":"FALSE";
        }
        PDF_in("[DOCUMENT]::[TEXT][BOLD]", $this->bold, array("TRUE", "FALSE", "") ); //Empty means tag is omitted and thus is false

        // Default positioning is relative
        $this->position='RELATIVE';
        if(array_key_exists("POSITION", $node->attrs)) {
            $this->position=(strlen($node->attrs["POSITION"])>0)?$node->attrs["POSITION"]:"RELATIVE";
        }
        PDF_in("[DOCUMENT]::[TEXT][[POSITION]",$this->position,array("ABSOLUTE","RELATIVE"));

        // Default not italic
        $this->italic="FALSE";
        if (array_key_exists("ITALIC",$node->attrs)) {
            $this->italic=(strtoupper($node->attrs["ITALIC"])=="TRUE")?"TRUE":"FALSE";
        }
        PDF_in("[DOCUMENT]::[TEXT][ITALIC]", $this->italic, array("TRUE", "FALSE","") ); //Empty means tag is omitted and thus is false

        if ($this->bold=="TRUE" || $this->italic=="TRUE"){
            $this->font.="-";
        }		
        if ($this->bold=="TRUE"){
            $this->font=$this->font."Bold";
        }
        if ($this->italic=="TRUE"){
			$this->font=$this->font."Oblique";
		}
		
		// Default not underline
        $this->underline="FALSE";
        if (array_key_exists("UNDERLINE",$node->attrs)) {
            $this->underline=(strtoupper($node->attrs["UNDERLINE"]=="TRUE"))?"TRUE":"FALSE";
        }
		PDF_in("[DOCUMENT]::[TEXT][UNDERLINE]", $this->underline, array("TRUE", "FALSE","") );		
        
        // Default x pos =0 
        $this->x = (array_key_exists('X',$node->attrs))?$node->attrs["X"]:0;
		$this->x = PDF_unit("[DOCUMENT]::[TEXT][X]",$this->x);
        $this->y = (array_key_exists('Y',$node->attrs))?$node->attrs["Y"]:0;
		$this->y= PDF_unit("[DOCUMENT]::[TEXT][Y]", $this->y);

		// If width is not specified set it to -1 and let renderer decide
		$this->width=(strlen($node->attrs["WIDTH"])>0)?$node->attrs["WIDTH"]:-1;
		$this->width = PDF_unit("[DOCUMENT]::[TEXT][WIDTH]",$this->width);

		// Height defaults to textsize
		$this->height = (strlen($node->attrs["HEIGHT"])>0)?$node->attrs["HEIGHT"]:$this->size;               
		$this->height = PDF_unit("[DOCUMENT]::[TEXT][HEIGHT]",$this->height);

		// Default align is left
        $this->align="LEFT";
        if (array_key_exists("ALIGN",$node->attrs)) {
            $this->align=(strlen($node->attrs["ALIGN"])>0)?strtoupper($node->attrs["ALIGN"]):"LEFT"; 
        }
		PDF_in("[DOCUMENT]::[TEXT][ALIGN]", $this->align, array("LEFT", "CENTER", "RIGHT") );

		// Default text color is black
        $this->color="000000";
        if (array_key_exists("COLOR",$node->attrs)) {
            $this->color=(strlen($node->attrs["COLOR"])>0)?$node->attrs["COLOR"]:"000000";
        }
		$this->red=hexdec(substr($this->color,0,2));
		$this->red=$this->red/255;
		$this->green=hexdec(substr($this->color,2,2));
		$this->green=$this->green/255;
		$this->blue=hexdec(substr($this->color,4,2));
		$this->blue=$this->blue/255;
        
		// Default background color is white
		$this->bgcolor=(array_key_exists('BGCOLOR',$node->attrs))?$node->attrs["BGCOLOR"]:"ffffff";
		$this->bred=hexdec(substr($this->bgcolor,0,2));
		$this->bred=$this->bred/255;
		$this->bgreen=hexdec(substr($this->bgcolor,2,2));
		$this->bgreen=$this->bgreen/255;
		$this->bblue=hexdec(substr($this->bgcolor,4,2));
		$this->bblue=$this->bblue/255;
		$this->text=$node->cdata;
	}
}

class image 
{
	var $x;
	var $y;
	var $width;
	var $height;
	var $src;
	var $type;
    var $position;
	
	function image($node) 
    {
        // Default positioning is relative
        $this->position='RELATIVE';
        if(array_key_exists("POSITION", $node->attrs)) {
            $this->position=(strlen($node->attrs["POSITION"])>0)?$node->attrs["POSITION"]:"RELATIVE";
        }
        PDF_in("[DOCUMENT]::[IMAGE][[POSITION]",$this->position,array("ABSOLUTE","RELATIVE"));

        $this->x = (strlen($node->attrs["X"])>0)?$node->attrs["X"]:0;
		$this->x = PDF_unit("[DOCUMENT]::[IMAGE][X]", $this->x);
        $this->y = (strlen($node->attrs["Y"])>0)?$node->attrs["Y"]:0;
		$this->y=  PDF_unit("[DOCUMENT]::[IMAGE][Y]", $node->attrs["Y"]);

		$this->width = PDF_unit("[DOCUMENT]::[IMAGE][WIDTH]",$node->attrs["WIDTH"]);
		$this->height= PDF_unit("[DOCUMENT]::[IMAGE][HEIGHT]",$node->attrs["HEIGHT"]);

		$this->src=$node->attrs["SRC"];
		$this->type=strtoupper($node->attrs["TYPE"]);
		PDF_in("[DOCUMENT]::[IMAGE][TYPE]", $this->type, array("JPEG", "PNG", "GIF") );
    }
}


class rectangle 
{
	var $x;
	var $y;
	var $width;
	var $height;
	var $type;
	var $color;
	var $bgcolor;
	var $linewidth;
	var $red;
	var $green;
	var $blue;
	var $bred;
	var $bgreen;
	var $bblue;
    var $position;
	
	function rectangle($node) 
    {
        // Default positioning is relative
        $this->position='RELATIVE';
        if(array_key_exists("POSITION", $node->attrs)) {
            $this->position=(strlen($node->attrs["POSITION"])>0)?$node->attrs["POSITION"]:"RELATIVE";
        }
        PDF_in("[DOCUMENT]::[IMAGE][[POSITION]",$this->position,array("ABSOLUTE","RELATIVE"));

        $this->x = (array_key_exist('X',$node->attrs))?$node->attrs["X"]:0;
		$this->x = PDF_unit("[DOCUMENT]::[RECTANGLE][X]",$this->x);
        $this->y = (array_key_exists('Y',$node->attrs))?$node->attrs["Y"]:0;
		$this->y = PDF_unit("[DOCUMENT]::[RECTANGLE][Y]",$this->y);

		$this->width=$node->attrs["WIDTH"];
		$this->width = PDF_unit("[DOCUMENT]::[RECTANGLE][WIDTH]",$this->width);
		$this->height=$node->attrs["HEIGHT"];
		$this->height = PDF_unit("[DOCUMENT]::[RECTANGLE][HEIGHT]",$this->height);

		$this->type=$node->attrs["TYPE"];
		PDF_in("[DOCUMENT]::[RECTANGLE][TYPE]", $this->type, array("0", "1", "2", "3", "4") );

		$this->linewidth=$node->attrs["LINEWIDTH"];
		$this->linewidth= PDF_unit("[DOCUMENT]::[RECTANGLE][LINEWIDTH]",$this->linewidth);

		$this->color=$node->attrs["COLOR"];
		$this->red=hexdec(substr($this->color,0,2));
		$this->red=$this->red/255;
		$this->green=hexdec(substr($this->color,2,2));
		$this->green=$this->green/255;
		$this->blue=hexdec(substr($this->color,4,2));
		$this->blue=$this->blue/255;
		$this->bgcolor=$node->attrs["BGCOLOR"];
		$this->bred=hexdec(substr($this->bgcolor,0,2));
		$this->bred=$this->bred/255;
		$this->bgreen=hexdec(substr($this->bgcolor,2,2));
		$this->bgreen=$this->bgreen/255;
		$this->bblue=hexdec(substr($this->bgcolor,4,2));
		$this->bblue=$this->bblue/255;
		$this->text=$node->cdata;
	}
}

class line 
{
	var $y;
	var $x1;
	var $y1;
	var $x2;
	var $y2;
	var $width;
	var $type;
	var $color;
	var $red;
	var $green;
	var $blue;
	var $height;
    var $position;
	
	function line($node) 
    {
        // Default positioning is relative
        $this->position='RELATIVE';
        if(array_key_exists("POSITION", $node->attrs)) {
            $this->position=(strlen($node->attrs["POSITION"])>0)?$node->attrs["POSITION"]:"RELATIVE";
        }
        PDF_in("[DOCUMENT]::[IMAGE][[POSITION]",$this->position,array("ABSOLUTE","RELATIVE"));

		$this->y=0;
        $this->x1 = (array_key_exists('X1',$node->attrs))?$node->attrs["X1"]:0;
		$this->x1 = PDF_unit("[DOCUMENT]::[LINE][X1]",$this->x1);

        $this->y1 = (array_key_exists('Y1',$node->attrs))?$node->attrs["Y1"]:0;
		$this->y1 = PDF_unit("[DOCUMENT]::[LINE][Y1]",$this->y1);

        $this->x2 = (array_key_exists('X2',$node->attrs))?$node->attrs["X2"]:0;
		$this->x2 = PDF_unit("[DOCUMENT]::[LINE][X2]",$this->x2);

        $this->y2= (array_key_exists('Y2',$node->attrs))?$node->attrs["Y2"]:0;
		$this->y2 = PDF_unit("[DOCUMENT]::[LINE][Y2]",$this->y2);

		$this->type=$node->attrs["TYPE"];
		PDF_in("[DOCUMENT]::[LINE][TYPE]", $this->type, array("0", "1", "2", "3", "4") );

		// Default linecolor is black
        $this->color="000000";
        if (array_key_exists("COLOR",$node->attrs)) {
            $this->color=(strlen($node->attrs["COLOR"])>0)?$node->attrs["COLOR"]:"000000";
        }
		$this->red=hexdec(substr($this->color,0,2));
		$this->red=$this->red/255;
		$this->green=hexdec(substr($this->color,2,2));
		$this->green=$this->green/255;
		$this->blue=hexdec(substr($this->color,4,2));
		$this->blue=$this->blue/255;
		if ($this->y2>$this->y1) {
			$this->height=$this->y2;
		} else {
			$this->height=$this->y1;
		}
		$this->width=$node->attrs["WIDTH"];
		PDF_numeric("[DOCUMENT]::[LINE][WIDTH]",$this->width);
	}
}

class breakpage 
{
	var $y;
	var $height;
	
	function breakpage($node) 
    {
		$this->y=0;
		$this->height=0;
	}
}

class renderer 
{
	var $width;
	var $height;
	var $left;
	var $right;
	var $top;
	var $bottom;
	var $pageheader;
	var $pagefooter;
	
	var $datasource;
	var $x;
	var $y;
	var $p;
	var $currentpage;
	var $totalpages;
	var $countpages;
	var $vara;
	var $doWrite;
	var $xrel;
	var $yrel;
	
	function renderer($width, $height, $left, $right, $top, $bottom, &$pageheader, &$pagefooter, $vars) 
    {
		$this->width=$width;
		$this->height=$height;
		$this->left=$left;
		$this->right=$right;
		$this->top=$top;
		$this->bottom=$bottom;
		$this->pageheader=$pageheader;
		$this->pagefooter=$pagefooter;	
		$this->vara=array();
		$this->vara=$vars;
		$this->y=$this->top;
		$this->x=$this->left;
		$this->xrel=0;
		$this->yrel=0;
	}
	function writePDF(&$doc) 
    {
		// Render the first time to calculate the Total No of Pages but do not write document
		// FIXME: can this be optimized if we don't need to know total no of pages?
		$this->totalpages=0;
		// $this->openPDF($doc,0);
        // 		$this->render($doc);
        // 		$this->closePDF();
		// Render and show the document
		$this->openPDF($doc,1);
        $this->render($doc);
        //die("just before closepdf");
		$content = $this->closePDF();
		$this->report->datasource->dbClose();
        return $content;
	}
	function openPDF(&$document,$doWrite) 
    {
		$document->report->datasource->getData($this,$this->report->datasource);
		$this->report->datasource=&$document->report->datasource;		
		$this->doWrite=$doWrite;
		$this->currentpage=0;
		if ($this->doWrite==1) {
            PDFLoadCode(PDF_LIBRARY);
            $this->p=&newPDFWrapper(PDF_LIBRARY);
            $this->p->Open("");
            $this->p->BeginPage($this->width,$this->height);
		}
		$this->currentpage++;
		$this->renderpageheader();
	}
	function closePDF() 
    {
		$this->renderpagefooter();
		$this->totalpages=$this->currentpage;
		if ($this->doWrite==1) {
			$this->p->EndPage();			
            $content= $this->p->CloseWeb();
		}
		$this->report->datasource->closeData();
        return $content;
	}
	function render(&$document,$x=0,$y=0,$width=0) 
    {
		$document->report->datasource->getData($this,$this->report->datasource);
        
		if ($x==0 && $y==0 && $width==0) {
			$this->renderblock($document->report->documentheader, 0, 0, $document->width - $document->left - $document->right, 
                               $document->report->documentheader->height,$document->report->datasource, 
                               $document->report->detail->flow, $document->report->detail->width);
            //die("block rendered");
            $this->renderdetail($document->report->detail, 0, 0, $document->width - $document->left - $document->right, 
                                $document->report->detail->height, $document->report->datasource, 
                                $document->report->detail->flow, $document->report->detail->width);
			$this->renderblock($document->report->documentfooter, 0, 0, $document->width - $document->left - $document->right, 
                               $document->report->documentfooter->height, 
                               $document->report->datasource, $document->report->detail->flow, $document->report->detail->width);
		} else {
			$this->renderblock($document->report->documentheader, $x, $y, $width, $document->report->documentheader->height,$document->report->datasource, 
                               $document->report->detail->flow, $document->report->detail->width);
			$this->renderdetail($document->report->detail, $x, $y, $width, $document->report->detail->height, $document->report->datasource, 
                                $document->report->detail->flow, $document->report->detail->width);
			$this->renderblock($document->report->documentfooter, $x, $y, $width, $document->report->documentfooter->height, $document->report->datasource, 
                               $document->report->detail->flow, $document->report->detail->width);
		}
	}
	function renderblock(&$block, $x, $y, $width, $height, &$datasource, $flow, $flowwidth) 
    {
		$this->xrel=0;
		$this->yrel=0;
		// De scos in cazul in care vreau afisare chiar daca rs-ul e gol !
		if ($datasource->rs_total>0) {
			if ($this->y - $height - $y<=$this->bottom + $this->pagefooter->height) {
				$this->newpage($datasource);
			}
			for ($i=0;$i< $block->n;$i++) {
				$this->renderElement($block->container[$i],get_class($block), $datasource, $x, $y, $width);
			}
			$this->y-=$height;
		}
	}
	function renderdetail(&$detail, $x, $y, $width, $height, &$datasource, $flow, $flowwidth) 
    {
		$this->xrel=0;
		$this->yrel=0;
		$initx=$x;
		$this->renderblock($detail->detailheader, $x, $y, $width, $detail->detailheader->height, $datasource, $flow, $flowwidth);
		$tmp=$datasource->rs_total;
		if ($detail->repeat!=0 && $detail->repeat<$tmp) {
			$tmp=$detail->repeat;
		}
		$datasource->repeat=$tmp;
		for($j=0;$j<$tmp;$j++) {
			if ($this->y - $detail->height - $y<=$this->bottom + $this->pagefooter->height) {
				$this->newpage($datasource);
				$this->renderblock($detail->detailheader, $x, $y, $width, $detail->detailheader->height, $datasource, $flow, $flowwidth);
			}
			for ($i=0;$i<$detail->n;$i++) {
                //			  echo $datasource->rs_current."-";
				$this->renderElement($detail->container[$i],get_class($detail), $datasource, $x, $y, $width);
			}              
            $datasource->rs->MoveNext();
            $datasource->rs_current++;  
			if ($flow=="VERTICAL") {
                // if-ul trebuie scos daca nu mai ie compatibil cu vechile rapoarte (pus pentru fisa magazie 6.31pm 08.08.2001	
				if ($detail->hasSub!=1) {
					$this->y-=$detail->height;
				}				
			} else {
				$x+=$flowwidth;
				if ($x>$initx+$width) {
					$j=$tmp;
				}
			}
		}
		if ($flow!="VERTICAL") {
			if ($detail->hasSub!=1) {
				$this->y-=$detail->height;
			}
		}
		$datasource->rs->MoveLast();
		$datasource->rs_current--;
	}
	function renderElement(&$element,$from="",&$datasource,$x=0,$y=0,$width=0) 
    {
        $this->report->datasource=&$datasource;
		switch (get_class($element)) {
        case "text":
            // If x or y are specified relative update the x/y property of this element
            if ($element->position="RELATIVE") {
                $element->x+=$this->xrel;
                $element->y+=$this->yrel;
            }

            if ($element->width==-1) {
                // No width specified, let renderer predict 
                $element->width = $this->p->GetTextWidth($this->solveexp($element->text,$datasource),$element->size); 
            }
            
            // Update the renderer positions
            $this->xrel=$element->x+$element->width;
            $this->yrel=$element->y+$element->height;
            if ($this->doWrite==1) {
                $this->p->SetAllColor($element->bred, $element->bgreen, $element->bblue);
                $this->p->SetLineStyle(0.5,1,0);
                $this->p->Bar($this->left + $element->x + $x, $this->y - $element->y - $y, $element->width, - $element->height);
                $this->p->SetAllColor($element->red, $element->green, $element->blue);
                $this->p->SetFont($element->font, $element->size, "false", "false", $element->underline);
                $this->p->ShowText($this->solveexp($element->text,$datasource),$this->left + $element->x + $x, $this->y - $element->y - $element->height - $y, $element->width, $element->height, $element->align);
            } else {
                // gives something like $ret="Text";
                $this->solveexp($element->text,$datasource);
            }
            break;
        case "image":
            if ($element->position="RELATIVE") {
                $element->x+=$this->xrel;
                $element->y+=$this->yrel;
            }
            $this->xrel=$element->x+$element->width;					
            $this->yrel=$element->y+$element->height;
            if ($this->doWrite==1) {
                $this->p->PlaceScaledImage($element->type, $element->src, ($this->left + $element->x + $x), ($this->y - $element->y - $element->height - $y), $element->width, $element->height );
            }
            break;
        case "line":
            if ($element->position="RELATIVE") {
                $element->x1+=$this->xrel;
                $element->x2=$element->x1+$element->x2;
                $element->y1+=$this->yrel;
                $element->y2=$element->y1+$element->y2;
            }
            $this->xrel=$element->x2;
            $this->yrel=$element->y2;

            if ($this->doWrite==1) {				
                $this->p->SetColor($element->red, $element->green, $element->blue);
                $this->p->SetLineStyle($element->width,1,0);
                $this->p->Line($this->left + $element->x1 + $x,$this->y - $element->y1 - $y,$this->left + $element->x2 +$x,$this->y - $element->y2);
            }
            break;
        case "rectangle":
            if ($element->position="RELATIVE") {
                $element->x+=$this->xrel;
                $element->y+=$this->yrel;
            }
            $this->xrel=$element->x+$element->width;					
            $this->yrel=$element->y+$element->height;
            if ($this->doWrite==1) {					
                $this->p->SetBGColor($element->bred, $element->bgreen, $element->bblue);
                $this->p->SetLineStyle($element->linewidth,1,0);
                $this->p->Bar($this->left + $element->x + $x,$this->y - $element->y - $y, $element->width, - $element->height);
                $this->p->SetColor($element->red, $element->green, $element->blue);
                $this->p->SetLineStyle($element->linewidth,1,0);
                $this->p->Rectangle($this->left + $element->x + $x,$this->y - $element->y - $y, $element->width, - $element->height);
            }
            break;
        default:
		}
	}
	function renderpageheader() 
    {
		$this->y=$this->height-$this->top;
		for ($i=0;$i<$this->pageheader->n;$i++) {
			$this->renderElement($this->pageheader->container[$i],"pageheader",$this->report->datasource);
		}
		$this->y=$this->height - $this->top - $this->pageheader->height;
	}
	function renderpagefooter() 
    {
		$this->y=$this->bottom + $this->pagefooter->height;
		for ($i=0;$i<$this->pagefooter->n;$i++) {
			$this->renderElement($this->pagefooter->container[$i],"pagefooter",$this->report->datasource);
		}		
		$this->y=$this->bottom;
	}
	function newpage(&$datasource) 
    {
		$this->renderpagefooter();
		if ($this->doWrite==1) {
			$this->p->EndPage();
		}
		$datasource->rs_lastpage=$datasource->rs_current;
		if ($this->doWrite==1) {
			$this->p->BeginPage($this->width,$this->height);
		}
		$this->currentpage++;
		$this->renderpageheader();
	}

	function solveexp($exp, &$datasource) 
    {
		//		$exp=$this->putthis($exp);
		//echo "In solveexp exp=".$exp."<br/>";
		if ($exp!="") {
			//echo $exp."<br/>";
			// Check for function
			$brace= strpos($exp,"(");
			if (!is_integer($brace)) {
				// No match in there, definitely not function, do normal stuff
				$str="\$ret=".$exp.";";
			} else {
				// Function might be there
				if (method_exists($this,substr($exp,0,$brace))) {
					// Method exists, eval it
					$str="\$ret=\$this->".$exp.";";
					//echo $str."<br/>";
				}
			}
			// FIXME: This allows only ONE expression in tag because we require it to be methods or php globals!!!
            //echo $str;
            @eval($str);
		}
		if (isset($ret)) {
			//echo "$exp=".$ret."<br/>";
			return $ret;
		} else {
			return "";
		}
	}
	function vars($exp) 
    {
		return $this->vara[$exp];
	}
	function chvars($exp,$exp2) 
    {
		$datasource=&$this->report->datasource;
		$this->vara[$exp]=$this->solveexp($exp2,$datasource);
		return $this->vara[$exp];
	}
	function fields($exp) 
    {
		$datasource=&$this->report->datasource;
        // FIXME: don't allow expressions in fields jus yet
		//return $datasource->rs->Fields($this->solveexp($exp,$datasource));
		return $datasource->rs->Fields($exp);
	}
	function sum($exp) 
    {
		$datasource=&$this->report->datasource;
		$ret=0;
		$datasource->rs->MoveFirst();
		for ($i=0;$i<$datasource->repeat;$i++) {
				$ret+=$this->solveexp($exp,$datasource);
				$datasource->rs->MoveNext();
		}
		$datasource->rs->MoveLast();
		return $ret;
	}
	function avg($exp) 
    {
		$datasource=&$this->report->datasource;
		$ret=0;
		$datasource->rs->MoveFirst();
		$cnt = $datasource->repeat;
		for ($i=0;$i<$datasource->repeat;$i++) {
				$ret+=$this->solveexp($exp,$datasource);
				$datasource->rs->MoveNext();
		}
		$datasource->rs->MoveLast();
		return $ret/$cnt;
	}
	function rcount($exp) 
    {
		$datasource=&$this->report->datasource;
		return $datasource->rs->RecordCount();
	}
	function page() 
    {
		return $this->currentpage;	
	}                           
	function pages() 
    {
		return $this->totalpages;
	}
	function putthis($exp) 
    {
		$ret=$exp;
		return $ret;
	}
	function date($format) 
    {
		return date($format);
	}
}

/*
 * DEPRECATED?
 */
// ***
// Renderer classes & functions
// ***
// function vars($exp) 
//{
//     global $KT_renderer;
//     return $KT_renderer->vars($exp);
// }
// function chvars($exp,$exp2) 
//{
//     global $KT_renderer;
//     return $KT_renderer->chvars($exp, $exp2);
// }
// function fields($exp) 
//{
//     global $KT_renderer;
//    	return $KT_renderer->fields($exp);
// }

// function sum($exp) 
//{
//     global $KT_renderer;
//     return $KT_renderer->sum($exp);
// }

// function avg($exp) 
//{
//     global $KT_renderer;
//     return $KT_renderer->avg($exp);
// }

// function rcount($exp) 
//{
//     global $KT_renderer;
//     return $KT_renderer->rcount($exp);
// }

// function page() 
//{
//     global $KT_renderer;
//    	return $KT_renderer->page();
// }

// function pages() 
//{
//     global $KT_renderer;
//    	return $KT_renderer->pages();
// }
?>
