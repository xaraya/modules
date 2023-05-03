<?php
/**
 *    $Id: class.calendar.php,v 1.5 2003/06/24 20:30:28 roger Exp $
 *  xarCalendar
 *    Class to gather data for a specific calendar view
 */

class Calendar
{
    var $startDayOfWeek;
    var $monthNamesLong = array();
    var $monthNamesShort = array();
    var $dayNamesLong = array();
    var $dayNamesMedium = array();
    var $dayNamesShort = array();

    /**
     *    constructor
     */
    function __construct()
    {
        // default start day of week = sunday
        // TODO::Make this an Admin/User Setting
        $this->startDayOfWeek = 0;

        // load the locale date
        $localeData =& xarLocale::loadData();
        //echo '<pre>'; print_r($localeData); echo '</pre>';
        // long month names from locale.xml
        $this->monthNamesLong = array(
            $localeData["/dateSymbols/months/1/full"],
            $localeData["/dateSymbols/months/2/full"],
            $localeData["/dateSymbols/months/3/full"],
            $localeData["/dateSymbols/months/4/full"],
            $localeData["/dateSymbols/months/5/full"],
            $localeData["/dateSymbols/months/6/full"],
            $localeData["/dateSymbols/months/7/full"],
            $localeData["/dateSymbols/months/8/full"],
            $localeData["/dateSymbols/months/9/full"],
            $localeData["/dateSymbols/months/10/full"],
            $localeData["/dateSymbols/months/11/full"],
            $localeData["/dateSymbols/months/12/full"]
        );

        // short month names from locale.xml
        $this->monthNamesShort = array(
            $localeData["/dateSymbols/months/1/short"],
            $localeData["/dateSymbols/months/2/short"],
            $localeData["/dateSymbols/months/3/short"],
            $localeData["/dateSymbols/months/4/short"],
            $localeData["/dateSymbols/months/5/short"],
            $localeData["/dateSymbols/months/6/short"],
            $localeData["/dateSymbols/months/7/short"],
            $localeData["/dateSymbols/months/8/short"],
            $localeData["/dateSymbols/months/9/short"],
            $localeData["/dateSymbols/months/10/short"],
            $localeData["/dateSymbols/months/11/short"],
            $localeData["/dateSymbols/months/12/short"]
        );

        // long day names from locale.xml
        $this->dayNamesLong = array(
            $localeData["/dateSymbols/weekdays/1/full"],
            $localeData["/dateSymbols/weekdays/2/full"],
            $localeData["/dateSymbols/weekdays/3/full"],
            $localeData["/dateSymbols/weekdays/4/full"],
            $localeData["/dateSymbols/weekdays/5/full"],
            $localeData["/dateSymbols/weekdays/6/full"],
            $localeData["/dateSymbols/weekdays/7/full"]
        );

        // short day names from locale.xml
        $this->dayNamesMedium = array(
            $localeData["/dateSymbols/weekdays/1/short"],
            $localeData["/dateSymbols/weekdays/2/short"],
            $localeData["/dateSymbols/weekdays/3/short"],
            $localeData["/dateSymbols/weekdays/4/short"],
            $localeData["/dateSymbols/weekdays/5/short"],
            $localeData["/dateSymbols/weekdays/6/short"],
            $localeData["/dateSymbols/weekdays/7/short"]
        );

        // returns just the first letter from the shortDayNames
        $this->dayNamesShort = array(substr($this->dayNamesMedium[0],0,1),
                                     substr($this->dayNamesMedium[1],0,1),
                                     substr($this->dayNamesMedium[2],0,1),
                                     substr($this->dayNamesMedium[3],0,1),
                                     substr($this->dayNamesMedium[4],0,1),
                                     substr($this->dayNamesMedium[5],0,1),
                                     substr($this->dayNamesMedium[6],0,1));
    }

    /**
     *    will return array of events for the range specified
     *    @params $date1 string valid date as YYYYMMDD
     *    @params $date2 string valid date as YYYYMMDD
     *    @return array events for the range specified
     */
    function &getEvents($date1,$date2=null)
    {
        return true;
    }

    /**
     *  creates an array used to build the final output
     *  @param string $d optional date of the week to build [ YYYYMMDD ]
     */
    function &getCalendarWeek($d=null)
    {
        if(!isset($d)) $d = xarMod::apiFunc('calendar','user','createUserDateTime','Ymd');
        $year = substr($d,0,4);
        $month = substr($d,4,2);
        $day = substr($d,6,2);

        $month = $this->getCalendarMonth($year.$month);

        foreach($month as $week) {
            if(in_array($d,$week)) {
                $week_array = $week;
                break;
            }
        }

        return $week;
    }

    /**
     *  creates an array used to build the final output
     *  @param string $d optional date of the month to build [ YYYYMM ]
     */
    function &getCalendarMonth($d=null)
    {
        if(!isset($d)) $d = xarMod::apiFunc('calendar','user','createUserDateTime','Ym');
        $year  = substr($d,0,4);
        $month = substr($d,4,2);

        $month_array = array();
        $numDays = gmdate('t',gmmktime(0,0,0,$month,1,$year));
        $dowFirstDay = gmdate('w',gmmktime(0,0,0,$month,1,$year));
        $dowLastDay = gmdate('w',gmmktime(0,0,0,$month,$numDays,$year));

        // calculate the days needed for a full starting week
        if($dowFirstDay < $this->startDayOfWeek) {
            $pastDays = $dowFirstDay - $this->startDayOfWeek + 7;
        } else {
            $pastDays = $dowFirstDay - $this->startDayOfWeek;
        }
        if($pastDays < 0) $pastDays = -$pastDays;

        // calculate the days needed for a full ending week
        if($dowLastDay < $this->startDayOfWeek) {
            $nextDays = $this->startDayOfWeek - $dowLastDay - 1;
        } else {
            $nextDays = $dowLastDay - $this->startDayOfWeek - 6;
        }
        if($nextDays < 0) $nextDays = -$nextDays;

        $start = gmdate('Ymd',gmmktime(0,0,0,$month,1-$pastDays,$year));
        $last = gmdate('Ymd',gmmktime(0,0,0,$month,$numDays+$nextDays,$year));
        $numWeeks = ceil(($this->dateToDays($last) - $this->dateToDays($start))/7);
        $current_day = $this->dateToDays($start);

        // build the month array
        for($i=0; $i<$numWeeks; $i++) {
            for($d=0; $d<7; $d++) {
                $date = $this->daysToDate($current_day);
                $month_array[$i][$d] = $date;
                $current_day++;
            }
        }

        return $month_array;
    }

    /**
     *  creates an array used to build the final output
     *  @param string $d optional date of the year to build [ YYYY ]
     */
    function &getCalendarYear($y=null)
    {
        if(!isset($y)) $y = xarMod::apiFunc('calendar','user','createUserDateTime','Y');

        $year_array = array();
        // year month loops
        for($i=1;$i<=12;$i++) {
            $m = sprintf('%02d',$i);
            $year_array[$i] = $this->getCalendarMonth($y.$m);
        }
        return $year_array;
    }

    /**
     *  Sets the day the calendar starts on (0=Sunday through 6=Saturday)
     *  @param int $d day of week the calendar should start on
     */
    function setStartDayOfWeek($d)
    {
        // validate the input
        if(!xarVar::validate('int:0:6',$d)) {
            // we'll just leave it as is then
            return true;
        }
        $this->startDayOfWeek =& $d;
        return true;
    }

    /**
     *  Returns the day the calendar starts on (0=Sunday through 6=Saturday)
     *  @return int day of week the calendar starts on
     */
    function &getStartDayOfWeek()
    {
        return $this->startDayOfWeek;
    }

    /**
     *  Returns the day the calendar starts on (0=Sunday through 6=Saturday)
     *  @return bool if the day is
     */
    function dayIs($dow=0,$date=null)
    {
        if(!isset($date)) $date = xarMod::apiFunc('calendar','user','createUserDateTime','Ymd');
        $year = substr($date,0,4);
        $month = substr($date,4,2);
        $day = substr($date,6,2);
        if($dow == date('w',mktime(0,0,0,$month,$day,$year))) {
            return true;
        }
        return false;
    }

    function &getLongMonthNames()
    {
        return $this->monthNamesLong;
    }

    function &getShortMonthNames()
    {
        return $this->monthNamesShort;
    }

    function &getLongDayNames($sdow=0)
    {
        if($sdow == 0) return $this->dayNamesLong;
        $ordered_array = array();
        for($i=0;$i<7;$i++) {
            $ordered_array[] = $this->dayNamesLong[$sdow];
            if(++$sdow > 6) $sdow=0;
        }
        return $ordered_array;
    }

    function &getMediumDayNames($sdow=0)
    {
        if($sdow == 0) return $this->dayNamesMedium;
        $ordered_array = array();
        for($i=0;$i<7;$i++) {
            $ordered_array[] = $this->dayNamesMedium[$sdow];
            if(++$sdow > 6) $sdow=0;
        }
        return $ordered_array;
    }

    function &getShortDayNames($sdow=0)
    {
        if($sdow == 0) return $this->dayNamesShort;
        $ordered_array = array();
        for($i=0;$i<7;$i++) {
            $ordered_array[] = $this->dayNamesShort[$sdow];
            if(++$sdow > 6) $sdow=0;
        }
        return $ordered_array;
    }

    function &MonthLong($month=1)
    {
        return $this->monthNamesLong[--$month];
    }
    function &MonthShort($month=1)
    {
        return $this->monthNamesLong[--$month];
    }
    function &DayLong($day)
    {
        if(!isset($day)) $day = 0;
        return $this->dayNamesLong[$day];
    }
    function &DayMedium($day)
    {
        if(!isset($day)) $day = 0;
        return $this->dayNamesMedium[$day];
    }
    function &DayShort($day)
    {
        if(!isset($day)) $day = 0;
        return $this->dayNamesShort[$day];
    }
    function &FormatDate($date)
    {
        return true;
    }

    /**
     *  dateToDays
     *    borrowed from Date_Calc class
     *    @access private
     */
    function dateToDays($d)
    {
        $century = substr($d,0,2);
        $year    = substr($d,2,2);
        $month   = substr($d,4,2);
        $day     = substr($d,6,2);
        if($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }
        return(floor((146097*$century)/4)+floor((1461*$year)/4)+floor((153*$month+2)/5)+$day+1721119);
    }
    /**
     *  daysToDate
     *    borrowed from Date_Calc class
     *    @access private
     */
    function daysToDate($days)
    {
        $days   -= 1721119;
        $century = floor((4*$days-1)/146097);
        $days    = floor(4*$days-1-146097*$century);
        $day     = floor($days/4);
        $year    = floor((4*$day+3)/1461);
        $day     = floor(4*$day+3-1461*$year);
        $day     = floor(($day+4)/4);
        $month   = floor((5*$day-3)/153);
        $day     = floor(5*$day-3-153*$month);
        $day     = floor(($day+5)/5);
        if($month < 10) {
            $month +=3;
        } else {
            $month -=9;
            if($year++ == 99) {
                $year = 0;
                $century++;
            }
        }
        $century = sprintf("%02d",$century);
        $year = sprintf("%02d",$year);
        return(gmdate('Ymd',gmmktime(0,0,0,$month,$day,$century.$year)));
    }


}
?>
