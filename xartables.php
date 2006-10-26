<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * Return changelog table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array
 */
function changelog_xartables()
{
    // Initialise table array
    $xarTables = array();
    // Get the name for the changelog item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $changelogTable = xarDBGetSiteTablePrefix() . '_changelog';
    // Set the table name
    $xarTables['changelog'] = $changelogTable;
    // Return the table information
    return $xarTables;
}

?>
