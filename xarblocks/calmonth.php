<?php
xarModAPILoad('overlib');
/**
 * 
 * Metrostat Calendar
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

/**
 * initialise block
 * 
 * @author David St.Clair 
 * @access public 
 * @param none $ 
 * @return nothing 
 * @throws no exceptions
 * @todo nothing
 */
function julian_calmonthblock_init()
{
    return true;
} 

/**
 * get information on block
 * 
 * @author David St.Clair 
 * @access public 
 * @param none $ 
 * @return data array
 * @throws no exceptions
 * @todo nothing
 */
function julian_calmonthblock_info()
{ 
    // Values
    return array('text_type'        => 'Calendar',
        'module'                    => 'julian',
        'text_type_long'            => 'Metrostat Calendar',
        'allow_multiple'            => false,
        'form_content'              => false,
        'form_refresh'              => false,
        'show_preview'              => true);
} 

/**
 * display calmonth block - this displays the current month
 * 
 * @author David St.Clair 
 * @access public 
 * @param none $ 
 * @return data array on success or void on failure
 * @throws no exceptions
 * @todo implement centre menu position
 */
function julian_calmonthblock_display($blockinfo)
{ 
    // Security Check
    if (!xarSecurityCheck('Viewjulian', 0)) return;
    
    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Calendar');
    }
    //set the selected date parts, timestamp, and cal_date in the data array
    $args = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    //load the calendar class
    $c =& xarModAPIFunc('julian','user','factory','calendar');
    $args['month2'] = $c->getCalendarMonth(date("Ym"));
    $args['cal_sdow'] = $c->getStartDayOfWeek();
    $args['shortDayNames'] = $c->getShortDayNames($args['cal_sdow']);
    $args['calendar'] =& $c;
    //determine today and the month that today is in. The current month is the month that will be displayed
    $args['todays_timestamp'] = strtotime("today");
    $args['todays_month']=$month = date("m");
    //set the current year
    $year=date("Y");
    //set the start date to the first day of the selected month
    $startdate = $year."-".$month."-01";
    //determine the number of days in the selected month
    $numdays=date('t',strtotime("today"));
    //set the end date to the last day of the selected month
    $enddate = $year."-".$month."-".$numdays;
    //get the events for the current month
//    $args['event_array']=$c->getEvents($startdate,$enddate);
	$args['event_array']= xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$enddate));
        
    if (empty($blockinfo['template'])) {
        $template = 'calmonth';
    } else {
        $template = $blockinfo['template'];
    }
    $blockinfo['content'] = xarTplBlock('julian', $template, $args);
    return $blockinfo;
} 
?>
