<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 *
 * @public
 * @author John Cox 
 * @returns output
 * @return output with censor Menu information
 */
function html_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditHTML')) return;

    // Get the admin menu
    $data = xarModAPIFunc('html', 'admin', 'menu');

    // Return the template variables defined in this function
    return $data;
}

?>
