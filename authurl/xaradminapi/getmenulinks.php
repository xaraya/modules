<?php


/**
 * utility function pass individual menu items to the main menu
 *
 * @author Court Shrock
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function authurl_adminapi_getmenulinks()
{
    # Security check
    if(xarSecurityCheck('AdminAuthURL')) {
        $menulinks[] = Array('url'   => xarModURL('authurl',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the configuration for the module'),
                             'label' => xarML('Modify Config'));
    } else {
        $menulinks = array();
    }// if

    return $menulinks;
}

?>
