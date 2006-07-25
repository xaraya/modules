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
    $menulinks = array();

    if (xarSecurityCheck('AddXTask', 0)) {

        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'admin',
                                                   'new'),
                              'title' => xarML('Create a new project'),
                              'label' => xarML('New Task'));
    }

    if (xarSecurityCheck('ReadXTask', 0)) {
        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('List of current projects'),
                              'label' => xarML('View Tasks'));

        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'user',
                                                   'search'),
                              'title' => xarML('Query project entries'),
                              'label' => xarML('Search Tasks'));
    }

    if (xarSecurityCheck('AdminXTask', 0)) {

        $menulinks[] = Array('url'   => xarModURL('xtasks',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for XTask'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>