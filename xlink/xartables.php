<?php
/**
 * File: $Id$
 * 
 * CrossLink table definitions function
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage xlink
 * @author mikespub
 */

/**
 * Return xlink table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function xlink_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 
    // Get the name for the xlink item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $xlinkTable = xarDBGetSiteTablePrefix() . '_xlink'; 
    // Set the table name
    $xarTables['xlink'] = $xlinkTable; 
    // Return the table information
    return $xarTables;
} 

?>
