<?php

/**
 * iCalendar_Journal class
 *
 * iCalendar VJOURNAL properties.
 *
 * @package calendar
 * @author Roger Raymond <iansym@xaraya.com> 
 */

class iCalendar_Journal
{

    // optional and can occur only once
    var $class;
    var $created;
    var $description;
    var $dtstart;
    var $dtstamp;
    var $last_modified;
    var $organizer;
    var $recurrence_id;
    var $sequence;
    var $status;
    var $summary;
    var $uid;
    var $url;

    // optional and can occur more than once
    var $attach;
    var $attendee;
    var $categories;
    var $comment;
    var $contact;
    var $exdate;
    var $exrule;
    var $related_to;
    var $rdate;
    var $rrule;
    var $request_status;

    // optional reference(s) to iCalendar_Alarm object
    var $valarm;

    function iCalendar_Journal()
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