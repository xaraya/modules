<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function userpoints_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminUserpoints')) {
        $menulinks[] = Array('url'   => xarModURL('userpoints',
                                                  'admin',
                                                  'newrank'),
                              'title' => xarML('Add A New User Rank'),
                              'label' => xarML('Add Rank'));
        $menulinks[] = Array('url'   => xarModURL('userpoints',
                                                  'admin',
                                                  'viewrank'),
                              'title' => xarML('View The Existing Ranks'),
                              'label' => xarML('View Ranks'));
        $menulinks[] = Array('url'   => xarModURL('userpoints',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the userpoints module configuration'),
                              'label' => xarML('Modify Config'));

    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
