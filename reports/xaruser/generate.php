<?php
/**
 * Generate a report
 *
 */
function reports_user_generate($args) 
{
    
    $rep_id = xarVarCleanFromInput('rep_id');
	$xmlfile = xarVarCleanFromInput('xmlfile');
	$conn_id = xarVarCleanFromInput('conn_id');
	extract($args);
    
	// Tests:
	// - does xmlfile exist
	// - 
	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
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

    if ($conn_id == 1) {
        $repconn =& $dbconn;
    } else {
	$sql = "SELECT $ccols[server],$ccols[type],$ccols[database],$ccols[user],$ccols[password] "
		." FROM $ctab "
		."WHERE $ccols[id] = '".xarVarPrepForStore($conn_id)."'";
    //echo $sql;
    
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
    }

    $doc=new document(getRootNode($reploc."/".$xmlfile),$repconn,"\"\"");
    
    $rend= new renderer($doc->width, $doc->height,$doc->left, $doc->right, $doc->top, $doc->bottom, $doc->report->pageheader, $doc->report->pagefooter, $vars);
    
    $content = $rend->writePDF($doc);
    //die("after content");

    $content = ltrim($content);
    // copied from stream() function in class.pdf.php
    // Poor man's debugger: comment first line, enable second
    header("Content-type: application/pdf");
    // header("Content-type: text/plain");
    header("Content-Length: ".strlen($content));
    $fileName = (isset($options['Content-Disposition'])?$options['Content-Disposition']:'file.pdf');
    header("Content-Disposition: inline; filename=".$fileName);
    if (isset($options['Accept-Ranges']) && $options['Accept-Ranges']==1){
        header("Accept-Ranges: ".strlen($content)); 
    }
    echo $content;
    exit;
//	return true;
}

?>