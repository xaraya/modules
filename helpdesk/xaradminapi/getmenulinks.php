<?php

/**
   utility function pass individual menu items to the main menu
 
   @return array containing the menulinks for the main menu items.
*/
function helpdesk_adminapi_getmenulinks()
{

    $menulinks = array();

// Security Check
	if (xarSecurityCheck('adminhelpdesk',0)) {

        $menulinks[] = Array('url'   => xarModURL('helpdesk',
                                                  'admin',
                                                  'main'),
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'));
    }

// Security Check
	if (xarSecurityCheck('adminhelpdesk',0)) {

        $menulinks[] = Array('url'   => xarModURL('helpdesk',
                                                  'admin',
                                                  'view',
                                                  array('itemtype' => 1)),
                              'title' => xarML('View Items'),
                              'label' => xarML('View Items'));
    }

// Security Check
	if (xarSecurityCheck('adminhelpdesk',0)) {
        $menulinks[] = Array('url'   => xarModURL('helpdesk',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify Config'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
