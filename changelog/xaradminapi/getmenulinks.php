<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author mikespub
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function changelog_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminChangeLog')) {
        $menulinks[] = Array('url'   => xarModURL('changelog',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the changelog configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
