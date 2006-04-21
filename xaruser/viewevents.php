<?php
/**
 * View all events in a list
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
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
 * @TODO MichelV: include the linked events here
 */

function julian_user_viewevents($args)
{
    // Extract args
    extract ($args);

    // Get parameters from the input.
    if (!xarVarFetch('startnum',    'int:0:', $startnum,    1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby',      'str:1:', $sortby,      'eventDate', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('orderby',     'str:1:', $orderby,     'DESC', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('event_id',    'int:0:', $event_id,    0)) return;
    if (!xarVarFetch('startmonth',  'str::',  $startmonth,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startday',    'str::',  $startday,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startyear',   'str::',  $startyear,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endmonth',    'str::',  $endmonth,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endday',      'str::',  $endday,      '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endyear',     'str::',  $endyear,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cal_date',    'str::',  $caldate,     '')) return;
    if (!xarVarFetch('catid',       'int:1:', $catid,       NULL, XARVAR_NOT_REQUIRED)) return;

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
    //$bl_data['shortDayNames'] = $c->getShortDayNames($cal_sdow);
    //$bl_data['calendar'] = $c;
    if (empty($startday)) {
        // Set the start day to the first month and day of the selected year.
        $startdate=$bl_data['selected_year']."-01-01";
    }
    if (empty($enddate)) {
        // Set the end date to the last month and last day of the selected year.
        $enddate=$bl_data['selected_year']."-12-31";
    }
    // Bullet style
    $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';
    // Get the events for the selected year.
   // $bl_data['event_array'] = xarModAPIFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$enddate, 'catid' => $catid));
    // Set the url to this page in session as the last page viewed.
  //  $lastview=xarModURL('julian','user','year',array('cal_date'=>$bl_data['cal_date']));
  //  xarSessionSetVar('lastview',$lastview);

    // Get the Event Name
    $bl_data['eventName'] = '';
    if ($event_id) {
        $event = xarModAPIFunc('julian',
                               'user',
                               'getevents',
                               array('event_id' => $eID));

        // Check for exceptions
        if (!isset($event) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return; // throw back
        }

        $bl_data['eventName'] = $event['eName'];
    }

    // Set Event ID.
    $bl_data['event_id'] = $event_id;

    // Prepare the array variables that will hold all items for display.
    $bl_data['reloadlabel'] = xarVarPrepForDisplay(xarML('Reload'));
    $bl_data['events'] = array();
    $bl_data['startnum'] = $startnum;
    $bl_data['sortby'] = $sortby;

    // Define the Start and End Dates.
    if ($caldate != '') {
        $startdate = $caldate;
    } else {
        $startdate = ($startyear . $startmonth . $startday);
    }
    $enddate = ($endyear . $endmonth . $endday);
    $bl_data['startdate'] = $startdate;
    $bl_data['enddate'] = $enddate;

    // If sorting by Event date, then sort in descending order,
    // so that the latest Event is first.
    if ($sortby == 'eventDate') {
        $orderby = 'DESC';
    }

    // The user API Function is called.
    $events = xarModAPIFunc('julian',
                            'user',
                            'getevents',
                            array('startnum'  => $startnum,
                                  'numitems'  => xarModGetVar('julian','itemsperpage'),
                                  'sortby'    => $sortby,
                                  'orderby'   => $orderby,
                                  'startdate' => $startdate,
                                  'enddate'   => $enddate,
                                  'event_id'  => $event_id,
                                  'catid'     => $catid));

    // Check for exceptions.
    if (!isset($events) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return; // throw back
    }

    // Add the array of Events to the template variables.
    $bl_data['events'] = $events;


    // Create sort by URLs.
    if ($sortby != 'eventDate' ) {
        $bl_data['eventdateurl'] = xarModURL('julian',
                                           'user',
                                           'viewevents',
                                           array('startnum' => 1,
                                                 'sortby' => 'eventDate',
                                                 'event_id' => $event_id,
                                                 'catid' => $catid));
    } else {
        $bl_data['eventdateurl'] = '';
    }

    if ($sortby != 'eventName' ) {
        $bl_data['eventnameurl'] = xarModURL('julian',
                                           'user',
                                           'viewevents',
                                           array('startnum' => 1,
                                                 'sortby' => 'eventName',
                                                 'event_id' => $event_id,
                                                 'catid' => $catid));
    } else {
        $bl_data['eventnameurl'] = '';
    }

    if ($sortby != 'eventDesc' ) {
        $bl_data['eventdescurl'] = xarModURL('julian',
                                           'user',
                                           'viewevents',
                                           array('startnum' => 1,
                                                 'sortby' => 'eventDesc',
                                                 'event_id' => $event_id,
                                                 'catid' => $catid));
    } else {
        $bl_data['eventdescurl'] = '';
    }

    if ($sortby != 'eventLocn' ) {
        $bl_data['eventlocnurl'] = xarModURL('julian',
                                           'user',
                                           'viewevents',
                                           array('startnum' => 1,
                                                 'sortby' => 'eventLocn',
                                                 'event_id' => $event_id,
                                                 'catid' => $catid));
    } else {
        $bl_data['eventlocnurl'] = '';
    }

    if ($sortby != 'eventCont' ) {
        $bl_data['eventconturl'] = xarModURL('julian',
                                           'user',
                                           'viewevents',
                                           array('startnum' => 1,
                                                 'sortby' => 'eventCont',
                                                 'event_id' => $event_id,
                                                 'catid' => $catid));
    } else {
        $bl_data['eventconturl'] = '';
    }

    if ($sortby != 'eventFee' ) {
        $bl_data['eventfeeurl'] = xarModURL('julian',
                                           'user',
                                           'viewevents',
                                           array('startnum' => 1,
                                                 'sortby' => 'eventFee',
                                                 'event_id' => $event_id,
                                                 'catid' => $catid));
    } else {
        $bl_data['eventfeeurl'] = '';
    }

    // Create Pagination.
    $bl_data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('julian',
                                                  'user',
                                                  'countevents',
                                                  array('event_id' => $event_id,
                                                        'catid' => $catid)),
                                    xarModURL('julian',
                                              'user',
                                              'viewevents',
                                              array('startnum' => '%%',
                                                    'sortby'   => $sortby,
                                                    'event_id' => $event_id,
                                                    'catid'    => $catid,
                                                    'orderby'  => $orderby)),
                                    xarModGetVar('julian', 'itemsperpage'));
    $bl_data['catid'] = $catid;
    // Return the template variables defined in this function.
    return $bl_data;
}
?>