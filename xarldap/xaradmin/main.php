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
