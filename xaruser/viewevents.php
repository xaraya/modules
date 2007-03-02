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
 * @TODO Use proper validation on input parameters
 * @TOOD Support variable numitems as a parameter
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

    if (!xarVarFetch('cal_date',    'str::',  $caldate,     '')) return;
    if (!xarVarFetch('catid',       'id',     $catid,       0, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('datenumber', 'int:0:365', $datenumber, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('datetype', 'pre:lower:passthru:enum:days:weeks:months:years', $datetype, '', XARVAR_NOT_REQUIRED)) return;


    // Security check
    if (!xarSecurityCheck('ReadJulian', 1)) return;

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

    // Start by defaulting the start and end dates. We can override the defaults later.
    // We will keep all dates in string format YYYYMMDD
    // Today:
    $startdate = date('Ymd');
    // One month from today:
    $enddate = date('Ymd', strtotime('+1 month'));
    
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

    // Bullet style
    $bl_data['Bullet'] = '&' . xarModGetVar('julian', 'BulletForm') . ';';

    // Prepare the array variables that will hold all items for display.
    $bl_data['events'] = array();
    $bl_data['startnum'] = $startnum;
    $bl_data['sortby'] = $sortby;

    // Define the Start and End Dates.
    if ($caldate != '') {
        // FIXME: this isn't used. Remove it or fix it?
        $startdate_chooser = $caldate;
    } else {
        $bl_data['startdate'] = ($startyear . $startmonth . $startday);
    }

    $enddate_chooser = ($endyear . $endmonth . $endday);
    //$bl_data['startdate'] = $startdate;
    $bl_data['enddate'] = $enddate_chooser;

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
    if (!isset($events) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return; // throw back
    }

    // Add the array of Events to the template variables.
    $bl_data['events'] = $events;

    // Create sort-by URLs.
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

?>