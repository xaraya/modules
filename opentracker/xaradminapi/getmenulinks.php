<?php
/**
 * utility function pass individual menu items to the admin panels
 * 
 * @author the Example module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function opentracker_adminapi_getmenulinks()
{ 
    $menulinks = array();

    // Security Check
    if (xarSecurityCheck('AdminOpentracker', 0)) {
        $menulinks[] = Array('url' => xarModURL('opentracker',
                                                'admin',
                                                'modifyconfig'),
                             'title' => xarML('Modify the configuration for the Opentracker module'),
                             'label' => xarML('Modify Config'));
    } 

    return $menulinks;
} 
?>
