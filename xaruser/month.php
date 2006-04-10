<?php
/**
 * View one month
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
 * This function gets the events for a particular month.
 *
 * This module:
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author MichelV <michelv@xaraya.com>
 *
 * @param int catid The ID of the category to filter for
 * @return array
 */
function julian_user_month($args)
{
    extract ($args);

    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('ViewJulian',1)) {
       return;
    }

    //get post/get vars
    $cal_sdow = xarModGetVar('julian','startDayOfWeek');
    //set the selected date parts, timestamp and cal_date in the data array
    $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    //set the start date to the first day of the selected month and year
    $startdate = $bl_data['selected_year']."-".$bl_data['selected_month']."-01";
    //determine the number of days for the selected month and year
    $numdays=date('t',strtotime($startdate));
    //set the end date to the last day of the selected month and year
    $enddate = $bl_data['selected_year']."-".$bl_data['selected_month']."-".$numdays;
    //load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    $bl_data['month'] = $c->getCalendarMonth($bl_data['selected_year'].$bl_data['selected_month']);
    // Starting day of the week
    $bl_data['cal_sdow'] = $cal_sdow;
    $bl_data['longDayNames'] = $c->getLongDayNames($cal_sdow);
    //get the events for the selected month
    $bl_data['event_array']=xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$enddate, 'catid' => $catid));
    $bl_data['calendar']=$c;
    $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';
    //set the url to this page in session as the last page viewed
    $lastview=xarModURL('julian','user','month',array('cal_date'=>$bl_data['cal_date'], 'catid' => $catid));
    xarSessionSetVar('lastview',$lastview);
    return $bl_data;
}
?>
