<?php
/**
 * Get a report
 *
 */
function reports_userapi_report_get($args) {
	list($rep_id) = xarVarCleanFromInput('rep_id');
	extract($args);
    
	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];
    
	$sql = "SELECT $cols[id],$cols[name],$cols[description],$cols[conn_id],$cols[xmlfile] "
		."FROM $tab WHERE $cols[id]='".xarVarPrepForStore($rep_id)."'";
	$res= $dbconn->Execute($sql);
	if ($res) {
		$row = $res->fields;
        return  array (
                       'id'=>$row[0],
                       'name'=>$row[1],
                       'description'=>$row[2],
                       'conn_id'=>$row[3],
                       'xmlfile'=>$row[4]);
        
		
	} else {
		return false;
	}
}

?>