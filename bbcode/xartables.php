<?php 
/**
 * File: $Id$
 * 
 * Xaraya Advanced BBCode
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage BBCode Module
 * @author John Cox
*/

function bbcode_xartables()
{
    // Initialise table array
    $xartable = array();
    $prefix = xarDBGetSiteTablePrefix();
    // Get the name for the autolinks item table
    $bbcode = $prefix . '_bbcode';
    // Set the table name
    $xartable['bbcode'] = $bbcode;
    // Return the table information
    return $xartable;
}
?>