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

function html_xartables()
{
    // Initialise table array
    $xartable = array();
    $prefix = xarDBGetSiteTablePrefix();

    // Set the prefix name for the html item table
    $html = $prefix . '_html';

    // Set the table name
    $xartable['html'] = $html;

    // Return the table information
    return $xartable;
}

?>
