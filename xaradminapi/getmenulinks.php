<?php
/**
 * utility function pass individual menu items to the admin panels
 * 
 * @author the Example module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function recommend_adminapi_getmenulinks()
{ 
    // Security Check
    if (xarSecurityCheck('EditRecommend', 0)) {
        $menulinks[] = Array('url' => xarModURL('recommend',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the recommend module'),
            'label' => xarML('Modify Config'));
    } 

    if (empty($menulinks)) {
        $menulinks = '';
    } 

    return $menulinks;
}
?>