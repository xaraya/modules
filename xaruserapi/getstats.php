<?php
//=========================================================================
// Gets number of tickets in DB
//=========================================================================
function helpdesk_userapi_getstats()
{
    // Database information
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];

    $sql = "SELECT count('xar_id')
            FROM $helpdesktable";
    $results = $dbconn->Execute($sql);
    if (!$results) return;

    list($ticketcount) = $results->fields;
    
    $results->Close();

    return $ticketcount;
}
?>
