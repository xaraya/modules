<?php
/**
 * Calendar defaults
 *
 * @package unassigned
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @link http://xaraya.simiansynapse.com
 * @author Roger Raymond <roger@xaraya.com>
 */

//======================================================================
//  Capture and set the calendar's First Day Of The Week
//======================================================================
$cal_sdow = (int) xarModUserVars::get('calendar', 'cal_sdow');
if(isset($cal_sdow)) {
    // Catch and set the first day of the week for the calendar views
    define('CALENDAR_FIRST_DAY_OF_WEEK', $cal_sdow);
} else {
    // set the first day of the week to the admin/user default
    define('CALENDAR_FIRST_DAY_OF_WEEK', 0);
}
//echo 'cal_sdow = '.CALENDAR_FIRST_DAY_OF_WEEK;
//======================================================================
// Define the location of the PEAR::Calendar files
//======================================================================
if (!defined('CALENDAR_ROOT')) {
    define('CALENDAR_ROOT', xarModVars::get('calendar','pearcalendar_root'));
}
//======================================================================
// Define directory locations for this module
//======================================================================
$modinfo = xarModGetInfo(xarModGetIdFromName('calendar'));
if (!defined('CALENDAR_MODULE_ROOT')) {
    define('CALENDAR_MODULE_ROOT',"modules/{$modinfo['directory']}/");
}
if (!defined('CALENDAR_MODULE_INCLUDES')) {
    define('CALENDAR_MODULE_INCLUDES',CALENDAR_MODULE_ROOT.'xarincludes/');
}

/**
 *  Used to get the current view the calendar is in (Day, Week, Month, Year)
 */
function calendar_userapi_currentView()
{
    xarVarFetch('func','str::',$func,'main',XARVAR_NOT_REQUIRED);
    $valid = array('day','week','month','year');
    $func = strtolower($func);
    if(!in_array($func,$valid)) {
        return xarModVars::get('calendar','default_view');
    } else {
        return $func;
    }
}


function calendar_userapi_buildURL($args=array())
{
    extract($args); unset($args);

    return xarModURL('calendar','user',$cal_view,
                array('cal_date'=>$cal_date));
}


function calendar_userapi_currentMonthURL()
{
    return xarModAPIFunc(
                'calendar',
                'user',
                'buildURL',
                array(
                    'cal_view'=>'month',
                    'cal_date'=>xarLocaleFormatDate('%Y%m')
                    )
                );
}

function calendar_userapi_currentWeekURL()
{
    return xarModAPIFunc(
                'calendar',
                'user',
                'buildURL',
                array(
                    'cal_view'=>'week',
                    'cal_date'=>xarLocaleFormatDate('%Y%m')
                    )
                );
}

function calendar_userapi_currentDayURL()
{
    return xarModAPIFunc(
                'calendar',
                'user',
                'buildURL',
                array(
                    'cal_view'=>'day',
                    'cal_date'=>xarLocaleFormatDate('%Y%m')
                    )
                );
}

function calendar_userapi_currentYearURL()
{
    return xarModAPIFunc(
                'calendar',
                'user',
                'buildURL',
                array(
                    'cal_view'=>'year',
                    'cal_date'=>xarLocaleFormatDate('%Y%m')
                    )
                );
}

?>