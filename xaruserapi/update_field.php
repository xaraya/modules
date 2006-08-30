<?php

function helpdesk_userapi_update_field($args)
{
    extract($args);

    if( empty($itemid) ){ return false; }
    if( empty($field) ){ return false; }
    if( empty($value) ){ return false; }

    // Generate SQL code for Ticket entry
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];

    $sql = "
        UPDATE $db_table
        SET
            xar_$field  = ?
            ,xar_updated  = ?
        WHERE xar_id = ?
    ";
    $bindvars = array($value, date("Y-m-d H:i:s"), $itemid);

    $result = $dbconn->Execute($sql, $bindvars);
    if( !$result ){ return false; }

    return true;
}
?>