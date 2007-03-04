<?php 

/**
 * Table definition file
 * @package calendar
 * @copyright (C) 2002 by the Xaraya Calendar Team
 */

/**
 * This function is called internally by the core 
 * whenever the module is loaded.
 */
function calendar_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 
    // Get the name for the example item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $calendarsTable = xarDBGetSiteTablePrefix() . '_calendars'; 
    // Set the table name
    $xarTables['calendars'] = $calendarsTable; 
    $calendarsTable = xarDBGetSiteTablePrefix() . '_calendars_files'; 
    $xarTables['calendars_files'] = $calendarsTable; 
    $calendarsTable = xarDBGetSiteTablePrefix() . '_calfiles'; 
    $xarTables['calfiles'] = $calendarsTable; 
    // Return the table information
    return $xarTables;
} ?>
