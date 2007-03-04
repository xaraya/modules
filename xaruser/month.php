<?php

include_once(CALENDAR_ROOT.'Month/Weekdays.php');
sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");

function calendar_user_month()
{
    $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');

    $Month = new Calendar_Month_Weekdays(
        $data['cal_year'],
        $data['cal_month'],
        CALENDAR_FIRST_DAY_OF_WEEK);

    $Month->build();
    $data['Month'] =& $Month;
    $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
    return $data;
}
?>