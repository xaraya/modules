<?php
/**
 * Update connection
 *
 */
function reports_adminapi_update_connection($args) {
	//Get arguments
	extract($args);

	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];

	$sql = "UPDATE $tab SET "
		."$cols[name]='".xarVarPrepForStore($conn_name)."',"
        ."$cols[description]='".xarVarPrepForStore($conn_desc)."',"
        ."$cols[server]='".xarVarPrepForStore($conn_server)."',"
        ."$cols[type]='".xarVarPrepForStore($conn_type)."',"
        ."$cols[database]='".xarVarPrepForStore($conn_database)."',"
        ."$cols[user]='".xarVarPrepForStore($conn_user)."',"
        ."$cols[password]='".xarVarPrepForStore($conn_password)."' "
		."WHERE $cols[id]='".xarVarPrepForStore($conn_id)."'";
    
    
	if($dbconn->Execute($sql)) {
		return true;
	} else {
		return false;
	}
	return true;
}
?>