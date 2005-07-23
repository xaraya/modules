<?php
/**
    @ Author jojodee/Michel V.
  
    @ Function: Send an e-mail to submitter/assignedto of the ticket
    @ param $mailaction: what e-mail to send
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
    
	switch($mailaction) {
	    case 'usernew':
	
		if (!empty($email)) {
		$recipients = $email;
		} else {
		$recipients = xarUserGetVar('email');
		}
		
		  // Does not generate errors
		if(isset($recipients)) {
			$fromname = xarModGetVar('mail', 'adminname'); // Get the webmaster name TODO: make admin var 
			$femail = xarModGetVar('mail', 'adminmail'); // Get the webmaster email
			$mailsubject = xarVarPrepForDisplay(xarML('New ticket submitted'));
			$viewticket = xarModUrl('helpdesk', 'user', 'display', array('tid' => $tid));
			$viewtickets = xarModUrl('helpdesk', 'user', 'view', array('selection' => 'MYALL'));
	
			// startnew message
			$textmessage= xarTplModule('helpdesk',
										   'user',
										   'sendmailnewuser', // template to use
										array('userid'      => $userid,
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
											  'viewticket'  => $viewticket,
											  'viewtickets' => $viewtickets,
											  'tid'         => $tid),
											'text');
		
			 $htmlmessage= xarTplModule('helpdesk',
										   'user',
										   'sendmailnewuser',
										array('userid'      => $userid,
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
											  'viewticket'  => $viewticket,
											  'viewtickets' => $viewtickets,
											  'tid'         => $tid),
											'html');
		
				//let's send the email now
				if($usermail = xarModAPIFunc('mail',
												   'admin',
												   'sendmail',
												   array('info'         => $recipients,
														 'name'         => $name,
														 'subject'      => $mailsubject,
														 'htmlmessage'  => $htmlmessage,
														 'message'      => $textmessage,
														 'from'         => $femail,
														 'fromname'     => $fromname
														))) {
				xarSessionSetVar('helpdesk_statusmsg', xarML('User mail sent','helpdesk'));
				} else {
						$msg = xarML('The message was not sent');
						xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
						return false;
				}
		  }
		  
		return true;
		
		case 'closed':
		
		if (!empty($email)) {
		$recipients = $email;
		} else {
		$recipients = xarUserGetVar('email');
		}
		
		  // Does not generate errors
		if(isset($recipients)) {
			$fromname = xarModGetVar('mail', 'adminname'); // Get the webmaster name TODO: make admin var 
			$femail = xarModGetVar('mail', 'adminmail'); // Get the webmaster email
			$mailsubject = xarVarPrepForDisplay(xarML('Your ticket has been closed'));
			$viewticket = xarModUrl('helpdesk', 'user', 'display', array('tid' => $tid));
			$viewtickets = xarModUrl('helpdesk', 'user', 'view', array('selection' => 'MYALL'));
	
			// startnew message
			$textmessage= xarTplModule('helpdesk',
										   'user',
										   'sendmailcloseduser', // template to use
										array('userid'      => $userid,
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
											  'viewticket'  => $viewticket,
											  'viewtickets' => $viewtickets,
											  'tid'         => $tid),
											'text');
		
			 $htmlmessage= xarTplModule('helpdesk',
										   'user',
										   'sendmailcloseduser',
										array('userid'      => $userid,
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
											  'viewticket'  => $viewticket,
											  'viewtickets' => $viewtickets,
											  'tid'         => $tid),
											'html');
		
				//let's send the email now
				if($usermail = xarModAPIFunc('mail',
												   'admin',
												   'sendmail',
												   array('info'         => $recipients,
														 'name'         => $name,
														 'subject'      => $mailsubject,
														 'htmlmessage'  => $htmlmessage,
														 'message'      => $textmessage,
														 'from'         => $femail,
														 'fromname'     => $fromname
														))) {
				xarSessionSetVar('helpdesk_statusmsg', xarML('User mail sent','helpdesk'));
				} else {
						$msg = xarML('The message was not sent');
						xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
						return false;
				}
		  }
		  
		return true;
		
		case 'assignednew':
		
		if (!empty($assignedto)) {
		$recipients = xarUserGetVar('email', $assignedto);
		$assignedtoname = xarUserGetVar('name', $assignedto);
		} else {
		$recipients = xarModGetVar('mail', 'adminmail');
		$assignedtoname = xarModGetVar('mail', 'adminname');
		}
		
		  // Does not generate errors
		if(isset($recipients)) {
			$fromname = xarModGetVar('mail', 'adminname'); // Get the webmaster name TODO: make admin var ?
			$femail = xarModGetVar('mail', 'adminmail'); // Get the webmaster email
			$mailsubject = xarVarPrepForDisplay(xarML('New ticket assigned to you'));
			$viewticket = xarModUrl('helpdesk', 'user', 'display', array('tid' => $tid));
			$viewtickets = xarModUrl('helpdesk', 'user', 'view', array('selection' => 'MYASSIGNEDALL'));
			
			// startnew message
			$textmessage= xarTplModule('helpdesk',
										   'user',
										   'sendmailnewassigned', // template to use
										array('userid'      => $userid,
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
											  'assignedtoname' => $assignedtoname,
											  'closedby'    => $closedby,
											  'issue'       => $issue,
											  'notes'       => $notes,
											  'viewticket'  => $viewticket,
											  'viewtickets' => $viewtickets,
											  'tid'         => $tid),
											'text');
		
			 $htmlmessage= xarTplModule('helpdesk',
										   'user',
										   'sendmailnewassigned',
										array('userid'      => $userid,
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
											  'assignedtoname' => $assignedtoname,
											  'closedby'    => $closedby,
											  'issue'       => $issue,
											  'notes'       => $notes,
											  'viewticket'  => $viewticket,
											  'viewtickets' => $viewtickets,
											  'tid'         => $tid),
											'html');
		
				//let's send the email now
				if($usermail = xarModAPIFunc('mail',
												   'admin',
												   'sendmail',
												   array('info'         => $recipients,
														 'name'         => $name,
														 'subject'      => $mailsubject,
														 'htmlmessage'  => $htmlmessage,
														 'message'      => $textmessage,
														 'from'         => $femail,
														 'fromname'     => $fromname
														))) {
				xarSessionSetVar('helpdesk_statusmsg', xarML('User mail sent','helpdesk'));
				} else {
						$msg = xarML('The message was not sent');
						xarErrorSet(XAR_SYSTEM_EXCEPTION, 'FUNCTION_FAILED', new SystemException($msg));
						return false;
				}
		  }
		return true;
		
	  } //End switch

}
?>
