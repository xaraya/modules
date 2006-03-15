<?php
/**
 * View one week
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */

/**
 * Displays a week of events.
 *
 * This module:
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @param int catid ID of the category to filter for
 */

function julian_user_week($args)
{
    extract ($args);
    // Get the category id
    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('ViewJulian', 1, 'Item', "All:All:All:$catid")) {
       return;
    }
    // get post/get vars
    $cal_sdow = xarModGetVar('julian','startDayOfWeek');
    //load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    // set the selected date parts,timestamp, and cal_date in the data array
    $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    // Week is an array with an index of 0 - 6 indicating the days of the week. (starting with Sunday = 0) The values are the dates in the
    // format of YYYYMMDD
    $bl_data['week'] = $c->getCalendarWeek($bl_data['selected_year'].$bl_data['selected_month'].$bl_data['selected_day']);
    $bl_data['longDayNames'] = $c->getLongDayNames($cal_sdow);
    $bl_data['calendar'] = $c;

    //set the start date to the first day of this week
    $startdate = date("Y-m-d",strtotime($bl_data['week'][0]));
    //set the end date to the last day of this week
    $enddate = date("Y-m-d",strtotime($bl_data['week'][6]));

    //get the events for the selected week
    $bl_data['event_array'] = xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$enddate, 'catid' => $catid));
    $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';

    //set the url to this page in session as the last page viewed
    $lastview = xarModURL('julian','user','week',array('cal_date'=>$bl_data['cal_date'], 'catid'=> $catid));
    xarSessionSetVar('lastview',$lastview);

    return $bl_data;
}
?>