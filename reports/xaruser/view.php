<?php
/**
 * Produce a list of available reports
 */
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
?>