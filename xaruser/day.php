<?php
/**
 * View one day
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 *
 * This function gets the events for a particular day.
 *
 * This module:
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author MichelV <michelv@xaraya.com>
 * @param int catid ID of the category to filter for OPTIONAL
 * @return array
 */
function julian_user_day()
{
    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;
    // get post/get vars
    xarVarFetch('cal_sdow','int:0:6',$cal_sdow,xarModGetVar('julian','startDayOfWeek'));

    // Security check
    if (!xarSecurityCheck('ViewJulian', 1)) {
       return;
    }
    // load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');

    //set the selected date parts, timestamp, and cal_date in the data array
    $data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    $data['month'] = $c->getCalendarMonth($data['selected_year'].$data['selected_month']);
    $data['longDayNames'] = $c->getLongDayNames($cal_sdow);
    $data['calendar'] = $c;

    // set the start date
    $startdate = $data['selected_year']."-".$data['selected_month']."-".$data['selected_day'];

    // get the events for the selected day
    $data['event_array']=xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$startdate));

    // the next two variables help determine which color is displayed for this day depending on whether
    // it is a weekend or the current day
    $data['daydate']=date('Ymd',strtotime($startdate));
    $data['isweekend']=$c->isWeekend($startdate);
    $data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';

    // Add the add link and include a security check
    if (xarSecurityCheck('AddJulian',0)) {
        $addlink = xarModURL('julian','user','addevent',array('cal_date'=>$data['cal_date']));
    } else {
        $addlink = '';
    }
    $data['addlink'] = $addlink;
    //set the url to this page in session as the last page viewed
    $lastview=xarModURL('julian','user','day',array('cal_date'=>$data['cal_date'], 'catid' => $catid));
    xarSessionSetVar('lastview',$lastview);

    // Return the day template
    return $data;
}
?>