<?php
/**
 * File: $Id: s.xartables.php 1.7 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * Events table definitions function
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage events
 * @author Events module development team
 */

/**
 * Upgraded to the new security schema by Vassilis Stratigakis
 * http://www.tequilastarrise.net
 */

/**
 * Return events table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array 
 */
function events_xartables()
{
    // Initialise table array
    $xarTables = array();

    // Get the name for the events item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $eventsTable = xarDBGetSiteTablePrefix() . '_events';

    // Set the table name
    $xarTables['events'] = $eventsTable;

    // Return the table information
    return $xarTables;
}

?>