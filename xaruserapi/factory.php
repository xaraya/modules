<?php
/**
 * Factory method that allows the creation of new objects
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian development Team
 */

/**
 *  Factory method that allows the creation of new objects
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

    if (!isset($modinfo)) {
        $modInfo = xarModGetInfo(xarModGetIDFromName('julian'));
    }

    switch(strtolower($class)) {

        case 'calendar':
            if(!isset($calobject)) {
                require_once("modules/$modInfo[osdirectory]/class.calendar.php");
                $calobject = new Calendar;
            }
            return $calobject;
            break;

        case 'ical_parser':
            if(!isset($icalobject)) {
                require_once("modules/$modInfo[osdirectory]/class.ical_parser.php");
                $icalobject = new iCal_Parser;
            }
            return $icalobject;
            break;

        case 'event':
            if(!isset($eventobject)) {
                require_once("modules/$modInfo[osdirectory]/class.event.php");
                $eventobject = new Event;
            }
            return $eventobject;
            break;

        default:
            break;
    }

    return;
}
?>