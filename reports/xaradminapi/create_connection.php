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

    $sql = "INSERT INTO $tab ($cols[id],$cols[name],$cols[description],$cols[server],$cols[type],$cols[database],$cols[user],$cols[password]) 
            VALUES (?,?,?,?,?,?,?,?)";
    $bindvars = array($conn_id, $conn_name, $conn_desc, $conn_server, $conn_type, $conn_database, $conn_user, $conn_password);

    if($dbconn->Execute($sql,$bindvars)) {
        return true;
    } else {
        return false;
    }
    return true;
}

?>