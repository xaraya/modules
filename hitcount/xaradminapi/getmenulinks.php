<?php
/**
 * utility function pass individual menu items to the admin panels
 * 
 * @author the Example module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function hitcount_adminapi_getmenulinks()
{ 
    // Security Check
    if (xarSecurityCheck('AdminHitcount', 0)) {
        $menulinks[] = Array('url' => xarModURL('hitcount',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the Hitcount module'),
            'label' => xarML('Modify Config'));
    } 

    if (empty($menulinks)) {
        $menulinks = '';
    } 

    return $menulinks;
} 
?>
