<?php

/**
 * utility function pass individual menu items to the main menu
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2009 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xtasks module
 * @author Chad Kraeft <stego@xaraya.com>
 *
 * @author the xTasks module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function xtasks_userapi_getmenulinks()
{
    $menulinks = array();
    
    if (xarSecurityCheck('ViewXTask', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'user',
                                                   'main'),
                              'active'=> array('main'),
                              'title' => xarML('The overview of this module and its functions'),
                              'label' => xarML('Overview'));
    }

    if (xarSecurityCheck('AddXTask', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Create a new task'),
                              'label' => xarML('New Task'));
    }

    if (xarSecurityCheck('ReadXTask', 0)) {
/*
        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('Open tasks assigned to you'),
                              'label' => xarML('My Tasks'));
        $menulinks[] = Array('url'    => xarModURL('xtasks',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('Tasks you have assigned to other'),
                              'label' => xarML('Open Tasks'));
        $menulinks[] = Array('url'    => xarModURL('xtasks',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('Recently closed tasks'),
                              'label' => xarML('Archive'));

        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'admin',
                                                   'search'),
                              'title' => xarML('Query project entries'),
                              'label' => xarML('Search Tasks'));
*/
        $menulinks[] = Array('url'    => xarModURL('xtasks',
                                                   'user',
                                                   'taskarchive'),
                              'active'=> array('taskarchive'),
                              'title' => xarML('Task Archive'),
                              'label' => xarML('Archive'));
    }

    $menulinks[] = Array('url'   => xarModURL('xtasks',
                                               'user',
                                               'settings'),
                          'active'=> array('settings'),
                          'title' => xarML('Change your task display preferences'),
                          'label' => xarML('Settings'));

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>
