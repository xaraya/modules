<?php

/**
* File: $Id: week.php,v 1.5 2005/04/01 12:15:19 michelv01 Exp $
*
* Displays a week of events.
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

function julian_user_week()
{

   // Security check - important to do this as early as possible to avoid
   // potential security holes or just too much wasted processing
   if (!xarSecurityCheck('Viewjulian')) return; 

   //get post/get vars
   $cal_sdow = xarModGetVar('julian','startDayOfWeek');
   //load the calendar class 
   $c =& xarModAPIFunc('julian','user','factory','calendar');
    $c->setStartDayOfWeek($cal_sdow);//
    //set the selected date parts,timestamp, and cal_date in the data array
    $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    //Week is an array with an index of 0 - 6 indicating the days of the week. (starting with Sunday = 0) The values are the dates in the
    //format of YYYYMMDD
    $bl_data['week'] = $c->getCalendarWeek($bl_data['selected_year'].$bl_data['selected_month'].$bl_data['selected_day']);  
    $bl_data['longDayNames'] = $c->getLongDayNames($c->getStartDayOfWeek());
   $bl_data['calendar'] = $c;   
   //set the start date to the first day of this week
   $startdate = date("Y-m-d",strtotime($bl_data['week'][0]));
   //set the end date to the last day of this week
   $enddate = date("Y-m-d",strtotime($bl_data['week'][6]));
   //get the events for the selected week
   $bl_data['event_array']=$c->getEvents($startdate,$enddate);
   $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';   
   //set the url to this page in session as the last page viewed
   $lastview=xarModURL('julian','user','week',array('cal_date'=>$bl_data['cal_date']));
   xarSessionSetVar('lastview',$lastview);
    return $bl_data;
}
?>
