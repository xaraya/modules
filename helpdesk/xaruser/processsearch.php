<?php
/**
    Process Search 

    Process the Search Request and Display Requested Tickets

    @author  Brian McGilligan bmcgilligan@abrasiontechnology.com
    @access  public / private / protected
    @param   
    @param   
    @return  template
    @throws  list of exception identifiers which can be thrown
    @todo    <Brian McGilligan> ;  
*/ 
function helpdesk_user_processsearch($args)
{
    extract($args);
    
    $data['UserLoggedIn'] = xarUserIsLoggedIn();
    $data['menu']         = xarModFunc('helpdesk', 'user', 'menu');
    $data['username'] = xarUserGetVar('uname');
    $data['userid']   = xarUserGetVar('uid');    
    
    $data['AllowUserCheckStatus'] = xarModGetVar('helpdesk', 'User can check status');
    $data['AllowUserSubmitTicket'] = xarModGetVar('helpdesk', 'User can Submit');
    $data['AllowAnonSubmitTicket'] = xarModGetVar('helpdesk', 'Anonymous can Submit');
    // Get Column View preferences
    $data['showassignedtoinsummary']    = xarModGetVar('helpdesk', 'ShowAssignedToInSummary');
    $data['showclosedbyinsummary']      = xarModGetVar('helpdesk', 'ShowClosedByInSummary');
    $data['showopenbyinsummary']        = xarModGetVar('helpdesk', 'ShowOpenedByInSummary');
    $data['showlastmodifiedinsummary']  = xarModGetVar('helpdesk', 'ShowLastModifiedInSummary');
    $data['showdateenteredinsummary']   = xarModGetVar('helpdesk', 'ShowDateEnteredInSummary');
    $data['showstatusinsummary']        = xarModGetVar('helpdesk', 'ShowStatusInSummary');
    $data['showpriorityinsummary']      = xarModGetVar('helpdesk', 'ShowPriorityInSummary');    
    $EditAccess = xarSecurityCheck('edithelpdesk', 0);
    $AdminAccess = xarSecurityCheck('adminhelpdesk', 0);

    // Security check
    // No need for a security check if Anonymous Adding is enabled:
    // So ONLY check security if AllowAnonAdd is NOT TRUE
    if (!$data['AllowUserCheckStatus']){
        if (!xarSecurityCheck('readhelpdesk')) {
            return;
        }
    }

    xarVarFetch('selection',    'str:1:',  $selection,  'ALL',  XARVAR_NOT_REQUIRED);
    xarVarFetch('sortorder',    'str:1:',  $sortorder,  'TICKET_ID', XARVAR_NOT_REQUIRED);
    xarVarFetch('order',        'str:1:',  $order,      'ASC',  XARVAR_NOT_REQUIRED);
    xarVarFetch('startnum',     'str:1:',  $startnum,    1,     XARVAR_NOT_REQUIRED);
    xarVarFetch('statusfilter', 'str:1:',  $statusfilter,null,  XARVAR_NOT_REQUIRED);
    
    
    xarVarFetch('keywords',   'str:1:',  $keywords,   null,  XARVAR_NOT_REQUIRED);
    
    xarVarFetch('subject',    'isset:1:',  $subject,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('history',    'isset:1:',  $history,    null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('notes',      'isset:1:',  $notes,      null,  XARVAR_NOT_REQUIRED);
    
    // Lets get the ticket now for the view
    $data['mytickets_data']  = xarModAPIFunc('helpdesk', 'user', 'gettickets', 
                                             array('userid'    => $data['userid'],
                                                   'selection' => $selection,
                                                   'sortorder' => $sortorder,
                                                   'order'     => $order,
                                                   'startnum'  => $startnum,
                                                   'statusfilter' => $statusfilter,
                                                   'countonly' => '0',
                                                   'subject'   => $subject,
                                                   'keywords'  => $keywords));

    $totaltickets = sizeOf($data['mytickets_data']);
    
    //Setup args for pager so we don't lose our place                                                                                  
    $args = array('selection' => $selection,
                  'sortorder' => $sortorder,
                  'order'     => $order,
                  'statusfilter' => $statusfilter,
                  'startnum' => '%%');  
    $data['pager'] = xarTplGetPager($startnum, 
                                    $totaltickets,
                                    xarModURL('helpdesk', 'user', 'view', $args),
                                    xarModGetVar('helpdesk', 'Default rows per page'));    
    
    // Sending state vars back into the form                                                                                                                                                         
    $data['selection'] = $selection;
    $data['sortorder'] = $sortorder;
    $data['order'] = $order;
    $data['statusfilter'] = $statusfilter;
    $data['status'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                    array('itemtype' => 3));    
    
    // Return the items
    return xarTplModule('helpdesk', 'user', 'view', $data);
}
?>