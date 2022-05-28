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
    public $author;            // event author
    public $startdate;         // (YYYYMMDD) event start date
    public $starttime;         // (HHMMSS) event start time
    public $enddate;           // (YYYYMMDD) event end date (if any)
    public $endtime;           // (HHMMSS) event end time (if any)
    public $duration;          // in seconds (-1 for all-day)
    public $repeat;            // type of repeating
    public $repeatfreq;        // frequency of the repeating
    public $repeattype;        // frequency type
    public $repeatonnum;       // repeat on 1st,2nd,3rd,4th,Last
    public $repeatonday;       //

    public $entry   = [];
    public $entries = [];

    public function __construct(Calendar $calendar)
    {   // set the author
        //$this->author = xarUser::getVar('id');
        Calendar_Decorator::Calendar_Decorator($calendar);
    }

    public function setEntry($entry)
    {
        $this->entry = $entry;
    }
    public function getEntry()
    {
        return $this->entry;
    }

    public function addEntry1($entry)
    {
        $this->entries[] = $entry;
    }

    public function getEntry1()
    {
        $entry = current($this->entries);
        if ($entry) {
            next($this->entries);
            return $entry;
        } else {
            reset($this->entries);
            return false;
        }
    }

    public function buildEvent(&$id)
    {
        return true;
    }

    /**
     *  appends data to the block layout array.
     *
     *  @param array $data the block layout data array
     */
    public function getEventDataForBL(&$data)
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

    public function setStartTime($time)
    {
        $this->starttime =& $time;
    }

    public function setEndTime($time)
    {
        $this->endtime =& $time;
    }

    public function setStartDate($date)
    {
        $this->startdate =& $date;
    }

    public function setEndDate($date)
    {
        $this->enddate =& $date;
    }

    public function setDuration($days, $hours, $minutes)
    {
        $seconds = (int) ($days * 24 * 60 * 60) ;
        $seconds += (int) ($hours * 60 * 60) ;
        $seconds += (int) ($minutes * 60) ;
        $this->duration =& $seconds;
    }

    public function setRepeat($repeat)
    {
        $this->repeat =& $repeat;
    }

    public function setRepeatFreq($freq)
    {
        $this->repeatfreq =& $freq;
    }

    public function setRepeatType($type)
    {
        $this->repeattype =& $type;
    }

    public function setRepeatOnNum($on)
    {
        $this->repeatonnum =& $on;
    }

    public function setRepeatOnDay($day)
    {
        $this->repeatonday =& $day;
    }
}
