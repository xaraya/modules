<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Add information about HTML module tables to xartables array
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @return true on success, false on failure
 * @throws none
 */
function html_xartables()
{
    // Initialise table array
    $xartable = array();
    $prefix = xarDB::getPrefix();

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
