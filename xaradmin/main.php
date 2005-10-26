<?php
/**
 *
 * XarLDAP Administrative Display Functions
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * xarldap_admin_main
 *
 * The main administration function
 *
 * @author Richard Cave
 * @access public
 * @param  none
 * @return array containing the menulinks for the main menu items.
 * @throws none
 * @todo   none
 */
function xarldap_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminXarLDAP')) return;

    // return array from admin-main template
    return array();
}

?>
