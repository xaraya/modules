<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */

function navigator_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminNavigator')) {
        $menulinks[] = Array('url'   => xarModURL('navigator',
                                                  'admin',
                                                  'dynimages'),
                            'title' => xarML('Edit Dynamic Images'),
                            'label' => xarML('Dynamic Images'));
        $menulinks[] = Array('url'   => xarModURL('navigator',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Navigator Configuration'),
                             'label' => xarML('Modify Config'));
        $menulinks[] = Array('url'   => xarModURL('navigator',
                                                  'admin',
                                                  'tag_generator'),
                             'title' => xarML('Navigator Tag Generator'),
                             'label' => xarML('Tag Generator'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}

?>