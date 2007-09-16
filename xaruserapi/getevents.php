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
 * @param docount if not empty, then returns a count of rows instead of the actual rows
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @todo MichelV: rewrite some queries for pgsql
 * @todo Combine getevents and getall APIs - they are just too similar to warrent being separate
 * @todo The linked events and the main events should be done in a more effective way, using a single query
 *       (even if it has to be called twice). The reason is two-fold: counts need to include both main events
 *       and linked events; and keeping the two queries in sync is error-prone.
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
    // JDJ: removed. The defaults should happen in the 
    //if (!isset($startdate)) $startdate = date('Ymd');

    // Argument check.
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) $invalid[] = 'startnum';
    if (!isset($numitems) || !is_numeric($numitems)) $invalid[] = 'numitems';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1)', join(', ', $invalid));
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return false;
    }

    // Items to return.
    $items = array();

    // Make an admin adjustable time format
    $dateformat = xarModGetVar('julian', 'dateformat');
    $timeformat = xarModGetVar('julian', 'timeformat');

    // Load categories API.
    // Needed?
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API', 'categories', 'user');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
        return false;
    }

    // Get database setup.
    $dbconn =& xarDBGetConn();

    // Get database tables.
    $xartable =& xarDBGetTables();

    // Set Events Table and Column definitions.
    $event_table = $xartable['julian_events'];

    // Decide whether we are going to just count items, or
    // fetch the full details of items.
    // CHECKME: I have a hunch the COUNT(DISTINT ...) may cause problems with some
    // earlier versions of MySQL. Also GROUP BY or ORDER BY columns may fail if they
    // are not present in the SELECT clause. Keep an eye on this, in particular
    // when using categories.

    if (!empty($docount)) {
        $query = 'SELECT COUNT(DISTINCT ev.event_id)';
    } else {
        $query = 'SELECT DISTINCT ev.event_id,'
            . ' ev.calendar_id, ev.summary, ev.description,'
            . ' ev.street1, ev.street2, ev.city, ev.state, ev.zip,'
            . ' ev.email, ev.phone, ev.location, ev.url,'
            . ' ev.contact, ev.organizer,'
            . ' ev.dtstart, ev.recur_until, ev.duration,'
            . ' ev.rrule, ev.isallday, ev.fee,'
            // Migrated from the get() API, in preparation for merging.
            . ' ev.type,'
            . ' ev.related_to,'
            . ' ev.reltype,'
            . ' ev.class,'
            . ' ev.share_uids,'
            . ' ev.priority,'
            . ' ev.status,'
            . ' ev.exdate,'
            . ' ev.categories,'
            . ' ev.recur_freq,'
            . ' ev.recur_count,'
            . ' ev.recur_interval,'
            . ' ev.dtend,'
            . ' ev.freebusy,'
            . ' ev.due,'
            . ' ev.transp,'
            . ' ev.created,'
            . ' ev.last_modified';
    }

    $bindvars = array();

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
    // TODO: format the database dates using an API (to do it properly)
    // The dtstart column is a datetime type, and takes the format 'YYYYMMDDHHMMSS'
    if ((!empty($startdate)) && (!empty($enddate))) {
        $query .= " AND ev.dtstart BETWEEN '${startdate}000000' AND '${enddate}235959'";
    } elseif (!empty($startdate)) {
        $query .= " AND ev.dtstart >= '${startdate}000000'";
    } elseif (!empty($enddate)) {
        $query .= " AND ev.dtstart <= '${enddate}235959'";
    }

    // Selection by event ID
    if (!empty($event_id)) {
        $query .= ' AND ev.event_id = ?';
        $bindvars[] = (int)$event_id;
    }

    // This is double now, as the array is being sorted anyway.
    if (isset($sortby) && empty($docount)) {
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

    // Fetch the events.
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);

    // Check for an error.
    if (!$result) return;

    // Check for no rows found.
    if ($result->EOF) {
        $result->Close();
        return $items;
    }

    // If we are asking for a count, then close the result set and return
    // immediately with that count.
    if (!empty($docount)) {
        list($count) = $result->fields;
        $result->Close();
        return $count;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($eID,
            $eCalendarID, $eName, $eDescription,
            $eStreet1, $eStreet2, $eCity, $eState, $eZip,
            $eEmail, $ePhone, $eLocation, $eUrl,
            $eContact, $eOrganizer,
            $eStart['timestamp'], $eRecur['timestamp'], $eDuration,
            $eRrule, $eIsallday, $eFee,
            // Migrated from get(), in preparation for merging.
            $eType,
            $eRelatedTo,
            $eReltype,
            $eClass,
            $eShareUIDs,
            $ePriority,
            $eStatus,
            $eExdate,
            $eCategories,
            $eRecurFreq,
            $eRecurCount,
            $eRecurInterval,
            $eDtend,
            $eFreebusy,
            $eDue,
            $eTransp,
            $eCreated,
            $eLastModified
        ) = $result->fields;

        // Security check
        if (xarSecurityCheck('ReadJulian', 0, 'Item', "$eID:$eOrganizer:$eCalendarID:All")) {
            $eEnd = array();

            // Convert the duration into hours and seconds (UNIX)
            if (preg_match('/^\d*:\d*$/', $eDuration)) {
                $eDurationSplit = explode(':', $eDuration);
                $eDurationHours = $eDurationSplit[0] + ($eDurationSplit[1]/60);
                $eDurationUnix = $eDurationSplit[0]*3600 + ($eDurationSplit[1]*60);
            } else {
                $eDurationHours = 0;
                $eDurationUnix = 0;
            }
            
            // Change date formats from UNIX timestamp to something readable.
            // TODO: why do we need all this display stuff in here?
            // TODO: the time format should be configurable, to allow the use of 24-hour clock format.
            if ($eStart['timestamp'] == 0 || empty($eStart['timestamp'])) {
                $eStart['unixtime'] = 0;
                $eStart['mon'] = "";
                $eStart['day'] = "";
                $eStart['year'] = "";
                $eStart['linkdate'] = '';
                $eStart['viewdate'] = '';
                $eStart['displaytime'] =  '';
            } else {
                $eStart['unixtime'] = strtotime($eStart['timestamp']);
                $eStart['linkdate'] = date('Ymd', $eStart['unixtime']);
                $eStart['viewdate'] = date($dateformat, $eStart['unixtime']);
                $eStart['displaytime'] = date($timeformat, $eStart['unixtime']);
                $eStart['starthours'] = date('H', $eStart['unixtime']) + (date('i', $eStart['unixtime'])/60);

                $eEnd['unixtime'] = $eStart['unixtime'] + $eDurationUnix;
                $eEnd['viewdate'] = date($dateformat, $eEnd['unixtime']);
                $eEnd['displaytime'] = date($timeformat, $eEnd['unixtime']);
            }

            if ($eRecur['timestamp'] == 0 || empty($eRecur['timestamp'])) {
                $eRecur['mon'] = "";
                $eRecur['day'] = "";
                $eRecur['year'] = "";
                $eRecur['linkdate'] = '';
                $eRecur['viewdate'] = '';
            } else {
                $eRecur['unixtime'] = strtotime($eRecur['timestamp']);
                $eRecur['linkdate'] = date('Ymd', $eRecur['unixtime']);
                $eRecur['viewdate'] = date($dateformat, $eRecur['unixtime']);
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
                'eEnd' => $eEnd,
                'i_moduleid' => '',
                'i_itemtype' => '',
                'i_itemid' => '',
                'i_DateTime' => strtotime($eStart['timestamp']),
                'eRecur' => $eRecur,
                'eDuration' => $eDuration,
                'eDurationHours' => $eDurationHours,
                'eRrule' => $eRrule,
                'eIsallday' => $eIsallday,
                'eFee' => $eFee,
                // Migrated from get(), in preparation for merging.
                'event_id' =>$eID,
                'calendar_id' =>$eCalendarID,
                'type' => $eType,
                'organizer' => $eOrganizer,
                'contact' => $eContact,
                'url' => $eUrl,
                'summary' => $eName,
                'description' => $eDescription,
                'related_to' => $eRelatedTo,
                'reltype' => $eReltype,
                'class' => $eClass,
                'share_uids' => $eShareUIDs,
                'priority' => $ePriority,
                'status' => $eStatus,
                'location' => $eLocation,
                'street1' => $eStreet1,
                'street2' => $eStreet2,
                'city' => $eCity,
                'state' => $eState,
                'zip' => $eZip,
                'phone' => $ePhone,
                'email' => $eEmail,
                'fee' => $eFee,
                'exdate' => $eExdate,
                'categories' => $eCategories,
                'recur_freq' => $eRecurFreq,
                'recur_until' => $eRecur,
                'recur_count' => $eRecurCount,
                'recur_interval' => $eRecurInterval,
                'dtstart' => $eStart,
                'dtend' => $eDtend,
                'duration' => $eDuration,
                'freebusy' => $eFreebusy,
                'due' => $eDue,
                'transp' => $eTransp,
                'created' => $eCreated,
                'last_modified' => $eLastModified
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
        $query_linked .= " WHERE dtstart BETWEEN '${startdate}000000' AND '${enddate}235959'";
    } elseif (!empty($startdate)) {
        $query_linked .= " WHERE dtstart >= '${startdate}000000'";
    } elseif (!empty($enddate)) {
        $query_linked .= " WHERE dtstart <= '${enddate}235959'";
    }

    // TODO: include all the other ordering options
    if (isset($sortby) && empty($docount)) {
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
            array('iid' => $hook_iid, 'itemtype'=> $hook_itemtype, 'modid' => $hook_modid)
        );

        if (!empty($itemlinks['description'])) {
            $eEnd = array();

            // Convert the duration into hours nad seconds (UNIX))
            if (preg_match('/^\d*:\d*$/', $eDuration)) {
                $eDurationSplit = explode(':', $eDuration);
                $eDurationHours = $eDurationSplit[0] + ($eDurationSplit[1]/60);
                $eDurationUnix = $eDurationSplit[0]*3600 + ($eDurationSplit[1]*60);
            } else {
                $eDurationHours = 0;
                $eDurationUnix = 0;
            }
            
            // Change date formats to configured types
            if ($eStart['timestamp'] == '0000-00-00 00:00:00') {
                $eStart['mon'] = '';
                $eStart['day'] = '';
                $eStart['year'] = '';
                $eStart['linkdate'] = '';
                $eStart['viewdate'] = '';
                $eStart['displaytime'] = '';
            } else {
                $eStart['unixtime'] = strtotime($eStart['timestamp']);
                $eStart['linkdate'] = date("Ymd", $eStart['unixtime']);
                $eStart['viewdate'] = date($dateformat, $eStart['unixtime']);

                $eEnd['unixtime'] = $eStart['unixtime'] + $eDurationUnix;
                $eEnd['viewdate'] = date($dateformat, $eEnd['unixtime']);
                $eEnd['displaytime'] = date($timeformat, $eEnd['unixtime']);
            }

            if ($eRrule ==0) {
                $eRecur['mon'] = '';
                $eRecur['day'] = '';
                $eRecur['year'] = '';
                $eRecur['linkdate'] = '';
                $eRecur['viewdate'] = '';
            } else {
                $eRecur['unixtime'] = strtotime($eRecur['timestamp']);
                $eRecur['linkdate'] = date('Ymd', $eRecur['unixtime']);
                $eRecur['viewdate'] = date($dateformat, $eRecur['unixtime']);
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
                'eEnd' => $eEnd,
                'i_DateTime' => strtotime($eStart['timestamp']),
                'eRecur' => $eRecur,
                'eDuration' => $eDuration,
                'eDurationHours' => $eDurationHours,
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

/**
 * TODO: PB.Create an separate event for every recursive event
 */
function julian_userapi_getrecur($start_date, $recur_freq, $rrule = null, $recur_count = null, $recur_freq = null, $recur_until = null)
{
    //Number of recursive events to fetch
    $recur_no = 10;
    
    //Define vars
    $start_date = strtotime($start_date);
    if (!empty($recur_until)) $recur_until = strtotime($recur_until);

    //First of all we need to determine if this is an 'every' or an 'on' type
    

    //Loop through the event dates.
    $recur_dates = array();
    $i = 1;
    while ($i <= $no_recur) {
        $recur_dates =
        $i++;
    }
}

?>