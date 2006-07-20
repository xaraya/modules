<?php
/**
 * Sniffer System
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
 */
/**
 * This function is called internally by the core whenever the module is loaded
 *
 * @access private
 * @return array
 */
function sniffer_xartables()
{
    // Initialise table array
    $xarTables = array();

    $sniffer = xarDBGetSiteTablePrefix() . '_sniffer';
    // Set the table name
    $xarTables['sniffer'] = $sniffer;
    // Return the table information
    return $xarTables;
}

?>