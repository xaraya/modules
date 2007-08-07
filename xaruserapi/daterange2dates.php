<?php

/**
 * Convert a 'daterange' string into a pair of dates.
 * @param range string The 'daterange' string, in any one of a number of possible formats
 * @param datetype string
 * @param datenumber integer
 * @param startdate integer
 * @param enddate integer
 * @param sstartdate string YYYY, YYYYMM or YYYYMMDD
 * @param senddate string YYYY, YYYYMM or YYYYMMDD
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
        //return $return; // Other valid combinations now TODO: define them all.
    }

    // Get module parameters
    extract(xarModAPIfunc('ievents', 'user', 'params',
        array('knames' => 'startdayofweek')
    ));

    // Some handy definitions.

    // Today at 00:00
    $today = strtotime(date('Y-m-d'));

    // The first day of the current week
    $daystostartweek = date('w', $today) - $startdayofweek;
    if ($daystostartweek < 0) $daystostartweek += 7;
    // Get the period start date (unix timestamp) by counting back the appropriate number of days
    $thisweekstart = strtotime("-$daystostartweek days", $today);

    // The start of the current month
    $thismonthstart = strtotime(date('Ym', $today) . '01');

    // The start of the current year
    $thisyearstart = strtotime(date('Y', $today) . '0101');

    // The start and end dates may have been passed in as a strings.
    // Convert them into dates if so (from YYYY, YYYYMM or YYYYMMDD)
    if (isset($sstartdate) && is_string($sstartdate) && preg_match('/([0-9]{4}|[0-9]{6}|[0-9]{8})/', $sstartdate)) {
        $y = substr($sstartdate, 0, 4);
        if (strlen($sstartdate) >= 6) $m = substr($sstartdate, 4, 2); else $m = '01';
        if (strlen($sstartdate) == 8) $d = substr($sstartdate, 6, 2); else $d = '01';

        if (checkdate($m, $d, $y)) $startdate = strtotime("$y-$m-$d");
    }

    if (isset($senddate) && is_string($senddate) && preg_match('/([0-9]{4}|[0-9]{6}|[0-9]{8})/', $senddate)) {
        $y = substr($senddate, 0, 4);
        if (strlen($senddate) >= 6) $m = substr($senddate, 4, 2); else $m = '01';
        if (strlen($senddate) == 8) $d = substr($senddate, 6, 2); else $d = '01';

        if (checkdate($m, $d, $y)) {
            // We need the end of the month or year, if the day is not specified.
            if (strlen($senddate) == 8) $enddate = strtotime("$y-$m-$d");
            elseif (strlen($senddate) == 6) $enddate = strtotime("$y-$m-$d +1 month -1 day");
            elseif (strlen($senddate) == 4) $enddate = strtotime("$y-$m-$d +1 year -1 day");
        }
    }

    // Next N units (days, weeks, months or years), starting today.
    // e.g. next2months next7days next1year
    if (isset($range) && preg_match('/^next[0-9]{1,3}(day|days|week|weeks|month|months|year|years)$/', $range)) {
        $datenumber = preg_replace('/[^0-9]/', '', $range);
        $datetype = rtrim(preg_replace('/^next[0-9]+/', '', $range), 's') . 's';
    }

    // Window of N units around the start date.
    // e.g. 'window2months' will be the current date plus or minus two months.
    if (isset($range) && preg_match('/^window[0-9]{1,3}(day|days|week|weeks|month|months|year|years)$/', $range)) {
        $windowsize = preg_replace('/[^0-9]/', '', $range);
        $datenumber = $windowsize * 2;
        $datetype = rtrim(preg_replace('/^window[0-9]+/', '', $range), 's') . 's';
        $startdate = strtotime("-${windowsize} ${datetype}", $today);
    }


    // User requested the 'datetype' and 'datenumber' pair.
    if (isset($datenumber) && isset($datetype)) {
        if (xarVarValidate('int:1:365', $datenumber, true)
        && xarVarValidate('pre:lower:passthru:enum:days:weeks:months:years', $datetype, true)) {
            if (!isset($startdate)) $startdate = $today;
            $enddate = strtotime("+$datenumber $datetype", $startdate);
        }
    }


    // Check a few other formats
    if (isset($range) && !isset($startdate)) {
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