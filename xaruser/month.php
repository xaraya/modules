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

        $MonthEvents = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'] + 1,
            xarModVars::get('calendar', 'cal_sdow'));
        $end_time = $MonthEvents->getTimestamp();
        $MonthEvents = new Calendar_Month_Weekdays(
            $data['cal_year'],
            $data['cal_month'],
            xarModVars::get('calendar', 'cal_sdow'));
        $start_time = $MonthEvents->getTimestamp();

        // get all the events. need to improve this query
        $xartable = xarDB::getTables();
        $q = new Query('SELECT', $xartable['calendar_event']);
        $q->ge('start_time', $start_time);
        $q->lt('start_time', $end_time);
//        $q->qecho();
        if (!$q->run()) return;
        $events = $q->output();

        $MonthDecorator = new MonthEvent_Decorator($MonthEvents);
        $MonthDecorator->build($events);
        $data['Month'] =& $MonthDecorator;

        $q = new Query('SELECT');
        $a[] = $q->plt('start_time',$start_time);
        $a[] = $q->pge('end_time',$start_time);
        $b[] = $q->plt('start_time',$end_time);
        $b[] = $q->pge('end_time',$end_time);
        $c[] = $q->pgt('start_time',$start_time);
        $c[] = $q->ple('end_time',$end_time);

        $d[] = $q->pqand($a);
        $d[] = $q->pqand($b);
        $d[] = $q->pqand($c);
        $q->qor($d);
        $data['conditions'] = $q;

        return $data;
    }

?>