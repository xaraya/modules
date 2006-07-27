<?php
/**
 *
 * Function xartables
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  Table information on this module
 *
 * @param to be added
 * @return array of information on the database tables used by this module
 *
 */

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function gmaps_xartables()
{
    // Initialise table array
    $xartable = array();

//    $gmaps = xarDBGetSiteTablePrefix() . '_gmaps';

    // Set the table name
//    $xartable['gmaps'] = $gmaps;

    // Return the table information
    return $xartable;
}

?>
