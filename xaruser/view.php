<?php
    sys::import("modules.calendar.class.Calendar.Decorator.event");
    sys::import("modules.calendar.class.Calendar.Decorator.dayevent");

    function calendar_user_view()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
        $DayEvents = new Calendar_Day($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
        $args = array(
            'day' => &$Day,
        );
        $events = xarModAPIFunc('icalendar','user','getevents',$args);
        return $data;
    }

?>
