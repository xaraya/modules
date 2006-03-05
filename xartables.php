<?php
/**
 * Define table information for Stats module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * This function is called internally by the core whenever the module is loaded
 *
 * The function stats_xartables() defines the name of the database tables
 * that are used (owned) by the Stats module. Its for abstraction purpose.
 *
 * @access  private
 * @param   none
 * @return  array $xartable - holds all table names used in the system
 */
function stats_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the template item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $stats = xarDBGetSiteTablePrefix() . '_stats';
    $sniffer = xarDBGetSiteTablePrefix() . '_sniffer';

    // Set the table name
    $xartable['stats'] = $stats;
    $xartable['sniffer'] = $sniffer;

    // Return the table information
    return $xartable;
}

?>
