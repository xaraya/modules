<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xslt_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminXSLT')) {
        $menulinks[] = Array('url'   => xarModURL('xslt',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the XSLT module configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
