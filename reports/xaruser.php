<?php

/**
 * File: $Id$
 *
 * User function for reports module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage reports
 * @author Marcel van der Boom <marcel@hsdev.com>
*/



function reports_user_main() {
    return reports_user_view();
}

//
// Produce a list of available reports
//
function reports_user_view($args=array()) {
	// FIXME: move this to reports_user_getall_reports();
	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['reports'];
	$col = &$xartables['reports_column'];
	$sql = "SELECT $col[id], $col[name], $col[xmlfile], $col[description], $col[conn_id] FROM $tab";
	$res =& $dbconn->Execute($sql);
    if (!$res) return false;

    // Produce table with report info
    $reportlist=array();
    $counter=1;
    while (!$res->EOF) {
        $row=$res->fields;
        $reportlist[$counter]['id']=$row[0];
        $reportlist[$counter]['name']=xarVarPrepForDisplay($row[1]);
        $reportlist[$counter]['xmlfile']=$row[2];
        $reportlist[$counter]['desc']=xarVarPrepForDisplay($row[3]);
        $reportlist[$counter]['conn_id']=$row[4];
        $counter++;
        $res->MoveNext();
    }
    
  
	// End the output
 	$data['reportlist']=$reportlist;
    return $data;
}


function DumpArray(&$array,$indent)
{
    for(Reset($array),$node=0;$node<count($array);Next($array),$node++)
        {
            echo $indent."\"".Key($array)."\"=";
            $value=$array[Key($array)];
            if(GetType($value)=="array")
                {
                    echo "<br>".$indent."[<br>";
                    DumpArray(&$value,$indent.">>");
                    echo $indent."]<br>";
                }
            else
                echo "\"$value\"<br>";
        }
}

function reports_user_generate($args) {
    
    $rep_id = xarVarCleanFromInput('rep_id');
	$xmlfile = xarVarCleanFromInput('xmlfile');
	$conn_id = xarVarCleanFromInput('conn_id');
	extract($args);
    
	// Tests:
	// - does xmlfile exist
	// - 
	list($dbconn) = xarDbGetConn();
	$xartables = xarDBGetTables();
	$ctab = $xartables['report_connections'];
	$ccols = &$xartables['report_connections_column'];
    
	$vars=array();
	// Get the settings for the connection for this report
	$reploc = xarModGetVar('reports','reports_location');
    $imgloc = xarModGetVar('reports','images_location');
	$pdf_backend = xarModGetVar('reports','pdf_backend');
	define('PDF_LIBRARY',$pdf_backend);
	//define('PDF_LIBRARY','debug');
    /**
     * As we have patched the original, we keep the original a bit around
     * to compare some things
     */
	//require("modules/reports/includes/PDFReportsLite.inc.php");
	require("modules/reports/xarincludes/reportrenderer.php");
	//require("modules/reports/includes/xml_parser.php");
	//XMLParseFile(&$parser,$file,$store_positions,$cache="",$case_folding=0,$target_encoding="ISO-8859-1",$simplified_xml=0,$fail_on_non_simplified_xml=0)
	//$testfile = $reploc."/".$xmlfile;
	//$error = XMLParseFile(&$parser,$testfile,1,$testfile.".cache");
    //dumparray($parser->structure,">>");
	//die();
	$sql = "SELECT $ccols[server],$ccols[type],$ccols[database],$ccols[user],$ccols[password] "
		." FROM $ctab "
		."WHERE $ccols[id] = '".xarVarPrepForStore($conn_id)."'";
    echo $sql;
    
	$res =& $dbconn->Execute($sql);
	if(!$res) return;
    
    $row = $res->fields;
    $repserver = $row[0];	$repdbtype = $row[1];
    $repdbname = $row[2];	$repdbuser = $row[3];
    $reppasswd = $row[4];   $replocale ="En";
    
    ADOLoadCode($repdbtype);
    
    $repconn =&ADONewConnection($repdbtype);
    if($repdbtype == "access" || $repdbtype == "odbc" || $repdbtype == "odbc_mssql"){
        $repconn->PConnect($repdbname, $repdbuser,$reppasswd,$replocale);
    } else if($repdbtype == "ibase") {
        $repconn->PConnect($repserver.":".$repdbname,$repdbuser,$reppasswd);
    }else {
        $repconn->PConnect($repserver,$repdbuser,$reppasswd,$repdbname,$replocale);
    }
    
    $doc=new document(getRootNode($reploc."/".$xmlfile),$repconn,"\"\"");
    
    $rend= new renderer($doc->width, $doc->height,$doc->left, $doc->right, $doc->top, $doc->bottom, $doc->report->pageheader, $doc->report->pagefooter, $vars);
    
    $content = $rend->writePDF($doc);
    die("after content");
    echo $content;
	return true;
}

?>