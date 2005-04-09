<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
*/


/**
 * utility function pass individual menu items to the main menu
 *
 * @author mikespub
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function keywords_adminapi_getmenulinks()
{
    $menulinks = array();
    // Security Check
    if (xarSecurityCheck('AdminKeywords')) {
        /* Removing the view function due to usuability.  Seems over complicated, since most of the editing is done via the original item.
        $menulinks[] = Array('url'   => xarModURL('keywords',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('Overview of the keyword assignments'),
                              'label' => xarML('View Keywords'));
        */
        $menulinks[] = Array('url'   => xarModURL('keywords',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the keywords configuration'),
                              'label' => xarML('Modify Config'));
    }

    return $menulinks;
}
?>