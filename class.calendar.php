<?php
/**
 *    $Id: class.calendar.php,v 1.4 2005/06/24 09:28:24 michelv01 Exp $
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
    function Calendar()
    {
        // default start day of week = sunday
        // TODO::Make this an Admin/User Setting
        $this->startDayOfWeek = xarModGetVar('julian','startDayOfWeek');//0;
        //xarVarFetch('startDayOfWeek','int:0:6',$startDayOfWeek,0);//$startDayofWeek=
        // load the locale date
        $localeData =& xarMLSLoadLocaleData();
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
     *    will return array of events for the date range specified for the current user
     *    @params $startdate string valid date as YYYY-MM-DD
     *    @params $enddate string valid date as YYYY-MM-DD
     *    @return array events for the range specified  
    * @package Xaraya eXtensible Management System
    * @copyright (C) 2004 by Metrostat Technologies, Inc.
    * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
    * @link http://www.metrostat.net
    *
    * @subpackage julian
    * initial template: Roger Raymond
    * @author Jodie Razdrh/John Kevlin/David St.Clair
     */
    function &getEvents($startdate,$enddate=null)
    {
      // Load up database
      $dbconn =& xarDBGetConn();
      //get db tables
      $xartable = xarDBGetTables();
      //set events table
      $event_table = $xartable['julian_events'];
      //load the event class
      $e = xarModAPIFunc('julian','user','factory','event');
      //build a day array
      $day_array = array("1"=>"Sunday","2"=>"Monday","3"=>"Tuesday","4"=>"Wednesday","5"=>"Thursday","6"=>"Friday","7"=>"Saturday");
      //build an array of units that coincides with an interval rule
      $units = array("1"=>"days","2"=>"weeks","3"=>"months","4"=>"years");
      $startdate=date('Y-m-d',strtotime($startdate));
      if(strcmp($enddate,""))
      {
         $enddate=date('Y-m-d',strtotime($enddate));
         $condition=" AND ((DATE_FORMAT(dtstart,'%Y-%m-%d')>='" . $startdate . "' AND DATE_FORMAT(dtstart,'%Y-%m-%d') <='" . $enddate . "') OR recur_freq>0) ";
      }
      else
      {
         $condition = " AND (DATE_FORMAT(dtstart,'%Y-%m-%d') ='". $startdate  ."' OR recur_freq>0)"; 
         //set the end date to the start date for recurring events
         $enddate=$startdate;
      }

      $event_data = array();
      //Getting all events that are scheduled for this user,public events for other users, and shared events for this user in
      //the date range specified.
      $current_user=xarUserGetVar('uid');
      $query = "SELECT event_id,created,dtstart,organizer,class,summary,description,categories,isallday,recur_freq,rrule,recur_count,recur_until,if(recur_until LIKE '0000%','',recur_until) as fRecurUntil,recur_interval,if(isallday,'',DATE_FORMAT(dtstart,'%l:%i %p')) as fStartTime,DATE_FORMAT(dtstart,'%Y-%m-%d') as fStartDate
                  FROM " . $event_table . "
                  WHERE (organizer='" . $current_user . "' OR (class='0' AND organizer!='" . $current_user . "') OR FIND_IN_SET('" . $current_user."',share_uids)) $condition
                  ORDER BY dtstart ASC;";
      $result = $dbconn->Execute($query);
       while(!$result->EOF)
       {
         $eventObj = $result->FetchObject(false);
         if (!$eventObj->recur_freq)
        {
            //this is a non-repeating event and falls in the current date range...add to the events array
            $e->setEventData($event_data,$eventObj->fStartDate,$eventObj);                                                                             
        }
        else
        {
            //determine if this repeating event ever falls in the current date range
            $nextTS = strtotime($eventObj->fStartDate);     
            $next_date = date("Y-m-d",$nextTS);
            //Keep adding the recurring events until we hit the enddate or the recur_until end date.
            $recur_until = 1;
            //If the db recur_until is set, check to see if we are past the recur_until date, otherwise it's 1 and it will fall through
            if (strcmp($eventObj->fRecurUntil,""))
                $recur_until = ($nextTS <= strtotime($eventObj->recur_until));
            while($nextTS <= strtotime($enddate) && $recur_until)
            {    
              //Add the event to the event array if the event is after or on the startdate
              if ($nextTS >= strtotime($startdate)) 
              {   
                $e->setEventData($event_data, $next_date,$eventObj);                                                             
              }
              /*calculate when this event would recur next. The loop will determine whether to add the next recur date or not*/
              if ($eventObj->recur_interval < 5 && $eventObj->recur_interval != 0)
              {
                 /*event repeats 1st, 2nd, 3rd or 4th day of the week every so many month(s) (i.e. 2nd Sunday every 3 months)*/   
                $newTS = strtotime(date("Y-m",strtotime($next_date))."-01 +"  . $eventObj->recur_freq . " ".$units[$eventObj->rrule]);  
                $next_date = date("Y-m-d",strtotime($eventObj->recur_interval ." ". $day_array[$eventObj->recur_count], $newTS)); 
              }
              else if ($eventObj->recur_interval == 5)
               {
                 /*event repeats a certain day the last week every so many month(s) (i.e. last Monday every two months)*/
                 $newTS = strtotime(date("Y-m",strtotime($next_date))."-01 +".$eventObj->recur_freq." ".$units[$eventObj->rrule]); 
                 $endMonthTS=strtotime(date('Y-m',$newTS)."-".date('t',$newTS));
                 $next_date= date('Y-m-d',strtotime("this " . $day_array[$eventObj->recur_count],strtotime("last week",$endMonthTS)));
               }
              else
              {
                 /*event repeats every so many days, weeks, months or years (i.e. every 2 weeks)*/
                 $next_date=date("Y-m-d",strtotime($next_date." +".$eventObj->recur_freq." ".$units[$eventObj->rrule]));
              }                                                                                                                    
              //Get the next recur date's timestamp                                                                        
              $nextTS = strtotime($next_date);
              //determine if the next recur date should be added 
              if (strcmp($eventObj->fRecurUntil,""))
                $recur_until = ($nextTS <= strtotime($eventObj->recur_until));                                                                                                                                                                                                                                                                                                   
            }    
        }                                                                    
         $result->MoveNext();
       }

        $result->Close();
        
      $event_linkage_table = $xartable['julian_events_linkage'];
      $query_linked = "SELECT event_id,hook_modid,hook_itemtype,hook_iid,dtstart,duration,isallday,rrule,recur_freq,recur_count,recur_until,if(recur_until LIKE '0000%','',recur_until) as fRecurUntil,recur_interval,if(isallday,'',DATE_FORMAT(dtstart,'%l:%i %p')) as fStartTime,DATE_FORMAT(dtstart,'%Y-%m-%d') as fStartDate
                  FROM `$event_linkage_table` WHERE (1)".$condition."ORDER BY dtstart ASC;";
      $result_linked = $dbconn->Execute($query_linked);
       while(!$result_linked->EOF)
       {
         $eventObj = $result_linked->FetchObject(false);
         if (!$eventObj->recur_freq)
        {
            //this is a non-repeating event and falls in the current date range...add to the events array
            $e->setLinkedEventData($event_data,$eventObj->fStartDate,$eventObj);                                                                             
        }
        else
        {
            //determine if this repeating event ever falls in the current date range
            $nextTS = strtotime($eventObj->fStartDate);     
            $next_date = date("Y-m-d",$nextTS);
            //Keep adding the recurring events until we hit the enddate or the recur_until end date.
            $recur_until = 1;
            //If the db recur_until is set, check to see if we are past the recur_until date, otherwise it's 1 and it will fall through
            if (strcmp($eventObj->fRecurUntil,""))
                $recur_until = ($nextTS <= strtotime($eventObj->recur_until));
            while($nextTS <= strtotime($enddate) && $recur_until)
            {    
              //Add the event to the event array if the event is after or on the startdate
              if ($nextTS >= strtotime($startdate)) 
              {   
                $e->setLinkedEventData($event_data, $next_date,$eventObj);                                                             
              }
              //calculate when this event would recur next. The loop will determine whether to add the next recur date or not
              if ($eventObj->recur_interval < 5 && $eventObj->recur_interval != 0)
              {
                 // event repeats 1st, 2nd, 3rd or 4th day of the week every so many month(s) (i.e. 2nd Sunday every 3 months)
                $newTS = strtotime(date("Y-m",strtotime($next_date))."-01 +"  . $eventObj->recur_freq . " ".$units[$eventObj->rrule]);  
                $next_date = date("Y-m-d",strtotime($eventObj->recur_interval ." ". $day_array[$eventObj->recur_count], $newTS)); 
              }
              else if ($eventObj->recur_interval == 5)
               {
                 // event repeats a certain day the last week every so many month(s) (i.e. last Monday every two months)
                 $newTS = strtotime(date("Y-m",strtotime($next_date))."-01 +".$eventObj->recur_freq." ".$units[$eventObj->rrule]); 
                 $endMonthTS=strtotime(date('Y-m',$newTS)."-".date('t',$newTS));
                 $next_date= date('Y-m-d',strtotime("this " . $day_array[$eventObj->recur_count],strtotime("last week",$endMonthTS)));
               }
              else
              {
                 // event repeats every so many days, weeks, months or years (i.e. every 2 weeks)
                 $next_date=date("Y-m-d",strtotime($next_date." +".$eventObj->recur_freq." ".$units[$eventObj->rrule]));
              }                                                                                                                    
              //Get the next recur date's timestamp                                                                        
              $nextTS = strtotime($next_date);
              //determine if the next recur date should be added 
              if (strcmp($eventObj->fRecurUntil,""))
                $recur_until = ($nextTS <= strtotime($eventObj->recur_until));                                                                                                                                                                                                                                                                                                   
            }    // while recurrence is still within wanted time span
              }    // if the event is recurring
          $result_linked->MoveNext();
       }    // while we have records
        $result_linked->Close();
      return $event_data;
    }
    
    /**
     *  creates an array used to build the final output
     *  @param string $d optional date of the week to build [ YYYYMMDD ] 
     */
    function &getCalendarWeek($d=null)
    {
        if(!isset($d)) $d =& xarModAPIFunc('julian','user','createUserDateTime','Ymd');
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
        if(!isset($d)) $d =& xarModAPIFunc('julian','user','createUserDateTime','Ym');
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
        if(!isset($y)) $y =& xarModAPIFunc('julian','user','createUserDateTime','Y');
        
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
        if(!xarVarValidate('int:0:6',$d)) {
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
        if(!isset($date)) $date =& xarModAPIFunc('julian','user','createUserDateTime','Ymd');
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
   /*determines if the current day is a Saturday or Sunday. Returns true or false
     The day param is expected to be a string date (i.e. YYYY-mm-dd, etc.)*/
   function isWeekend($day)
   {
      //convert day to a timestamp
      $dateTS=strtotime($day);
      //check to see if this day is on a Saturday (6) or Sunday (0)
      $isweekend=date('w',$dateTS)==0 || date('w',$dateTS)==6 ?1:0;
      return $isweekend;
   
   }     
}
?>
