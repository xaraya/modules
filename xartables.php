<?php
/**
 * File: $Id$
 * 
 * Scheduler table definitions function
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage scheduler
 * @author mikespub
 */

/**
 * Return scheduler table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function scheduler_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 

    // Return the table information
    return $xarTables;
} 

?>
