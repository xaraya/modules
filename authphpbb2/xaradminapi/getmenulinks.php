<?php
/**
 * File: $Id$
 *
 * AuthphpBB2 Administrative Display Functions
 * 
 */

/**
 * utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function authphpbb2_adminapi_getmenulinks()
{
    // Security check 
    if (xarSecurityCheck('AdminAuthphpBB2')) {
        $menulinks[] = Array('url'   => xarModURL('authphpbb2',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}

?>