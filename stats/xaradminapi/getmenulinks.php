<?php
/**
 * utility function pass individual menu items to the admin panels
 * 
 * @author the Example module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function stats_adminapi_getmenulinks()
{ 
    // Security Check
    if (xarSecurityCheck('AdminStats', 0)) {
        $menulinks[] = Array('url' => xarModURL('stats',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the stats module'),
            'label' => xarML('Modify Config'));
    } 

    if (empty($menulinks)) {
        $menulinks = '';
    } 

    return $menulinks;
} 
?>
