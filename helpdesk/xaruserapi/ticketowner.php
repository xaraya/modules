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

    // Run API function to query database
    $owner = xarModAPIFunc('helpdesk','user','isticketowner', $ticket_id);
    return $owner;
}
?>
