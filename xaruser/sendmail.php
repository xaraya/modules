<?php
/**
    Send an e-mail to submitter/assignedto of the ticket
    
    @author jojodee/Michel V.
  
    @param $mailaction: what e-mail to send
*/
function helpdesk_user_sendmail($args)
{
  
    // Get parameters
    extract ($args);
    if (!xarVarFetch('tid', 'int::', $tid, '', XARVAR_NOT_REQUIRED)) return; // The ticket ID
    if (!xarVarFetch('userid', 'int::', $userid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str::', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('whosubmit', 'int', $whosubmit, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phone', 'str', $phone, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('email', 'email', $email, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject', 'str::', $subject, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('domain', 'str::', $domain, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('source', 'int:1:', $source, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority', 'int::', $priority, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'int::', $statusid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('openedby', 'int::', $openedby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('assignedto', 'int::', $assignedto, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closedby', 'str::', $closedby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issue', 'str::', $issue, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mailaction', 'str::', $mailaction, 'usernew')) return;
    
    $ticket_args = array(
        'userid'      => $userid,
        'name'        => $name,
        'whosubmit'   => $whosubmit,
        'phone'       => $phone,
        'email'       => $email,
        'subject'     => $subject,
        'domain'      => $domain,
        'source'      => $source,
        'priority'    => $priority,
        'status'      => $statusid,
        'openedby'    => $openedby,
        'assignedto'  => $assignedto,
        'closedby'    => $closedby,
        'issue'       => $issue,
        'notes'       => $notes,
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
                $ticket_args['mailsubject'] = xarVarPrepForDisplay(xarML('New ticket submitted'));
                $ticket_args['viewtickets'] = xarModUrl('helpdesk', 'user', 'view', array('selection' => 'MYALL'));
                
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
                $ticket_args['mailsubject'] = xarVarPrepForDisplay(xarML('Your ticket has been closed'));
                $ticket_args['viewtickets'] = xarModUrl('helpdesk', 'user', 'view', array('selection' => 'MYALL'));
                
                // startnew message
                $textmessage = xarTplModule('helpdesk', 'user', 'sendmailcloseduser', $ticket_args, 'text');
                
                $htmlmessage = xarTplModule('helpdesk', 'user', 'sendmailcloseduser', $ticket_args, 'html');
            }
            
            break;
        
        case 'assignednew':
        
            if( !empty($assignedto) ) 
            {
                $recipients = xarUserGetVar('email', $assignedto);
                $ticket_args['assignedtoname'] = xarUserGetVar('name', $assignedto);
            } 
            else 
            {
                $recipients = xarModGetVar('mail', 'adminmail');
                $ticket_args['assignedtoname'] = xarModGetVar('mail', 'adminname');
            }
            
            // Does not generate errors
            if(isset($recipients)) 
            {
                $ticket_args['mailsubject'] = xarVarPrepForDisplay(xarML('New ticket assigned to you'));

                $ticket_args['viewtickets'] = xarModUrl('helpdesk', 'user', 'view', array('selection' => 'MYASSIGNEDALL'));
                
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
        $fromname = xarModGetVar('mail', 'adminname'); // Get the webmaster name TODO: make admin var 
        $femail = xarModGetVar('mail', 'adminmail'); // Get the webmaster email

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
        if( $usermail ) {
            xarSessionSetVar('helpdesk_statusmsg', xarML('User mail sent','helpdesk'));
        } else {
            /*
                I don't want to set the exception here as the calling function 
                will hide this one so what is the point.                
                Brian M.
            */
            //$msg = xarML('The message was not sent');
            //xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', $msg);
            return false;
        }    
    }
    
    return true;
}
?>
