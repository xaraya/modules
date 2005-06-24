<?php
/**
 *  Factory method that allows the creation of new objects
 *  @version $Id: factory.php,v 1.2 2005/01/26 08:45:26 michelv01 Exp $
 *  @param string $class the name of the object to create
 *  @return object the created object
 */
function &julian_userapi_factory($class)
{
    static $calobject;
    static $icalobject;
    static $eventobject;
    static $importobject;
    static $exportobject;
    static $alarmobject;
    static $modinfo;
    
    if(!isset($modinfo)) {
        $modInfo =& xarModGetInfo(xarModGetIDFromName('julian'));
    }
    
    switch(strtolower($class)) {
    
        case 'calendar':
            if(!isset($calobject)) {
                require_once("modules/$modInfo[osdirectory]/class.calendar.php");
                $calobject =& new Calendar;
            }
            return $calobject;
            break;
        
        case 'ical_parser':
            if(!isset($icalobject)) {
                require_once("modules/$modInfo[osdirectory]/class.ical_parser.php");
                $icalobject =& new iCal_Parser;
            }
            return $icalobject;
            break;
        
        case 'event':
            if(!isset($eventobject)) {
                require_once("modules/$modInfo[osdirectory]/class.event.php");
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
