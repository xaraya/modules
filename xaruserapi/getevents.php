<?php
/**
 * Get all events.
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Get all Julian Calendar Event items.
 *
 * This functions returns an array with a listing of events. The events
 * are not formatted for display. When a calendar oriented listing is needed,
 * use xaruser-getall.php
 *
 * initial template: Roger Raymond
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @param array  $args an array of arguments
 * @param int    startnum start with this item number (default 1)
 * @param int    numitems the number of items to retrieve (default -1 = all)
 * @param string sortby sort by 'date', 'eventName', 'eventCat', 'eventLocn', 'eventCont' or 'eventFee'
 * @param int    external retrieve events marked external (1=true, 0=false) - ToDo:
 * @param string orderby order by 'ASC' or 'DESC' (default = ASC)
 * @param int    catid Category ID
 * @param string startdate Start date in (Ymd) YYYYMMDD format; default: current day
 * @param string enddate End date in (Ymd) YYYYMMDD format
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @todo MichelV: rewrite some queries for pgsql
 * @todo Combine getevents and getall APIs - they are just too similar to warrent being separate
 */
function julian_userapi_getevents($args)
{
    // Security check.
    if (!xarSecurityCheck('ViewJulian')) return;

    // Get arguments
    extract($args);

    // Optional arguments.
    if (!isset($startnum)) $startnum = 1;
    if (!isset($numitems)) $numitems = -1;
    if (!isset($sortby)) $sortby = 'eventDate';
    if (!isset($orderby)) $orderby = 'ASC';

    // Default the start date to today.
    if (!isset($startdate)) $startdate = date('Ymd');

    // Argument check.
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1)', join(', ',$invalid));
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Items to return.
    $items = array();

    // Make an admin adjustable time format
    $dateformat = xarModGetVar('julian', 'dateformat');

    // Load categories API.
    // Needed?
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
        return false;
    }

    // Get database setup.
    $dbconn =& xarDBGetConn();

    // Get database tables.
    $xartable =& xarDBGetTables();

    // Set Events Table and Column definitions.
    $event_table = $xartable['julian_events'];

    // Get items.
    $query = 'SELECT DISTINCT ev.event_id,'
        . ' ev.calendar_id, ev.summary, ev.description,'
        . ' ev.street1, ev.street2, ev.city, ev.state, ev.zip,'
        . ' ev.email, ev.phone, ev.location, ev.url,'
        . ' ev.contact, ev.organizer,'
        . ' ev.dtstart, ev.recur_until, ev.duration,'
        . ' ev.rrule, ev.isallday, ev.fee';

    // Select on categories
    if (xarModIsHooked('categories', 'julian') && !empty($catid)) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories', 'user', 'leftjoin',
            array('modid' => xarModGetIDFromName('julian'), 'catid' => $catid)
        );

        $query .= ' FROM (' . $event_table. ' AS ev'
            . ' LEFT JOIN ' . $categoriesdef['table']
            . ' ON ' . $categoriesdef['field'] . ' = ev.event_id)'
            . $categoriesdef['more']
            . ' WHERE ' . $categoriesdef['where'];
    } else {
        $query .= ' FROM ' . $event_table . ' AS ev WHERE 1 = 1';
    }

    // FIXME: date formats should be validated
    // The dtstart column is a datetime type, and takes the format 'YYYYMMDDHHMMSS'
    if ((!empty($startdate)) && (!empty($enddate))) {
        $query .= " AND ev.dtstart BETWEEN '${startdate}000000' AND '${enddate}235959'";
    } elseif (!empty($startdate)) {
        $query .= " AND ev.dtstart >= '${startdate}000000'";
    } elseif (!empty($enddate)) {
        $query .= " AND ev.dtstart <= '${enddate}235959'";
    }

    // This is double now, as the array is being sorted anyway
    if (isset($sortby)) {
        switch ($sortby) {
            case 'eventDate':
                $query .= " ORDER BY ev.dtstart $orderby";
                break;
            case 'eventName':
                $query .= " ORDER BY ev.summary $orderby";
                break;
            case 'eventDesc':
                $query .= " ORDER BY ev.description $orderby";
                break;
            case 'eventLocn':
                $query .= " ORDER BY ev.location $orderby";
                break;
            case 'eventCont':
                $query .= " ORDER BY ev.contact $orderby";
                break;
            case 'eventFee':
                $query .= " ORDER BY ev.fee $orderby";
                break;
        }
    }

    // FIXME: this query does not restrict on start and end dates.
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
        list($eID,
            $eCalendarID, $eName, $eDescription,
            $eStreet1, $eStreet2, $eCity, $eState, $eZip,
            $eEmail, $ePhone, $eLocation, $eUrl,
            $eContact, $eOrganizer,
            $eStart['timestamp'], $eRecur['timestamp'], $eDuration,
            $eRrule, $eIsallday, $eFee
        ) = $result->fields;

        // Security check
        if (xarSecurityCheck('ReadJulian', 0, 'Item', "$eID:$eOrganizer:$eCalendarID:All")) {
            // Change date formats from UNIX timestamp to something readable.
            // TODO: why do we need all this display stuff in here?
            if ($eStart['timestamp'] == 0 || empty($eStart['timestamp'])) {
                $eStart['mon'] = "";
                $eStart['day'] = "";
                $eStart['year'] = "";
                $eStart['linkdate'] = '';
                $eStart['viewdate'] = '';
            } else {
                $eStart['linkdate'] = date("Ymd", strtotime($eStart['timestamp']));
                $eStart['viewdate'] = date("$dateformat", strtotime($eStart['timestamp']));
            }

            if ($eRecur['timestamp'] == 0 || empty($eRecur['timestamp'])) {
                $eRecur['mon'] = "";
                $eRecur['day'] = "";
                $eRecur['year'] = "";
                $eRecur['linkdate'] = '';
                $eRecur['viewdate'] = '';
            } else {
                $eRecur['linkdate'] = date("Ymd", strtotime($eRecur['timestamp']));
                $eRecur['viewdate'] = date("$dateformat", strtotime($eRecur['timestamp']));
            }

            $items[] = array(
                'eID' => $eID,
                'eName' => $eName,
                'eDescription' => $eDescription,
                'eStreet1' => $eStreet1,
                'eStreet2' => $eStreet2,
                'eCity' => $eCity,
                'eState' => $eState,
                'eZip' => $eZip,
                'eEmail' => $eEmail,
                'ePhone' => $ePhone,
                'eLocation' => $eLocation,
                'eUrl' => $eUrl,
                'eContact' => $eContact,
                'eOrganizer' => $eOrganizer,
                'eStart' => $eStart,
                'i_moduleid' => '',
                'i_itemtype' => '',
                'i_itemid' => '',
                'i_DateTime' => strtotime($eStart['timestamp']),
                'eRecur' => $eRecur,
                'eDuration' => $eDuration,
                'eRrule' => $eRrule,
                'eIsallday' => $eIsallday,
                'eFee' => $eFee
            );
        }
    }

    // Close first result set
    $result->Close();

    // Get the linked events
    $event_linkage_table = $xartable['julian_events_linkage'];
    $query_linked = 'SELECT DISTINCT event_id,'
        . ' hook_modid, hook_itemtype, hook_iid,'
        . ' summary, dtstart, duration, isallday,'
        . ' rrule, recur_freq, recur_count, recur_until, recur_interval'
        . ' FROM ' . $event_linkage_table . ' AS el';

    if ((!empty($startdate)) && (!empty($enddate))) {
        // FIXME: dates should be quoted at least; check and validate formats
        // FIXME: allow for unset enddate
        $query_linked .= " WHERE dtstart BETWEEN '${startdate}000000' AND '${enddate}235959'";
    } elseif (!empty($startdate)) {
        $query_linked .= " WHERE dtstart >= '${startdate}000000'";
    } elseif (!empty($enddate)) {
        $query_linked .= " WHERE dtstart <= '${enddate}235959'";
    }

    // TODO: include all the other ordering options
    if (isset($sortby)) {
        switch ($sortby) {
            case 'eventDate':
                $query_linked .= " ORDER BY el.dtstart $orderby";
                break;
            case 'eventName':
                $query_linked .= " ORDER BY el.summary $orderby";
                break;
        }
    }

    $result_linked =& $dbconn->Execute($query_linked);
    if (!$result_linked) return $items;

    // Check for no rows found.
    if ($result_linked->EOF) {
        $result_linked->Close();
        return $items;
    }

    // Put items into result array
    for (; !$result_linked->EOF; $result_linked->MoveNext()) {
        list($eID,
            $hook_modid,
            $hook_itemtype,
            $hook_iid,
            $eSummary,
            $eStart['timestamp'],
            $eDuration,
            $eIsallday,
            $eRrule,
            $eRecurFreq,
            $eRecurCount,
            $eRecurUntil['timestamp'],
            $recur_interval
        ) = $result_linked->fields;

        $itemlinks = xarModAPIFunc('julian', 'user', 'geteventinfo',
            array('iid' => $hook_iid, 'itemtype'=> $hook_itemtype, 'modid'   => $hook_modid)
        );

        if (!empty($itemlinks['description'])) {
            // Change date formats to configured types
            if ($eStart['timestamp'] == '0000-00-00 00:00:00') {
                $eStart['mon'] = "";
                $eStart['day'] = "";
                $eStart['year'] = "";
                $eStart['linkdate'] = '';
                $eStart['viewdate'] = '';
            } else {
                $eStart['linkdate'] = date("Ymd", strtotime($eStart['timestamp']));
                $eStart['viewdate'] = date("$dateformat", strtotime($eStart['timestamp']));
            }

            if ($eRrule ==0) {//$eRecurUntil['timestamp'] == '0000-00-00 00:00:00') {
                $eRecur['mon'] = "";
                $eRecur['day'] = "";
                $eRecur['year'] = "";
                $eRecur['linkdate'] = '';
                $eRecur['viewdate'] = '';
            } else {
                $eRecur['linkdate'] = date("Ymd", strtotime($eRecurUntil['timestamp']));
                $eRecur['viewdate'] = date("$dateformat", strtotime($eRecurUntil['timestamp']));
            }

            $items[] = array(
                'eID' => $eID.'_link',
                'eName' => $eSummary,
                'eDescription' => $itemlinks['description'],
                'eStreet1' => '',
                'eStreet2' => '',
                'eCity' => '',
                'eState' => '',
                'eZip' => '',
                'eEmail' => '',
                'ePhone' => '',
                'eLocation' => '',
                'eUrl' => '',
                'eContact' => '',
                'eOrganizer' => '',
                'i_moduleid' =>  $hook_modid,
                'i_itemtype' => $hook_itemtype,
                'i_itemid' => $hook_iid,
                'eStart' => $eStart,
                'i_DateTime' => strtotime($eStart['timestamp']),
                'eRecur' => $eRecur,
                'eDuration' => $eDuration,
                'eRrule' => $eRrule,
                'eIsallday' => $eIsallday,
                'eFee' => ''
            );
        }
    }

    // Close linked result set
    $result_linked->Close();

    // Sort all items
    usort($items, 'julian_userapi_getevents_datecompare');

    // Return the items
    return $items;
}

/**
 * Sort the array
 * @param string sortby
 * @param orderby
 * @param array Array of item
 * @return int 0, -1 or 1 depending of the sort needed
 * @since 23 April 2006
 */
function julian_userapi_getevents_datecompare($x, $y)
{
    if (!xarVarFetch('sortby',  'str:1:', $sortby,  'eventDate', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby', 'str:1:', $orderby, 'DESC',      XARVAR_NOT_REQUIRED)) return;

    switch ($sortby) {
        case 'eventDate':
            $sort = 'i_DateTime';
            break;
        case 'eventName':
            $sort = 'eName';
            break;
        case 'eventDesc':
            $sort = 'eDescription';
            break;
        case 'eventLocn':
            $sort = 'eLocation';
            break;
        case 'eventCont':
            $sort = 'eContact';
            break;
        case 'eventFee':
            $sort = 'eFee';
            break;
    }

    switch ($orderby) {
        case 'DESC':
            $first = -1;
            $sec   = 1;
            break;
        case 'ASC':
            $first = 1;
            $sec   =-1;
            break;
    }

    if ($x[$sort] == $y[$sort]) {
        return 0;
    } else if ($x[$sort] < $y[$sort]) {
        return $first;
    } else {
        return $sec;
    }
}

?>