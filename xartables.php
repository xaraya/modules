<?php
/**
 * File: $Id$
 * 
 * Change Log table definitions function
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage changelog
 * @author mikespub
 */

/**
 * Return changelog table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function changelog_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 
    // Get the name for the changelog item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $changelogTable = xarDBGetSiteTablePrefix() . '_changelog'; 
    // Set the table name
    $xarTables['changelog'] = $changelogTable; 
    // Return the table information
    return $xarTables;
} 

?>
