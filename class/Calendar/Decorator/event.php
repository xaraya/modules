<?php

include_once CALENDAR_ROOT.'Day.php';
include_once CALENDAR_ROOT.'Hour.php';
include_once CALENDAR_ROOT.'Decorator.php';

/**
 *  Event
 *  container class for event information
 */
class Event extends Calendar_Decorator
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

    public $cE;
    public $year;
    public $month;
    public $day;

    public $entry   = array();
    public $entries = array();

    function __construct(Calendar $calendar)
    {   // set the author
        //$this->author = xarUserGetVar('uid');
        Calendar_Decorator::Calendar_Decorator($calendar);
        $this->cE = & $this->getEngine();
        $this->year = $this->calendar->year;
        $this->month = $this->calendar->month;
        $this->day = $this->calendar->day;
    }

    function setEntry($entry) {
        $this->entry = $entry;
    }
    function getEntry() {
        return $this->entry;
    }

    function addEntry1($entry) {
        $this->entries[] = $entry;
    }

    function getEntries() {
        return $this->entries;
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
    function current($day)
    {
        return $this->calendar->current();
    }



}

?>
