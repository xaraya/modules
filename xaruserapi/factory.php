<?php
/**
 *  Factory method that allows the creation of new objects
 *  @version $Id: factory.php,v 1.5 2003/06/24 21:30:30 roger Exp $
 *  @param string $class the name of the object to create
 *  @return object the created object
 */
function &calendar_userapi_factory($class)
{
    static $calobject;
    static $icalobject;
    static $eventobject;
    static $importobject;
    static $exportobject;
    static $alarmobject;
    static $modinfo;

    if(!isset($modinfo)) {
        $modInfo =& xarModGetInfo(xarMod::getRegID('calendar'));
    }

    switch(strtolower($class)) {

        case 'calendar':
            if(!isset($calobject)) {
                sys::import("modules.$modInfo[osdirectory].class.calendar");
                $calobject =& new Calendar;
            }
            return $calobject;
            break;

        case 'ical_parser':
            if(!isset($icalobject)) {
                sys::import("modules.$modInfo[osdirectory].class.ical_parser");
                $icalobject =& new iCal_Parser;
            }
            return $icalobject;
            break;

        case 'event':
            if(!isset($eventobject)) {
                sys::import("modules.$modInfo[osdirectory].class.event");
                $eventobject =& new Event;
            }
            return $eventobject;
            break;

        /*
        case 'import':
            break;

        case 'export':
            break;

        case 'alarm':
            break;
        */
        default:
            return;
            break;
    }
}
?>
