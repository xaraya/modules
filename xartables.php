<?php 
/**
 * File: $Id$
 * 
 * Xaraya html
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage html Module
 * @author John Cox
*/

/**
 * Add information about HTML module tables to xartables array
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @return true on success, false on failure
 * @raise none
 */
function html_xartables()
{
    // Initialise table array
    $xartable = array();
    $prefix = xarDBGetSiteTablePrefix();

    // Set the prefix name for the html table
    $html = $prefix . '_html';

    // Set the table name
    $xartable['html'] = $html;

    // Set the prefix name for the html types table
    $htmltypes = $prefix . '_htmltypes';

    // Set the table name
    $xartable['htmltypes'] = $htmltypes;

    // Return the table information
    return $xartable;
}

?>
