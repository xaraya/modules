<?php

/**
   utility function pass individual menu items to the main menu
 
   @return array containing the menulinks for the main menu items.
*/
function mailbag_adminapi_getmenulinks()
{

    $menulinks = array();

// Security Check
	if (xarSecurityCheck('adminmailbag',0)) {

        $menulinks[] = Array('url'   => xarModURL('mailbag',
                                                  'admin',
                                                  'main'),
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'));
    }

// Security Check
	if (xarSecurityCheck('adminmailbag',0)) {

        $menulinks[] = Array('url'   => xarModURL('mailbag',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View Items'),
                              'label' => xarML('View Items'));
    }

// Security Check
	if (xarSecurityCheck('adminmailbag',0)) {
        $menulinks[] = Array('url'   => xarModURL('mailbag',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify Config'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>