<?php
/**
 * Get all connections
 *
 */
function reports_userapi_connection_getall() {
	$dbconn =& xarDBGetConn();
	$xartables =& xarDBGetTables();
	$tab = $xartables['report_connections'];
	$cols = &$xartables['report_connections_column'];
    
	$sql = "SELECT $cols[id],$cols[name],$cols[description],$cols[server],$cols[type],$cols[database],$cols[user],$cols[password] "
		."FROM $tab";
	$res= $dbconn->Execute($sql);
	if ($res) {
		$ret = array();
		while (!($res->EOF)) {
			$row = $res->fields;
			$ret[] =  array (
                             'id'=>$row[0],
                             'name'=>$row[1],
                             'description'=>$row[2],
                             'server'=>$row[3],
                             'type'=>$row[4],
                             'database'=>$row[5],
                             'user'=>$row[6],
                             'password'=>$row[7]);
			$res->MoveNext();
		}
		return $ret;
	} else {
		return false;
	}
}

?>