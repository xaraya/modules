<?php
/**
 * File: $Id$
 *
 * XarLDAP Administrative Display Functions
 * 
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * xarldap_adminapi_getmenulinks
 *
 * Utility function pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @access public
 * @param  none
 * @return array containing the menulinks for the main menu items.
 * @throws none
 * @todo   none
 */
function xarldap_adminapi_getmenulinks()
{
    // Security check 
    if(xarSecurityCheck('AdminXarLDAP')) {
        $menulinks[] = Array('url'   => xarModURL('xarldap',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the configuration for the module'),
                             'label' => xarML('Modify Config'));

        $menulinks[] = Array('url'   => xarModURL('xarldap',
                                                  'admin',
                                                  'connecttest'),
                             'title' => xarML('Test the connection to the LDAP server'),
                             'label' => xarML('Test Connection'));
 
        $menulinks[] = Array('url'   => xarModURL('xarldap',
                                                  'admin',
                                                  'usersearch'),
                             'title' => xarML('Search for a user on the LDAP server'),
                             'label' => xarML('User Search'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}

?>
