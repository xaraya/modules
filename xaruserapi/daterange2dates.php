<?php

/**
 * Convert a 'daterange' string into a pair of dates.
 * @param range string The 'daterange' string, in any one of a number of possible formats
 * @returns array
 *
 * Return elements:
 * - startdate: the start date in unix timestamp format
 * - enddate: the end date in unix timestamp format
 *
 */

function ievents_userapi_daterange2dates($args)
{
    extract($args);

    // Default return value, indicating an error.
    $return = array();

    if (!isset($range) || !is_string($range)) {
        return $return;
    }

    // Get module parameters
    list($startdayofweek) = xarModAPIfunc('ievents', 'user', 'params',
        array('names' => 'startdayofweek')
    );

    // Some handy definitions.

    // Today at 00:00
    $today = strtotime(date('Y-m-d'));

    // The first day of the current week
    $daystostartweek = date('w', time()) - $startdayofweek;
    if ($daystostartweek < 0) $daystostartweek += 7;
    // Get the period start date (unix timestamp) by counting back the appropriate number of days
    $thisweekstart = strtotime("-$daystostartweek days", $today);

    // The start of the current month
    $thismonthstart = strtotime(date('Ym', $today) . '01');

    // The start of the current year
    $thisyearstart = strtotime(date('Y', $today) . '0101');


    // Next N units (days, weeks, months or years), starting today.
    // e.g. next2months next7days next1year
    if (!isset($startdate) && preg_match('/^next[0-9]{1,3}(day|days|week|weeks|month|months|year|years)$/', $range)) {
        $datenumber = preg_replace('/[^0-9]/', '', $range);
        $datetype = rtrim(preg_replace('/^next[0-9]+/', '', $range), 's') . 's';
    }


    // User requested the 'datetype' and 'datenumber' pair.
    if (isset($datenumber) && isset($datetype)) {
        $startdate = $today;
        $enddate = strtotime("+$datenumber $datetype", $startdate);
    }


    // Check a few other formats
    if (!isset($startdate)) {
        switch($range) {
            case 'yesterday':
            case 'today':
            case 'tomorrow':
                // Reference these to the $today value to ensure they are truncated to 00:00
                $startdate = strtotime($range, $today);
                $enddate = strtotime($range, $today);
                break;
            case 'lastweek':
                $startdate = strtotime('-1 week', $thisweekstart);;
                $enddate = strtotime('-1 day', $thisweekstart);
                break;
            case 'thisweek':
            case 'week':
                $startdate = $thisweekstart;
                $enddate = strtotime('-1 second', strtotime('+1 week', $thisweekstart));
                break;
            case 'nextweek':
                $startdate = strtotime('+1 week', $thisweekstart);
                $enddate = strtotime('-1 second', strtotime('+2 weeks', $thisweekstart));
                break;
            case 'lastmonth':
                $startdate = strtotime('-1 month', $thismonthstart);
                $enddate = strtotime('-1 second', $thismonthstart);
                break;
            case 'thismonth':
            case 'month':
                $startdate = $thismonthstart;
                $enddate = strtotime('-1 second', strtotime('+1 month', $thismonthstart));
                break;
            case 'nextmonth':
                $startdate = strtotime('+1 month', $thismonthstart);
                $enddate = strtotime('-1 second', strtotime('+2 months', $thismonthstart));
                break;
            case 'lastyear':
                $startdate = strtotime('-1 year', $thisyearstart);
                $enddate = strtotime('-1 second', $thisyearstart);
                break;
            case 'thisyear':
            case 'year':
                $startdate = $thisyearstart;
                $enddate = strtotime('-1 second', strtotime('+1 year', $thisyearstart));
                break;
            case 'nextyear':
                $startdate = strtotime('+1 year', $thisyearstart);
                $enddate = strtotime('-1 second', strtotime('+2 years', $thisyearstart));
                break;
        }
    }

    // Set the return values if we have them.
    if (isset($startdate)) $return['startdate'] = $startdate;
    if (isset($enddate)) $return['enddate'] = $enddate;

    return $return;
}

?>