<?php
/**
 * File: $Id$
 *
 * paypalipn table defintions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html} 
 * @link http://www.xaraya.com
 *
 * @subpackage paypalipn
 * @author John Cox (niceguyeddie@xaraya.com)
 */
/**
 * Passes table definitons back to Xaraya core
 *
 * @return string
 */
function paypalipn_xartables()
{
    // Initialise table array
    $tables = array();
    $systemPrefix = xarDBGetSystemTablePrefix();
    // Assign to the array
    $tables['ipnlog'] = $systemPrefix . '_ipnlog';
    // Return the table information
    return $tables;
}
?>