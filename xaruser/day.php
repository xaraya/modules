<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

include_once(CALENDAR_ROOT.'Hour.php');
include_once(CALENDAR_ROOT.'Day.php');
// grab the Xaraya decorator class
sys::import("modules.calendar.class.Calendar.Decorator.Xaraya");
sys::import("modules.calendar.class.Calendar.Decorator.event");
sys::import("modules.calendar.class.Calendar.Decorator.dayevent");
sys::import('xaraya.structures.query');

function calendar_user_day()
{
    $data = xarMod::apiFunc('calendar', 'user', 'getUserDateTimeInfo');
    $DayEvents = new Calendar_Day($data['cal_year'], $data['cal_month'], $data['cal_day'], CALENDAR_FIRST_DAY_OF_WEEK);
    $args = [
        'day' => &$Day,
    ];
    $day_endts = $DayEvents->getTimestamp() + xarModVars::get('calendar', 'day_end') + 3600;

    // get all the events. need to improve this query
    $xartable =& xarDB::getTables();
    $q = new Query('SELECT', $xartable['calendar_event']);
//        $q->qecho();
    if (!$q->run()) {
        return;
    }
    $events = $q->output();

    // Do some calculations to complete the entries' info
    $slots = [];

    // Loop through the events
    $eventcount = count($events);
    for ($j=0;$j<$eventcount;$j++) {
        // make sure events don't go past the end of the day
        $events[$j]['end_time'] = min($events[$j]['end_time'], $day_endts);

        $placed = false;
        $slotcount = count($slots);
        for ($i=0;$i<$slotcount;$i++) {
            if ($events[$j]['start_time'] >= $slots[$i][1]) {
                foreach ($slots as $slot) {
                    $events[$slot[0]]['neighbors'] = $slotcount;
                }
                $thisslot = $i;
                $slots = [0 => [$j,$events[$j]['end_time']]];
                $placed = true;
                break;
            }
        }
        if (!$placed) {
            $thisslot = $slotcount;
            $slots[] = [$j,$events[$j]['end_time']];
        }
        $events[$j]['place'] = $thisslot;
    }
    foreach ($slots as $slot) {
        $events[$slot[0]]['neighbors'] = $slotcount;
    }

    //foreach($events as $event) {var_dump($event);echo "<br />";}
    /*
        $selection = array();
        foreach ( $entries as $entry ) {
            $Hour = new Calendar_Hour(2000,1,1,1);
            $Hour->setTimeStamp($entry['start_time']);

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
