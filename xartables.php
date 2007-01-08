<?php
/**
 * SIGMAPersonnel table definitions function
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author Michel V.
 */
/**
 * Return sigmapersonnel table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array
 */
function sigmapersonnel_xartables()
{
    // Initialise table array
    $xarTables = array();
    // Get the name for the sigmapersonnel item table.
    $sigmapersonneltable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_person';
    $xarTables['sigmapersonnel_person'] = $sigmapersonneltable;

    $presencetable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_presence';
    $xarTables['sigmapersonnel_presence'] = $presencetable;

    $presencetypestable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_presencetypes';
    $xarTables['sigmapersonnel_presencetypes'] = $presencetypestable;

    $citiestable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_cities';
    $xarTables['sigmapersonnel_cities'] = $citiestable;

    $statustable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_status';
    $xarTables['sigmapersonnel_status'] = $statustable;

    $districtstable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_districts';
    $xarTables['sigmapersonnel_districts'] = $districtstable;
    // Return the table information
    return $xarTables;
}

?>
