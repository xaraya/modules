<?php
//=========================================================================
// Deletes the ticket and the history which goes with that ticket
//=========================================================================
function helpdesk_userapi_deleteticket($args)
{
    extract($args);
    $ticket_id = xarVarCleanFromInput('ticket_id');
    // Database information
    $dbconn =& xarDBGetConn();
    $xartable       =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];
    $helpdeskcolumn = &$xartable['helpdesk_tickets_column'];

    $sql = "DELETE FROM  $helpdesktable 
                   WHERE $helpdeskcolumn[ticket_id] = ".$ticket_id;

    $result = $dbconn->Execute($sql);
    if (!$result) return;
        /*$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'ticket ID', 'userapi', 'deleteticket', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));*/

    $result->Close();

    $helpdesktable  = $xartable['helpdesk_histories'];
    $helpdeskcolumn = &$xartable['helpdesk_histories_column'];

    $sql = "DELETE FROM  $helpdesktable 
                   WHERE $helpdeskcolumn[ticket_id] = ".$ticket_id;
    $result = $dbconn->Execute($sql);
    if (!$result) return;
    $result->Close();
    
    xarResponseRedirect(xarModURL('helpdesk', 'user', 'main'));
}
?>
