<?php
/**
 * Subitems initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */

/**
 * Return Subitems table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array
 */
function subitems_xartables()
{
    // Initialise table array
    $xarTables = array();
    // Get the name for the example item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $exampleTable = xarDBGetSiteTablePrefix() . '_subitems';
    // Set the table name
    $xarTables['subitems_ddids'] = $exampleTable.'_ddids';
    $xarTables['subitems_ddobjects'] = $exampleTable.'_ddobjects';
    // Return the table information
    return $xarTables;
}

?>