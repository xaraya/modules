<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function ping_adminapi_getmenulinks()
{   // Security Check
    if(xarSecurityCheck('Adminping')) {
        $menulinks[] = Array('url'   => xarModURL('ping',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new site to ping'),
                              'label' => xarML('Add'));
        $menulinks[] = Array('url'   => xarModURL('ping',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit'),
                              'label' => xarML('View'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>