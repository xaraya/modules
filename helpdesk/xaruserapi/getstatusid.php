<?php
// WHERE is THIS FUNC USED?

//=========================================================================
// Get status id
//=========================================================================
function helpdesk_userapi_getstatusid($args)
{
    if(is_array($args)){
        extract($args);
    }else{
        $ticket_id = $args;
    }
    list($dbconn) = xarDBGetConn();
    $xartable     = xarDBGetTables();
    $db_table     = $xartable['helpdesk_tickets'];
    $db_column    = &$xartable['helpdesk_tickets_column'];
    $sql = "SELECT $db_column[ticket_statusid]
            FROM   $db_table
            WHERE  $db_column[ticket_id] = $ticket_id";
    $results = $dbconn->Execute($sql);
    if ($results === false) {
        return false;
    }
    return $results->fields[0];
}
?>
