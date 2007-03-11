<?php
/**
 * View all events in a list
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian development Team
 */
/**
 * Views all events.
 *
 * This Module:
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * @author Roger Raymond
 * @TODO Start with a smarter array of events.
 * @TODO Support start/end dates as single parameters (YYYYMMDD, YYYYMM and YYYY)
 * @TODO Support multiple categories and AND/OR selection
 * @TODO Eventually merge getall, getitems and countitems into one consistent function
 */

function julian_user_viewevents($args)
{
    // Extract args
    extract ($args);

    // Get parameters from the input. These come from the date selection tool
    if (!xarVarFetch('startnum',    'int:1:', $startnum,    1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems',    'int:1:200', $numitems, xarModGetVar('julian', 'itemsperpage'), XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('sortby', 'enum:eventDate:eventName:eventDesc:eventLocn:eventCont:eventFee', $sortby, 'eventDate', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',     'enum:ASC:DESC', $orderby,     'ASC', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('start_year',  'int:0:9999',  $startyear,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('start_month', 'int:0:12',  $startmonth,  0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('start_day',   'int:0:31',  $startday,    0, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('end_year',    'int:0:9999',  $endyear,     0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end_month',   'int:0:12',  $endmonth,    0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end_day',     'int:0:31',  $endday,      0, XARVAR_NOT_REQUIRED)) return;

    // Start by defaulting the start and end dates. We can override the defaults later, through alternative parameters.
    // We will keep all dates in string format YYYYMMDD
    if (!xarVarFetch('startdate', 'pre:num:passthru:str:8:8', $startdate, date('Ymd'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate', 'pre:num:passthru:str:8:8', $enddate, date('Ymd', strtotime('+1 month')), XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('cal_date', 'str::', $caldate, '')) return;
    if (!xarVarFetch('catid', 'id', $catid, 0, XARVAR_NOT_REQUIRED)) return;

    // These two parameters handle "next N days/weeks/months/years" date selection.
    if (!xarVarFetch('datenumber', 'int:0:365', $datenumber, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('datetype', 'pre:lower:passthru:enum:days:weeks:months:years', $datetype, '', XARVAR_NOT_REQUIRED)) return;

    // Grouping of listed items.
    // TODO: make the default grouping configurable (with no grouping - '' - being an option)
    if (!xarVarFetch('group', 'pre:lower:passthru:enum:day:week:month:year:', $group, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('ReadJulian', 1)) return;

    // If grouping by date ranges, then must also order by date.
    if (!empty($group)) $sortby = 'eventDate';

    // Get the Start Day Of Week value.
    $cal_sdow = xarModGetVar('julian', 'startDayOfWeek');

    // Load the calendar class
    $c = xarModAPIFunc('julian', 'user', 'factory', 'calendar');

    $bl_data = array();

    // Set the selected date parts,timestamp, and cal_date in the data array.
    $bl_data = xarModAPIFunc('julian', 'user', 'getUserDateTimeInfo');
    $bl_data['year'] = $c->getCalendarYear($bl_data['selected_year']);

    // Create the date stretch to get events for.
    // We need to find 'start' and 'end', which can be derived from a number of places.

    // Validate the start and end date, and default if not valid.
    if (!checkdate(substr($startdate,2,2), substr($startdate,4,2), substr($startdate,0,4))) $startdate = date('Ymd');
    if (!checkdate(substr($enddate,2,2), substr($enddate,4,2), substr($enddate,0,4))) $enddate = date('Ymd', strtotime('+1 month'));
    
    // Check the start date components
    if (!empty($startyear)) {
        if (!empty($startmonth)) {
            if (!empty($startday)) {
                // Year month and day
                $startdate = date('Ymd', strtotime("$startyear-$startmonth-$startday"));
            } else {
                // Just year and month (pick first day of the month)
                $startdate = date('Ymd', strtotime("$startyear-$startmonth-01"));
            }
        } else {
            // Just the year (pick first day of the year)
            $startdate = date('Ymd', strtotime("$startyear-01-01"));
        }
    }

    // Check the end date components
    if (!empty($endyear)) {
        if (!empty($endmonth)) {
            if (!empty($endday)) {
                // Year month and day
                $enddate = date('Ymd', strtotime("$endyear-$endmonth-$endday"));
            } else {
                // Just year and month (pick last day of the month)
                $enddate = date('Ymd', strtotime("$endyear-$endmonth-" . date('d', mktime(0, 0, 0, ($endmonth + 1), 0, $endyear))));
            }
        } else {
            // Just the year (pick last day of the year)
            $enddate = date('Ymd', strtotime("$endyear-12-31"));
        }
    }

    if (!empty($datenumber) && !empty($datetype)) {
        // Set the end date to the start date plus any number of days, weeks, months or years.
        $enddate = date('Ymd', strtotime("+$datenumber $datetype", strtotime($startdate)));
    }

    // Now we have a start and end date, we should make sure they are the right way around.
    if (strtotime($enddate) < strtotime($startdate)) {
        // End date is earlier, so default it to startdate plus one month.
        $enddate = date('Ymd', strtotime('+1 month', strtotime($startdate)));
    }

    // Bullet style
    $bl_data['Bullet'] = '&' . xarModGetVar('julian', 'BulletForm') . ';';

    // Prepare the array variables that will hold all items for display.
    $bl_data['startnum'] = $startnum;
    $bl_data['sortby'] = $sortby;

    // Pass the start and end dates (Ymd) into the template.
    $bl_data['startdate'] = $startdate;
    $bl_data['enddate'] = $enddate;

    // The user API Function is called: get all events for these selectors
    $events = xarModAPIFunc('julian', 'user', 'getevents',
        array(
            'startnum'  => $startnum,
            'numitems'  => $numitems,
            'sortby'    => $sortby,
            'orderby'   => $orderby,
            'startdate' => $startdate,
            'enddate'   => $enddate,
            'catid'     => $catid
        )
    );

    // Check for exceptions.
    // FIXME: errors should be indicated some other way, such as a NULL return.
    if (!isset($events) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // Add the array of Events to the template variables.
    $bl_data['events'] = $events;

    // Now create a reference array of these event IDs, allowing events to be grouped.
    // Loop though each event to put them into a group (day, week, month or year), if required.
    if (!empty($group)) {
        $groups = array();
        foreach($events as $eventkey => $event) {
            list($periodtype, $periodnumber, $period1start, $periodstart, $periodend) =
                julian_user_viewevents_get_period($startdate, $event['eStart']['timestamp'], $group);
            if (!isset($groups[$periodnumber])) {
                // This group has not been encountered yet; create an array element for it.
                $groups[$periodnumber] = array();
                $groups[$periodnumber]['periodtype'] = $periodtype;
                $groups[$periodnumber]['events'] = array($eventkey);
                $groups[$periodnumber]['period1start'] = $period1start;
                $groups[$periodnumber]['periodstart'] = $periodstart;
                $groups[$periodnumber]['periodend'] = $periodend;
            } else {
                // Already know about this period; add this event key to the list.
                $groups[$periodnumber]['events'][] = $eventkey;
            }
        }

        // Sort the array keys, just to make sure (since the events themselves
        // may not be sorted by date).

        // Pass this data to the template so it can be used for grouping the displayed events.
        $bl_data['groups'] = $groups;
    }
    $bl_data['group'] = $group;

    // Create sort-by URLs.
    // FIXME: these don't include all the possible parameters there could be.
    // Good use of xarServerGetCurrentURL() should do the job
    if ($sortby != 'eventDate' ) {
        $bl_data['eventdateurl'] = xarModURL('julian', 'user', 'viewevents',
            array('startnum' => 1, 'sortby' => 'eventDate', 'catid' => $catid)
        );
    } else {
        $bl_data['eventdateurl'] = '';
    }

    if ($sortby != 'eventName' ) {
        $bl_data['eventnameurl'] = xarModURL('julian', 'user', 'viewevents',
            array('startnum' => 1, 'sortby' => 'eventName', 'catid' => $catid)
        );
    } else {
        $bl_data['eventnameurl'] = '';
    }

    if ($sortby != 'eventDesc' ) {
        $bl_data['eventdescurl'] = xarModURL('julian', 'user', 'viewevents',
            array('startnum' => 1, 'sortby' => 'eventDesc', 'catid' => $catid)
        );
    } else {
        $bl_data['eventdescurl'] = '';
    }

    if ($sortby != 'eventLocn' ) {
        $bl_data['eventlocnurl'] = xarModURL('julian', 'user', 'viewevents',
            array('startnum' => 1, 'sortby' => 'eventLocn', 'catid' => $catid)
        );
    } else {
        $bl_data['eventlocnurl'] = '';
    }

    if ($sortby != 'eventCont' ) {
        $bl_data['eventconturl'] = xarModURL('julian', 'user', 'viewevents',
            array('startnum' => 1, 'sortby' => 'eventCont', 'catid' => $catid)
        );
    } else {
        $bl_data['eventconturl'] = '';
    }

    if ($sortby != 'eventFee' ) {
        $bl_data['eventfeeurl'] = xarModURL('julian', 'user', 'viewevents',
            array('startnum' => 1, 'sortby' => 'eventFee', 'catid' => $catid)
        );
    } else {
        $bl_data['eventfeeurl'] = '';
    }

    // Start and end date components to pass back to the template.
    $bl_data['start_year'] = date("Y", strtotime($startdate));
    $bl_data['start_month'] = date("m", strtotime($startdate));
    $bl_data['start_day'] = date("d", strtotime($startdate));

    $bl_data['end_year'] = date("Y", strtotime($enddate));
    $bl_data['end_month'] = date("m", strtotime($enddate));
    $bl_data['end_day'] = date("d", strtotime($enddate));

    // Pass the datenumber and datetype to the template
    if (empty($datenumber)) {
        // Make up a new set, based on the actual start and end dates.
        // It will be an approximation, but hopefully a useful one.
        $period_days = round((strtotime($enddate) - strtotime($startdate)) / (60  * 60 * 24));
        if ($period_days <= 28) {
            if ($period_days % 7 == 0) {
                $datenumber = $period_days / 7;
                $datetype = 'weeks';
            } else {
                $datenumber = $period_days;
                $datetype = 'days';
            }
        } elseif ($period_days <= 365) {
            // If period is divisible by 30, plus or minus a few days
            if (($period_days + 30 - 4) % 30 <= 8){
                $datenumber = round($period_days / 30);
                $datetype = 'months';
            } elseif ($period_days % 7 == 0) {
                $datenumber = $period_days / 7;
                $datetype = 'weeks';
            } else {
                $datenumber = $period_days;
                $datetype = 'days';
            }
        } else {
            $datenumber = round($period_days / 365);
            $datetype = 'years';
        }
    }

    $bl_data['datenumber'] = $datenumber;
    $bl_data['datetype'] = $datetype;

    // Create Pagination.
    // FIXME: the count does not take dates into account; suggest modifying getevents to return a count based on main selection
    // FIXME: the pager URL does not take other selection criteria into account; suggest trying xarServerGetCurrentURL()
    $bl_data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('julian', 'user', 'countevents', array('catid' => $catid)),
        xarModURL('julian', 'user', 'viewevents',
            array(
                'startnum' => '%%', 
                'sortby'   => $sortby,
                'catid'    => $catid,
                'orderby'  => $orderby
            )
        ), $numitems
    );

    $bl_data['catid'] = $catid;

    // Return the template variables defined in this function.
    return $bl_data;
}


/**
 * @param startdate at date that is located in period 1 (Ymd)
 * @param eventdate the date to determine the period for (Ymd)
 * @param periodtype the type of the period (day, week, month, year)
 */

function julian_user_viewevents_get_period($startdate, $eventdate, $periodtype = 'week')
{
    // Validate and default the group type.
    $periodtype = strtolower($periodtype);
    if (!xarVarValidate('enum:day:week:month:year', $periodtype, true)) $periodtype = 'week';

    // Get everything into unix timestamps for simplicity
    if (is_string($startdate)) $startdate = strtotime($startdate);
    if (is_string($eventdate)) $eventdate = strtotime($eventdate);

    switch ($periodtype) {
    case 'week':
    default:
        $startdayofweek = xarModGetVar('julian', 'startDayOfWeek');
        $startdateday = date('w', $startdate);
	    $daystostartperiod = $startdateday - $startdayofweek;
        if ($daystostartperiod < 0) $daystostartperiod += 7;

        // Get the period start date (unix timestamp) by counting back the appropriate number of days
        $period1start = strtotime("-$daystostartperiod days", $startdate);

        // Calculate the week number (integer blocks of seven days)
        $periodnumber = floor(($eventdate - $period1start) / (60  * 60 * 24) / 7) + 1;

        // Get the start and end dates for the period, just for info.
        $periodstart = strtotime('+' . ($periodnumber-1) . ' weeks', $period1start);
        $periodend = strtotime('+6 days', $periodstart);
        $periodtype = 'week';

        break;

    case 'day':
        // This is an easy one: the startdate is day 1; count the days
        $period1start = $startdate;
        $periodnumber = floor(($eventdate - $period1start) / (60  * 60 * 24)) + 1;
        $periodstart = $eventdate;
        $periodend = $eventdate;
        break;

    case 'month':
        // Count the months between the two dates.
        $period1start = strtotime(date('Ym', $startdate) . '01');
        $periodnumber = (12 * date('Y', $eventdate) + date('m', $eventdate)) - (12 * date('Y', $startdate) + date('m', $startdate)) + 1;
        $periodstart = strtotime('+' . ($periodnumber - 1) . ' months', $period1start);
        $periodend = strtotime('-1 day', strtotime('+1 month', $periodstart));
        break;

    case 'year':
        $period1start = strtotime(date('Y', $startdate) . '0101');
        $periodnumber = date('Y', $eventdate) - date('Y', $startdate) + 1;
        $periodstart = strtotime('+' . ($periodnumber - 1) . ' years', $period1start);
        $periodend = strtotime('-1 day', strtotime('+1 year', $periodstart));
        break;
    }

    //echo "period=$periodtype periodnumber=$periodnumber periodstart=" . date('Y-m-d', $periodstart) . " periodend=" . date('Y-m-d', $periodend) . " period1start=" . date('Y-m-d', $period1start) . "<br/>";

    // Numeric array elements, so list() can be used. Don't change the order!
    return array(
        $periodtype,
        $periodnumber,
        $period1start,
        $periodstart,
        $periodend,
    );
}

?>