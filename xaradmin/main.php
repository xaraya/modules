<?php
/*
 * File: $Id: $
 *
 * phpLDAPadmin 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team 
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage phpldapadmin module
 * @author Richard Cave <rcave@schwabfoundation.org : rcave@xaraya.com>
 * @link http://xavier.schwabfoundation.org
*/

/**
 * the main administration function
 *
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function phpldapadmin_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminphpLDAPadmin')) return;

    // Get the admin menu
    $data['menutitle'] = xarML('phpLDAPadmin'); 

    // Return the template variables defined in this function
    return $data;
}

?>
