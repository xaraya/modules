<?php
// $Id: month.php,v 1.3 2003/06/24 21:22:21 roger Exp $
include_once(CALENDAR_ROOT.'Month/Weekdays.php');
// grab the Xaraya decorator class
include_once('modules/calendar/xarincludes/Calendar/Decorator/Xaraya.php');

function calendar_user_month()
{
    $bl_data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');

    $Month =& new Calendar_Month_Weekdays(
        $bl_data['cal_year'],
        $bl_data['cal_month'],
        CALENDAR_FIRST_DAY_OF_WEEK);

    $Month->build();
    $bl_data['Month'] =& $Month;
    $bl_data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
    return $bl_data;
}
?>