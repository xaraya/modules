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
    Send an e-mail to submitter/assignedto of the ticket

    @author jojodee/Michel V.

    @param $mailaction: what e-mail to send
*/
function helpdesk_user_sendmail($args)
{
    // Get parameters
    extract ($args);

    $ticket_args = array(
        'userid'      => $userid,
        'name'        => $name,
        'phone'       => $phone,
        'email'       => $email,
        'subject'     => $subject,
        'domain'      => $domain,
        'source'      => $source,
        'priority'    => $priority,
        'status'      => $status,
        'openedby'    => $openedby,
        'assignedto'  => $assignedto,
        'closedby'    => $closedby,
        // Issue and notes vars are only needed for new tickets
        'issue'       => isset($issue) ? $issue : null,
        'notes'       => isset($notes) ? $notes : null,
        'viewticket'  => xarModUrl('helpdesk', 'user', 'display', array('tid' => $tid)),
        'tid'         => $tid
    );

    /*
        Generate the HTML and Text versions of the message and
        let the mail module decide which to send
    */
    switch($mailaction)
    {
        case 'usernew':

            if( !empty($email) ){ $recipients = $email; }
            else{ $recipients = xarUserGetVar('email'); }

            // Does not generate errors
            if( isset($recipients) )
            {
                $ticket_args['mailsubject'] =
                    xarModGetVar('themes', 'SiteName') . " [#$tid] $subject ";
                    //xarVarPrepForDisplay(xarML('New ticket submitted'));
                $ticket_args['viewtickets'] =
                    xarModUrl('helpdesk', 'user', 'view',
                        array('selection' => 'MYALL')
                    );

                // startnew message
                $textmessage = xarTplModule('helpdesk', 'user', 'sendmailnewuser', $ticket_args, 'text');
                $htmlmessage = xarTplModule('helpdesk', 'user', 'sendmailnewuser', $ticket_args, 'html');
            }

            break;

        case 'closed':

            if( !empty($email) ){ $recipients = $email;  }
            else { $recipients = xarUserGetVar('email'); }

            // Does not generate errors
            if( isset($recipients) )
            {
                $ticket_args['mailsubject'] =
                    "Closed: " . xarModGetVar('themes', 'SiteName') .
                    " [#$tid] $subject ";

                $ticket_args['viewtickets'] =
                    xarModUrl('helpdesk', 'user', 'view',
                        array('selection' => 'MYALL')
                    );

                // startnew message
                $textmessage = xarTplModule('helpdesk', 'user', 'sendmailcloseduser', $ticket_args, 'text');

                $htmlmessage = xarTplModule('helpdesk', 'user', 'sendmailcloseduser', $ticket_args, 'html');
            }

            break;

        case 'assignednew':

            if( !empty($assignedto) )
            {
                $recipients = xarUserGetVar('email', $assignedto);
                $ticket_args['assignedtoname'] =
                    xarUserGetVar('name', $assignedto);
            }
            else
            {
                $recipients = xarModGetVar('mail', 'adminmail');
                $ticket_args['assignedtoname'] =
                    xarModGetVar('mail', 'adminname');
            }

            // Does not generate errors
            if(isset($recipients))
            {
                $ticket_args['mailsubject'] =
                    xarModGetVar('themes', 'SiteName') . " [#$tid] $subject ";
                //    xarVarPrepForDisplay(xarML('New ticket assigned to you'));

                $ticket_args['viewtickets'] =
                    xarModUrl('helpdesk', 'user', 'view',
                        array('selection' => 'MYASSIGNEDALL')
                    );

                // startnew message
                $textmessage = xarTplModule('helpdesk', 'user', 'sendmailnewassigned', $ticket_args, 'text');

                $htmlmessage = xarTplModule('helpdesk', 'user', 'sendmailnewassigned', $ticket_args, 'html');

             }

             break;
    } //End switch

    /*
        Text and HTML Messages have been generated,
        Now send the message to the recipients
    */
    if( isset($recipients) )
    {
        // Get the webmaster name TODO: make admin var
        $fromname = xarModGetVar('mail', 'adminname');
        // Get the webmaster email
        $femail = xarModGetVar('mail', 'adminmail');

        //let's send the email now
        $usermail = xarModAPIFunc('mail', 'admin', 'sendmail',
            array(
                'info'         => $recipients,
                'name'         => $name,
                'subject'      => $ticket_args['mailsubject'],
                'htmlmessage'  => $htmlmessage,
                'message'      => $textmessage,
                'from'         => $femail,
                'fromname'     => $fromname
            )
        );

        if( $usermail )
        {
            xarSessionSetVar('helpdesk_statusmsg', xarML('User mail sent','helpdesk'));
        }
        else
        {
            return false;
        }
    }

    return true;
}
?>
