<?php
/**
 * File: $Id$
 *
 * iCalendar VTODO class
 *
 * @package unassigned
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @link  link to information for the subpackage
 * @author Roger Raymond <iansym@xaraya.com> 
 */

/**
 * iCalendar_Todo class
 *
 * iCalendar VTODO properties.
 *
 * @package calendar
 * @author Roger Raymond <iansym@xaraya.com> 
 */

class iCalendar_Todo
{
    // optional and can occur only once
    var $class;
    var $created;
    var $completed;
    var $description;
    var $dtstamp;
    var $dtstart;
    var $geo;
    var $last_modified;
    var $location;
    var $organizer;
    var $priority;
    var $percent;
    var $recurrence_id;
    var $sequence;
    var $status;
    var $summary;
    var $due;
    var $duration;
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
    var $rdate;
    var $related_to;
    var $resources;
    var $request_status;
    var $rrule;
    
    // optional reference(s) to iCalendar_Alarm object
    var $valarm;
    
    function iCalendar_Todo()
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