<?php
/**
 * File: $Id: s.xartables.php 1.7 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 * 
 * Example table definitions function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team 
 */

/**
 * Upgraded to the new security schema by Vassilis Stratigakis
 * http://www.tequilastarrise.net
 */

/**
 * Return example table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function example_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 
    // Get the name for the example item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $exampleTable = xarDBGetSiteTablePrefix() . '_example'; 
    // Set the table name
    $xarTables['example'] = $exampleTable; 
    // Return the table information
    return $xarTables;
} 

?>
