<?php
/**
 *
 * AuthLDAP Administrative Display Functions
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * the main administration function
 */
function authldap_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthLDAP')) return;

    // return array from admin-main template
    return array();
}

?>
