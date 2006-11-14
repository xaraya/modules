<?php
/**
 * AuthLDAP Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/50.html
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
*/
/**
 * utility function pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @return array Array containing the menulinks for the main menu items.
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