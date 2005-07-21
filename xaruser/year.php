<?php
// $Id: year.php,v 1.2 2003/06/24 20:08:10 roger Exp $
include_once(CALENDAR_ROOT.'Year.php');
// grab the Xaraya decorator class
include_once('modules/calendar/xarincludes/Calendar/Decorator/Xaraya.php');

define('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKDAYS);
function calendar_user_year()
{
    $bl_data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
    $Year =& new Calendar_Year($bl_data['cal_year']);
    $Year->build(); // TODO: find a better way to handle this
    $bl_data['Year'] =& $Year;
    $bl_data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
    return $bl_data ;
}

?>