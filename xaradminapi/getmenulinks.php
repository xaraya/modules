<?php

/**
   utility function pass individual menu items to the main menu

   @return array containing the menulinks for the main menu items.
*/
function helpdesk_adminapi_getmenulinks()
{

    $menulinks = array();

    if( xarSecurityCheck('adminhelpdesk',0) )
    {
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'admin', 'overview'),
            'title' => xarML('Overview'),
            'label' => xarML('Overview')
        );
    }

    if( xarSecurityCheck('adminhelpdesk',0) )
    {
        $menulinks[] = Array('url'   => xarModURL('helpdesk', 'admin', 'view'),
            'title' => xarML('View Items'),
            'label' => xarML('View Items')
        );
    }

    if( xarSecurityCheck('adminhelpdesk',0) )
    {
        $menulinks[] = Array(
            'url'   => xarModURL('helpdesk', 'admin', 'modifyconfig'),
            'title' => xarML('Modify Config'),
            'label' => xarML('Modify Config')
        );
    }

    if( xarSecurityCheck('adminhelpdesk',0) )
    {
        $menulinks[] = array(
            'url'   => xarModURL('helpdesk', 'admin', 'setup_security'),
            'title' => xarML('Setup Security'),
            'label' => xarML('Setup Security')
        );
    }

    return $menulinks;
}
?>
