<?php 
/**
 * File: $Id$
 * 
 * Xaraya Censor
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Censor Module
 * @author John Cox
*/

function censor_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the autolinks item table
    $censor = xarConfigGetVar('prefix') . '_censor';

    // Set the table name
    $xartable['censor'] = $censor;

    // Return the table information
    return $xartable;
}

?>