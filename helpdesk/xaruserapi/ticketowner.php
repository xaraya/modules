<?php
// Ticket Owner Function
// Arugments: userid and ticket_id
// output: true or false
//
function helpdesk_userapi_ticketowner($args)
{
    extract($args);
    if (!isset($ticket_id)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'ticket id', 'userapi', 'isticketowner', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table    = $xartable['helpdesk_tickets'];
    $column   = $xartable['helpdesk_tickets_column'];
    
    $sql = "SELECT $column[ticket_id], $column[ticket_openedby]
	    FROM $table
	    WHERE $column[ticket_id] = $ticket_id AND $column[ticket_openedby] = " . xarUserGetVar('uid');
    $result = $dbconn->Execute($sql);
    
    return $result->Rowcount();
}
?>
