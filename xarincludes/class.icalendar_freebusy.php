<?php

/**
 * iCalendar_Freebusy class
 *
 * iCalendar VFREEBUSY properties.
 *
 * @package calendar
 * @author Roger Raymond <iansym@xaraya.com> 
 */

class iCalendar_Freebusy
{
    var $dtstart;
    var $dtstamp;
    var $organizer;
    var $uid;
    var $url;
    var $dtend;
    var $duration;
    var $contact;

    var $attendee;
    var $comment;
    var $request_status;
    var $freebusy;
    
    function iCalendar_Freebusy()
    {
        
    }

    function &get($var) 
    {
        if(isset($this->{$var})) {
            return $this->{$var};
        } else {
            // we need to return some sort of error or exit code.
        }
    }
}

?>