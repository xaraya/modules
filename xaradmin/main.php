<?php
/**
 * File: $Id$
 *
 * Sniffer Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Sniffer Module
 * @author Frank Besler
 *
 * Using phpSniffer by Roger Raymond
 * Purpose of file: find out the browser and OS of the visitor
*/

/**
 * Add a standard screen upon entry to the module.
 *
 * @public
 * @author Richard Cave
 * @returns output
 * @return output with censor Menu information
 */
function sniffer_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminSniffer')) return;

    // Get the admin menu
    $data = xarModAPIFunc('sniffer', 'admin', 'menu');

    // Return the template variables defined in this function
    return $data;
}

?>
