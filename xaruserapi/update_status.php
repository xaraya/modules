<?php

/**
 * Updates just the status of a ticket
 *
 * @param array $args
 */
function helpdesk_userapi_update_status($args)
{
    extract($args);

    if( empty($itemid) ){ return false; }
    if( empty($status) ){ return false; }

    // Generate SQL code for Ticket entry
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];

    if( $status == '3' ){ $closer = xarUserGet('uid'); }
    else{ $closer = null; }

    $sql = "
        UPDATE $db_table
        SET
            xar_statusid  = ?
            ,xar_closedby = ?
            ,xar_updated  = ?
        WHERE xar_id = ?
    ";
    $bindvars = array($status, $closer, date("Y-m-d H:i:s"), $itemid);


    $result = $dbconn->Execute($sql, $bindvars);
    if( !$result ){ return false; }

    return true;
}
?>