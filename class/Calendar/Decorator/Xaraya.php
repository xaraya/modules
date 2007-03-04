<?php
/**
 * File: $Id$
 * Xaraya Decorator for PEAR::Calendar Objects
 * Adds a wrapper to basic Xaraya Module APIs
 *
 */

sys::import('modules.calendar.pear.Calendar.Decorator');

class Calendar_Decorator_Xaraya extends Calendar_Decorator
{
    function __construct(&$Calendar)
    {
        parent::__construct($Calendar);
    }

    /**
     * Gets the URI string for the previous calendar unit
     * @param string calendar unit to fetch uri for (year,month,week or day etc)
     * @return string
     * @access public
     */
    function prev($unit)
    {
        $method = 'prev'.$unit;
        $stamp  = $this->{$method}('timestamp');
        return $this->buildUriString($unit, $stamp);
    }

    /**
     * Gets the URI string for the current calendar unit
     * @param string calendar unit to fetch uri for (year,month,week or day etc)
     * @return string
     * @access public
     */
    function current($unit)
    {
        $method = 'this'.$unit;
        $stamp  = $this->{$method}('timestamp');
        return $this->buildUriString($unit, $stamp);
    }

    /**
     * Gets the URI string for the next calendar unit
     * @param string calendar unit to fetch uri for (year,month,week or day etc)
     * @return string
     * @access public
     */
    function next($unit)
    {
        $method = 'next'.$unit;
        $stamp  = $this->{$method}('timestamp');
        return $this->buildUriString($unit, $stamp);
    }

    /**
     * Returns the value for the previous year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2002 or timestamp
     * @access public
     */
    function prevYear($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year-1, $this->calendar->month, $this->calendar->day, 0, 0, 0);
        return $this->calendar->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for this year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2003 or timestamp
     * @access public
     */
    function thisYear($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month, $this->calendar->day, 0, 0, 0);
        return $this->calendar->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for next year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2004 or timestamp
     * @access public
     */
    function nextYear($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year+1, $this->calendar->month, $this->calendar->day, 0, 0, 0);
        return $this->calendar->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for the previous month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 4 or Unix timestamp
     * @access public
     */
    function prevMonth($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month-1, $this->calendar->day, 0, 0, 0);
        return $this->calendar->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for this month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 5 or timestamp
     * @access public
     */
    function thisMonth($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month, $this->calendar->day, 0, 0, 0);
        return $this->calendar->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for next month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 6 or timestamp
     * @access public
     */
    function nextMonth($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month+1, $this->calendar->day, 0, 0, 0);
        return $this->calendar->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for the previous week
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 10 or timestamp
     * @access public
     */
    function prevWeek($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month, $this->calendar->children[1]->day-7, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for this week
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 11 or timestamp
     * @access public
     */
    function thisWeek($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month, $this->calendar->children[1]->day, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for the next week
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 12 or timestamp
     * @access public
     */
    function nextWeek($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month, $this->calendar->children[1]->day+7, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for the previous day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 10 or timestamp
     * @access public
     */
    function prevDay($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month, $this->calendar->day-1, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for this day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 11 or timestamp
     * @access public
     */
    function thisDay($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month, $this->calendar->day, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Returns the value for the next day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 12 or timestamp
     * @access public
     */
    function nextDay($format = 'int')
    {
        $ts = $this->calendar->cE->dateToStamp($this->calendar->year, $this->calendar->month, $this->calendar->day+1, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->calendar->cE->stampToDay($ts));
    }

    /**
     * Build the URI string
     * @param string method substring
     * @param int timestamp
     * @return string build uri string
     * @access private
     */
    function buildUriString($method, $stamp)
    {

        //var_dump(get_class_methods($this->calendar));
        //var_dump($this);
        //die();
        // we want to create the method by seeing what Object is creating this
        $obj_methods = get_class_methods($this->calendar);
        switch($obj_methods[0]) {
            case 'Calendar_Day':
                $method = 'day';
                break;
            case 'Calendar_Week':
                $method = 'week';
                break;
            case 'Calendar_Month_Weeks':
            case 'Calendar_Month_Weekdays':
                $method = 'month';
                break;
            case 'Calendar_Year':
                $method = 'year';
                break;
        }

        $cal_date = xarLocaleFormatDate('%Y%m%d',$stamp);
        $uriString = xarModURL('calendar','user',$method,array('cal_date'=>$cal_date));
        return $uriString;
    }

}

?>
