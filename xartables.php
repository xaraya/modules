<?php
/**
 * Object table definitions function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage object
 * @author mikespub
 */

/**
 * Return object table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function object_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 

    // Return the table information
    return $xarTables;
} 

?>
