<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function chat_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AdminChat', 0)) {
        $menulinks[] = Array('url'   => xarModURL('chat',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for chat'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>