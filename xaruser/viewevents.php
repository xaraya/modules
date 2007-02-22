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
    if (!xarVarFetch('startnum',    'int:0:', $startnum,    1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby',      'str:1:', $sortby,      'eventDate', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',     'str:1:', $orderby,     'DESC', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('start_month', 'str::',  $startmonth,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('start_day',   'str::',  $startday,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('start_year',  'str::',  $startyear,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end_month',   'str::',  $endmonth,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end_day',     'str::',  $endday,      '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end_year',    'str::',  $endyear,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cal_date',    'str::',  $caldate,     '')) return;
    if (!xarVarFetch('catid',       'int:1:', $catid,       0, XARVAR_NOT_REQUIRED)) return;

   // Security check
   if (!xarSecurityCheck('ReadJulian', 1)) {
       return;
   }
    // Get the Start Day Of Week value.
    $cal_sdow = xarModGetVar('julian','startDayOfWeek');
    // Load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    $bl_data = array();
    // Set the selected date parts,timestamp, and cal_date in the data array.
    $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    $bl_data['year'] = $c->getCalendarYear($bl_data['selected_year']);
    // Create the date stretch to get events for.
    // We start with listing from today on.
    
    // First see if we are given a time stretch
    if (empty($startday)) {
        // Set the start day to the first month and day of the selected year.
        //$startdate=$bl_data['selected_year']."-01-01";
        //Use today
        $startdate = date("Ymd");
        $bl_data['start_year']=date("Y");
        $bl_data['start_month']=date("m");
        $bl_data['start_day']=date("d");
    } else {
        $startdate = $startyear.$startmonth.$startday;
        $bl_data['start_year']=$startyear;
        $bl_data['start_month']=$startmonth;
        $bl_data['start_day']=$startday;
        //echo "$startdate <br />";
    }
    if (empty($endday)) {
        // Set the end date to the last month and last day of the selected year.
        $nextmonth = date("Ymd",(mktime(0, 0, 0, date("m")+1, date("d"),  date("Y"))));
        $enddate=$nextmonth;

        $bl_data['end_year']=date("Y")+1;
        $bl_data['end_month']=date("m");
        $bl_data['end_day']=date("d");
    } else {
        $enddate = $endyear.$endmonth.$endday;
        $bl_data['end_year']=$endyear;
        $bl_data['end_month']=$endmonth;
        $bl_data['end_day']=$endday;
        //echo $enddate;
    }
    // Bullet style
    $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';

    // Prepare the array variables that will hold all items for display.
    $bl_data['reloadlabel'] = xarVarPrepForDisplay(xarML('Reload'));
    $bl_data['events'] = array();
    $bl_data['startnum'] = $startnum;
    $bl_data['sortby'] = $sortby;

    // Define the Start and End Dates.
    if ($caldate != '') {
        $startdate_chooser = $caldate;
    } else {
        $bl_data['startdate'] = ($startyear . $startmonth . $startday);
    }

    $enddate_chooser = ($endyear . $endmonth . $endday);
    //$bl_data['startdate'] = $startdate;
    $bl_data['enddate'] = $enddate_chooser;

    // If sorting by Event date, then sort in descending order,
    // so that the latest Event is first.
    if ($sortby == 'eventDate') {
        $orderby = 'DESC';
    }

    // The user API Function is called: get all events for these selectors
    $events = xarModAPIFunc('julian', 'user', 'getevents',
        array(
            'startnum'  => $startnum,
            'numitems'  => xarModGetVar('julian','itemsperpage'),
            'sortby'    => $sortby,
            'orderby'   => $orderby,
            'startdate' => $startdate,
            'enddate'   => $enddate,
            'catid'     => $catid
        )
    );

    // Check for exceptions.
    if (!isset($events) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return; // throw back
    }

    // Add the array of Events to the template variables.
    $bl_data['events'] = $events;

    // Create sort by URLs.
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

    // Create Pagination.
    $bl_data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('julian', 'user', 'countevents', array('catid' => $catid)),
        xarModURL('julian', 'user', 'viewevents',
            array(
                'startnum' => '%%', 
                'sortby'   => $sortby,
                'catid'    => $catid,
                'orderby'  => $orderby
            )
        ), xarModGetVar('julian', 'itemsperpage')
    );

    $bl_data['catid'] = $catid;

    // Return the template variables defined in this function.
    return $bl_data;
}
?>