<?php
/**
 * AuthLDAP User API
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
 * @public
 * @author Richard Cave
 * @return array Array containing the menulinks for the main menu items.
 */
function authldap_userapi_getmenulinks()
{
    // No menu links for users
    $menulinks = array();
    return $menulinks;
}

?>