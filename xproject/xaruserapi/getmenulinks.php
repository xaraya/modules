<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xproject_userapi_getmenulinks()
{

    if (xarSecAuthAction(0, 'xproject::', '::', ACCESS_OVERVIEW)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'main'),
                              'title' => xarML('The overview of this module and its functions'),
                              'label' => xarML('Overview'));
    }

    if (xarSecAuthAction(0, 'xproject::', '::', ACCESS_ADD)) {

        $menulinks[] = Array('url'   => xarModURL('xproject',
                                                   'user',
                                                   'new'),
                              'title' => xarML('Create a new project'),
                              'label' => xarML('New Project'));
    }

    if (xarSecAuthAction(0, 'xproject::', '::', ACCESS_READ)) {

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

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>