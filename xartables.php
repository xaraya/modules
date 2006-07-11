<?php
/**
 * AuthLDAP
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
 * Return authldap table names to xaraya
 * 
 * @return array 
 */
function authldap_xartables()
{ 
    return array('authldap_usercache'
         => xarDBGetSiteTablePrefix().'_authldap_usercache');
} 
?>