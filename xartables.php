<?php
/**
 * Lists table definitions function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage lists
 */

/**
 * Return lists table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @author Lists module development team
 * @access private 
 * @return array 
 */
function lists_xartables()
{ 
    // Initialise table array
    $xarTables = array();
    $basename = 'lists';

    foreach(array('types', 'items') as $table) {
        // Set the table name.
        $xarTables[$basename . '_' . $table] = xarDBGetSiteTablePrefix() . '_' . $basename . '_' . $table;
    }

    // Return the table information
    return $xarTables;
} 

?>