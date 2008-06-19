<?php

    include_once(CALENDAR_ROOT.'Week.php');
    // grab the Xaraya decorator class
    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    sys::import("modules.calendar.class.Calendar.Decorator.event");
    sys::import("modules.calendar.class.Calendar.Decorator.weekevent");
    sys::import('modules.query.class.query');

    function calendar_user_week()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');

        $WeekEvents = new Calendar_Week($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);

        $start_time = $WeekEvents->thisWeek;
        $end_time = $WeekEvents->nextWeek;
        // get all the events. need to improve this query and combine it with the uery in the template
        $xartable = xarDB::getTables();
        $q = new Query('SELECT', $xartable['calendar_event']);
        $q->ge('start_time', $start_time);
        $q->lt('start_time', $end_time);
//        $q->qecho();
        if (!$q->run()) return;
        $events = $q->output();

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

        $WeekDecorator = new WeekEvent_Decorator($WeekEvents);
        $WeekDecorator->build($events);
        $data['Week'] =& $WeekDecorator; // pass a reference to the object to the template
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data;
    }
?>