<?php 
/**
 * File: $Id$
 * 
 * Xaraya xarbb
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage xarbb Module
 * @author John Cox
*/

function xarbb_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the autolinks item table
    $xbbforums = xarConfigGetVar('prefix') . '_xbbforums';
    $xbbtopics = xarConfigGetVar('prefix') . '_xbbtopics';

    // Set the table name
    $xartable['xbbforums'] = $xbbforums;
    $xartable['xbbtopics'] = $xbbtopics;

    // Return the table information
    return $xartable;
}

?>