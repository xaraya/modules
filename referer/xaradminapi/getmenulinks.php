<?php
/**
 * utility function pass individual menu items to the admin panels
 * 
 * @author the Example module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function referer_adminapi_getmenulinks()
{
    if (xarSecurityCheck('EditReferer', 0)) {
        $menulinks[] = Array('url' => xarModURL('referer',
                'admin',
                'view'),
            'title' => xarML('View Referers'),
            'label' => xarML('View'));
    } 

    if (xarSecurityCheck('AdminReferer', 0)) {
        $menulinks[] = Array('url' => xarModURL('referer',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration'),
            'label' => xarML('Modify Config'));
    } 

    if (empty($menulinks)) {
        $menulinks = '';
    } 

    return $menulinks;
} 

?>