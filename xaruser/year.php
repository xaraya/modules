<?php

/**
*
* This function gets the events for the selected year.
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

function julian_user_year($args)
{
    extract ($args);

    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;

    
    // Security check
    if (!xarSecurityCheck('Viewjulian')) return; 
    // Starting day of the week   
    $cal_sdow = xarModGetVar('julian','startDayOfWeek');
    //load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    //set the selected date parts,timestamp, and cal_date in the data array
    $bl_data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    $bl_data['year'] =& $c->getCalendarYear($bl_data['selected_year']);
    $bl_data['shortDayNames'] =& $c->getShortDayNames($cal_sdow);
    $bl_data['calendar'] =& $c;
    
    //set the start day to the first month and day of the selected year  
    $startdate=$bl_data['selected_year']."-01-01";
    //set the end date to the last month and last day of the selected year
    $enddate=$bl_data['selected_year']."-12-31";
    //get the events for the selected year
    $bl_data['event_array']=xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$enddate, 'catid' => $catid));
    //set the url to this page in session as the last page viewed
    $lastview=xarModURL('julian','user','year',array('cal_date'=>$bl_data['cal_date']));
    
    xarSessionSetVar('lastview',$lastview);
    
    return $bl_data;
}
?>
