<?php

    include_once(CALENDAR_ROOT.'Month/Weekdays.php');
    include_once(CALENDAR_ROOT.'Day.php');

    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    sys::import("modules.calendar.class.Calendar.Decorator.event");
    sys::import("modules.calendar.class.Calendar.Decorator.monthevent");
    sys::import('modules.query.class.query');

    function calendar_user_month()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
/*        $Month = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'],
            CALENDAR_FIRST_DAY_OF_WEEK);
*/
//        $events = xarModAPIFunc('icalendar','user','getevents',array());

        // get all the events. need to improve this query
        $xartable = xarDB::getTables();
        $q = new Query('SELECT', $xartable['calendar_event']);
//        $q->qecho();
        if (!$q->run()) return;
        $events = $q->output();

        $MonthEvents = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'],
            xarModVars::get('calendar', 'cal_sdow'));

        $MonthDecorator = new MonthEvent_Decorator($MonthEvents);
        $MonthDecorator->build($events);
        $data['Month'] =& $MonthDecorator;
        return $data;
    }

?>