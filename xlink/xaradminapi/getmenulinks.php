<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author mikespub
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xlink_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminXLink')) {
        $menulinks[] = Array('url'   => xarModURL('xlink',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the xlink configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
