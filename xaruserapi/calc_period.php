<?php

/**
 * Given a start date, an event date, and a grouping period, calculate which
 * group that start date appears in.
 * Used to group events into days, weeks, months etc.
 * All dates are unix timestamps.
 *
 * @param startdate at date that is located in period 1
 * @param eventdate the date to determine the period for
 * @param periodtype the type of the period (day, week, month, year)
 */

function ievents_userapi_calc_period($args)
{
    extract($args);

    if (!isset($startdate) || !isset($eventdate)) return;

    // Validate and default the group type.
    if (!xarVarValidate('pre:lower:passthru:enum:day:week:month:year', $group, true)) $group = 'week';

    // Get global parameters.
    $gparams = xarModAPIfunc('ievents', 'user', 'params');

    switch ($group) {
    case 'week':
    default:
        // TODO: does the first day of the week need to be configurable?
        $startdayofweek = $gparams['startdayofweek'];
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
        $group = 'week';

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

    // Return numeric array elements, so list() can be used. Don't change the order!
    return array(
        $group,
        $periodnumber,
        $period1start,
        $periodstart,
        $periodend,
    );
}

?>