<?php
/**
 * utility function pass individual menu items to the main menu
 */

function netquery_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminNetquery', 0)) {
        $menulinks[] = Array('url'   => xarModURL('netquery',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify Netquery configuration'),
                              'label' => xarML('Modify Config'));
    }
    if (xarSecurityCheck('EditNetquery', 0)) {
        $menulinks[] = Array('url'   => xarModURL('netquery',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and edit whois lookup links'),
                              'label' => xarML('Edit Whois'));
    }
    if (xarSecurityCheck('AddNetquery', 0)) {
        $menulinks[] = Array('url'   => xarModURL('netquery',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new whois lookup link'),
                              'label' => xarML('Add Whois'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>
