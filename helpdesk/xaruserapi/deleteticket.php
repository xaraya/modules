<?php
//=========================================================================
// Deletes the ticket 
//=========================================================================
function helpdesk_userapi_deleteticket($args)
{
    extract($args);
    $ticket_id = xarVarCleanFromInput('ticket_id');
    // Database information
    $dbconn =& xarDBGetConn();
    $xartable       =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];

    $sql = "DELETE FROM  $helpdesktable 
                   WHERE xar_id = ?";

    $result = $dbconn->Execute($sql, array($ticket_id));
    if (!$result) return;
    $result->Close();

    xarResponseRedirect(xarModURL('helpdesk', 'user', 'main'));
}
?>
