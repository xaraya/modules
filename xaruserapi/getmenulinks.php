<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * utility function pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function newsletter_userapi_getmenulinks()
{
    $menulinks = array();

    // Check if user is logged in
    if (xarUserIsLoggedIn()) {
        $menulinks[] = Array('url'   => xarModURL('newsletter',
                                                  'user',
                                                  'newsubscription'),
                             'title' => xarML('Subscribe to a Newsletter'),
                             'label' => xarML('Subscribe'));
    }

    // Show past issues
    $menulinks[] = Array('url'   => xarModURL('newsletter',
                                              'user',
                                              'viewarchives'),
                         'title' => xarML('View archives of past issues'),
                         'label' => xarML('View Archives'));

    if (empty($menulinks)) {
        $menulinks = '';
    }

    return $menulinks;
}

?>
