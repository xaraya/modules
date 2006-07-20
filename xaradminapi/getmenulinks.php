<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function scheduler_adminapi_getmenulinks()
{
    $menulinks = array();
    if (xarSecurityCheck('AdminScheduler', 0)) {
        $menulinks[] = Array('url' => xarModURL('scheduler', 'admin', 'search'),
                             'title' => xarML('Search for scheduler API functions'),
                             'label' => xarML('Find Functions'));
        $menulinks[] = Array('url' => xarModURL('scheduler', 'admin', 'modifyconfig'),
                             'title' => xarML('Modify the configuration for the module'),
                             'label' => xarML('Modify Config'));
    }
    return $menulinks;
}

?>
