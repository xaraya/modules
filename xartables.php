<?php
/**
 * File: $Id$
 * 
 * Paid Membership table definitions function
 * 
 * @copyright (C) 2003 by the Wyome Consulting, LLC
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.wyome.com
 * @subpackage pmember
 * @author John Cox <john.cox@wyome.com>
 */
/**
 * Return pmember table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function pmember_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 
    $pmemberTable = xarDBGetSiteTablePrefix() . '_pmember'; 
    // Set the table name
    $xarTables['pmember'] = $pmemberTable; 
    // Return the table information
    return $xarTables;
} 
?>