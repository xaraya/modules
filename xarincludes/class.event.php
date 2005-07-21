<?php

/**
 *  xarEvent
 *  container class for event information
 */
class Event
{
    var $author;            // event author
    var $startdate;         // (YYYYMMDD) event start date
    var $starttime;         // (HHMMSS) event start time
    var $enddate;           // (YYYYMMDD) event end date (if any)
    var $endtime;           // (HHMMSS) event end time (if any)
    var $duration;          // in seconds (-1 for all-day)
    var $repeat;            // type of repeating
    var $repeatfreq;        // frequency of the repeating
    var $repeattype;        // frequency type
    var $repeatonnum;       // repeat on 1st,2nd,3rd,4th,Last
    var $repeatonday;       // 

    function Event()
    {   // set the author
        //$this->author = xarUserGetVar('uid');
    }

    function buildEvent(&$id)
    {
        return true;
    }

    /**
     *  appends data to the block layout array.
     *
     *  @param array $bl_data the block layout data array
     */
    function getEventDataForBL(&$bl_data)
    {
        $bl_data['event_id']            = 0;
        $bl_data['calendar_id']         = 'default';
        $bl_data['event_owner']         = 0;
        $bl_data['event_start_date']    = 'YYYYMMDD';
        $bl_data['event_start_time']    = 'HHMMSS';
        $bl_data['event_end_date']      = 'YYYYMMDD';
        $bl_data['event_end_time']      = 'HHMMSS';
        $bl_data['event_title']         = 'title text';
        $bl_data['event_description']   = 'event description';
        // event recurrence rule
        $bl_data['event_rrule_'] = null;
        // event exceptions
        $bl_data['event_exdate'] = null;
        $bl_data['event_exrrule_'] = null;
    }

    function setStartTime($time)
    {
        $this->starttime =& $time;
    }

    function setEndTime($time)
    {
        $this->endtime =& $time;
    }
    
    function setStartDate($date)
    {
        $this->startdate =& $date;
    }
    
    function setEndDate($date)
    {
        $this->enddate =& $date;
    }
    
    function setDuration($days,$hours,$minutes)
    {
        $seconds = (int) ($days * 24 * 60 * 60) ;
        $seconds += (int) ($hours * 60 * 60) ;
        $seconds += (int) ($minutes * 60) ;
        $this->duration =& $seconds;
    }
    
    function setRepeat($repeat)
    {
        $this->repeat =& $repeat;
    }
    
    function setRepeatFreq($freq)
    {
        $this->repeatfreq =& $freq;
    }
    
    function setRepeatType($type)
    {
        $this->repeattype =& $type;
    }
    
    function setRepeatOnNum($on)
    {
        $this->repeatonnum =& $on;
    }
    
    function setRepeatOnDay($day)
    {
        $this->repeatonday =& $day;
    }
    
    
    
}

?>
