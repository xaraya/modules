<?php
/**
 * File: $Id$
 * 
 * Ephemerids
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Ephemerids Module
 * @author Volodymyr Metenchuk
*/

function ephemerids_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for ephemerids database entities
    $ephem = xarDB::getPrefix() . '_ephem';

    // Table name
    $xartable['ephem'] = $ephem;

    return $xartable;
}
?>