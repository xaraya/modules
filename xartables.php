<?php
/**
 * Xaraya Smilies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, John Cox, Mikespub
*/

function smilies_xartables()
{
    // Initialise table array
    $xartable = array();
    $prefix = xarDBGetSiteTablePrefix();
    // Get the name for the autolinks item table
    $smilies = $prefix . '_smilies';

    // Set the table name
    $xartable['smilies'] = $smilies;

    // Return the table information
    return $xartable;
}

?>