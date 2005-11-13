<?php
/**
 * File: $Id: s.xartables.php 1.7 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 * 
 * maxercalls table definitions function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage maxercalls
 * @author maxercalls module development team 
 */

/**
 * Return maxercalls table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function maxercalls_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 
    // Set the table name
    $xarTables['maxercalls'] = xarDBGetSiteTablePrefix() . '_maxercalls';
    $xarTables['maxercalls_types'] = xarDBGetSiteTablePrefix() . '_maxercalls_types';
    // Return the table information
    return $xarTables;
} 

?>
