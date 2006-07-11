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