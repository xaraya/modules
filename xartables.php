<?php
/**
 * ITSP table definition functions
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * ITSP table definition functions
 * Return itsp table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * @author ITSP Module development team
 * @access private
 * @return array
 */
function itsp_xartables()
{
    /* Initialise table array */
    $xarTables = array();
    /* Get the name for the itsp item table.  This is not necessary
     * but helps in the following statements and keeps them readable
     */
    $basename = 'itsp';

    foreach(array('plans', 'planitems', 'planlinks', 'itsp', 'itsp_courselinks', 'itsp_courses') as $table) {
        // Set the table name.
        $xarTables[$basename . '_' . $table] = xarDBGetSiteTablePrefix() . '_' . $basename . '_' . $table;
    }

    /* Return the table information */
    return $xarTables;
}
?>