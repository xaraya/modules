<?php
/**
 * utility function pass individual menu items to the main menu
 */
function netquery_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminNetquery', 0)) {
        $menulinks[] = Array('url'   => xarModURL('netquery',
                                                  'admin',
                                                  'config'),
                              'title' => xarML('Modify main configuration settings'),
                              'label' => xarML('Modify Configuration'));
    }
    if (xarSecurityCheck('EditNetquery', 0)) {
        $menulinks[] = Array('url'   => xarModURL('netquery',
                                                  'admin',
                                                  'wiview'),
                              'title' => xarML('View-edit-add whois lookup links'),
                              'label' => xarML('Edit Whois Links'));
    }
    if (xarSecurityCheck('AddNetquery', 0)) {
        $menulinks[] = Array('url'   => xarModURL('netquery',
                                                  'admin',
                                                  'lgview'),
                              'title' => xarML('View-edit-add looking glass routers'),
                              'label' => xarML('Edit LG Routers'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>