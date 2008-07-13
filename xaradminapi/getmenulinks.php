<?php
/**
 * Utility function to pass admin menu links
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the XTask module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xtasks_adminapi_getmenulinks()
{
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');

    $menulinks = array();
    
    $menulinks[] = Array('url'   => xarModURL('xtasks',
                                               'user',
                                               'settings'),
                          'title' => xarML('Change your task display preferences'),
                          'label' => xarML('Settings'));

    if (!xarSecurityCheck('ViewXTask', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'user',
                                                   'main'),
                              'title' => xarML('The overview of this module and its functions'),
                              'label' => xarML('Overview'));
    }

    if (xarSecurityCheck('AddXTask', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'user',
                                                   'quick'),
                              'title' => xarML('Quick Project Task Hours'),
                              'label' => xarML('Quick Form'));
        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Create a new project'),
                              'label' => xarML('New Task'));
    }

    if (xarSecurityCheck('ReadXTask', 0)) {
    
        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'admin',
                                                   'view',
                                                   array('memberid' => $mymemberid)),
                              'title' => xarML('Open tasks assigned to you'),
                              'label' => xarML('My Tasks'));
        $menulinks[] = Array('url'    => xarModURL('xtasks',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('Tasks you have assigned to other'),
                              'label' => xarML('Open Tasks'));
        $menulinks[] = Array('url'    => xarModURL('xtasks',
                                                   'admin',
                                                   'view',
                                                   array('status' => "Closed")),
                              'title' => xarML('Recently closed tasks'),
                              'label' => xarML('Archive'));

        $menulinks[] = Array('url'    => xarModURL('xtasks',
                                                   'user',
                                                   'search'),
                              'title' => xarML('Query project entries'),
                              'label' => xarML('Search Tasks'));
    }

    if (xarSecurityCheck('AdminXTask', 0)) {
        $menulinks[] = Array('url'    => xarModURL('xtasks',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the module configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>