<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function ratings_adminapi_getmenulinks()
{

// Security Check
    // Security Check
	if (xarSecurityCheck('AdminRatings')) {
        $menulinks[] = Array('url'   => xarModURL('ratings',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the ratings module configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
