<?php
// $Id:$
include_once(CALENDAR_ROOT.'/Week.php');
// grab the Xaraya decorator class
include_once(CALENDAR_MODULE_INCLUDES.'Calendar/Decorator/Xaraya.php');

function calendar_user_week()
{
    $bl_data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
    $Week =& new Calendar_Week($bl_data['cal_year'],$bl_data['cal_month'],$bl_data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
    $Week->build();
    $bl_data['Week'] =& $Week; // pass a reference to the object to the template
    $bl_data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
    return $bl_data;
}
?>
