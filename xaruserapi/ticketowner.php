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
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table    = $xartable['helpdesk_tickets'];
    
    $sql = "SELECT xar_id, xar_openedby
            FROM $table
            WHERE xar_id = ? AND 
                  xar_openedby = ?";
    $bindvars =  array($ticket_id, xarUserGetVar('uid'));
    $result = $dbconn->Execute($sql, $bindvars);
    
    return $result->Rowcount();
}
?>
