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

    if (xarSecurityCheck('AdminNewsGroups', 0)) {
        $menulinks[] = Array('url'   => xarModURL('newsgroups',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for the newsgroups'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>