<?php
/**
 * View one year
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
 * This function gets the events for the selected year.
 *
 * This module:
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author MichelV
 * @param int catid ID of the category to filter for
 * @param   type param2 Description of parameter 2
 */

function julian_user_year($args)
{
    extract ($args);
    // Get the category id
    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('ViewJulian', 1)) {
       return;
    }
    // Starting day of the week
    $cal_sdow = xarModGetVar('julian','startDayOfWeek');
    // load the calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    // set the selected date parts,timestamp, and cal_date in the data array
    $data = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    $data['year'] = $c->getCalendarYear($data['selected_year']);
    $data['shortDayNames'] = $c->getShortDayNames($cal_sdow);
    $data['calendar'] = $c;

    // set the start day to the first month and day of the selected year
    $startdate=$data['selected_year']."-01-01";
    // set the end date to the last month and last day of the selected year
    $enddate=$data['selected_year']."-12-31";
    // get the events for the selected year
    $data['event_array']=xarModApiFunc('julian','user','getall', array('startdate'=>$startdate, 'enddate'=>$enddate, 'catid' => $catid));
    // set the url to this page in session as the last page viewed
    $lastview=xarModURL('julian','user','year',array('cal_date'=>$data['cal_date']));

    xarSessionSetVar('lastview',$lastview);

    return $data;
}
?>
