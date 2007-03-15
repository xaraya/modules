<?php
    include_once(CALENDAR_ROOT.'Hour.php');
    include_once(CALENDAR_ROOT.'Day.php');
    // grab the Xaraya decorator class
    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    sys::import("modules.calendar.class.Calendar.Decorator.event");
    sys::import("modules.calendar.class.Calendar.Decorator.dayevent");

    function calendar_user_day()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
        $DayEvents = new Calendar_Day($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
        $args = array(
            'day' => &$Day,
        );
        $events = xarModAPIFunc('icalendar','user','getevents',$args);
        $selection = array();
        $slots = array(0 => 0);
        foreach ( $events as $row ) {
            $Hour = new Calendar_Hour(2000,1,1,1);
            $Hour->setTimeStamp($row['start']);

            // Create the decorator, passing it the Hour
            $event = new Event($Hour);

            $entry = array(
                        'name' => $row['desc'],
                        'start' => $row['start'],
                        'end' => $row['end'],
                        'length' => $row['end'] - $row['start'],
                    );
            // Attach the payload
            $event->setEntry($entry);

            // Add the decorator to the selection
            $selection[] = $event;
        }
        $DayDecorator = new DayEvent_Decorator($DayEvents);
        $DayDecorator->build($events);
        $data['Day'] =& $DayDecorator;
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data;
    }

?>
