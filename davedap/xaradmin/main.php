<?php
/*
 * File: $Id: $
 *
 * DaveDAP 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team 
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage davedap module
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
function davedap_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminDaveDAP')) return;

    // Get the admin menu
    $data['menutitle'] = xarML('DaveDAP'); 

    // Return the template variables defined in this function
    return $data;
}

?>
