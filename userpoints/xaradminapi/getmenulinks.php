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
                                                  'view'),
                              'title' => xarML('View userpoints statistics per module'),
                              'label' => xarML('View Statistics'));
        $menulinks[] = Array('url'   => xarModURL('userpoints',
                                                  'admin',
                                                  'pointstypes'),
                              'title' => xarML('View userpoints types'),
                              'label' => xarML('View Points Types'));
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
