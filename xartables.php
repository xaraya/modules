<?php
/**
 * File: $Id$
 * 
 * Sniffer table definitions
 * 
 * @package Xaraya eXtensible Management System
 * @subpackage Sniffer
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @author Frank Besler 
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