<?php
  /**
 * Delete report
 *
 */
function reports_adminapi_delete_report($args) {
	//Get arguments
	extract($args);

	list($dbconn) = xarDbGetConn();
	$xartables = xarDbGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];

	$sql = "DELETE FROM $tab WHERE $cols[id] = '".xarVarPrepForStore($rep_id)."'";
	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

?>