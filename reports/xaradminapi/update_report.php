<?php
/**
 * Update report
 *
 */
function reports_adminapi_update_report($args) 
{
	//Get arguments
	extract($args);

	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];

	$sql = "UPDATE $tab SET "
		."$cols[name]='".xarVarPrepForStore($rep_name)."',"
    ."$cols[description]='".xarVarPrepForStore($rep_desc)."',"
    ."$cols[conn_id]='".xarVarPrepForStore($rep_conn)."',"
    ."$cols[xmlfile]='".xarVarPrepForStore($rep_xmlfile)."' "
		."WHERE $cols[id]='".xarVarPrepForStore($rep_id)."'";


	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}?>