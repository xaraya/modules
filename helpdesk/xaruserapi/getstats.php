<?php
//=========================================================================
// Gets number of tickets in DB
//=========================================================================
function helpdesk_userapi_getstats()
{
    // Database information
    list($dbconn) = xarDBGetConn();
    $xartable     = xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];
    $helpdeskcolumn = &$xartable['helpdesk_tickets_column'];

    $sql = "SELECT count($helpdeskcolumn[ticket_id])
            FROM $helpdesktable";
    $results = $dbconn->Execute($sql);
    if (!$results) return;

    list($ticketcount) = $results->fields;
    
    $results->Close();

    return $ticketcount;
}
?>
