<?php
/**
 * Get a report connection
 *
 * Retrieves the connection info for a certain report
 *
 * @author  Marcel van der Boom <marcel@hsdev.com>
 * @access  public 
 * @param   conn_id integer identification of connection
 * @return  boolean 
*/
function reports_userapi_connection_get($args) {
	list($conn_id) = xarVarCleanFromInput('conn_id');
	extract($args);
    
	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];
    
	$sql = "SELECT $cols[id],$cols[name],$cols[description],$cols[server],$cols[type],$cols[database],$cols[user],$cols[password] "
		."FROM $tab WHERE $cols[id]='".xarVarPrepForStore($conn_id)."'";
	$res= $dbconn->Execute($sql);
	if ($res) {
		$row = $res->fields;
        return  array (
                       'id'=>$row[0],
                       'name'=>$row[1],
                       'description'=>$row[2],
                       'server'=>$row[3],
                       'type'=>$row[4],
                       'database'=>$row[5],
                       'user'=>$row[6],
                       'password'=>$row[7]);
	} else {
		return false;
	}
}
?>