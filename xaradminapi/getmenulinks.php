<?php

/**
 * utility function pass individual menu items to the main menu
 * 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function scheduler_adminapi_getmenulinks()
{
    $menulinks = array();
    if (xarSecurityCheck('AdminScheduler', 0)) {
        $menulinks[] = Array('url' => xarModURL('scheduler', 'admin', 'search'), 
                             'title' => xarML('Search for scheduler API functions'),
                             'label' => xarML('Find Functions'));
        $menulinks[] = Array('url' => xarModURL('scheduler', 'admin', 'modifyconfig'), 
                             'title' => xarML('Modify the configuration for the module'),
                             'label' => xarML('Modify Config'));
    }
    return $menulinks;
}

?>
