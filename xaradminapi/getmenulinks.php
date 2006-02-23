<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function pmember_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminPMember', 0)) {
        $menulinks[] = Array('url'   => xarModURL('pmember', 'admin', 'modifyconfig'),
                              'title' => xarML('Modify the configuration'),
                              'label' => xarML('Modify Config'));
        $menulinks[] = Array('url'   => xarModURL('pmember', 'admin', 'view'),
                              'title' => xarML('View Subscriptions'),
                              'label' => xarML('View Subscriptions'));
        $menulinks[] = Array('url'   => xarModURL('pmember', 'admin', 'new'),
                              'title' => xarML('Add Subscriptions'),
                              'label' => xarML('Add Subscriptions'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>