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
 * @todo Transform hooks on the HTML fields
 * @todo Support different display formats (list, calendar, etc)
 * @todo Provide next/previous day/week/month/year/group links for the template (for any view)
 * @todo Create a textual description of the search that has been performed
 * @todo Pass a 'feed_params' base url to the templates, containing relevant parameters to base a feed off (relative dates, no grouping etc) [half done]
 */

function ievents_user_view($args)
{
    extract($args);

    // General security check.
    if (!xarSecurityCheck('OverviewIEvent')) return;

    // Get module parameters
    $module = 'ievents';
    $default_numitems = xarModGetVar('ievents', 'default_numitems');
    $max_numitems = xarModGetVar('ievents','max_numitems');
    $default_startdate = xarModGetVar('ievents','default_startdate');
    $default_enddate = xarModGetVar('ievents','default_enddate');
    $startdayofweek = xarModGetVar('ievents','startdayofweek');
    $itemtype_events = xarModGetVar('ievents','itemtype_events');
    $year_range_min = xarModGetVar('ievents','year_range_min');
    $year_range_max = xarModGetVar('ievents','year_range_max');
    $default_group = xarModGetVar('ievents','default_group');
    $default_display_format = xarModGetVar('ievents','default_display_format');

    extract(xarModAPIfunc('ievents', 'user', 'params',
        array('knames' => 'html_fields,q_fields,display_formats,locale')
    ));

    // Get the user parameters.
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:' . $max_numitems, $numitems, $default_numitems, XARVAR_NOT_REQUIRED)) return;

    // TODO: sorting and ordering flags and parameters

    // Get the display format.
    // The possible display formats are any from a set list, plus the export formats.
    $export_object = xarModAPIfunc('ievents', 'export', 'new_export');
    if (!empty($export_object) && is_array($export_object->handlers)) {
        $export_handlers = $export_object->handlers;
        $export_formats = array_keys($export_handlers);
    } else {
        $export_handlers = array();
        $export_formats = array();
    }
    $valid_formats = $display_formats;
    foreach($export_handlers as $export_handler_key => $export_handler) {
        $valid_formats[$export_handler_key] = $export_handler_key;
    }
    //$valid_formats = array_merge($export_formats, $display_formats);
    // Include the rss format if the theme is available.
    if (xarThemeIsAvailable('rss')) $valid_formats['rss'] = 'RSS';
    xarVarFetch('format', 'enum:' . implode(':', array_keys($valid_formats)), $format, $default_display_format, XARVAR_NOT_REQUIRED);


    //
    // Various start/end date selection 
    //

    // unix timestamp format start/end.
    // These are the master start/end variables. Other parameters may override them, but all dates
    // ultimately come to these two.
    // These set the defaults also - events from today to one month ahead.
    xarVarFetch('ustartdate', 'int', $ustartdate, strtotime($default_startdate), XARVAR_NOT_REQUIRED);
    xarVarFetch('uenddate', 'int', $uenddate, strtotime($default_enddate), XARVAR_NOT_REQUIRED);

    // Allow selection by year/month/day parts
    xarVarFetch('startyear', 'pre:num:passthru:str:4', $startyear, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('startmonth', 'pre:num:passthru:str:1:2', $startmonth, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('startday', 'pre:num:passthru:str:1:2', $startday, '', XARVAR_NOT_REQUIRED);

    xarVarFetch('endyear', 'pre:num:passthru:str:4', $endyear, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('endmonth', 'pre:num:passthru:str:1:2', $endmonth, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('endday', 'pre:num:passthru:str:1:2', $endday, '', XARVAR_NOT_REQUIRED);

    // The startdate and enddate can be passed in YYYYMMDD format.
    xarVarFetch('startdate', 'pre:num:passthru:regexp:/([0-9]{4}|[0-9]{6}|[0-9]{8})/', $startdate, '', XARVAR_NOT_REQUIRED);
    xarVarFetch('enddate', 'pre:num:passthru:regexp:/([0-9]{4}|[0-9]{6}|[0-9]{8})/', $enddate, '', XARVAR_NOT_REQUIRED);
    
    // Check the start date individual components.
    // Input can consist of year, year/month or year/month/day
    if ($startdate == '' && !empty($startyear)) {
        if (!empty($startmonth)) {
            // Pad the month out to two characters.
            $startmonth = str_pad($startmonth, 2, '0', STR_PAD_LEFT);
            if (!empty($startday)) {
                // Year month and day
                $startday = str_pad($startday, 2, '0', STR_PAD_LEFT);
                $startdate = "${startyear}${startmonth}${startday}";
            } else {
                // Just year and month
                $startdate = "${startyear}${startmonth}";
            }
        } else {
            // Just the year
            $startdate = "${startyear}";
        }
    }

    // Check the end date individual components.
    if ($enddate == '' && !empty($endyear)) {
        if (!empty($endmonth)) {
            $endmonth = str_pad($endmonth, 2, '0', STR_PAD_LEFT);
            if (!empty($endday)) {
                // Year month and day
                $endday = str_pad($endday, 2, '0', STR_PAD_LEFT);
                $enddate = "${endyear}${endmonth}${endday}";
            } else {
                // Just year and month
                $enddate = "${endyear}${endmonth}";
            }
        } else {
            // Just the year
            $enddate = "${endyear}";
        }
    }

    // If the startdate or enddate strings are set, then convert them into unix timestamps
    if (!empty($startdate)) {
        $urange = xarModAPIfunc('ievents', 'user', 'daterange2dates', array('sstartdate' => $startdate));
        if (!empty($urange)) {
            $ustartdate = $urange['startdate'];
        } else {
            // The startdate is invalid, since it could not be converted.
            $startdate = '';
        }
    }

    if (!empty($enddate)) {
        $urange = xarModAPIfunc('ievents', 'user', 'daterange2dates', array('senddate' => $enddate));
        if (!empty($urange)) {
            $uenddate = $urange['enddate'];
        } else {
            // The enddate is invalid, since it could not be converted.
            $enddate = '';
        }
    }

    // These two parameters handle "next N days/weeks/months/years" date selection.
    xarVarFetch('datenumber', 'int:0:365', $datenumber, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('datetype', 'pre:lower:passthru:enum:days:weeks:months:quarters:years', $datetype, '', XARVAR_NOT_REQUIRED);

    // Grouping of listed items.
    // Grouping should affect the sorting too, since grouping my a time period
    // would imply sorting by that time period too.
    // The default grouping is configurable (with no grouping - '' - being an option)
    xarVarFetch('group', 'pre:lower:passthru:enum:day:week:month:quarter:year:none', $group, $default_group, XARVAR_NOT_REQUIRED);
    if ($group == 'none') $group = '';

    // Event ID.
    // The user has selected an individual event ID.
    xarVarFetch('eid', 'id', $eid, 0, XARVAR_NOT_REQUIRED);

    // Calendar ID
    xarVarFetch('cid', 'id', $cid, 0, XARVAR_NOT_REQUIRED);

    // Query text
    xarVarFetch('q', 'pre:trim:left:200:passthru:strlist: :pre:trim:passthru:str::30', $q, '', XARVAR_NOT_REQUIRED);
    // Remove duplicate runs of spaces, then split into words
    // We don't support "quoted phrases" in this simple keyword search.
    $q = preg_replace('/ +/', ' ', $q);

    //
    // Validate and process some of the date parameters.
    // Any time component is irrelevant and will be stripped off later.
    //

    // Textual ranges override any other values.
    xarVarFetch('range', 'str', $range, '', XARVAR_NOT_REQUIRED);
    if (!empty($range)) {
        $urange = xarModAPIfunc('ievents', 'user', 'daterange2dates', array('range' => $range));
        if (!empty($urange)) {
            $ustartdate = $urange['startdate'];
            $uenddate = $urange['enddate'];
        }
    }

    // Check the simple/advanced search form flag.
    xarVarFetch('advanced', 'str:1:1', $advanced, '', XARVAR_NOT_REQUIRED);

    // The user may have selected a date range measured in other units (days, weeks, months)
    // TODO: deprecate these, in preference to the single 'range' string.
    if (!empty($datenumber) && !empty($datetype)) {
        // Set the end date to the start date plus any number of days, weeks, months or years.
        $uenddate = strtotime("+$datenumber $datetype", $ustartdate);
    }

    // Now we have a start and end date, we should make sure they are the right way around.
    if ($uenddate < $ustartdate) {
        // End date is earlier, so swap them around.
        list($uenddate, $ustartdate) = array($ustartdate, $uenddate);
    }

    // Links to other views of the calendar.
    // These are not links as such, but different starting dates for each view.
    $cal_links = array();
    $cal_links_labels = array();

    // If a calendar view, then get the dates into the right groupings.
    // i.e. for a month-view calendar, a full month only is displayed.
    if ($format == 'cal') {
        switch($group) {
            case 'year':
                $ustartdate = strtotime(date('Y', $ustartdate) . '0101');
                $uenddate = strtotime('+1 year -1 day', $ustartdate);
                $cal_links_labels['this_view'] = date('Y', $ustartdate);
                break;

            case 'quarter':
            	$ustartdate = mktime(0, 0, 0, ((floor(((date('m', $ustartdate)) / 3) + 1) * 3) - 2), '01', date('Y', $ustartdate));
                $uenddate = strtotime('+3 months -1 day', $ustartdate);
                $cal_links_labels['this_view'] = xarML('Quarter #(1), #(2)', 
                	floor(((date('m', $ustartdate)) / 3) + 1), 
                	date('Y', $ustartdate)
                );
                break;

            case 'month':
                $ustartdate = strtotime(date('Ym', $ustartdate) . '01');
                $uenddate = strtotime('+1 month -1 day', $ustartdate);
                $cal_links_labels['this_view'] = $locale['months']['long'][(date('m', $ustartdate) + 11) % 12 + 1] . ' ' . date('Y', $ustartdate);
                break;

            case 'week':
                // Get the start and end dates to the start and end of the week.
                $daystostartweek = date('w', $ustartdate) - $startdayofweek;
                if ($daystostartweek < 0) $daystostartweek += 7;
                $ustartdate = strtotime("-$daystostartweek days", $ustartdate);
                $uenddate = strtotime('+7 days', $ustartdate);
                $cal_links_labels['this_view'] = xarML('Week Starting #(1)', xarLocaleGetFormattedDate('long', $ustartdate));
                break;

			case 'day':
            default:
                // Default to a single day.
                $uenddate = $ustartdate;
                $cal_links_labels['this_view'] = $locale['days']['long'][date('w', $ustartdate)]
                    . ' ' . xarLocaleGetFormattedDate('long', $ustartdate);
                break;
        }

        $cal_links['next_year'] = date('Y', strtotime('+1 year', $ustartdate));
        $cal_links['prev_year'] = date('Y', strtotime('-1 year', $ustartdate));
        $cal_links['next_quarter'] = date('Ym', strtotime('+3 months', $ustartdate));
        $cal_links['prev_quarter'] = date('Ym', strtotime('-3 months', $ustartdate));
        $cal_links['next_month'] = date('Ym', strtotime('+1 month', $ustartdate));
        $cal_links['prev_month'] = date('Ym', strtotime('-1 month', $ustartdate));
        $cal_links['next_week'] = date('Ymd', strtotime('+1 week', $ustartdate));
        $cal_links['prev_week'] = date('Ymd', strtotime('-1 week', $ustartdate));
        $cal_links['next_day'] = date('Ymd', strtotime('+1 day', $ustartdate));
        $cal_links['prev_day'] = date('Ymd', strtotime('-1 day', $ustartdate));

        $cal_links_labels['next_year'] = date('Y', strtotime('+1 year', $ustartdate));
        $cal_links_labels['prev_year'] = date('Y', strtotime('-1 year', $ustartdate));
        $cal_links_labels['next_quarter'] = 'Q' . floor(((date('m', strtotime('+3 months', $ustartdate)) / 3) + 1)) . ', ' . date('Y', strtotime('+3 months', $ustartdate));
        $cal_links_labels['prev_quarter'] = 'Q' . floor(((date('m', strtotime('-3 months', $ustartdate)) / 3) + 1)) . ', ' . date('Y', strtotime('-3 months', $ustartdate));
        $cal_links_labels['next_month'] = $locale['months']['long'][(date('m', strtotime('+1 month', $ustartdate)) + 11) % 12 + 1];
        $cal_links_labels['prev_month'] = $locale['months']['long'][(date('m', strtotime('-1 month', $ustartdate)) + 11) % 12 + 1];
        $cal_links_labels['next_week'] = xarLocaleGetFormattedDate('medium', strtotime('+1 week', $ustartdate));
        $cal_links_labels['prev_week'] = xarLocaleGetFormattedDate('medium', strtotime('-1 week', $ustartdate));;
        $cal_links_labels['next_day'] = $locale['days']['long'][(date('w', strtotime('+1 day', $ustartdate)) + 7) % 7];
        $cal_links_labels['prev_day'] = $locale['days']['long'][(date('w', strtotime('-1 day', $ustartdate)) + 7) % 7];
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
    xarVarFetch('catid', 'regexp:/_{0,1}[0-9]+/', $catid, 0, XARVAR_NOT_REQUIRED);
    if (!empty($catid)) $catids = array($catid);

    // Category joining rule.
    xarVarFetch('crule', 'pre:lower:passthru:enum:and:or', $crule, 'and', XARVAR_NOT_REQUIRED);

    // List of catids[]
    xarVarFetch('catids', 'list:regexp:/_{0,1}[0-9]+/', $catids, array(), XARVAR_NOT_REQUIRED);

    // Everything in a single string.
    // N+M+..
    // Chose a joiner of ' ' or '+' because some servers convert all '+' chars in the URL to ' '.
    xarVarFetch('cats', 'strlist: +:regexp:/_{0,1}[0-9]+/', $cats1, '', XARVAR_NOT_REQUIRED);
    if (!empty($cats1)) {
        $catids = explode(' ', $cats1);
        $crule = 'and';
    } else {
        xarVarFetch('cats', 'strlist:-:regexp:/_{0,1}[0-9]+/', $cats2, '', XARVAR_NOT_REQUIRED);
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
    if (!empty($q) && !empty($q_fields)) $event_params['q'] = $q;

    // Add in the category restrictions if required.
    if (!empty($catids)) {
        $event_params['catids'] = $catids;
        $event_params['crule'] = $crule;
    }

    // Fetch the events.
    $events = xarModAPIfunc('ievents', 'user', 'getevents', $event_params);
    //echo "<pre>"; var_dump($events); echo "</pre>";

    //
    // If the user has selected a specific event, then
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
                $event =& $events[$eid];
            } else {
                // Still not found the event - probably does not exist.
                $event = array();
            }
            $prev_event = array();
            $next_event = array();
            $page_position = 1;
            $list_position = 1;
        } else {
            // Count which element it is, i.e. the position on the page.
            // A handy value in the list helps us there.
            $page_eventcount = count($events);
            $page_position = $events[$eid]['position'];
            $event =& $events[$eid];

            // If we are at the start of a page:
            // - if on the first page, then there is no previous event
            // - if not on the first page, then the previous event is on the previous page
            if ($page_position == 1) {
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
                $prev_event_arr = array_slice($events, $page_position - 1 - 1, 1);
                $prev_event = reset($prev_event_arr);
                $prev_event['startnum'] = $startnum;
            }

            // If we are at the end of a page:
            // - if on the last page, then there is no next event
            // - if not on the last page, then the next event is on the next page
            if ($page_position == $page_eventcount) {
                // Last event on the page
                // If we are on the last page, then there are no more events.
                // We won't know that unless we try fetching the next event.
                // Note: a shortcut is to look at the position on the page - if less than
                // numitems, then we are almost certainly on the last page OR privileges
                // have chopped a few out of the list (we cannot tell which).
                //if ($page_position == $numitems) {
                    $next_event = xarModAPIfunc('ievents', 'user', 'getevent',
                        array_merge($event_params, array('startnum' => $startnum + $page_eventcount, 'numitems' => 1))
                    );
                //}
                if (!empty($next_event)) {
                    // Set startnum to the next page.
                    $next_event['startnum'] = $numitems + $startnum;
                }
            } else {
                // Not the last on the page.
                // +1: next element; -1: zero-based offset for array_slice
                $next_event_arr = array_slice($events, $page_position, 1);
                $next_event = reset($next_event_arr);
                $next_event['startnum'] = $startnum;
            }

            // The position of the current event in the total matched list.
            $list_position = $page_position + $startnum - 1;
        }
    } else {
        $prev_event = array();
        $next_event = array();
        $event = array();
        $page_position = 0;
        $list_position = 0;
    }


    // Create pagination.
    // The url params would be slightly different to the event params (no unix timestamps
    // for a start, and possibly different category parameter formats).
    $url_params = array(
        'startdate' => $startdate,
        'enddate' => $enddate,
    );

    // Include some items only if not the default (to try and keep URLs shorter).
    if ($startnum != 1) $url_params['startnum'] = $startnum;
    if ($numitems != $default_numitems) $url_params['numitems'] = $numitems;
    if ($group != $default_group) $url_params['group'] = $group;
    if ($format != $default_display_format) $url_params['format'] = $format;
    if (!empty($advanced)) $url_params['advanced'] = 'y';

    // Add the categories selection in if available.
    if (!empty($cats)) $url_params['cats'] = $cats;
    if (!empty($cid)) $url_params['cid'] = $cid;
    if (!empty($q) && !empty($q_fields)) $event_params['q'] = $q;

    // If calendar view, then default to maximum number of events, starting at number 1
    if ($format == 'cal') {
        $event_params['startnum'] = 1;
        $event_params['numitems'] = $max_numitems;
    }
    
    // Count of all matching events.
    $total_events = xarModAPIFunc('ievents', 'user', 'countevents', $event_params);

    // The pager is a block of HTML
    $pager = xarTplGetPager($startnum, $total_events,
        xarModURL('ievents', 'user', 'view', array_merge($url_params, array('startnum' => '%%'))), $numitems
    );

    //
    // Create a 'feed_params' array containing the pertinent details for a feed URL to this page.
    //
    $feed_params = array();
    if (!empty($cats)) $feed_params['cats'] = $cats;
    if (!empty($cid)) $feed_params['cid'] = $cid;
    if (!empty($q) && !empty($q_fields)) $feed_params['q'] = $q;
    // TODO: include some more intelligently-selected relative dates
    $feed_params['range'] = 'next6months';


    //
    // Perform grouping of events if required.
    //

    // Now create a reference array of these event IDs, allowing events to be grouped.
    // Loop though each event to put them into a group (quanta, day, week, month, quarter or year), if required.
    $groups = array();
    foreach($events as $eventkey => $eventvalue) {
        // Add some other details to each event, that will be useful.
        // Add the detail URL, taking into account the current search criteria.
        $events[$eventkey]['detail_url'] = xarModURL(
            'ievents', 'user', 'view',
            array_merge($url_params, array('eid' => $eventvalue['eid']))
        );

        if (!empty($group)) {
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

    // If calendar view, then create a calendar object.
    // TODO: give the object a name in a better namespace.
    if ($format == 'cal') {
        include_once(dirname(__FILE__) . '/../xarincludes/calendar.inc.php');
        $cal = new ieventsCalendar;

        // Set the format.
        // TODO: include daily.
        switch($group) {
            case 'year':
                $cal->calFormat = 'fullYear';
                $cal->DOWformat = 'xshort';
                break;
            case 'quarter':
                $cal->calFormat = 'quarterYear';
                $cal->DOWformat = 'xshort';
                break;
            case 'month':
                $cal->calFormat = 'largeMonth';
                break;
            case 'week':
                $cal->calFormat = 'weekly';
                $cal->DOWformat = 'short';
                break;
            case 'day':
                $cal->calFormat = 'daily';
                break;
        }

        if (!empty($cid)) $cal->cid = $cid;
        $cal->displayPrevNext = false;
        $cal->displayEvents = true;
        $cal->startingDOW = $startdayofweek;
        $cal->showWeek = false;
        $cal->calDay = strtotime(date('Ymd', $ustartdate));
        $cal->calWeek = $ustartdate;
        $cal->calMonth = date('m', $ustartdate);
        $cal->calQuarter = floor(((date('m', $ustartdate) - 1) / 3) + 1);
        $cal->calYear = date('Y', $ustartdate);
		$cal->quanta = xarModGetVar('ievents', 'quanta');

        // Pass the locale data in.
        $cal->dayNames = $locale['days']['long'];
        $cal->dayNamesShort = $locale['days']['short'];
        $cal->dayNamesXShort = $locale['days']['xshort'];

        $cal->monthNames = $locale['months']['long'];
        $cal->monthNamesShort = $locale['months']['short'];

        // Add the events
        // TODO: move this markup to the templates.
        foreach($events as $eventkey => $eventvalue) {
            $cal->addEvent(
                $eventvalue['startdate'],
                $eventvalue['title'],
                $eventvalue['detail_url'],
                $eventvalue['all_day'],
                xarModURL('ievents','user','view',array(
                    'startdate' => date('Ymd', $eventvalue['startdate']),
                    'enddate' => date('Ymd', $eventvalue['startdate']),
                    'group' => 'day'
                ))
            );
        }
        $cal_output = $cal->display();
    } else {
        $cal = NULL;
    }

    // Get a list of calendars the user has access to.
    $calendars = xarModAPIfunc('ievents', 'user', 'getcalendars', array('event_priv' => 'OVERVIEW'));

    // Do the output transforms.
    if (!empty($html_fields)) {
        $html_fields = explode(',', $html_fields);

        foreach($events as $eventkey => $eventvalue) {
            // Make sure the field is HTML
            foreach($html_fields as $html_field) {
                if (!empty($eventvalue[$html_field])) {
                    $events[$eventkey][$html_field] = xarModAPIfunc('ievents','user','transform',
                        array('html' => $eventvalue[$html_field])
                    );
                }
            }

            // Transform hooks.
            // If the fields have been limited for transform, then pass those
            // fields into the transform hook too.
            $events[$eventkey]['transform'] = $html_fields;

            // Set the itemtype for the transform hook system.
            $events[$eventkey]['itemtype'] = $itemtype_events;

            // The vast majority of the data will not need transforming, but all fields will
            // be passed in just in case they are needed somewhere.
            $transformed = xarModCallHooks('item', 'transform', $events[$eventkey]['eid'], $events[$eventkey], $module);

            // Merge just the transformed fields back into the event.
            // We must do them individually, as some could be linked from grouped fields.
            foreach($html_fields as $html_field) {
                $events[$eventkey][$html_field] = $transformed[$html_field];
            }
        }
    }

    // Display hook for the current event, but only if there is a current event.
    if (!empty($eid) && !empty($event)) {
        $item = $event;
        $item['module'] = $module;
        $item['itemtype'] = $itemtype_events;
        $item['itemid'] = $eid;
        $item['returnurl'] = xarServerGetCurrentURL(array(),'false');

        // Get the display hook stuff.
        $hooks = xarModCallHooks('item', 'display', $eid, $item);
    }

    // Get all category information
    $categories = xarModAPIfunc('ievents', 'user', 'getallcategories');
    //echo "<pre>"; var_dump($categories); echo "</pre>";


    // Perform an export if required.
    if (!empty($export_formats)) {
        // Check if the user has asked for an export.
        if (in_array($format, $export_formats)) {
            // Set the export handler.
            $export_object->set_handler($format);

            // Stream the export (or redirect to an error page)
            return $export_object->stream_export($events);
        }
    }


    // Create some arrays useful for date drop-downs
    // TODO: move this to an API
    // TODO: add days of the week etc.
    $lists = array();
    $lists['daynum'] = array('');
    for($i = 1; $i <= 31; $i++) $lists['daynum'][$i] = $i;
    $localeData = xarMLSLoadLocaleData();
    $lists['monthnum'] = array('');
    $lists['monthshort'] = array('');
    $lists['monthlong'] = array('');
    for($i = 1; $i <= 12; $i++) {
        $lists['monthnum'][$i] = $i;
        $lists['monthshort'][$i] = $localeData["/dateSymbols/months/${i}/short"];
        $lists['monthlong'][$i] = $localeData["/dateSymbols/months/${i}/full"];
    }
    $thisyear = (int)date('Y');
    $lists['yearnum'] = array('');
    for($i = $thisyear + $year_range_min; $i <= $thisyear + $year_range_max; $i++) {
        $lists['yearnum'][$i] = $i;
    }



    //
    // Pass data back out to the template
    //

    // By keeping the bl data and variable names the same, passing data is easy.
    $bl_data = @compact(
        // Dates
        'ustartdate', 'uenddate',
        'startdate', 'enddate',
        'startyear', 'startmonth', 'startday',
        'endyear', 'endmonth', 'endday',
        'datenumber', 'datetype',

        // Groups and grouping
        'group', 'groups',

        // Navigation within the matched event list
        'next_event', 'prev_event',
        'eid', 'event', 'page_position', 'list_position', 'total_events',
        'feed_params',

        // Pager
        'pager', 'url_params',
        'startnum', 'numitems', 'default_numitems',

        // Lists (data and lookup)
        'events', 'calendars',
        'categories',
        'lists', // TODO: move to an API, for use directly in templates

        // Categories
        'cats', 'catid', 'catids', 'crule',
        'cid',

        // Other
        'hooks', 'q', 'q_fields', 'export_handlers', 'format',
        'cal_links', 'cal_links_labels', 'cal', 'cal_output', 'advanced'
    );

    // RSS - switch to the RSS theme if the format is RSS
    if ($format == 'rss' && xarThemeIsAvailable('rss')) xarTplSetThemeName('rss');

    return $bl_data;
}

?>
