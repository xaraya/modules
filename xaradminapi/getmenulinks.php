<?php
/**
 * File: $Id: getmenulinks.php,v 1.1.1.1 2003/11/20 05:35:21 roger Exp $
 *
 * AuthSQL Administrative Display Functions
 * 
 * @copyright (C) 2003 ninthave
 * @author James Cooper jbt_cooper@bigpond.com
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
        $menulinks[] = Array('url'   => xarModURL('authsql',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the module'),
                              'label' => xarML('Modify Config'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}

?>
