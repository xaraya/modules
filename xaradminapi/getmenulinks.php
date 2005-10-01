<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function pop3gateway_adminapi_getmenulinks()
{
    // Security Check
    if(xarSecurityCheck('AdminPOP3Gateway')) {

        $menulinks[] = Array('url'   => xarModURL('pop3gateway',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Edit the POP3 Gateway Configuration'),
                              'label' => xarML('Modify Config'));
        $menulinks[] = Array('url'   => xarModURL('pop3gateway',
                                                  'admin',
                                                  'import'),
                              'title' => xarML('Import Blog Entries Manually'),
                              'label' => xarML('Manual Import'));
    }
    if (empty($menulinks)){
        $menulinks = array();
    }
    return $menulinks;
}
?>