<?php

/**
 * utility function pass individual menu items to the main menu
 * 
 * @author the xarLinkMe module development team 
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xarlinkme_adminapi_getmenulinks()
{ 

    // Security Check
    if (xarSecurityCheck('AdminxarLinkMe', 0)) {
                                               	
        $menulinks[] = Array('url' => xarModURL('xarlinkme',
                'admin',
                'main'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Overview of the xarLinkMe Module'),
            'label' => xarML('Overview'));

        $menulinks[] = Array('url' => xarModURL('xarlinkme',
                'admin',
                'modifyconfig'),
            // In order to display the tool tips and label in any language,
            // we must encapsulate the calls in the xarML in the API.
            'title' => xarML('Configure the Link Me module'),
            'label' => xarML('Modify Config'));
     }

    // If we return nothing, then we need to tell PHP this, in order to avoid an ugly
    // E_ALL error.
    if (empty($menulinks)) {
        $menulinks = '';
    } 
    // The final thing that we need to do in this function is return the values back
    // to the main menu for display.
    return $menulinks;
} 

?>
