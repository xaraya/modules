<?php
/**
   View Tickets

   @param $selection - The Key of what is being viewed (optional)
   @param $startnum - id of item to start the page with (optional)
   @param $sortorder - The order in which we do the sort (optional)

   @return module output
*/
function helpdesk_user_view($args)
{
    // Get arguments
    extract($args);

    if( !xarVarFetch('selection',    'str:1:',  $selection,  'MYALL',  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('sortorder',    'str:1:',  $sortorder,  'TICKET_ID', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('order',        'str:1:',  $order,      'ASC',  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('startnum',     'str:1:',  $startnum,    1,     XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('statusfilter', 'str:1:',  $statusfilter,null,  XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('catid',        'str',     $catid,       null,  XARVAR_NOT_REQUIRED) ){ return false; }
     if( !xarVarFetch('search_fields','array',$search_fields, null,  XARVAR_NOT_REQUIRED) ){ return false; }
     if( !xarVarFetch('keywords',    'str',     $keywords,    null,  XARVAR_NOT_REQUIRED) ){ return false; }

    $data = array();
    $data['menu']    = xarModFunc('helpdesk', 'user', 'menu');
    $data['summary'] = xarModFunc('helpdesk', 'user', 'summaryfooter');

    // Need to think if annon should be able to view some tickets
    $data['UserLoggedIn'] = xarUserIsLoggedIn();
    if (!$data['UserLoggedIn']) {
        return xarTplModule('helpdesk', 'user', 'view', $data);
    }

    // Lets get the ticket now for the view
    $data['tickets']  = xarModAPIFunc('helpdesk', 'user', 'gettickets',
        array(
            'userid'       => xarUserGetVar('uid'),
            'catid'        => $catid,
            'selection'    => $selection,
            'sortorder'    => $sortorder,
            'order'        => $order,
            'startnum'     => $startnum,
            'statusfilter' => $statusfilter,
            'search_fields'=> $search_fields,
            'keywords'     => $keywords
        )
    );
    /*
        Counts the number of tickets in the system
    */
    $totaltickets  = xarModAPIFunc('helpdesk', 'user', 'count_tickets',
        array(
            'userid'       => xarUserGetVar('uid'),
            'catid'        => $catid,
            'selection'    => $selection,
            'sortorder'    => $sortorder,
            'order'        => $order,
            'startnum'     => $startnum,
            'statusfilter' => $statusfilter,
            'search_fields'=> $search_fields,
            'keywords'     => $keywords
        )
    );

    /*
        Setup args for pager so we don't lose our place
    */
    $args = array(
        'selection'    => $selection,
        'sortorder'    => $sortorder,
        'order'        => $order,
        'statusfilter' => $statusfilter,
        'search_fields'=> $search_fields,
        'keywords'     => $keywords,
        'startnum'     => '%%'
    );
    $url_template   = xarModURL('helpdesk', 'user', 'view', $args);
    $items_per_page = xarModGetVar('helpdesk', 'Default rows per page');
    $data['pager']  = xarTplGetPager($startnum, $totaltickets, $url_template, $items_per_page);

    $data['selections'] = array(
        'MYALL'         => xarML('My Tickets'),
        'ALL'           => xarML('All Tickets'),
        'MYASSIGNEDALL' => xarML('My Assigned Tickets'),
        'UNASSIGNED'    => xarML('Unassigned Tickets')
    );

    // Sending state vars back into the form
    $data['catid']        = $catid;
    $data['selection']    = $selection;
    $data['sortorder']    = $sortorder;
    $data['order']        = $order;
    $data['statusfilter'] = $statusfilter;
    $data['status']       = xarModAPIFunc('helpdesk', 'user', 'gets', 
        array('itemtype' => 3)
    );
    
    // Get Column View preferences
    $data['showassignedtoinsummary']    = xarModGetVar('helpdesk', 'ShowAssignedToInSummary');
    $data['showclosedbyinsummary']      = xarModGetVar('helpdesk', 'ShowClosedByInSummary');
    $data['showopenbyinsummary']        = xarModGetVar('helpdesk', 'ShowOpenedByInSummary');
    $data['showlastmodifiedinsummary']  = xarModGetVar('helpdesk', 'ShowLastModifiedInSummary');
    $data['showdateenteredinsummary']   = xarModGetVar('helpdesk', 'ShowDateEnteredInSummary');
    $data['showstatusinsummary']        = xarModGetVar('helpdesk', 'ShowStatusInSummary');
    $data['showpriorityinsummary']      = xarModGetVar('helpdesk', 'ShowPriorityInSummary');

    return xarTplModule('helpdesk', 'user', 'view', $data);
}
?>
