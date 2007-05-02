<?php
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function trackback_adminapi_getmenulinks()
{

// Security Check
        $menulinks[] = Array('url'   => xarModURL('trackback',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Ping another website'),
                              'label' => xarML('Ping'));

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>