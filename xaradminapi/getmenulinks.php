<?php
/**
 * Utility function to pass individual menu items to the main menu
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xarbb_adminapi_getmenulinks()
{

 
    if (xarSecurityCheck('AddxarBB', 0,'Forum')) {

        $menulinks[] = Array('url'   => xarModURL('xarbb',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a New forum'),
                              'label' => xarML('Add'));
    }

    if (xarSecurityCheck('EditxarBB', 0,'Forum')) {

        $menulinks[] = Array('url'   => xarModURL('xarbb',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit Forums'),
                              'label' => xarML('View'));
    }
    if (xarSecurityCheck('EditxarBB', 0,'Forum')) {

        $menulinks[] = Array('url'   => xarModURL('xarbb',
                                                  'admin',
                                                  'reorder'),
                              'title' => xarML('Reorder Forums'),
                              'label' => xarML('Reorder Forums'));
    }
    if (xarSecurityCheck('AdminxarBB', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xarbb',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration for the XarBB'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
