<?php
/**
 * Count all events.
 *
 * @package modules
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Utility function to count the number of Events in the Calendar.
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author MichelV <michelv@xaraya.com>
 * @param $args an array of arguments
 * @param int $args['event_id'] The ID of the Event
 * @param int $args['external'] retrieve events marked external (1=true, 0=false) - ToDo:
 * @return integer number of items
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @todo MichelV: Include count of linked items
 *       MichelV: Include categories in the count
 */
function julian_userapi_countevents($args)
{
    // Security check
    if (!xarSecurityCheck('ViewJulian')) return;

    // Get arguments from argument array
    extract($args);

    // Set defaults
    if (!isset($event_id)) {
        $event_id = 0;
    }
    if (!isset($cids)) {
        $cids = array();
    }
    if (!isset($andcids)) {
        $andcids = false;
    }
/*  Haven't looked at Archives yet.
    if (!isset($external)) {
        $external = 0;
    }
*/

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // Set Events Table.
    $event_table = $xartable['julian_events'];

    // Create a query to select Events.
    $bindvars = array();
    /*
    if ($event_id) {
        // Get the list of Events.
        $query = "SELECT COUNT(1)
                  FROM  $event_table
                  WHERE $event_table.event_id = ?
                  AND   $event_table.event_id != 0";
        $bindvars[] = array($event_id);
    } else {
        // Get all Events
        $query = "SELECT COUNT(1) FROM $event_table
                  WHERE $event_table.event_id != 0";
    }
    */

    if (xarModIsHooked('categories','julian')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('julian'),
                                             'itemtype' => NULL,
                                             'cids' => $cids,
                                             'andcids' => $andcids));
        $query = "SELECT COUNT(*)
                  FROM ( $event_table
                  LEFT JOIN $categoriesdef[table]
                  ON $categoriesdef[field] = event_id )
                  $categoriesdef[more]
                  WHERE $categoriesdef[where]
                  AND $event_table.event_id != 0";
    } else {
        $query = "SELECT COUNT(*)
                  FROM $event_table
                  WHERE $event_table.event_id != 0";
    }

    if ($event_id) {
        $query .= " AND $event_table.event_id = ? ";
        $bindvars[] = array($event_id);
    }




    // Check if we want to display external issues.  This is only
    // applicable to viewing issue archives.
/*    if ($external) {
        $query .= " AND $issuesTable.xar_external = 1";
    }
*/

    $result = $dbconn->Execute($query, $bindvars);
    $noresult = 0;
    // Check for an error
    if (!$result) return $noresult;

    // Obtain the number of items
    list($numitems) = $result->fields;
    // Close result set
    $result->Close();
    // bug 4833: Turn result into Integer
    $numitems = (INT)$numitems;
    // Return the number of items
    return $numitems;
}

?>
