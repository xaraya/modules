<?php
// $Id: day.php,v 1.2 2003/06/24 20:08:10 roger Exp $

include_once(CALENDAR_ROOT.'Day.php');
// grab the Xaraya decorator class
include_once('modules/calendar/xarincludes/Calendar/Decorator/Xaraya.php');

function calendar_user_day()
{
    $bl_data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
    $Day =& new Calendar_Day($bl_data['cal_year'],$bl_data['cal_month'],$bl_data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
    $Day->build();
    $bl_data['Day'] =& $Day;
    $bl_data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
    return $bl_data;
}

?>
