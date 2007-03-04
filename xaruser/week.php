<?php

include_once(CALENDAR_ROOT.'Week.php');
// grab the Xaraya decorator class
sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");

function calendar_user_week()
{
    $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
    $Week = new Calendar_Week($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
    $Week->build();
    $data['Week'] =& $Week; // pass a reference to the object to the template
    $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
    return $data;
}
?>
