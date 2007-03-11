<?php

    include_once(CALENDAR_ROOT.'Month/Weekdays.php');
    include_once(CALENDAR_ROOT.'Day.php');

    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    sys::import("modules.calendar.class.Calendar.Decorator.event");
    sys::import("modules.calendar.class.Calendar.Decorator.monthevent");

    function calendar_user_month()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
        $Month = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'],
            CALENDAR_FIRST_DAY_OF_WEEK);

        $events = xarModAPIFunc('icalendar','user','getevents',array());

        $MonthEvents = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'],
            CALENDAR_FIRST_DAY_OF_WEEK);

        $MonthDecorator = new MonthEvent_Decorator($MonthEvents);
        $MonthDecorator->build($events);
        $data['Month'] =& $MonthDecorator;
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data;
    }

?>