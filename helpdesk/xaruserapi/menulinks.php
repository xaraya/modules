<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author Brian McGilligan
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function helpdesk_userapi_menulinks()
{
    // Some of these values get used more than once in this procedure.
    // Make the call to get their value here to prevent multiple function calls
    // and/or db queries
    $allowusercheckstatus  = xarModGetVar('helpdesk', 'User can check status');
    $allowusersubmitticket = xarModGetVar('helpdesk', 'User can Submit');
    $allowanonsubmitticket = xarModGetVar('helpdesk', 'Anonymous can Submit');
    $userisloggedin        = xarUserIsLoggedIn();

    $menulinks = array();

    // Security Check
    if (xarSecurityCheck('readhelpdesk',0)) {

        $menulinks[] = Array('url'   => xarModURL('helpdesk',
                                                  'user',
                                                  'main'),
                             'page' => 'main',
                              'title' => xarML('Main Page'),
                              'label' => xarML('Main Page'));
    }
    
    // Security Check
    if (xarSecurityCheck('adminhelpdesk',0)) {

        $menulinks[] = Array('url'   => xarModURL('helpdesk',
                                                  'admin',
                                                  'main'),
                             'page' => 'main',
                             'type' => 'admin',
                             'title' => xarML('Administration'),
                             'label' => xarML('Administration'));
    }

    // Security Check
    if (xarSecurityCheck('readhelpdesk',0) && 
        (($userisloggedin && $allowusersubmitticket) || 
         (!$userisloggedin && $allowanonsubmitticket))) {

        $menulinks[] = Array('url'   => xarModURL('helpdesk',
                                                  'user',
                                                  'new'),
                              'page' => 'new',
                              'title' => xarML('New Ticket'),
                              'label' => xarML('New Ticket'));
    }

    // Security Check
    if (xarSecurityCheck('readhelpdesk',0)) {
        $menulinks[] = Array('url'   => xarModURL('helpdesk',
                                                  'user',
                                                  'search'),
                             'page' => 'search',
                             'title' => xarML('Search'),
                             'label' => xarML('Search'));
    }
    
    // Security Check
    if (xarSecurityCheck('readhelpdesk',0)) {
        $menulinks[] = Array('url'   => xarModURL('helpdesk',
                                                  'user',
                                                  'view',
                                                  array('selection' => 'MYALL')),
                             'page' => 'view',
                             'selection' => 'MYALL',
                             'title' => xarML('View Tickets'),
                             'label' => xarML('View Tickets'));
    }
    

    return $menulinks;
}
?>