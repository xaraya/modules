<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
 * Add history against a ticket item.
 *
 * @param array $args
 */
function helpdesk_user_add_history($args)
{
    if( !xarSecConfirmAuthKey() ){ return false; }
    if( !xarModAPILoad('helpdesk', 'user') ){ return false; }
    if( !xarModAPILoad('security', 'user') ){ return false; }

    if( !xarVarFetch('itemid',  'id',      $itemid) ){ return false; }
    if( !xarVarFetch('status',  'int',     $statusid,  null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('assigned_to', 'int', $assigned_to,  null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('comment', 'html:basic', $comment,  null) ){ return false; }
    extract($args);

    if( empty($comment) )
    {
        $msg = xarML("Missing required comment.");
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', $msg);
        return false;
    }

    $has_security = Security::check(SECURITY_COMMENT, 'helpdesk', TICKET_ITEMTYPE, $itemid);
    if( !$has_security ){ return false; }

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

    if( Security::check(SECURITY_MANAGE, 'helpdesk', TICKET_ITEMTYPE, $itemid, false) )
    {
        if( !is_null($assigned_to) and $ticket['assignedto'] != $assigned_to )
        {
            $result = xarModAPIFunc('helpdesk', 'user', 'update_field',
                array(
                    'itemid' => $itemid
                    , 'field'  => 'assignedto'
                    , 'value'  => $assigned_to
                )
            );
            if( !$result ){ return false; }
            $ticket['assignedto'] = $assigned_to;
        }
    }

    /*
        Compare Current Status with New status to determine if it needs to be
        updated and wether or not to send mail out.
    */
    $mailsent = false;
    if( Security::check(SECURITY_WRITE, 'helpdesk', TICKET_ITEMTYPE, $itemid, false) )
    {
        if(  !is_null($statusid) and $ticket['statusid'] != $statusid )
        {
            $result = xarModAPIFunc('helpdesk', 'user', 'update_status',
                array(
                    'itemid' => $itemid,
                    'status' => $statusid
                )
            );
            if( !$result ){ return false; }

            $resolved_statuses = xarModAPIFunc('helpdesk', 'user', 'get_resolved_statuses');
            if( in_array($statusid, $resolved_statuses) == true )
            {
                $result = xarModFunc('helpdesk','user','sendmail',
                    array(
                        'userid'      => xarUserGetVar('uid'),
                        'subject'     => $ticket['subject'],
                        'status'      => $statusid,
                        'openedby'    => $ticket['openedby'],
                        'email'       => $ticket['email'], // done for anon submitted tickets
                        'assignedto'  => $ticket['assignedto'],
                        'closedby'    => $ticket['closedby'],
                        'comment'     => $comment,
                        'tid'         => $itemid,
                        'mailaction'  => 'closed'
                    )
                );
                if( !$result ){ return false; }
                $mailsent = true;
            }
        }
    }

    if( $mailsent === false )
    {
        /**
         * Only send with messsage iff the closed message was not sent.
         */
        $result = xarModFunc('helpdesk','user','sendmail',
            array(
                'userid'      => xarUserGetVar('uid'),
                'subject'     => $ticket['subject'],
                'status'      => $statusid,
                'openedby'    => $ticket['openedby'],
                'email'       => $ticket['email'], // done for anon submitted tickets
                'assignedto'  => $ticket['assignedto'],
                'closedby'    => $ticket['closedby'],
                'comment'     => $comment,
                'tid'         => $itemid,
                'mailaction'  => 'additionalcomment'
            )
        );
        if( !$result ){ return false; }

    }

    // Return to where we can from
    xarResponseRedirect(xarServerGetVar('HTTP_REFERER'));
    return false;
}
?>