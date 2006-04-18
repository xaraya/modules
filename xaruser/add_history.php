<?php
/**
 * Add history against a ticket item.
 *
 * @param array $args
 */
function helpdesk_user_add_history($args)
{
    extract($args);

    xarModAPILoad('helpdesk');

    if( !xarVarFetch('itemid',  'id',      $itemid) ){ return false; }
    if( !xarVarFetch('status',  'int',     $statusid,  null) ){ return false; }
    if( !xarVarFetch('comment', 'html:basic', $comment,  null) ){ return false; }

    $ticket = xarModAPIFunc('helpdesk', 'user', 'getticket',
        array(
            'tid' => $itemid
        )
    );
    if( empty($ticket) ){ return false; }

    $result = xarModAPIFunc('comments', 'user', 'add',
        array(
            'modid'    => xarModGetIdFromName('helpdesk'),
            'objectid' => $itemid,
            'itemtype' => TICKET_ITEMTYPE,
            'title'    => $ticket['subject'],
            'comment'  => $comment,
            'author'   => xarUserGetVar('uid')
        )
    );
    if( !$result ){ return false; }

    // Send Mail
    $result = xarModFunc('helpdesk','user','sendmail',
        array(
            'userid'      => xarUserGetVar('uid'),
            'subject'     => $ticket['subject'],
            'status'      => $statusid,
            'openedby'    => $ticket['openedby'],
            'assignedto'  => $ticket['assignedto'],
            'closedby'    => $ticket['closedby'],
            'comment'     => $comment,
            'tid'         => $itemid,
            'mailaction'  => 'additionalcomment'
        )
    );
    if( !$result ){ return false; }

    /*
        Compare Current Status with New status to determine if it needs to be
        updated and wether or not to send mail out.
    */
    if( $ticket['statusid'] != $statusid )
    {
        $result = xarModAPIFunc('helpdesk', 'user', 'update_status',
            array(
                'itemid' => $itemid,
                'status' => $statusid
            )
        );
        if( !$result ){ return false; }
        $result = xarModFunc('helpdesk','user','sendmail',
            array(
                'userid'      => xarUserGetVar('uid'),
                'subject'     => $ticket['subject'],
                'status'      => $statusid,
                'openedby'    => $ticket['openedby'],
                'assignedto'  => $ticket['assignedto'],
                'closedby'    => $ticket['closedby'],
                'comment'     => $comment,
                'tid'         => $itemid,
                'mailaction'  => 'closed'
            )
        );
        if( !$result ){ return false; }
    }

    // Return to where we can from
    xarResponseRedirect(xarServerGetVar('HTTP_REFERER'));
    return false;
}
?>