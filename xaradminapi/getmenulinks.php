<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function newsgroups_adminapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('AdminNewsGroups', 0)) {
        $menulinks[] = Array('url'   => xarModURL('newsgroups',
                                                  'admin',
                                                  'selectgroups'),
                              'title' => xarML('Select the newsgroups you want to display'),
                              'label' => xarML('Select Newsgroups'));
        $menulinks[] = Array('url'   => xarModURL('newsgroups',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for the newsgroups'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>
