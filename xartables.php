<?php
/**
 * Example table definition functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */
/**
 * Example table definition functions
 * Return example table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * @author Example Module development team
 * @access private
 * @return array
 */
function example_xartables()
{ 
    /* Initialise table array */
    $xarTables = array();
    /* Get the name for the example item table.  This is not necessary
     * but helps in the following statements and keeps them readable
     */
    $exampleTable = xarDBGetSiteTablePrefix() . '_example';
    
    /* Set the table name */
    $xarTables['example'] = $exampleTable;
    /* Return the table information */
    return $xarTables;
}
?>