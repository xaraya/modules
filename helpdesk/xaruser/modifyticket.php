<?php
function helpdesk_user_modifyticket($args)
{
    extract($args);
    $enforceauthkey = xarModGetVar('helpdesk', 'EnforceAuthKey');
    // Possible formaction values:
    // UPDATE / MODIFY / DELETE / DELETE_VERIFIED
    xarVarFetch('formaction',  'str:1:',  $formaction, null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('ticket_id',   'int:1:',  $ticket_id,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('userid',      'int:1:',  $userid,     null,  XARVAR_NOT_REQUIRED);
    
    switch($formaction){
        case 'UPDATE':
            // Add new history entry:
            // Display history form with minor ticket information above it:
            $output = updateticket($ticket_id,$userid);
            break;
        case 'MODIFY':
            // Modify core ticket
            // Display Ticket Form with modifiable fields
            // Also list out history updates with their own links to modify
            $output = modifyticket($ticket_id);

            break;	
    }
    return $output;
}

function updateticket($ticket_id,$userid)
{
    //This function is ONLY called internally

    // Create output object
    $output = new pnHTML();
    // Some of these values get used more than once in this procedure.
    // Make the call to get their value here to prevent multiple function calls
    // and/or db queries
    $EditAccess     = xarSecurityCheck('edithelpdesk');
    $UserLoggedIn   = xarUserIsLoggedIn();
    $enforceauthkey = xarModGetVar('helpdesk', 'EnforceAuthKey');

    $output->SetInputMode(_PNH_VERBATIMINPUT);

    if (!xarModAPILoad('helpdesk', 'user')) {
        $output->Text(_LOADFAILED);
        return $output->GetOutput();
        break;
    }
    $enabledimages 	= xarModGetVar('helpdesk', 'Enable Images');
    // Necessary Viewable fields: history, history_notes, history_hours, history_minutes
    // Necessary Hidden fields: ticket_id, userid
    $output->Text("<center>");
    $output->Text(xarModFunc('helpdesk', 'user', 'menu'));
    $output->TableStart('','',1,'500');
    $output->Text('<form action="'.xarModURL('helpdesk', 'user', 'addhistory').'" method="post" name="ADDHISTORY">');
    $output->FormHidden('userid', $userid);
    $output->FormHidden('ticket_id', $ticket_id);
    $ticket_statusid = xarModAPIFunc('helpdesk','user','getstatusid',array('ticket_id' => $ticket_id));
    $statuslist_data = xarModAPIFunc('helpdesk','user','statusselect',array('selectid'=>$ticket_statusid));
    $output->TableRowStart();
                $output->TableColStart(2,'left');
                $output->Text('<strong>'._FULLHISTDESC.'</strong>:');
                $output->TableColEnd();
                $output->TableColStart(1,'right');
                    $output->Text(_STATUS.':');
                $output->TableColEnd();
                $output->TableColStart(1,'center');
                    $output->FormSelectMultiple('ticket_statusid',$statuslist_data);
                $output->TableColEnd();
            $output->TableRowEnd();
            $output->TableRowStart();
                $output->TableColStart(4,'center');
                $output->FormTextArea('history','',6,60);
                $output->TableColEnd();
            $output->TableRowEnd();
            // If user is Help Desk Staff or a technician (Has EDIT access) then
            // Give them the "Notes" field.
            // Remember, end users do NOT see this field unless they have EDIT access
            if ($EditAccess) {
                $output->TableRowStart();
                    $output->TableColStart(4,'left');
                    $output->Text(_TECHNOTES.':');
                    $output->TableColEnd();
                $output->TableRowEnd();
                $output->TableRowStart();
                    $output->TableColStart(4,'center');
                    $output->FormTextArea('history_notes','',6,60);
                    $output->TableColEnd();
                $output->TableRowEnd();
                $output->TableRowStart();
                    $output->TableColStart(4,'center');
                        $output->Text(_TIMESPENT.':&nbsp;&nbsp;');
                        $output->Text(_HOURS.': ');
                        $output->FormText('history_hours','',4,4);
                        $output->Text(_MINUTES.': ');
                        $output->FormText('history_minutes','',4,4);
                    $output->TableColEnd();
                $output->TableRowEnd();
            }
            // End Help Desk Staff / Technician Notes
            $output->TableRowStart();
                $output->TableColStart(4,'center');
                    if ($enabledimages)
                        {
                            $output->Text('<a href="javascript:document.ADDHISTORY.submit();" title="'._ADDNEWHISTORY.'"><img src="'.get_icon("submit.gif").'" border=0></a>');
                        }else{
                            $output->Text('<a href="javascript:document.ADDHISTORY.submit();" title="'._ADDNEWHISTORY.'">['._SUBMIT.']</a>');
                        }
                $output->TableColEnd();
            $output->TableRowEnd();
        $output->TableEnd();
    $output->FormEnd();
    $output->Text('</center>');

    // To keep ticket info available while updating, re-load the ticket
    $PassVars=array('ticket_id'=>$ticket_id,
                    'authid'=>xarSecGenAuthKey(),
                    'userid'=>$userid,
                    'activity'=>'REVIEW');
    $output->Text(xarModFunc('helpdesk', 'user', 'viewticket', $PassVars));

    return $output->GetOutput();
}


function modifyticket($ticket_id)
{
    //This function is ONLY called internally

    // Some of these values get used more than once in this procedure.
    // Make the call to get their value here to prevent multiple function calls
    // and/or db queries
    $data['EditAccess']     = xarSecurityCheck('edithelpdesk');
    $data['UserLoggedIn']   = xarUserIsLoggedIn();
    $data['enforceauthkey'] = xarModGetVar('helpdesk', 'EnforceAuthKey');
    $data['enabledimages']  = xarModGetVar('helpdesk', 'Enable Images');

    if (!xarModAPILoad('helpdesk', 'user')) {
        return false;
        break;
    }

    $data['menu']    = xarModFunc('helpdesk', 'user', 'menu');
    
    if ($data['UserLoggedIn']) {
        // New [0.710] method
        $data['username'] = xarUserGetVar('uname');
        $data['userid']   = xarUserGetVar('uid');
    }else {
        $data['username'] = 'Anonymous';
        $data['userid']   = '1';
    }
    
    // Get the ticket Data:
    $data['ticketdata']   = xarModAPIFunc('helpdesk','user','getticket',array('ticket_id'=>$ticket_id));
    
    if(!empty($data['ticketdata']['openedby'])){
        $data['openedby']     = xarModAPIFunc('roles', 'user', 'get', array('uid' => $data['ticketdata']['openedby']));
    }else{
        $data['openedby'] = array('uid' => '', 'name' => '');
    }
    if(!empty($data['ticketdata']['assignedto'])){
        $data['assignedto']   = xarModAPIFunc('roles', 'user', 'get', array('uid' => $data['ticketdata']['assignedto']));
    }else{
        $data['assignedto'] = array('uid' => '', 'name' => '');
    }
    if(!empty($data['ticketdata']['closedby'])){
        $data['closedby']     = xarModAPIFunc('roles', 'user', 'get', array('uid' => $data['ticketdata']['closedby']));
    }else{
        $data['closedby'] = array('uid' => '', 'name' => '');
    }
    
    //Build Ticket Type Drop-Down
    $data['tickettypesel_data']     = xarModAPIFunc('helpdesk','user','tickettypeselect',array('selectid'=>$data['ticketdata']['typeid']));
    //Build TicketPriority Drop-Down
    $data['ticketprioritysel_data'] = xarModAPIFunc('helpdesk','user','prioritytypeselect',array('selectid'=>$data['ticketdata']['priorityid']));
    //Build Status Drop-Down
    $data['statuslist_data']        = xarModAPIFunc('helpdesk','user','statusselect',array('selectid'=>$data['ticketdata']['statusid']));

    $allowsoftwarechoice            = xarModGetVar('helpdesk', 'AllowSoftwareChoice');
    if ($allowsoftwarechoice){
        $data['swdropdown'] = xarModAPIFunc('helpdesk',
                                            'user',
                                            'buildswdrops',
                                            array('formname'    => 'MODIFYTICKET', 
                                                  'softwareid'  => $data['ticketdata']['softwareid'],
                                                  'swversionid' => $data['ticketdata']['swversionid']));
    }
    $data['ticket_id'] = $ticket_id;

    if ($data['EditAccess']) {
        //Build TicketPriority Drop-Down
        $data['users'] = xarModAPIFunc('roles', 'user', 'getall');
        //Build TicketSource Drop-Down
        $data['ticketsource_data'] = xarModAPIFunc('helpdesk','user','sourceselect',array('selectid'=>$data['ticketdata']['sourceid']));
    }

    $results              = xarModAPIFunc('helpdesk','user','gethistories',array('ticket_id' => $ticket_id));
    $data['historycount'] = count($results);
    $data['results']      = $results;    
    
    $hours   = $results[0]['historyhours'];
    $minutes = $results[0]['historyminutes'];
    if(!$hours)   { $data['hours'] = 0; }
    if(!$minutes) { $data['minutes'] = 0; }
    
            
    $data['ticket']  = xarModFunc('helpdesk', 'user', 'viewticket', $ticket_id);
    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');
    
    return xarTplModule('helpdesk', 'user', 'modifyticket', $data);
}
?>
