<?php

    include_once(CALENDAR_ROOT.'Year.php');
    define('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKDAYS);
    function calendar_user_year()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
        $Year = new Calendar_Year($data['cal_year']);
        $Year->build(); // TODO: find a better way to handle this
        $data['Year'] =& $Year;
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data ;
    }

?>