<?php
/**
 * Month Block  - standard Initialization function
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
 * This module:
 * Metrostat Calendar
 *
 * @link http://www.metrostat.net
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 */
/**
 * initialise block
 *
 * @author David St.Clair
 * @access public
 * @param none $
 * @return bool true
 * @throws no exceptions
 * @todo nothing
 */
function julian_calmonthblock_init()
{
    return array('catfilter' => '');
}

/**
 * get information on block
 *
 * @author David St.Clair
 * @access public
 * @param none $
 * @return array data
 * @throws no exceptions
 * @todo nothing
 */
function julian_calmonthblock_info()
{
    // Values
    return array('text_type'  => 'Calendar',
        'module'              => 'julian',
        'text_type_long'      => 'Metrostat Calendar',
        'allow_multiple'      => false,
        'form_content'        => false,
        'form_refresh'        => false,
        'show_preview'        => true);
}

/**
 * display calmonth block - this displays the current month
 *
 * @author David St.Clair, MichelV
 * @access public
 * @param array $blockinfo
 * @return array blockinfo on success or void on failure
 * @throws no exceptions
 * @todo implement centre menu position
 */
function julian_calmonthblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ReadJulianBlock', 0, 'Block', $blockinfo['title'])) {return;}

    /* Get variables from content block.
     * Content is a serialized array for legacy support, but will be
     * an array (not serialized) once all blocks have been converted.
     */
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Set the selected date parts, timestamp, and cal_date in the data array
    $args = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    // Load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    $args['month2'] = $c->getCalendarMonth(date("Ym"));
    $args['cal_sdow'] = xarModGetVar('julian','startDayOfWeek'); //$c->getStartDayOfWeek();
    $args['shortDayNames'] = $c->getShortDayNames($args['cal_sdow']);
    $args['calendar'] = $c;
    // Determine today and the month that today is in. The current month is the month that will be displayed
    $args['todays_timestamp'] = strtotime("today");
    $args['todays_month']=$month = date("m");
    // Set the current year
    $year=date("Y");
    // Set the start date to the first day of the selected month
    $startdate = $year."-".$month."-01";
    // Determine the number of days in the selected month
    $numdays=date('t',strtotime("today"));
    // Set the end date to the last day of the selected month
    $enddate = $year."-".$month."-".$numdays;

    if (isset ($vars['CatAware'])) {
        $CatAware = $vars['CatAware'];
    } else {
        $CatAware = 0;
    }
    $args['CatAware'] = $CatAware; // Needed?

    // Get the events for the current month and set catid empty
    $args['event_array']= xarModApiFunc('julian','user','getall',
                                         array('startdate'=>$startdate,
                                               'enddate'=>$enddate,
                                               'catid' =>(((!$CatAware==0) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)));

    if (empty($blockinfo['template'])) {
        $template = 'calmonth';
    } else {
        $template = $blockinfo['template'];
    }
    $blockinfo['content'] = xarTplBlock('julian', $template, $args);
    return $blockinfo;
}
?>
