<?php
/**
 * Utility function to pass individual menu items to main menu
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Roles Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function encyclopedia_adminapi_getmenulinks()
{

    if (xarSecurityCheck('AddEncyclopedia',0,'Entry')) {
        $menulinks[] = Array('url'   => xarModURL('encyclopedia',
                                                  'admin',
                                                  'newentry'),
                              'title' => xarML('Add a new entry to the encyclopedia'),
                              'label' => xarML('Add Entry'));
    }

    if (xarSecurityCheck('EditEncyclopedia',0)) {
        $menulinks[] = Array('url'   => xarModURL('encyclopedia',
                                                  'admin',
                                                  'getrecent'),
                              'title' => xarML('Review recent entries to the encyclopedia'),
                              'label' => xarML('Recent Entries'));
    }

    if (xarSecurityCheck('EditEncyclopedia',0)) {
        $menulinks[] = Array('url'   => xarModURL('encyclopedia',
                                                  'admin',
                                                  'inactive'),
                              'title' => xarML('View inactive entries in the encyclopedia'),
                              'label' => xarML('Inactive Entries'));
    }

    if (xarSecurityCheck('EditEncyclopedia',0)) {
        $menulinks[] = Array('url'   => xarModURL('encyclopedia',
                                                  'admin',
                                                  'volumes'),
                              'title' => xarML('Add or change volumes in the encyclopedia'),
                              'label' => xarML('Manage Volumes'));
    }

    if (xarSecurityCheck('AdminEncyclopedia',0)) {
        $menulinks[] = Array('url'   => xarModURL('encyclopedia',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the encyclopedia configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>