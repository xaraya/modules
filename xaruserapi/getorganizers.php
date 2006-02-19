<?php
/**
 * Get all events.
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
 * Get all organizers in the db
 *
 * This functions returns an array with a listing of organisers. Organizers are
  the user that have entered the events
 *
 * @author MichelV <michelv@xaraya.com>
 * @param array $args an array of arguments
 * @param int $args['startnum'] start with this item number (default 1)
 * @param int $args['numitems'] the number of items to retrieve (default -1 = all)
 * @param int $args['external'] retrieve events marked external (1=true, 0=false) - ToDo:
 * @param int $args['catid'] Category ID
 * @since 19 Feb 2006
 * @return array of org_id -> organizername
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function julian_userapi_getorganizers($args)
{
    // Get arguments
    extract($args);

    // Optional arguments.
    if(!isset($startnum)) {
        $startnum = 1;
    }

    if (!isset($numitems)) {
        $numitems = -1;
    }
    // Argument check.
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getorganizers', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $items = array();
    // Security check.
    if (!xarSecurityCheck('Viewjulian')) return;

    // Load categories API.
    // Needed?
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
        return false;
    }

    // Get database setup.
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $event_table = $xartable['julian_events'];
    // Get items.
    $query = "SELECT DISTINCT organizer,
                              calendar_id";

    // Select on categories
    if (xarModIsHooked('categories','julian') && !empty($catid)) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' =>
                                              xarModGetIDFromName('julian'),
                                             'catid' => $catid));
        $query .= " FROM ( $event_table
                  LEFT JOIN $categoriesdef[table]
                  ON $categoriesdef[field] = event_id )
                  $categoriesdef[more]
                  WHERE $categoriesdef[where] ";
    } else {
        $query .= " FROM $event_table ";
    }

    if (!empty($calendar_id) && is_numeric($calendar_id) && empty($catid)) {
        $query .= " WHERE calendar_id = $calendar_id ";
    } elseif (!empty($calendar_id) && is_numeric($calendar_id) && !empty($catid)) {
        $query .= " AND calendar_id = $calendar_id ";
    }
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);

    // Check for an error.
    if (!$result) return;

    // Check for no rows found.
    if ($result->EOF) {
        $result->Close();
        return $items;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($org_id, $calendarID) = $result->fields;
          // Security check
          if (xarSecurityCheck('ReadJulian', 1, 'Item', "All:$org_id:$calendarID:All")) {
             $items[$org_id] = xarUserGetVar('name',$org_id);
          }
    }
    // Close first result set
    $result->Close();
    // Return the items
    return $items;
}
?>
