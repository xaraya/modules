<?php
/**
 *  Report administrative functions
 *
 */
function reports_adminapi_create_report($args) 
{
	//Get arguments
	extract($args);

	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$tab = $xartables['reports'];
	$cols = &$xartables['reports_column'];

	$conn_id = $dbconn->GenId();

	$sql = "INSERT INTO $tab ($cols[id],$cols[conn_id],$cols[name],$cols[description],$cols[xmlfile]) VALUES ('"
		.xarVarPrepForStore($rep_id)."','"
		.xarVarPrepForStore($rep_conn_id)."','"
		.xarVarPrepForStore($rep_name)."','"
		.xarVarPrepForStore($rep_desc)."','"
		.xarVarPrepForStore($rep_xmlfile)."')";

	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

?>