<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function photoshare_userapi_getmenulinks()
{
    $menulinks = array();

    // Security Check
    if (!xarSecurityCheck('ViewFolder',0)) {
        return $menulinks;
    }

    $menulinks[] = Array('url'   => xarModURL('photoshare',
                                              'user',
                                              'viewallfolders'),
                         'title' => xarML('View all photo albums'),
                         'label' => xarML('All albums'));

    if (xarSecurityCheck('EditFolder',0)) {
	    $menulinks[] = Array('url'   => xarModURL('photoshare',
	                                              'user',
	                                              'view'),
	                         'title' => xarML('Edit your own album'),
	                         'label' => xarML('My Albums'));
    }
    return $menulinks;
}

?>
