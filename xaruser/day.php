<?php
    include_once(CALENDAR_ROOT.'Hour.php');
    include_once(CALENDAR_ROOT.'Day.php');
    // grab the Xaraya decorator class
    sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
    sys::import("modules.calendar.class.Calendar.Decorator.event");
    sys::import("modules.calendar.class.Calendar.Decorator.dayevent");
    sys::import("modules.xen.xarclasses.xenquery");

    function calendar_user_day()
    {
        $data = xarModAPIFunc('calendar','user','getUserDateTimeInfo');
        $DayEvents = new Calendar_Day($data['cal_year'],$data['cal_month'],$data['cal_day'],CALENDAR_FIRST_DAY_OF_WEEK);
        $args = array(
            'day' => &$Day,
        );
        $day_endts = $DayEvents->getTimestamp() + xarModGetVar('calendar','day_end') + 3600;
//        $events = xarModAPIFunc('icalendar','user','getevents',$args);

        // get all the events. need to improve this query
        $xartable =& xarDBGetTables();
        $q = new xenQuery('SELECT', $xartable['calendar_event']);
//        $q->qecho();
        if (!$q->run()) return;
        $events = $q->output();

        // Do some calculations to complete the entries' info
        $slots = array();

        // Loop through the events
        $eventcount = count($events);
        for ($j=0;$j<$eventcount;$j++) {
            // make sure events don't go past the end of the day
            $events[$j]['end'] = min($events[$j]['end'], $day_endts);

            $placed = false;
            $slotcount = count($slots);
            for ($i=0;$i<$slotcount;$i++) {
                if ($events[$j]['start'] >= $slots[$i][1]) {
                        foreach ($slots as $slot) {
                            $events[$slot[0]]['neighbors'] = $slotcount;
                        }
                    $thisslot = $i;
                    $slots = array(0 => array($j,$events[$j]['end']));
                    $placed = true;
                    break;
                }
            }
            if (!$placed) {
                $thisslot = $slotcount;
                $slots[] = array($j,$events[$j]['end']);
            }
            $events[$j]['place'] = $thisslot;
        }
        foreach ($slots as $slot) $events[$slot[0]]['neighbors'] = $slotcount;

//foreach($events as $event) {var_dump($event);echo "<br />";}
/*
        $selection = array();
        foreach ( $entries as $entry ) {
            $Hour = new Calendar_Hour(2000,1,1,1);
            $Hour->setTimeStamp($entry['start']);

            // Create the decorator, passing it the Hour
            $event = new Event($Hour);

            // Attach the payload
            $event->setEntry($entry);

            // Add the decorator to the selection
            $selection[] = $event;
        }
        */
        $DayDecorator = new DayEvent_Decorator($DayEvents);
        $DayDecorator->build($events);
        $data['Day'] =& $DayDecorator;
        $data['cal_sdow'] = CALENDAR_FIRST_DAY_OF_WEEK;
        return $data;
    }

?>
