<?php
/**
   View Tickets
   
   @param $selection - The Key of what is being viewed (optional)
   @param $startnum - id of item to start the page with (optional)
   $param $sortorder - The order in which we do the sort (optional)
   $param $avtivepage - I don't know what this is (optional)
   @returns data used in a template
*/
function helpdesk_user_view($args)
{
    // Need to think if annon should be able to view some tickets
    if (!xarUserIsLoggedIn()) {
        return xarTplModule('helpdesk', 'user', 'view', $data);
    }
    
    xarVarFetch('selection',    'str:1:',  $selection,  'ALL',  XARVAR_NOT_REQUIRED);
    xarVarFetch('sortorder',    'str:1:',  $sortorder,  'TICKET_ID', XARVAR_NOT_REQUIRED);
    xarVarFetch('order',        'str:1:',  $order,      'ASC',  XARVAR_NOT_REQUIRED);
    xarVarFetch('startnum',     'str:1:',  $startnum,    1,     XARVAR_NOT_REQUIRED);
    xarVarFetch('statusfilter', 'str:1:',  $statusfilter,null,  XARVAR_NOT_REQUIRED);
    if(!xarVarFetch('catid',    'str',     $catid,       null,  XARVAR_NOT_REQUIRED)) {return;}
    
    // if user doesn't have edit access only allow user to view his tickets
    $EditAccess = xarSecurityCheck('edithelpdesk', 0);
    if (!$EditAccess && substr($selection, 0, 2) != 'MY') {
        $msg = xarML('Illegal Access - You are not allowed to be here!');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    $data = array();
    $data['menu']    = xarModFunc('helpdesk', 'user', 'menu');
    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');
    
    $data['username'] = xarUserGetVar('uname');
    $data['userid']   = xarUserGetVar('uid');    
        
    $data['enforceauthkey'] = xarModGetVar('helpdesk', 'EnforceAuthKey');    
    $viewlimit              = xarModGetVar('helpdesk', 'Default rows per page');
    
    // Get Column View preferences
    $data['showassignedtoinsummary']    = xarModGetVar('helpdesk', 'ShowAssignedToInSummary');
    $data['showclosedbyinsummary']      = xarModGetVar('helpdesk', 'ShowClosedByInSummary');
    $data['showopenbyinsummary']        = xarModGetVar('helpdesk', 'ShowOpenedByInSummary');
    $data['showlastmodifiedinsummary']  = xarModGetVar('helpdesk', 'ShowLastModifiedInSummary');
    $data['showdateenteredinsummary']   = xarModGetVar('helpdesk', 'ShowDateEnteredInSummary');
    $data['showstatusinsummary']        = xarModGetVar('helpdesk', 'ShowStatusInSummary');
    $data['showpriorityinsummary']      = xarModGetVar('helpdesk', 'ShowPriorityInSummary');
    
    /*
*/

    // Lets get the ticket now for the view
    $data['mytickets_data']  = xarModAPIFunc('helpdesk', 
                                             'user', 
                                             'gettickets', 
                                             array('userid'    => $data['userid'],
                                                   'catid'     => $catid,
                                                   'selection' => $selection,
                                                   'sortorder' => $sortorder,
                                                   'order'     => $order,
                                                   'startnum'  => $startnum,
                                                   'statusfilter' => $statusfilter));
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

    $data['selections'] = array('MYALL'         => 'My Tickets',
                                'ALL'           => 'All Tickets',
                                'MYASSIGNEDALL' => 'My Assigned Tickets',
                                'UNASSIGNED'    => 'Unassigned Tickets');
                                 
    // Sending state vars back into the form           
    $data['selection'] = $selection;
    $data['sortorder'] = $sortorder;
    $data['order'] = $order;
    $data['statusfilter'] = $statusfilter;
    $data['status'] = xarModAPIFunc('helpdesk', 'user', 'gets', 
                                    array('itemtype' => 3));    
    $data['catid'] = $catid;
    return xarTplModule('helpdesk', 'user', 'view', $data);
}
?>