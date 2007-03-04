<?php

    include_once(CALENDAR_ROOT.'Hour.php');
    include_once(CALENDAR_ROOT.'Day.php');
    // grab the Xaraya decorator class
    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    sys::import("modules.calendar.class.Calendar.Decorator.Event");

    function calendar_user_day()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
        $Day = new Calendar_Day($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
        $args = array(
            'day' => &$Day,
        );
        $events = xarModAPIFunc('calendar','user','getevents',$args);

        $selection = array();
        foreach ( $events as $row ) {
            $Hour = new Calendar_Hour(2000,1,1,1);
            $Hour->setTimeStamp($row['start_time']);

            // Create the decorator, passing it the Hour
            $event = new Event($Hour);

            // Attach the payload
            $event->setEntry($row['name']);

            // Add the decorator to the selection
            $selection[] = $event;
        }
        $Day->build($selection);

        $data['Day'] =& $Day;
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data;
    }

?>
