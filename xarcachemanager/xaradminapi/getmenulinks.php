<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author jsb| mikespub
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xarcachemanager_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminXarCache')) {
        $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                  'admin',
                                                  'flushcache'),
                             'title' => xarML('Flush the output cache of xarCache'),
                             'label' => xarML('Flush Cache'));
        $menulinks[] = Array('url'   => xarModURL('xarcachemanager',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the xarCache configuration'),
                             'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
