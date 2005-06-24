<?php

/**
* File: $Id: day.php,v 1.4 2005/04/01 12:15:19 michelv01 Exp $
*
* This function gets the events for a particular day.
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

function julian_user_day()
{
   // Security check - important to do this as early as possible to avoid
   // potential security holes or just too much wasted processing
   if (!xarSecurityCheck('Viewjulian')) return; 

   //get post/get vars
   xarVarFetch('cal_sdow','int:0:6',$cal_sdow,0);
 
   //load the calendar class
   $c =& xarModAPIFunc('julian','user','factory','calendar');
   $c->setStartDayOfWeek($cal_sdow);
   //set the selected date parts, timestamp, and cal_date in the data array
   $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
   $bl_data['month'] = $c->getCalendarMonth($bl_data['selected_year'].$bl_data['selected_month']);
   $bl_data['longDayNames'] = $c->getLongDayNames($c->getStartDayOfWeek());
   $bl_data['calendar'] = $c;  
   //set the start date 
   $startdate = $bl_data['selected_year']."-".$bl_data['selected_month']."-".$bl_data['selected_day'];
   //get the events for the selected day
   $bl_data['event_array']=$c->getEvents($startdate); 
   //the next two variables help determine which color is displayed for this day depending on whether
   //it is a weekend or the current day
   $bl_data['daydate']=date('Ymd',strtotime($startdate));
   $bl_data['isweekend']=$c->isWeekend($startdate);
   $bl_data['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';
   //set the url to this page in session as the last page viewed
   $lastview=xarModURL('julian','user','day',array('cal_date'=>$bl_data['cal_date']));
   xarSessionSetVar('lastview',$lastview);
    return $bl_data;
}
?>
