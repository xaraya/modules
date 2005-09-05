<?php

/**
* File: $Id: month.php,v 1.5 2005/04/01 12:15:19 michelv01 Exp $
*
* This function gets the events for a particular month.
*
* @package Xaraya eXtensible Management System
* @copyright (C) 2004 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin/David St.Clair
*/
function julian_user_month()
{
   // Security check
   if (!xarSecurityCheck('Viewjulian')) return; 

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
   $bl_data['event_array']=xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$enddate));
   $bl_data['calendar']=$c;
   $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';
   //set the url to this page in session as the last page viewed
   $lastview=xarModURL('julian','user','month',array('cal_date'=>$bl_data['cal_date']));
   xarSessionSetVar('lastview',$lastview);
    return $bl_data;
}
?>
