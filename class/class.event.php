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

    function __construct()
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
     *  @param array $data the block layout data array
     */
    function getEventDataForBL(&$data)
    {
        $data['event_id']            = 0;
        $data['calendar_id']         = 'default';
        $data['event_owner']         = 0;
        $data['event_start_date']    = 'YYYYMMDD';
        $data['event_start_time']    = 'HHMMSS';
        $data['event_end_date']      = 'YYYYMMDD';
        $data['event_end_time']      = 'HHMMSS';
        $data['event_title']         = 'title text';
        $data['event_description']   = 'event description';
        // event recurrence rule
        $data['event_rrule_'] = null;
        // event exceptions
        $data['event_exdate'] = null;
        $data['event_exrrule_'] = null;
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
