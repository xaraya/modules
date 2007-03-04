<?php

include_once(CALENDAR_ROOT.'Day.php');
// grab the Xaraya decorator class
sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");

function calendar_user_day()
{
    $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
    $Day = new Calendar_Day($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
    $Day->build();
    $data['Day'] =& $Day;
    $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
    return $data;
}

?>
