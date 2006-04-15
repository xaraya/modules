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
    if( !xarVarFetch('comment', 'str:1:',  $comment,  null) ){ return false; }

    $ticket = xarModAPIFunc('helpdesk', 'user', 'getticket',
        array(
            'tid' => $itemid
        )
    );
    if( empty($ticket) ){ return false; }

    if( $ticket['statusid'] != $statusid )
    {
        $result = xarModAPIFunc('helpdesk', 'user', 'update_status',
            array(
                'itemid' => $itemid,
                'status' => $statusid
            )
        );
    }

    $result = xarModAPIFunc('comments', 'user', 'add',
        array(
            'modid'    => xarModGetIdFromName('helpdesk'),
            'objectid' => $itemid,
            'itemtype' => TICKET_ITEMTYPE,
            //'pid'      => 0, // parent id
            'title'    => $ticket['subject'],
            'comment'  => $comment,
            //'postanon' => 0,
            'author'   => xarUserGetVar('uid')
        )
    );

    xarResponseRedirect(xarServerGetVar('HTTP_REFERER'));
    return false;
}
?>