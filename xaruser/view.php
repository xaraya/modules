<?php

/**
 * View events.
 * This will consist of a flat listing initially, but
 * may be used as an entry into the calendar-type views.
 *
 * The 'view' and 'display' functions will accept the same parameters,
 * allowing the user to switch easily between them. [rethink this - we
 * actually need a way to get the next and previous events in a listing
 * even from within the display function]
 *
 * @todo Transform hooks on the HTML fields (
 * @todo Support calendar_id as an input parameter
 * @todo Support [multiple] categories
 */

function ievents_user_view($args)
{
    extract($args);

    // General security check.
    if (!xarSecurityCheck('OverviewIEvent')) return;

    // Get the user parameters.
    // TODO: make the default configurable.
    // TODO: allow the numitems range to be 200 or the default (whichever is the larger)
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:200', $numitems, 20, XARVAR_NOT_REQUIRED)) return;

    // TODO: sorting and ordering flags and parameters
    // ...

    //
    // Various start/end date selection 
    //

    // unix timestamp format start/end.
    // These are the master start/end variables. Other parameters may override them, but all dates
    // ultimately come to these two.
    // These set the defaults also - events from today to one month ahead.
    xarVarFetch('ustartdate', 'int', $ustartdate, time(), XARVAR_NOT_REQUIRED);
    xarVarFetch('uenddate', 'int', $uenddate, strtotime('+1 month'), XARVAR_NOT_REQUIRED);

    // Allow selection by year/month/day parts
    xarVarFetch('startyear', 'pre:num:passthru:str:4', $startyear, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('startmonth', 'pre:num:passthru:str:1:2', $startmonth, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('startday', 'pre:num:passthru:str:1:2', $startday, '', XARVAR_NOT_REQUIRED);

    xarVarFetch('endyear', 'pre:num:passthru:str:4', $endyear, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('endmonth', 'pre:num:passthru:str:1:2', $endmonth, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('endday', 'pre:num:passthru:str:1:2', $endday, '', XARVAR_NOT_REQUIRED);

    // The startdate and enddate can be passed in YYYYMMDD format.
    xarVarFetch('startdate', 'pre:num:passthru:str:8:8', $startdate, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('enddate', 'pre:num:passthru:str:8:8', $enddate, '', XARVAR_NOT_REQUIRED);

    // These two parameters handle "next N days/weeks/months/years" date selection.
    xarVarFetch('datenumber', 'int:0:365', $datenumber, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('datetype', 'pre:lower:passthru:enum:days:weeks:months:years', $datetype, '', XARVAR_NOT_REQUIRED);

    // Another way of selecting a date, using text
    xarVarFetch('range', 'pre:lower:passthru:enum:today:tomorrow:yesterday:thisweek:week:nextweek:lastweek:thismonth:month:nextmonth:lastmonth:thisyear:year:nextyear:lastyear', $range, '', XARVAR_NOT_REQUIRED);

    // Grouping of listed items.
    // Grouping should affect the sorting too, since grouping my a time period
    // would imply sorting by that time period too.
    // TODO: make the default grouping configurable (with no grouping - '' - being an option)
    xarVarFetch('group', 'pre:lower:passthru:enum:day:week:month:year:', $group, '', XARVAR_NOT_REQUIRED);

    // Event ID.
    // The user has selected an individual event ID.
    xarVarFetch('eid', 'id', $eid, 0, XARVAR_NOT_REQUIRED);

    // Calendar ID
    xarVarFetch('cid', 'id', $cid, 0, XARVAR_NOT_REQUIRED);

    // Get global parameters.
    $gparams = xarModAPIfunc('ievents', 'user', 'params');

    //
    // Validate and process some of the date parameters.
    // Any time component is irrelevant and will be stripped off later.
    //

    // Textual ranges
    if (!empty($range)) {
        // TODO: put these range functions into an API so they can be used in display functions

        // Find the first day of the current week
        $daystostartweek = date('w', time()) - $gparams['startdayofweek'];
        if ($daystostartweek < 0) $daystostartweek += 7;
        // Get the period start date (unix timestamp) by counting back the appropriate number of days
        $thisweekstart = strtotime("-$daystostartweek days", strtotime(date('Y-m-d', time())));

        // Find the start of the current month
        $thismonthstart = strtotime(date('Ym', time()) . '01');

        // Find the start of the current year
        $thisyearstart = strtotime(date('Y', time()) . '0101');

        // Set the date ranges.
        switch($range) {
            case 'yesterday':
            case 'today':
            case 'tomorrow':
                $ustartdate = strtotime($range);
                $uenddate = strtotime($range);
                break;
            case 'lastweek':
                $ustartdate = strtotime('-1 week', $thisweekstart);;
                $uenddate = strtotime('-1 day', $thisweekstart);
                break;
            case 'thisweek':
            case 'week':
                $ustartdate = $thisweekstart;
                $uenddate = strtotime('-1 second', strtotime('+1 week', $thisweekstart));
                break;
            case 'nextweek':
                $ustartdate = strtotime('+1 week', $thisweekstart);
                $uenddate = strtotime('-1 second', strtotime('+2 weeks', $thisweekstart));
                break;
            case 'lastmonth':
                $ustartdate = strtotime('-1 month', $thismonthstart);
                $uenddate = strtotime('-1 second', $thismonthstart);
                break;
            case 'thismonth':
            case 'month':
                $ustartdate = $thismonthstart;
                $uenddate = strtotime('-1 second', strtotime('+1 month', $thismonthstart));
                break;
            case 'nextmonth':
                $ustartdate = strtotime('+1 month', $thismonthstart);
                $uenddate = strtotime('-1 second', strtotime('+2 months', $thismonthstart));
                break;
            case 'lastyear':
                $ustartdate = strtotime('-1 year', $thisyearstart);
                $uenddate = strtotime('-1 second', $thisyearstart);
                break;
            case 'thisyear':
            case 'year':
                $ustartdate = $thisyearstart;
                $uenddate = strtotime('-1 second', strtotime('+1 year', $thisyearstart));
                break;
            case 'nextyear':
                $ustartdate = strtotime('+1 year', $thisyearstart);
                $uenddate = strtotime('-1 second', strtotime('+2 years', $thisyearstart));
                break;
        }
    }

    // Check the start date individual components.
    // Input can consist of year, year/month or year/month/day
    if (!empty($startyear)) {
        if (!empty($startmonth)) {
            // Pad the month out to two characters.
            $startmonth = str_pad($startmonth, 2, '0', STR_PAD_LEFT);
            if (!empty($startday)) {
                // Year month and day
                $startday = str_pad($startday, 2, '0', STR_PAD_LEFT);
                $startdate = "${startyear}${startmonth}${startday}";
            } else {
                // Just year and month (pick first day of the month)
                $startdate = "${startyear}${startmonth}01";
            }
        } else {
            // Just the year (pick first day of the year)
            $startdate = "${startyear}0101";
        }
    }

    // Check the end date individual components.
    if (!empty($endyear)) {
        if (!empty($endmonth)) {
            $endmonth = str_pad($endmonth, 2, '0', STR_PAD_LEFT);
            if (!empty($endday)) {
                // Year month and day
                $endday = str_pad($endday, 2, '0', STR_PAD_LEFT);
                $enddate = "${endyear}${endmonth}${endday}";
            } else {
                // Just year and month (pick last day of the month)
                $enddate = "${endyear}${endmonth}" . date('d', mktime(0, 0, 0, ($endmonth + 1), 0, $endyear));
            }
        } else {
            // Just the year (pick last day of the year - 1231 == 31 December)
            $enddate = "${endyear}1231";
        }
    }

    // Validate the start and end date, and default if not valid (in YYYYMMDD format).
    if (!empty($startdate) && !checkdate(substr($startdate,2,2), substr($startdate,4,2), substr($startdate,0,4))) $startdate = '';
    if (!empty($enddate) && !checkdate(substr($enddate,2,2), substr($enddate,4,2), substr($enddate,0,4))) $enddate = '';

    // If the start and end date strings have got this far, then treat them as valid dates.
    if (!empty($startdate)) $ustartdate = strtotime($startdate);
    if (!empty($enddate)) $uenddate = strtotime($enddate);

    // The user hay have selected a date range measured in other units (days, weeks, months)
    if (!empty($datenumber) && !empty($datetype)) {
        // Set the end date to the start date plus any number of days, weeks, months or years.
        $uenddate = strtotime("+$datenumber $datetype", $ustartdate);
    }

    // Now we have a start and end date, we should make sure they are the right way around.
    if ($uenddate < $ustartdate) {
        // End date is earlier, so default it to startdate plus one month.
        $uenddate = strtotime('+1 month', $ustartdate);
    }

    // Categories
    // Parameters accepted:
    // 1. catid=N where N is an integer
    // 2. catids[]=N&catids[]=M ...
    // 2a. crule=and|or defines the rule for multiple catids (default: and)
    // 3. cats=N+M | cats=N-M, equivalent to crule=and|or
    // All above formats will be made available to the calling template, regardless
    // of which was passed in. 'catid' will be set to the first in the list of catids.
    // The 'cats' format will be used in the default URL (for pagers etc.)

    // Start by fetching page parameters.
    xarVarFetch('catid', 'id', $catid, 0, XARVAR_NOT_REQUIRED);
    if (!empty($catid)) $catids = array($catid);

    // Category joining rule.
    xarVarFetch('crule', 'pre:lower:passthru:enum:and:or', $crule, 'and', XARVAR_NOT_REQUIRED);

    // List of catids[]
    xarVarFetch('catids', 'list:id', $catids, array(), XARVAR_NOT_REQUIRED);

    // Everything in a single string.
    // N+M+..
    // Chose a joiner of ' ' or '+' because some servers convert all '+' chars in the URL to ' '.
    xarVarFetch('cats', 'strlist: +:id', $cats1, '', XARVAR_NOT_REQUIRED);
    if (!empty($cats1)) {
        $catids = explode(' ', $cats1);
        $crule = 'and';
    } else {
        xarVarFetch('cats', 'strlist:-:id', $cats2, '', XARVAR_NOT_REQUIRED);
        if (!empty($cats2)) {
            $catids = explode('-', $cats2);
            $crule = 'or';
        }
    }
    // Now recreate missing page parameters.
    if (!empty($catids)) {
        // Select the first item only.
        $catid = reset($catids);
        $cats = implode((($crule == 'or') ? '-' : '+'), $catids);
    } else {
        $catid = 0;
        $cats = '';
    }

    //
    // Create missing parameters from the supplied parameters
    //
    
    // Now we have collected data from all the date components, into a single
    // pair of unix variables, we can 'fill in the blanks' and return those
    // dates back to the components, mainly for use in the templates for this
    // page.

    // Create a datenumber and datetype if we don't have a set.
    // This is a bit of a guestimate, but useful.
    if (empty($datenumber) || empty($datetype)) {
        // Make up a new set, based on the actual start and end dates.
        // It will be an approximation, but hopefully a useful one.
        $period_days = round(($uenddate - $ustartdate) / (60  * 60 * 24));
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
            if (abs(($period_days + 3) % 30 - 3) <= 3){
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

    // Set the textual start and end date
    $startdate = date('Ymd', $ustartdate);
    $enddate = date('Ymd', $uenddate);

    // Set the start and end date components
    list($startyear, $startmonth, $startday) = explode('-', date('Y-m-d', $ustartdate));
    list($endyear, $endmonth, $endday) = explode('-', date('Y-m-d', $uenddate));

    //
    // Fetch events from the database
    //

    // Create an array with all the data needed to search for the events.
    // TODO: categories
    $event_params = array(
        'startnum' => $startnum,
        'numitems' => $numitems,
        'startdate' => $ustartdate,
        'enddate' => $uenddate,
    );

    if (!empty($cid)) $event_params['cid'] = $cid;

    // Add in the category restrictions if required.
    if (!empty($catids)) {
        $event_params['catids'] = $catids;
        $event_params['crule'] = $crule;
    }

    // Fetch the events.
    $events = xarModAPIfunc('ievents', 'user', 'getevents', $event_params);
    //echo "<pre>"; var_dump($events); echo "</pre>";

    //
    // TODO: If the user has selected a specific event, then
    // we need to work out where it appears in the list, and also
    // provide some next/previous links (the links would contain
    // the event IDs, as well as the startnum value).
    // If we are at the start or end of a page of events, then
    // we need to fetch the event over the page to complete the
    // links.
    // If the selected event is not in the list, then the user
    // just gets the list (we ignore the event ID).
    //

    if (!empty($eid)) {
        // Event ID requested.
        if (!isset($events[$eid])) {
            // Event not found in the list we have.
            // Fetch the individual event, and treat that as the list.
            // Go back to the first page for this one, as we know there should be
            // (at most) one event.
            $event_params['eid'] = $eid;
            $event_params['startnum'] = 1;
            $event_params['startdate'] = NULL;
            $event_params['enddate'] = NULL;
            $events = xarModAPIfunc('ievents', 'user', 'getevents', $event_params);
            if (isset($events[$eid])) {
                $event = $events[$eid];
            } else {
                // Still not found the event - probably does not exist.
                $event = array();
            }
            $prev_event = array();
            $next_event = array();
        } else {
            // Count which element it is, i.e. the position on the page.
            // A handy value in the list helps us there.
            $eventcount = count($events);
            $position = $events[$eid]['position'];
            $event = $events[$eid];

            // If we are at the start of a page:
            // - if on the first page, then there is no previous event
            // - if not on the first page, then the previous event is on the previous page
            if ($position == 1) {
                // At the start of a page.
                if ($startnum > 1) {
                    // We are not on the first page.
                    // - Fetch the last event from the previous page, to get its ID.
                    // - Set the previous item to be on the previous page.
                    // This is slightly risky, because we may not have permission to view that item.
                    $prev_event = xarModAPIfunc('ievents', 'user', 'getevent',
                        array_merge($event_params, array('startnum' => $startnum - 1, 'numitems' => 1))
                    );
                    // Set the startnum to be the start of the previous page.
                    if (!empty($prev_event)) $prev_event['startnum'] = $startnum - $numitems;
                } else {
                    $prev_event = array();
                }
            } else {
                // Not at the start of the page, so the previous event should be easily
                // available in the events array.
                // -1: previous element; -1: zero-based offset for array_slice
                $prev_event_arr = array_slice($events, $position - 1 - 1, 1);
                $prev_event = reset($prev_event_arr);
                $prev_event['startnum'] = $startnum;
            }

            // If we are at the end of a page:
            // - if on the last page, then there is no next event
            // - if not on the last page, then the next event is on the next page
            if ($position == $eventcount) {
                // Last event on the page
                // If we are on the last page, then there are no more events.
                // We won't know that unless we try fetching the next event.
                // Note: a shortcut is to look at the position on the page - if less than
                // numitems, then we are almost certainly on the last page OR privileges
                // have chopped a few out of the list (we cannot tell which).
                //if ($position == $numitems) {
                    $next_event = xarModAPIfunc('ievents', 'user', 'getevent',
                        array_merge($event_params, array('startnum' => $startnum + $eventcount, 'numitems' => 1))
                    );
                //}
                if (!empty($next_event)) {
                    // Set startnum to the next page.
                    $next_event['startnum'] = $numitems + $startnum;
                }
            } else {
                // Not the last on the page.
                // +1: next element; -1: zero-based offset for array_slice
                $next_event_arr = array_slice($events, $position, 1);
                $next_event = reset($next_event_arr);
                $next_event['startnum'] = $startnum;
            }
        }
    } else {
        $prev_event = array();
        $next_event = array();
        $event = array();
    }

    
    // Create pagination.
    // The url params would be slightly different to the event params (no unix timestamps
    // for a start, and possibly different category parameter formats).
    $url_params = array(
        'numitems' => $numitems,
        'startnum' => '%%',
        'startdate' => $startdate,
        'enddate' => $enddate,
        'group' => $group,
    );

    // Add the categories selection in if available.
    if (!empty($cats)) $url_params['cats'] = $cats;

    if (!empty($cid)) $url_params['calendar_id'] = $cid;
    
    $event_count = xarModAPIFunc('ievents', 'user', 'countevents', $event_params);

    $pager = xarTplGetPager($startnum, $event_count,
        xarModURL('ievents', 'user', 'view', $url_params), $numitems
    );

    //
    // Perform grouping of events if required.
    //

    // Now create a reference array of these event IDs, allowing events to be grouped.
    // Loop though each event to put them into a group (day, week, month or year), if required.
    $groups = array();
    if (!empty($group)) {
        foreach($events as $eventkey => $eventvalue) {
            list($periodtype, $periodnumber, $period1start, $periodstart, $periodend) =
                xarModAPIfunc('ievents', 'user', 'calc_period',
                    array('startdate' => $ustartdate, 'eventdate' => $eventvalue['startdate'], 'group' => $group)
                );
            if (!isset($groups[$periodnumber])) {
                // This group has not been encountered yet; create an array element for it.
                $groups[$periodnumber] = array();
                $groups[$periodnumber]['periodtype'] = $periodtype;
                $groups[$periodnumber]['events'] = array();
                $groups[$periodnumber]['period1start'] = $period1start;
                $groups[$periodnumber]['periodstart'] = $periodstart;
                $groups[$periodnumber]['periodend'] = $periodend;
            }

            // Add this event key to the list.
            $groups[$periodnumber]['events'][] = $eventkey;
        }
    }

    // Get a list of calendars the user has access to.
    $calendars = xarModAPIfunc('ievents', 'user', 'getcalendars', array('event_priv' => 'OVERVIEW'));

    //
    // Pass data back out to the template
    //

    // By keeping the bl data and variable names the same, passing data is a sinch.
    $bl_data = @compact(array(
        'ustartdate', 'uenddate',
        'startdate', 'enddate',
        'startyear', 'startmonth', 'startday',
        'endyear', 'endmonth', 'endday',
        'datenumber', 'datetype',
        'group', 'groups',
        'next_event', 'prev_event',
        'eid', 'event',
        'events', 'pager',
        'calendars',
        'cats', 'catid', 'catids', 'crule',
    ));
    //echo "<pre>"; var_dump($bl_data); echo "</pre>";

    //echo "ustartdate=$ustartdate (" . date('Y-m-d', $ustartdate) . ") uenddate=$uenddate (" . date('Y-m-d', $uenddate) . ")<br />";
    //echo "DONE"; return true;

    return $bl_data;
}

?>