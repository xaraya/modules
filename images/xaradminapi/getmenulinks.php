<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function images_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminImages')) {
        $menulinks[] = Array('url'   => xarModURL('images',
                                                  'admin',
                                                  'main'),
                             'title' => xarML('Images Module Overview'),
                             'label' => xarML('Overview'));
        $menulinks[] = Array('url'   => xarModURL('images',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Edit the Images Configuration'),
                             'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>
