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

	$sql = "INSERT INTO $tab ($cols[id],$cols[conn_id],$cols[name],$cols[description],$cols[xmlfile]) 
            VALUES (?,?,?,?,?)";
    $bindvars = array($rep_id, $rep_conn_id, $rep_name, $rep_desc, $rep_xmlfile);
    
	if($dbconn->Execute($sql,$bindvars)) {
		return true;
	} else {
		return false;
	}
	return true;
}

?>