<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the XProject module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xproject_adminapi_getmenulinks()
{

    if (xarSecurityCheck('ViewXProject', 0)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'main'),
                              'title' => xarML('The overview of this module and its functions'),
                              'label' => xarML('Overview'));
    }

    if (xarSecurityCheck('AddXProject', 0)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Create a new project'),
                              'label' => xarML('New Project'));
    }

    if (xarSecurityCheck('ReadXProject', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'view'),
                              'title' => xarML('List of current projects'),
                              'label' => xarML('View Projects'));

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'search'),
                              'title' => xarML('Query project entries'),
                              'label' => xarML('Search Projects'));
    }

    if (xarSecurityCheck('AdminXProject', 0)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for XProject'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>