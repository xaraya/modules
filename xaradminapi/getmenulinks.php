<?php
/**
 * utility function pass individual menu items to the admin panels
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function paypalipn_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminPayPalIPN', 0)) {
        $menulinks[] = Array('url' => xarModURL('paypalipn',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration.'),
            'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    return $menulinks;
}
?>