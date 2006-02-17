<?php
/**
 * View one day
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
 * @param int catid ID of the category to filter for
 * @return array
 */

function julian_user_day()
{
    // Security check
    if (!xarSecurityCheck('ReadJulian')) return;
    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;
    //get post/get vars
    xarVarFetch('cal_sdow','int:0:6',$cal_sdow,xarModGetVar('julian','startDayOfWeek'));

    //load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    //set the selected date parts, timestamp, and cal_date in the data array
    $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    $bl_data['month'] = $c->getCalendarMonth($bl_data['selected_year'].$bl_data['selected_month']);
    $bl_data['longDayNames'] = $c->getLongDayNames($cal_sdow);
    $bl_data['calendar'] = $c;
    //set the start date
    $startdate = $bl_data['selected_year']."-".$bl_data['selected_month']."-".$bl_data['selected_day'];
    //get the events for the selected day
    $bl_data['event_array']=xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$startdate));
    //the next two variables help determine which color is displayed for this day depending on whether
    //it is a weekend or the current day
    $bl_data['daydate']=date('Ymd',strtotime($startdate));
    $bl_data['isweekend']=$c->isWeekend($startdate);
    $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';
    //set the url to this page in session as the last page viewed
    $lastview=xarModURL('julian','user','day',array('cal_date'=>$bl_data['cal_date'], 'catid' => $catid));
    xarSessionSetVar('lastview',$lastview);
    return $bl_data;
}
?>