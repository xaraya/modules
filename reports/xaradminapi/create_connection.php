<?php
/**
 * Connection admin
 *
 */
function reports_adminapi_create_connection($args) 
{
	//Get arguments
	extract($args);

	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];

	$conn_id = $dbconn->GenId();

	$sql = "INSERT INTO $tab ($cols[id],$cols[name],$cols[description],$cols[server],$cols[type],$cols[database],$cols[user],$cols[password]) "
		."VALUES ('"
		.xarVarPrepForStore($conn_id)."','"
		.xarVarPrepForStore($conn_name)."','"
		.xarVarPrepForStore($conn_desc)."','"
		.xarVarPrepForStore($conn_server)."','"
		.xarVarPrepForStore($conn_type)."','"
		.xarVarPrepForStore($conn_database)."','"
		.xarVarPrepForStore($conn_user)."','"
		.xarVarPrepForStore($conn_password)."')";


	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}

?>