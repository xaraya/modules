<?php

/**
   utility function pass individual menu items to the main menu

   @return array containing the menulinks for the main menu items.
*/
function helpdesk_adminapi_getmenulinks()
{
    $menulinks = array();
    if( xarSecurityCheck('adminhelpdesk',0) ) {
        $menulinks[] = array('url'   => xarModURL('helpdesk', 'admin', 'view'),
            'title' => xarML('View Items'),
            'label' => xarML('View Items')
        );
        /* TODO: Currently missing function */
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'admin', 'setup_security'),
            'title' => xarML('Setup Security'),
            'label' => xarML('Setup Security')
        );
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'admin', 'modifyconfig'),
            'title' => xarML('Modify Config'),
            'label' => xarML('Modify Config')
        );
    }
    // Check for emptiness
    if (empty($menulinks)) {
        $menulinks = '';
    }
    return $menulinks;
}
?>
