<?php
/**
 *  Factory method that allows the creation of new objects
 *  @version $Id:$
 *  @param string $class the name of the object to create
 *  @return object the created object
 */
function &icalendar_userapi_factory($class)
{
    static $icalobject;
    static $modinfo;
    
    if(!isset($modinfo)) {
        $modInfo =& xarModGetInfo(xarModGetIDFromName('icalendar'));
    }
    
    switch(strtolower($class)) {
    
        case 'ical_parser':
            if(!isset($icalobject)) {
                require_once("modules/$modInfo[osdirectory]/class.ical_parser.php");
                $icalobject =& new iCal_Parser;
            }
            return $icalobject;
            break;
        
        default:
            return;
            break;
    }
}
?>
