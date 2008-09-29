<?php
/**
 * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function smilies_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AddSmilies', 0)) {

        $menulinks[] = Array('url'   => xarModURL('smilies',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new :) into the system'),
                              'label' => xarML('Add'));
    }

    if (xarSecurityCheck('EditSmilies', 0)) {

        $menulinks[] = Array('url'   => xarModURL('smilies',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit :)'),
                              'label' => xarML('View'));
    }
    if (xarSecurityCheck('AdminSmilies', 0)) {
        $menulinks[] = Array('url'   => xarModURL('smilies',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for the smilies'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>