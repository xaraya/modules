<?php
  /**
 * Delete connection
 *
 */
function reports_adminapi_delete_connection($args) {
	//Get arguments
	extract($args);
    
	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];

	$sql = "DELETE FROM $tab WHERE $cols[id] = '".xarVarPrepForStore($conn_id)."'";
	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

?>