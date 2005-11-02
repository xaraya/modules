<?php
/*
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * the main administration function
 *
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_configdesc()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'configmenu');

    // Return the template variables defined in this function
    return $data;
}

?>
