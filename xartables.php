<?php
/**
 * File: $Id:
 * 
 * xarcpshop table definitions function
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 */

/**
 * Return xarcpshop table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array
 */
function xarcpshop_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 
    // Get the name for the xarcpshop item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $cpstorestable = xarDBGetSiteTablePrefix() . '_cpstores';
    $cpitemstable  = xarDBGetSiteTablePrefix() . '_cpitems';
    $cptypestable  = xarDBGetSiteTablePrefix() . '_cptypes';
    // Set the table name
    $xarTables['cpstores'] = $cpstorestable;
    $xarTables['cpitems'] = $cpitemstable;
    $xarTables['cptypes'] = $cptypestable;
    // Return the table information
    return $xarTables;
} 

?>
