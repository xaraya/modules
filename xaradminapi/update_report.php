<?php
/**
 * Update report
 *
 */
function reports_adminapi_update_report($args) 
{
    //Get arguments
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();
    $tab = $xartables['reports'];
    $cols = &$xartables['reports_column'];

    $sql = "UPDATE $tab SET 
        $cols[name]=?, $cols[description]=?,
        $cols[conn_id]=?, $cols[xmlfile]=?
        WHERE $cols[id]=?";

    $bindvars = array($rep_name, $rep_desc, $rep_conn, $rep_xmlfile, $rep_id);
    if($dbconn->Execute($sql,$bindvars)) {
        return true;
    } else {
        return false;
    }
    return true;
}?>