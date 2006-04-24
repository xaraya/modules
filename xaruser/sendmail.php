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
 * Send an e-mails based on action against a ticket.
 * Valid Events:
 *      new
 *      closed
 *      additionalcomment
 *
 * @author jojodee/Michel V.
 *
 * @param $mailaction: what e-mail to send
 */
function helpdesk_user_sendmail($args)
{
    // Get parameters
    extract($args);

    /*
        NOTE: $ticket_args releated directly to the ticket.
        $ticket_args used to generate $mail_args which is used to send mail to an user.
        $ticket_args maybe read and mail may get sent out to multiple users.

        In the templates $mail_args and $template_args are merged together. where
        $userid, $name and $email releated to the user receiving that particular email.
    */

    $newline_chars = array("/\r\n/","/\n/","/\r/");
    $ticket_args = array(
        'subject'     => $subject,
        'domain'      => isset($domain) ? $domain : null,
        'source'      => isset($source) ? $source : null,
        'priority'    => isset($priority) ? $priority : null,
        'status'      => $status,
        'openedby'    => $openedby,
        // Not perfect but a start
        'openedby_email'=> isset($email) ? $email : xarUserGetVar('email',$openedby),
        'assignedto'  => $assignedto,
        'closedby'    => $closedby,
        // Issue and notes vars are only needed for new tickets
        'issue'       => isset($issue) ? $issue : null,
        'issue_html'  => isset($issue) ? preg_replace($newline_chars, "<br />", $issue) : null,
        'notes'       => isset($notes) ? $notes : null,
        'notes_html'  => isset($notes) ? preg_replace($newline_chars, "<br />", $notes) : null,
        // Used for additional comments
        'comment'     => isset($comment) ? $comment : null,
        'comment_html'=> isset($comment) ? preg_replace($newline_chars, "<br />", $comment) : null,
        'viewticket'  => xarModUrl('helpdesk', 'user', 'display', array('tid' => $tid)),
        'tid'         => $tid
    );

    /*
        Generate the HTML and Text versions of the message and
        let the mail module decide which to send
    */
    $mail_args = array();
    switch($mailaction)
    {
        case 'new':
            // Setup Ticket.
            $mail_args = array();
            $mail_args['userid'] = $ticket_args['openedby'];
            $mail_args['name'] = xarUserGetVar('name',$ticket_args['openedby']);
            $mail_args['email'] = $ticket_args['openedby_email'];
            $mail_args['mailsubject'] =
                xarModGetVar('themes', 'SiteName') . " [#$tid] $subject ";
            $ticket_args['viewtickets'] = xarModUrl('helpdesk', 'user', 'view',
                array('selection' => 'MYALL')
            );

            $data = array_merge($ticket_args, $mail_args);
            $textmessage = xarTplModule('helpdesk', 'user', 'sendmailnewuser', $data, 'text');
            $htmlmessage = xarTplModule('helpdesk', 'user', 'sendmailnewuser', $data, 'html');
            helpdesk_userapi_sendmail($mail_args, $htmlmessage, $textmessage);

            /**
             * Send out a email to user assigned to ticket if one exists
             */
            if( !empty($ticket_args['assignedto']) )
            {
                $mail_args = array();
                $mail_args['userid'] = $ticket_args['assignedto'];
                $mail_args['name'] = xarUserGetVar('name',$ticket_args['assignedto']);
                $mail_args['email'] = xarUserGetVar('email',$ticket_args['assignedto']);
                $mail_args['mailsubject'] =
                    xarModGetVar('themes', 'SiteName') . " [#$tid] $subject ";

                $ticket_args['viewtickets'] = xarModUrl('helpdesk', 'user', 'view',
                    array('selection' => 'MYASSIGNEDALL')
                );

                $data = array_merge($ticket_args, $mail_args);
                $textmessage = xarTplModule('helpdesk', 'user', 'sendmailnewassigned', $data, 'text');
                $htmlmessage = xarTplModule('helpdesk', 'user', 'sendmailnewassigned', $data, 'html');
                helpdesk_userapi_sendmail($mail_args, $htmlmessage, $textmessage);

            }
            break;

        case 'closed':

            $users = array('openedby', 'assignedto', 'closedby');
            $sent_mail = array();
            foreach( $users as $user )
            {
                if( !empty($ticket_args[$user]) && !in_array($ticket_args[$user], $sent_mail) )
                {
                    $mail_args = array();
                    $mail_args['userid'] = $ticket_args[$user];
                    $mail_args['name'] = xarUserGetVar('name',$ticket_args[$user]);
                    // done for anon submitted tickets
                    if( $user == 'openedby' )
                        $mail_args['email'] = $ticket_args['openedby_email'];
                    else
                        $mail_args['email'] = xarUserGetVar('email',$ticket_args[$user]);
                    $mail_args['mailsubject'] =
                        "Closed: " . xarModGetVar('themes', 'SiteName') . " [#$tid] $subject ";
                    $ticket_args['viewtickets'] = xarModUrl('helpdesk', 'user', 'view',
                        array('selection' => 'MYALL')
                    );

                    $data = array_merge($ticket_args, $mail_args);
                    $textmessage = xarTplModule('helpdesk', 'user', 'sendmailcloseduser', $data, 'text');
                    $htmlmessage = xarTplModule('helpdesk', 'user', 'sendmailcloseduser', $data, 'html');
                    helpdesk_userapi_sendmail($mail_args, $htmlmessage, $textmessage);
                    $sent_mail[] = $ticket_args[$user];
                }
            }

            break;

        /**
         * Addition comments should be sent out to all users associated with the
         * ticket except for the current user.
         */
        case 'additionalcomment':

            $users = array('openedby', 'assignedto', 'closedby');
            $sent_mail = array();
            foreach( $users as $user )
            {
                if( !empty($ticket_args[$user]) && !in_array($ticket_args[$user], $sent_mail) )
                {
                    $mail_args = array();
                    $mail_args['userid'] = $ticket_args[$user];
                    $mail_args['name'] = xarUserGetVar('name',$ticket_args[$user]);
                    // done for anon submitted tickets
                    if( $user == 'openedby' )
                        $mail_args['email'] = $ticket_args['openedby_email'];
                    else
                        $mail_args['email'] = xarUserGetVar('email',$ticket_args[$user]);
                    $mail_args['mailsubject'] =
                        xarModGetVar('themes', 'SiteName') . " [#$tid] $subject ";
                    $ticket_args['viewtickets'] = xarModUrl('helpdesk', 'user', 'view',
                        array('selection' => 'MYALL')
                    );

                    $data = array_merge($ticket_args, $mail_args);
                    $textmessage = xarTplModule('helpdesk', 'user', 'sendmailadditionalcomment', $data, 'text');
                    $htmlmessage = xarTplModule('helpdesk', 'user', 'sendmailadditionalcomment', $data, 'html');
                    helpdesk_userapi_sendmail($mail_args, $htmlmessage, $textmessage);
                    $sent_mail[] = $ticket_args[$user];
                }
            }

            break;

    } //End switch


    return true;
}
/**
 * Send the actual email
 * @param
 * @return
 */
function helpdesk_userapi_sendmail($mail_args, $htmlmessage=null, $textmessage=null)
{
 //   return true;
    $usermail = false;
    if( isset($mail_args['email']) )
    {
        //let's send the email now
        $usermail = xarModAPIFunc('mail', 'admin', 'sendmail',
            array(
                'info'         => $mail_args['email'],
                'name'         => $mail_args['name'],
                'subject'      => $mail_args['mailsubject'],
                'htmlmessage'  => $htmlmessage,
                'message'      => $textmessage,
                //'from'         => $femail,
                //'fromname'     => $fromname
            )
        );
    }

    return $usermail;
}

?>
