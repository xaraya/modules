<?php 
/**
 * File: $Id$
 * 
 * Xaraya Autolinks
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Autolinks Module
 * @author Jim McDonald
*/

function autolinks_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the autolinks item table
    $autolinks = xarDBGetSiteTablePrefix() . '_autolinks';

    // Set the table name
    $xartable['autolinks'] = $autolinks;

    // Return the table information
    return $xartable;
}

?>
