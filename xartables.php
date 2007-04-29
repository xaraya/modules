<?php

/**
 * File: $Id$
 * 
 * Xarpages table definitions function.
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Jason Judge 
 */

/**
 * Return xarpages table names to xaraya.
 * 
 * @access private 
 * @return array 
 */

function ievents_xartables()
{ 
    // Initialise table array.
    $xarTables = array();
    $basename = 'ievents';

    // Loop for each table.
    foreach(array('events', 'calendars') as $table) {
        // Set the table name.
        $xarTables[$basename . '_' . $table] = xarDBGetSiteTablePrefix() . '_' . $basename . '_' . $table;
    }

    // Return the table information
    return $xarTables;
} 

?>