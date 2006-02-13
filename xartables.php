<?php
/**
 * Legis Module Table functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Legis table definition functions
 * Return Legis table names to xaraya
 *
 * @author Jo Dalle Nogare
 * @access private
 * @return array
 */
function legis_xartables()
{
    /* Initialise table array */
    $xarTables = array();

    $LegisMasterTable = xarDBGetSiteTablePrefix() . '_legis_master';
    $xarTables['legis_master'] = $LegisMasterTable;

    $LegisDocletsTable = xarDBGetSiteTablePrefix() . '_legis_doclets';
    $xarTables['legis_doclets'] = $LegisDocletsTable;

    $LegisCompiledTable = xarDBGetSiteTablePrefix() . '_legis_compiled';
    $xarTables['legis_compiled'] = $LegisCompiledTable;

    /* Return the table information */
    return $xarTables;
}
?>
