<?php
/**
 * Update connection
 *
 */
function reports_adminapi_update_connection($args) 
{
	//Get arguments
	extract($args);

	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];

	$sql = "UPDATE $tab SET 
		$cols[name]= ?, $cols[description]=?,
        $cols[server]=?, $cols[type]=?,
        $cols[database]=?, $cols[user]=?,
        $cols[password]=?
		WHERE $cols[id]=?";
    
    $bindvars = array($conn_name, $conn_desc, $conn_server, $conn_type, $conn_database, $conn_user, $conn_password, $conn_id);
	if($dbconn->Execute($sql,$bindvars)) {
		return true;
	} else {
		return false;
	}
	return true;
}
?>