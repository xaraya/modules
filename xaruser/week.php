<?php

    include_once(CALENDAR_ROOT.'Week.php');
    // grab the Xaraya decorator class
    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    sys::import("modules.calendar.class.Calendar.Decorator.event");
    sys::import("modules.calendar.class.Calendar.Decorator.weekevent");
    sys::import("modules.xen.xarclasses.xenquery");

    function calendar_user_week()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');

        // get all the events. need to improve this query
        $xartable =& xarDBGetTables();
        $q = new xenQuery('SELECT', $xartable['calendar_event']);
//        $q->qecho();
        if (!$q->run()) return;
        $events = $q->output();

        $WeekEvents = new Calendar_Week($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
        $WeekDecorator = new WeekEvent_Decorator($WeekEvents);
        $WeekDecorator->build($events);
        $data['Week'] =& $WeekDecorator; // pass a reference to the object to the template
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data;
    }
?>