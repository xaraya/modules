<?php
/**
 * File: $Id$
 * 
 * Xaraya Multisites
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
*/

function multisites_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the multisites item table
    $multisites = xarDBGetSiteTablePrefix() . '_multisites';

    // Set the table name
    $xartable['multisites'] = $multisites;

    // Return the table information
    return $xartable;
}

?>
