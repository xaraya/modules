<?php
/**
 * File: $Id:
 *
 * Utility function to pass individual menu items to the main menu
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function ebulletin_userapi_getmenulinks()
{
    // initialize array of links
    $menulinks = array();

    // security check
    if (xarSecurityCheck('ReadeBulletin', 0)) {

        // My Subscriptions
        $menulinks['subscriptions'] = array(
            'url' => xarModURL('ebulletin', 'user', 'main'),
            'title' => xarML('Modify my subscriptions.'),
            'label' => xarML('Subscriptions')
        );

        // Archive
        $menulinks['archive'] = array(
            'url' => xarModURL('ebulletin', 'user', 'view'),
            'title' => xarML('View newsletter archive.'),
            'label' => xarML('Archive')
        );

    }

    return $menulinks;
}

?>
