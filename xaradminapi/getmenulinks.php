<?php

/**
 * File: $Id$
 *
 * BlackList API 
 *
 * @package Modules
 * @copyright (C) 2002-2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BlackList
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
*/

/**
 * Passes individual menu items to the main menu
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private 
 * @return array Array of menulinks to add to the main menu.
 */
function blacklist_adminapi_getmenulinks()
{
    $menulinks[] = Array('url'   => xarModURL('blacklist', 'admin', 'main'),
                          'title' => xarML('An Overview of the blacklist Module'),
                          'label' => xarML('Overview'));

    $menulinks[] = Array('url'   => xarModURL('blacklist', 'admin', 'view'),
                         'title' => xarML('View blacklist domain patterns'),
                         'label' => xarML('View blacklist'));

    $menulinks[] = Array('url'   => xarModURL('blacklist', 'admin', 'modifyconfig'),
                         'title' => xarML('Modify the blacklist module configuration'),
                         'label' => xarML('Modify Config'));

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
