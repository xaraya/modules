<?php
/**
 * AuthSQL Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSQL Module
 * @link http://xaraya.com/index.php/release/10512.html
 * @author Roger Keays and James Cooper
*/

/**
 * utility function pass individual menu items to the main menu
 *
 * @author James Cooper
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function authsql_adminapi_getmenulinks()
{
    // Security check 
    if(xarSecurityCheck('AdminAuthSQL')) {
        $menulinks[] = Array('url'   => xarModURL('authsql', 'admin', 'modifyconfig'),
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}

?>