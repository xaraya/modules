<?php
/**
 * File: $Id: getmenulinks.php,v 1.1 2005/06/23 05:57:00 root Exp $
 *
 * AuthLDAP Administrative Display Functions
 * 
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * utility function pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function authldap_adminapi_getmenulinks()
{
  // Security check 
  if(xarSecurityCheck('AdminAuthLDAP')) {
    $menulinks[] = Array('url'   => xarModURL('authldap',
                          'admin',
                          'modifyconfig'),
             'title' => xarML('Modify the configuration for the module'),
             'label' => xarML('Modify Config'));
    $menulinks[] = Array('url'   => xarModURL('authldap',
                          'admin',
                          'manuallysyncgroups'),
             'title' => xarML('Forces group synchronization with LDAP'),
             'label' => xarML('Manually Sync Groups'));
  } else {
    $menulinks = '';
  }
  
  return $menulinks;
}
?>
